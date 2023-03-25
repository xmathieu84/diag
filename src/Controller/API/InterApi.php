<?php


namespace App\Controller\API;



use App\Entity\Intervention;
use App\Helper\DemandeurRepoTrait;

use App\Service\DefinirDate;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class InterApi extends AbstractController
{
use DemandeurRepoTrait;

private EntityManagerInterface $entityManager;
private DefinirDate $definirDate;



    public function __construct(EntityManagerInterface $entityManager,DefinirDate $definirDate)
    {
        $this->entityManager=$entityManager;
        $this->definirDate= $definirDate;


    }
    public function __invoke(Intervention $data):Intervention
    {
       $demandeur = $this->demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $data->setIntDem($demandeur)
            ->setStatuInter('Nouvelle demande')
            ->setCreatedAT($this->definirDate->aujourdhui());
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        return $data;
    }

}