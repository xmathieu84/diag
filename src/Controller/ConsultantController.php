<?php

namespace App\Controller;




use App\Helper\DemandeurRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\RapportRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Repository\ConsultantHDDRepository;
use App\Service\choixTemplate;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ConsultantController
 * @package App\Controller
 */
class ConsultantController extends AbstractController
{
    use RequestTrait, EntityManagerTrait, RapportRepoTrait,DemandeurRepoTrait;

    /**
     * @Route("/accueilConsultant",name="accueilConsultant")
     * @isGranted("ROLE_CONSULTANT")
     *
     * @return Response
     */
    public function accueilConsultant():Response
    {
        $consultant = $this->demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        return $this->render('consultant/accueil.html.twig', [
                'consultant'=>$consultant
        ]);
    }

    /**
     * @Route("/rapportConsultant",name="rapportConsultant")
     * @isGranted("ROLE_CONSULTANT")
     * @return Response
     */
    public function rapportConsultant():Response
    {
        $consultant = $this->demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $rapports  = $consultant->getRapports();

        return $this->render('consultant/rapport.html.twig', [
            'rapports' => $rapports
        ]);
    }

    /**
     * @Route("/trouverRapport")
     * @isGranted("ROLE_CONSULTANT")
     * @return JsonResponse
     */
    public function trouverRapport():JsonResponse
    {
        $codeRecherche = $this->request->getContent();
        $rapport = $this->rapportRepository->findOneBy(['codeRecherche' => $codeRecherche]);
        $reponse = new JsonResponse();
        if ($rapport) {
            $reponse->setData(['trouve' => 'ok', 'archive' => $rapport->getArchive(), 'idRapport' => $rapport->getId()]);
        } else {
            $reponse->setData(['trouve' => 'non']);
        }

        return $reponse;
    }

    /**
     * @Route("/lierRapport")
     * @isGranted("ROLE_CONSULTANT")
     * @return JsonResponse
     */
    public function lierRapport():JsonResponse
    {
        $id = $this->request->getContent();
        $consultant = $this->demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $rapport = $this->rapportRepository->findOneBy(['id' => $id]);
        $rapport->addConsultant($consultant);
        $this->manager->persist($rapport);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/devenirDemandeur",name="devenirDemandeur")
     * @isGranted("ROLE_CONSULTANT")
     *
     * @param GuardAuthenticatorHandler $guardHandler
     * @return RedirectResponse
     */
    public function devenirDemandeur(GuardAuthenticatorHandler $guardHandler):RedirectResponse
    {
        $user = $this->getUser();
        $user->setRoles(['ROLE_DEMANDEUR','ROLE_CONSULTANT']);

        $this->manager->persist($user);
        $this->manager->flush();

        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );
        $this->container->get('security.token_storage')->setToken($token);

        return $this->redirectToRoute('demandeur');
    }

    /**
     * @param int $id
     * @param choixTemplate $choixTemplate
     * @return Response
     * @throws NonUniqueResultException
     * @Route ("/voirRapport/{id}",name="voirRapportConsultant")
     * @Security ("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT') or is_granted('ROLE_GRANDCOMPTE') or is_granted('ROLE_INSTITUTION')")
     */
    public function streamRapport(int $id,choixTemplate $choixTemplate){
        $rapport = $this->rapportRepository->findForConsultant($this->getUser(),$id);

        return $this->render('consultant/voirRapport.html.twig',[
            'rapport'=>$rapport,

        ]);


    }
}
