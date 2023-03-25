<?php

namespace App\Controller;

use App\Entity\CodePromo;
use App\Form\CodePromoType;
use App\Helper\AbonnementGcirepoTrait;
use App\Helper\AboRepoTrait;
use App\Helper\CodePromoRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\CodePromoRepository;
use App\Repository\FichierOTDRepository;
use App\Service\Mail;
use Doctrine\ORM\NonUniqueResultException;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminCodePromoController
 * @package App\Controller
 * Gestion des codes promotionnels coté adminiqtrateur du site
 */
class AdminCodePromoController extends AbstractController
{
    use RequestTrait, EntityManagerTrait, etatAboRepoTrait, AboRepoTrait, CodePromoRepoTrait,AbonnementGcirepoTrait,CodePromoRepoTrait;
    /**
     * @Route("/administrateur/code/promo", name="admin_code_promo")
     */
    public function index(FichierOTDRepository $fichierOTD, Mail $mail)

    {
        $abonnements = $this->abonnementsRepository->findAll();
        $code = $this->codePromoRepository->findBy([],['dateDebut'=>'ASC']);

        return $this->render('administrateur/admin_code_promo/index.html.twig', [
                'abonnements'=>$abonnements,
                'codes'=>$code
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/administrateur/promoOtd")
     */
    public function promoOTD():JsonResponse{
        $content = json_decode($this->request->getContent());
        $debut = \DateTime::createFromFormat('Y-m-d',$content->debut);
        $fin = \DateTime::createFromFormat('Y-m-d',$content->fin);
        $abonnement = $this->abonnementsRepository->findOneBy(['id'=>$content->type]);
        $code = new CodePromo();
        $code->setActif(true)
            ->setAbonnementOtd($abonnement)
            ->setProfil('otd')
            ->setDateDebut($debut)
            ->setDateFin($fin)
            ->setCodeReduc($content->code)
            ->setRemise($content->remise);

       $this->manager->persist($code);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route("administrateur/promoGc")
     */
    public function promoGc():JsonResponse{
        $content = json_decode($this->request->getContent());
        $debut = \DateTime::createFromFormat('Y-m-d',$content->debut);
        $fin = \DateTime::createFromFormat('Y-m-d',$content->fin);

        $abonnements = $this->abonnementGciRepository->findBy(['profil'=>$content->type]);
        foreach ($abonnements as $key=> $abonnement){
            $code = new CodePromo();
            $code->setActif(true)
                ->setAbonnementGci($abonnement)
                ->setProfil($content->type)
                ->setDateDebut($debut)
                ->setDateFin($fin)
                ->setCodeReduc($content->code.$key)
                ->setRemise($content->remise);

            $this->manager->persist($code);
        }

        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @Route("/administrateur/promoInsti")
     */
    public function promoInsti():JsonResponse{
        $content = json_decode($this->request->getContent());
        $debut = \DateTime::createFromFormat('Y-m-d',$content->debut);
        $fin = \DateTime::createFromFormat('Y-m-d',$content->fin);

        if (intval($content->type)===0){
            $abonnement = $this->abonnementGciRepository->findOneBy(['profil'=>$content->type]);
            $profil = $content->type;

        }
        else{
            $abonnement = $this->abonnementGciRepository->abonnementInsti($content->type,'insti');
            $profil = 'insti';
        }
        $code = new CodePromo();
        $code->setActif(true)
            ->setAbonnementGci($abonnement)
            ->setProfil($profil)
            ->setDateDebut($debut)
            ->setDateFin($fin)
            ->setCodeReduc($content->code)
            ->setRemise($content->remise);
        $this->manager->persist($code);
        $this->manager->flush();
        return new JsonResponse();
    }

}
