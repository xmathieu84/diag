<?php

namespace App\Controller;

use App\Form\PasswordType;
use App\Form\UserType;

use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Service\Mail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


use App\Repository\UserRepository;
use App\Service\codeActivation;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;




class SecurityController extends AbstractController
{
    use EntityManagerTrait, RequestTrait;


    /**
     * @Route("/motDePasseOublie", name="oubliepsswrd")
     *
     * @param UserRepository $user
     * @return void
     */
    public function demandeMail()
    {
        return $this->render('security/motdepasseoublie.html.twig');
    }

    /**
     * @Route("/verifyMail")
     *
     * @param UserRepository $userRepository
     * @param codeActivation $codeActivation
     * @param Mail $mail
     * @return JsonResponse
     */
    public function verifyMail(UserRepository $userRepository, codeActivation $codeActivation, Mail $mail)
    {
        $contenu = $this->request->getContent();

        $code = $codeActivation->generer();
        $reponse = new JsonResponse();
        $user = $userRepository->findOneBy(['email'=>$contenu]);
        $mail->mailPerdu($user->getEmail(),$code);
        $user->setResetPassword($code);
        $this->manager->persist($user);
        $this->manager->flush();

        return $reponse->setData('trouve');
    }

    /**
     * @Route("/bonmail",name="bonmail")
     */
    public function reussitemotedepasse()
    {
        return $this->render('security/reussitemail.html.twig');
    }

    /**
     * @Route("/newpassword/{code}",name="newpassword")
     */
    public function nouveauPassword(
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        Request $request,
        $code,
        UserPasswordHasherInterface $encoder,
        MailerInterface $mailer
    ) {


        $user = $userRepository->findOneBy(['resetPassword' => $code]);
        $form = $this->createForm(PasswordType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['password']->getData() === $form['confirmPassword']->getData()) {

                $user->setResetPassword(null);
                $pass = $encoder->hashPassword($user, $user->getPassword());
                $user->setPassword($pass);
                $manager->persist($user);
                $manager->flush();


                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('security/newPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }



    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        $user=  $this->getUser();
        $user->setIsConnect(false);
        $this->manager->persist($user);
        $this->manager->flush();
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}
