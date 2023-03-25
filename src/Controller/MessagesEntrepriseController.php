<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\ContactDiagDroneType;

use App\Form\ReponseType;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\MessageRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;

use App\Service\Mail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MessagesEntrepriseController extends AbstractController
{
    use EntityManagerTrait, RequestTrait,EntrepriseRepoTrait;
    /**
     * @Route("/entreprise/message/messagesRecu", name="messageReçu")
     * @isGranted("ROLE_ENTREPRISE")
     */
    public function messagerecu(MessageRepository $messageRepository, choixTemplate $choixTemplate)
    {
        $user = $this->getUser();
        $mail = $user->getEmail();

        $messages = $messageRepository->findBy(['destinataire' => $mail, 'statut' => 'non-lu']);

        return $this->render('entreprise/messageRecu.html.twig', [

            'messages' => $messages,

        ]);
    }
    /**
     * @Route ("/entreprise/message/lire/{id}",name="lire")
     */
    public function lireMessage($id, MessageRepository $messageRepository, choixTemplate $choixTemplate, DefinirDate $definirDate)
    {
        $user = $this->getUser();
        $entreprise = $this->entrepriseRepository->findOneBy(['user'=>$user]);

        $message = $messageRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(ReponseType::class, $message);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

            $date = $definirDate->aujourdhui();
            $message->setDateReponse($date);
            $this->manager->persist($message);
            $this->manager->flush();
        }
        return $this->render('entreprise/lireMessage.html.twig', [
            'message' => $message,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("entreprise/message/lu/{id}",name="lu")
     * @isGranted("ROLE_ENTREPRISE")
     */

    public function marque($id, MessageRepository $messageRepository)
    {
        $message = $messageRepository->findOneBy(['id' => $id]);
        $message->setStatut('lu');
        $this->manager->persist($message);
        $this->manager->flush();
        $reponse = new JsonResponse();
        $reponse->setData(['reponse' => json_encode('lu')]);
        return $reponse;
    }
    /**
     * 
     * @Route("/entreprise/message/contacterDiagDrone",name="contactDD")
     * @isGranted("ROLE_ENTREPRISE")
     */

    public function contactDiagDrone( DefinirDate $definirDate,Mail $email)
    {
        $user = $this->getUser();
        $mail = $user->getEmail();
        $message = new Message();
        $date = $definirDate->aujourdhui();
        $message->setExpediteur($mail)
            ->setDate($date)
            ->setDestinataire('diag-drone');
        $form = $this->createForm(ContactDiagDroneType::class, $message);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($message);
            $this->manager->flush();
            $email->mailContact($message->getExpediteur(),$message->getSujet(),$message->getContenu());
            return $this->redirectToRoute('messageReçu');
        }
        return $this->render('entreprise/contactDD.html.twig', [

            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/entreprise/message/messageArchive",name="messaArchi")
     * isGranted("ROLE_ENTREPRISE")
     * 
     */
    public function messageArchive(MessageRepository $messageRepository, choixTemplate $choixTemplate)
    {
        $user = $this->getUser();

        $mail = $user->getEmail();

        $messagesArchives = $messageRepository->archive('non-lu', $mail);

        return $this->render('entreprise/messageArchive.html.twig', [

            'messages' => $messagesArchives,
            'entreprise' => $user->getSalarie()->getEntreprise()
        ]);
    }
}
