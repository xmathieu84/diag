<?php

namespace App\Controller;

use DateTime;
use App\Entity\Message;
use App\Form\ReponseType;
use App\Form\AdminMessageType;
use Symfony\Component\Mime\Email;
use App\Repository\MessageRepository;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AbonnementsRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMessageController extends AbstractController
{
    /**
     * @Route("/admin/message", name="adminMessage")
     */
    public function Message(
        EntityManagerInterface $manager,
        Request $request,
        MailerInterface $mailer,
        EntrepriseRepository $entrepriseRepository,
        AbonnementsRepository $abonnementsRepository
    ) {
        $message = new Message();
        $form = $this->createForm(AdminMessageType::class, $message);
        $date = new DateTime('NOW');
        $message->setDate($date);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $destinataire = $form['selectionDestinataire']->getData();
            $message->setExpediteur('contact@drone.com');

            switch ($destinataire) {
                case 'entreprise':
                    $email = (new Email())
                        ->from('contact@drone.com')
                        ->to($form['mail']->getData())
                        ->priority(Email::PRIORITY_HIGH)
                        ->subject($message->getSujet())
                        ->text($message->getContenu());
                    $mailer->send($email);
                    $message->setDestinataire($form['mail']->getData());

                    break;
                case 'all':

                    $entreprises = $entrepriseRepository->findAll();
                    foreach ($entreprises as $entreprise) {
                        $email = (new Email())
                            ->from('contact@drone.com')
                            ->to($entreprise->getUser()->getEmail())
                            ->priority(Email::PRIORITY_HIGH)
                            ->subject($message->getSujet())
                            ->text($message->getContenu());
                        $mailer->send($email);
                        $message->setDestinataire($destinataire);
                    }
                    break;
                case 'premium':
                    $abonnement = $abonnementsRepository->findOneBy(['nom'=>$destinataire]);
                    $entreprises = $entrepriseRepository->findBy(['abonnements' => $abonnement]);
                    foreach ($entreprises as $entreprise) {
                        $email = (new Email())
                            ->from('contact@drone.com')
                            ->to($entreprise->getUser()->getEmail())
                            ->priority(Email::PRIORITY_HIGH)
                            ->subject($message->getSujet())
                            ->text($message->getContenu());
                        $mailer->send($email);
                        $message->setDestinataire($destinataire);
                    }
                    break;
            }
            $manager->persist($message);
            $manager->flush();
            return $this->redirectToRoute('administrateur');
        }
        return $this->render('administrateur/message.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/administrateur/consulterMessages",name="consulterMessages")
     */
    public function consulterMessages(MessageRepository $messageRepository)
    {
        $messages = $messageRepository->findBy(['statut' => 'non-lu', 'destinataire' => 'diag-drone']);

        return $this->render('administrateur/consulter.html.twig', [
            'messages' => $messages
        ]);
    }
    /**
     * @Route("/admin/lireMessage/{id}",name="lireMessage")
     */
    public function LireMessage(MessageRepository $messageRepository, $id, EntityManagerInterface $manager, Request $request, MailerInterface $mailer)
    {
        $message = $messageRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(ReponseType::class, $message);
        $date = new DateTime('NOW');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message->setDateReponse($date);
            $message->setStatut('repondu');
            $manager->persist($message);
            $manager->flush();
            $email = (new Email())
                ->from('contact@diag-drone.com')
                ->to($message->getExpediteur())

                ->priority(Email::PRIORITY_HIGH)
                ->subject('Reponse à votre message')
                ->text($message->getReponse());



            $mailer->send($email);
            return $this->redirectToRoute('consulterMessages');
        }
        return $this->render('administrateur/liremessage.html.twig', [
            'message' => $message,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/compte")
     */
    public function compter(MessageRepository $messageRepository)
    {
        $message = $messageRepository->count(['statut' => 'non-lu', 'destinataire' => 'diag-drone']);

        $reponse = new JsonResponse();
        $reponse->setData(['nombreMessage' => json_encode($message)]);
        return $reponse;
    }
    /**
     * @Route("/admin/lu/{id}",name="messageLu")
     */
    public function marque($id, MessageRepository $messageRepository, EntityManagerInterface $manager)
    {
        $message = $messageRepository->findOneById($id);
        $message->setStatut('lu');
        $manager->persist($message);
        $manager->flush();
        $reponse = new JsonResponse();
        $reponse->setData(['reponse' => json_encode('lu')]);
        return $reponse;
    }
    /**
     * 
     * @Route("/admin/messageArchive",name="archive")
     */
    public function ancienMessage(MessageRepository $messageRepository)
    {
        $messages = $messageRepository->archive('non-lu', 'diag-drone');

        return $this->render('administrateur/archive.html.twig', [
            'messages' => $messages
        ]);
    }
    /**
     * 
     * @Route("/admin/repondre/{id}",name="repondre")
     */
    public function repondre(
        $id,
        MessageRepository $messageRepository,
        EntityManagerInterface $manager,
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer
    ) {
        $message = $messageRepository->findOneById($id);
        $expediteur = $message->getExpediteur();
        $form = $this->createForm(ReponseType::class, $message);
        $date = new DateTime('NOW', new \DateTimeZone('Europe/Paris'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contenu = $message->getContenu();
            $message->setDateReponse($date);
            $manager->persist($message);
            $manager->flush();

            $user = $userRepository->findOneByEmail($expediteur);
            if ($user && $user->getUserEnt()) {
                $email = (new Email())
                    ->from('contact@diag-drone.com')
                    ->to($expediteur)
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('Nouveau message')
                    ->text("Vous avez un nouveau message dans votre espace diag-drone.");
                $mailer->send($email);
            } else {
                $email = (new Email())
                    ->from('contact@diag-drone.com')
                    ->to($expediteur)
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('Nouveau message')
                    ->text($contenu);
                $mailer->send($email);
            }
        }
        return $this->render('administrateur/repondre.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
}
