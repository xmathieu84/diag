<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Helper\DemandeurRepoTrait;
use DateTime;
use App\Entity\Photo;
use App\Service\Mail;
use App\Service\Fichier;
use App\Entity\Proposition;
use App\Entity\Intervention;
use App\Form\InterInstiType;
use App\Helper\RequestTrait;
use App\Service\DefinirDate;
use App\Helper\InterRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\TypeInterRepoTrait;
use App\Helper\ListeInterRepoTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InterInstitutionnelController extends AbstractController
{
    use TypeInterRepoTrait, ListeInterRepoTrait, EntityManagerTrait, InterRepoTrait, SalarieRepoTrait, RequestTrait,DemandeurRepoTrait;

    /**
     * @Route("/creerInterInsti/{idListe}/{idType}/{date}/{otd}/{ville}/{codePostal}")
     * @Security("is_granted('ROLE_NIVEAU1') and is_granted('ROLE_ABONNE') or is_granted('ROLE_NIVEAU1GC') and is_granted('ROLE_ABONNE')")
     * @param integer $idListe
     * @param integer $idType
     * @param datetime $date
     * @param integer $otd
     * @param string $ville
     * @param string $codePostal
     * @return RedirectResponse
     */
    public function creerInterInstitutionnel($idListe, $idType, $date, $otd, $ville, $codePostal): RedirectResponse
    {
        $liste = $this->listeInterRepository->findOneBy(['id' => $idListe]);

        $demandeur = $this->demandeurRepository->findOneBy(['user'=>$this->getUser()]);
        $type = $this->typInterRepository->findOneBy(['id' => $idType]);
        $dateinter = new DateTime();
        $intervention = new Intervention();
        $intervention->setTypeInter($type)
            ->setListeInter($liste)
            ->setRdvAT($dateinter->setTimestamp($date))
            ->setIntDem($demandeur)
            ->setStatuInter('Nouvelle demande');
        $this->manager->persist($intervention);
        $this->manager->flush();
        return $this->redirectToRoute('terminerInter', [
            'otd' => $otd,
            'ville' => $ville,
            'codePostal' => $codePostal,
            'idInter' => $intervention->getId(),


        ]);
    }

    /**
     * @Route("/terminerInter/{otd}/{ville}/{codePostal}/{idInter}",name="terminerInter")
     *
     * @param $otd
     * @param $ville
     * @param $codePostal
     * @param $idInter
     * @param Fichier $fichier
     * @param DefinirDate $definirDate
     * @param Mail $mail
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function terminerInter($otd, $ville, $codePostal, $idInter, Fichier $fichier, DefinirDate $definirDate, Mail $mail): Response
    {
        $intervention = $this->interventionRepository->findOneBy(['id' => $idInter]);
        $salarie = $this->salarieRepository->findOneBy(['id' => $otd]);

        $form = $this->createForm(InterInstiType::class, $intervention);
        $form->handleRequest($this->request);
        $user = $this->getUser();
        $maintenant = $definirDate->aujourdhui();
        $demain = $definirDate->duree($maintenant, 'P1D');
        if ($form->isSubmitted() && $form->isValid()) {
            $listePhoto = new ArrayCollection();
            $type=[];
            $photos = $form->get('photos')->getData();
            foreach ($photos as $photo) {
                $nouveauNom = $fichier->moveFile($photo, $this->getParameter('photos_directory'));
                $foto = new Photo();
                $foto->setNom($nouveauNom);
                $listePhoto->add($foto);
            }
            foreach ($listePhoto as $PHOTO) {
                $intervention->addPhotoInter($PHOTO);
            }
            if ($form['toiture']->getData()){
                $type[] = $form['toiture']->getData()[0];

            }
            if ($form['gros']->getData()){
                $type[] = $form['gros']->getData()[0];
            }
            if ($form['menuiserie']->getData()){
                $type[] = $form['menuiserie']->getData()[0];
            }
            if ($form['electricite']->getData()){
                $type[] = $form['electricite']->getData()[0];
            }
            if ($form['reseaux']->getData()){
                $type[] = $form['reseaux']->getData()[0];
            }
            if ($form['exterieur']->getData()){
                $type[] = $form['exterieur']->getData()[0];
            }
            if (!empty($form['intemperie']->getData()) && $form['dateIntemperie']->getData()){
                $intemp[] = ['dateIntemp' => $form['dateIntemperie']->getData()];

                $intemp[] = ['intemp' => $form['intemperie']->getData()];
                $intervention->setIntemperie($intemp);

            }

            if ($form['autreType']->getData()){
                $type[] = $form['autreType']->getData();
            }
            if ($form['autreType']->getData()){
                array_push($type,$form['autreType']->getData());
            }
            $intervention->setTypeDemande($type);


            $proposition = new Proposition();
            $proposition->setSalarie($salarie)
                ->setDateFin($demain);
            $intervention->addProposition($proposition)
                ->setTypeDemande($type)
            ->setIntDem($this->getUser()->getAgent()->getDemandeur());
            $reservation = new Reservation();
            $reservation->setIntervention($intervention);
            $this->manager->persist($reservation);
            $this->manager->persist($intervention);
            $this->manager->persist($proposition);
            $this->manager->flush();
            $adresseMail = $mail->getMail($salarie->getId());

             $mail->mailInter([$adresseMail]);
            return $this->redirectToRoute('demandeur_encours');
        }

        return $this->render('inter_institutionnel/interInsiti.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'ville' => $ville,
            'codePostal' => $codePostal,
            'intervention' => $intervention
        ]);
    }
}
