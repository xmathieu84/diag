<?php

namespace App\Controller;

use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\DemandeurRepository;
use App\Repository\UserRepository;

use App\Service\codeActivation;
use App\Service\DefinirDate;
use App\Service\Mail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class EmailController
 * @package App\Controller
 */
class EmailController extends AbstractController
{
    use EntityManagerTrait,EntrepriseRepoTrait,SalarieRepoTrait,DemandeurRepoTrait,etatAboRepoTrait,AgentRepoTrait;
    /**
     * @Route("/activation", name="activation")
     */
    public function index()
    {
        return $this->render('email/activation.html.twig', []);
    }

    /**
     * @Route("/activation/{code}",name="codeActivation")
     * @param $code
     * @param UserRepository $userRepository
     *
     * @return RedirectResponse
     */
    public function activerCompte($code, UserRepository $userRepository,DefinirDate $definirDate):Response
    {

        $user = $userRepository->findOneBy(['codeActivation' => $code]);
        if (!$user){
            return $this->render('accueil/activationFaite.html.twig');
        }
        else{
            $user->setActive(true)
                ->setCodeActivation(null);
            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('accept');
        }

    }

    /**
     * @Route("/activationAcceptée",name="accept")
     *
     * @param null $id
     * @return Response
     */
    public function activationOK($id = null)
    {
        return $this->render('accueil/activation.html.twig', [
            'id' => $id
        ]);
    }

    /**
     * @return Response
     * @Route("/compte non actif",name="compteNonActif")
     * @Security("is_granted('ROLE_SALARIE') or is_granted('ROLE_GRANDCOMPTE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT') or is_granted('ROLE_INSTITUTION')")
     */
    public function compteNonActif(){
        $user = $this->getUser();

        return $this->render('accueil/compteNonActif.html.twig',[
            'id'=>$user->getId()
        ]);
    }

    /**
     * @param codeActivation $codeActivation
     * @param Mail $mail
     * @return JsonResponse
     * @throws TransportExceptionInterface
     * @Route("/nouveauLienActivation")
     * @Security("is_granted('ROLE_SALARIE') or is_granted('ROLE_GRANDCOMPTE') or is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT') or is_granted('ROLE_INSTITUTION')")
     */
    public function nouveauLienActivation(codeActivation $codeActivation,Mail $mail){
        $user = $this->getUser();
        $code = $codeActivation->generer();
        $user->setCodeActivation($code);
        $this->manager->persist($user);
        $this->manager->flush();
        if ($user->hasRole('ROLE_INSTITUTION')||$user->hasRole('ROLE_GRANDCOMPTE')){
            $mail->mailInscriptionAgent($user->getAgent());
        }
        if ($user->hasRole('ROLE_ENTREPRISE')||$user->hasRole('ROLE_SALARIE')){
            $mail->mailInscriptionSalarie($user->getSalarie());
        }
        else{
            $mail->confirmerMail($user->getCodeActivation(), $user->getEmail());
        }

       return new JsonResponse();

    }
}
