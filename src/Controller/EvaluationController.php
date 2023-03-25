<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Helper\ReservationRepoTrait;
use App\Repository\NoteRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

class EvaluationController extends AbstractController
{
    use EntityManagerTrait, RequestTrait, ReservationRepoTrait;

    /**
     * @Route("/demandeur/note/{id}",name="noteSalarie")
     * @isGranted("ROLE_DEMANDEUR")
     *
     * @param integer $id
     * @param definirDate $definirDate
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function noteSalarie($id, definirDate $definirDate, choixTemplate $choixTemplate): Response
    {
        $template = $choixTemplate->templateDem($this->getUser());
        $reservation = $this->reservationRepository->findOneBy(['id' => $id]);
        $salarie = $reservation->getSalarie();
        $intervention = $reservation->getIntervention();
        $maintenant = $definirDate->aujourdhui();
        $note = new Note();
        $note->setSalarie($salarie)
            ->setDate($maintenant);
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $intervention->setNote($note);
            $this->manager->persist($note);
            $this->manager->persist($intervention);
            $this->manager->flush();
            return $this->redirectToRoute('demandeur_encours');
        }

        return $this->render('evaluation/note.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
            'intervention' => $intervention,
            'template' => $template
        ]);
    }

    /**
     * @Route("/demandeur/commentaire/{salarie}",name="com")
     *  @isGranted("ROLE_DEMANDEUR")
     *
     * @param [type] $salarie
     * @param NoteRepository $noteRepository
     * @param choixTemplate $choixTemplate
     * @return Response
     */
    public function commentaire($salarie, NoteRepository $noteRepository, choixTemplate $choixTemplate): Response
    {

        $notes = $noteRepository->findBy(['salarie' => $salarie], ['date' => 'DESC']);
        $template = $choixTemplate->templateDem($this->getUser());
        return $this->render('evaluation/commentaire.html.twig', [
            'user' => $this->getUser(),
            'notes' => $notes,
            'template' => $template
        ]);
    }
}
