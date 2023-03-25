<?php

namespace App\Controller;

use App\Repository\SalarieRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception as ExceptionAlias;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeclarationHonneurController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    private SalarieRepository $salarieRepository;
    private EntityManager $manager;

    /**
     * @param SalarieRepository $salarieRepository
     * @param \Doctrine\ORM\EntityManagerInterface $manager
     */
    public function __construct(SalarieRepository $salarieRepository, EntityManagerInterface $manager)
    {
        $this->salarieRepository = $salarieRepository;
        $this->manager = $manager;
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/entreprise/declaration-honneur/{id}",name="declarationH")
     */
    public function declarationHonneur($id):RedirectResponse{
        dump($id);
        $salarie = $this->salarieRepository->find($id);
        $salarie->setIsHonneur(true);
        $this->manager->persist($salarie);
        $this->manager->flush();
        return $this->redirectToRoute('listePack');
    }
}