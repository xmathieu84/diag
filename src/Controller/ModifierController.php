<?php

namespace App\Controller;

use App\Entity\AlphaTango;
use App\Entity\LicenceDgac;
use App\Event\AdressEvent;
use App\Form\ModifierEntType;

use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\RequestTrait;
use App\Helper\SalarieRepoTrait;

use App\Repository\DemandeurRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\DefinirObjet;
use App\Service\Fichier;
use App\Service\Geoloc;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModifierController extends AbstractController
{
    use EntityManagerTrait, RequestTrait, SalarieRepoTrait,EntrepriseRepoTrait,DemandeurRepoTrait;
    private string $logoDirectory;
    private string $licenceDirectory;
    private string $justificatifDirectory;

    /**
     * @Route("/modifier", name="modifierEnt")
     * @isGranted("ROLE_ENTREPRISE")
     * @param Geoloc $geoloc
     * @param Fichier $fichier
     * @param choixTemplate $choixTemplate
     * @param DefinirObjet $definirObjet
     *
     */
    public function modifierInfogeneraleEnt(Geoloc $geoloc, Fichier $fichier, choixTemplate $choixTemplate, DefinirObjet $definirObjet):Response
    {

        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $coordonnées = $entreprise->getAdresse()->getCoordonnees();


        $form = $this->createForm(ModifierEntType::class, $entreprise);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $logo = $form['logo']->getData();

            if ($logo != null) {
                $fileName = $fichier->moveFile($logo, $this->getParameter('logo_directory'),'logo');
                $entreprise->setLogo($fileName);
            }
            $localisation = $geoloc->geolocalisation($entreprise);
             $definirObjet->definirCoordonnee($coordonnées, $entreprise->getAdresse(), $localisation[0], $localisation[1], null, null, null, null);

            if ($entreprise->getFormJuridique() == 'auto-entrepreneur') {
                $salarie = $this->salarieRepository->findOneBy(['entreprise'=>$entreprise]);
                $AdresseSalarie = $salarie->getAdresse();

                $AdresseSalarie = $definirObjet->definirAdresse(
                    $AdresseSalarie,
                    $entreprise->getAdresse()->getNumero(),
                    $entreprise->getAdresse()->getNomVoie(),
                    $entreprise->getAdresse()->getCodePostal(),
                    $entreprise->getAdresse()->getVille()
                );
                $coordonnéeSalarie = $salarie->getAdresse()->getCoordonnees();
                $distance = $geoloc->distance($localisation[0], $localisation[1], $salarie->getPeriInter());

              $definirObjet->definirCoordonnee(
                    $coordonnéeSalarie,
                    $AdresseSalarie,
                    $localisation[0],
                    $localisation[1],
                    $distance[0],
                    $distance[1],
                    $distance[2],
                    $distance[3]
                );

                $salarie->setEntreprise($entreprise);
                $salarie->getTelephone()->setNumero($form['telephone']['numero']->getData());


                $salarie->getCivilite()->setNom($form['dirigeant']['nom']->getData())
                    ->setPrenom($form['dirigeant']['prenom']->getData())
                    ->setType($form['dirigeant']['type']->getData());

                $salarie->setAdresse($AdresseSalarie);

                $this->manager->persist($AdresseSalarie);
                $this->manager->persist($coordonnéeSalarie);


                $this->manager->persist($salarie);
            }

            $this->manager->persist($entreprise);
            $this->manager->flush();
            return $this->redirectToRoute('entreprise');
        }


        return $this->render('modifier/entreprise.html.twig', [
            'form' => $form->createView(),
            'entreprise'=>$entreprise
        ]);
    }

    /**
     * @return Response
     * @Route("/modifiez mes informations",name="modifInfo")
     * @Security ("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT')")
     */
    public function infoDemandeur(){
        $demandeur = $this->getUser()->getDemandeur();
        return $this->render('demandeur/info.html.twig',[
            'demandeur'=>$demandeur
        ]);
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @return JsonResponse
     * @Route ("/modifierAdresseDemandeur")
     * @Security ("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT')")
     */
    public function modifAdresseDem(EventDispatcherInterface $eventDispatcher){
        $content = json_decode($this->request->getContent());
        $adresse = $this->getUser()->getDemandeur()->getAdresse();
        $adresse->setNumero($content->num)
            ->setNomVoie($content->rue)
            ->setCodePostal($content->code)
            ->setVille($content->city);
        $localisationEvent = new GenericEvent($this->getUser()->getDemandeur());
        $eventDispatcher->dispatch($localisationEvent,  AdressEvent::NAME);
        $this->manager->persist($adresse);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route("/modifTelDemandeur")
     * @Security ("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT')")
     */
    public function modifTelDemandeur(){
        $content = $this->request->getContent();
        $demandeur = $this->getUser()->getDemandeur();
        $demandeur->getTelephon()->setNumero($content);
        $this->manager->persist($demandeur);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     * @Route("/modifTvaDemandeur")
     * @Security ("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT')")
     */
    public function modifTvaDemandeur():JsonResponse{
        $content = $this->request->getContent();
        $demandeur = $this->getUser()->getDemandeur();
        $demandeur->getSiretTva()->setTva($content);
        $this->manager->persist($demandeur);
        $this->manager->flush();
        return new JsonResponse();
    }
    /**
     * @return JsonResponse
     * @Route("/modifSiretDemandeur")
     * @Security ("is_granted('ROLE_DEMANDEUR') or is_granted('ROLE_CONSULTANT')")
     */
    public function modifSiretDemandeur():JsonResponse{
        $content = $this->request->getContent();
        $demandeur = $this->getUser()->getDemandeur();
        $demandeur->getSiretTva()->setSiret($content);
        $this->manager->persist($demandeur);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @return Response
     * @Route("/typeModification",name="typeModif")
     * @Security ("is_granted('ROLE_SALARIE')")
     */
    public function menuModifier():Response{
        $salarie = $this->getUser()->getSalarie();
        return $this->render('entreprise/menuModfifier.html.twig',[
            'salarie'=>$salarie
        ]);
    }

    /**
     * @param int|null $id
     * @param DefinirDate $definirDate
     * @return Response
     * @throws \Exception
     * @Route("/modifierLicence/{id}",name="modLicence")
     * @Security ("is_granted('ROLE_SALARIE')")
     */
    public function modifierLicence(int $id =null,DefinirDate $definirDate):Response{
        $salarie = $this->salarieRepository->findOneBy(['id'=>$id]);
        $day = $definirDate->aujourdhui();
        if ($salarie->getAlphaTango() && $salarie->getAlphaTango()->getFinValidite()){
            $validite = $salarie->getAlphaTango()->getFinValidite()->format("Y-m-d");
        }
        else{
            $validite = $day->format('Y-m-d');
        }
         return $this->render("salarie/modifierLicence.html.twig",[
             'salarie'=>$salarie,
             'limite'=>$day->format('Y-m-d'),
             'validite'=>$validite
         ]);
    }

    /**
     * @param $type
     * @param Fichier $Fichier
     * @return JsonResponse
     * @Route ("/saveFile/{type}")
     * @Security ("is_granted('ROLE_SALARIE')")
     */
    public function saveLicenceFIle($type,Fichier $Fichier):JsonResponse{
        $content = $this->request->files->get('fichier');
        $id = $this->request->get('salarie');
        $salarie = $this->salarieRepository->findOneBy(['id'=>$id]);
        if ($type ==="catt"){
            $nomFichier = 'licence' . $salarie->getCivilite()->getPrenom() . $salarie->getCivilite()->getNom() . '.' . $content->guessExtension();
            $content->move($this->getParameter('licence_directory'), $nomFichier);
            $licence = $salarie->getLicenceDgac();
            if (!$licence){
                $licence = new LicenceDgac();
                $salarie->setLicenceDgac($licence);
                $this->manager->persist($salarie);
            }
            $licence->setFichierLicence($nomFichier);
            $this->manager->persist($licence);
        }
        if ($type ==='alpha'){
            $dossierJustificatif = $this->getParameter('justificatif_directory');
            $nomValidite = 'justificatif' . $salarie->getCivilite()->getPrenom() . $salarie->getCivilite()->getNom() . '.' . $content->guessExtension();
            $content->move($dossierJustificatif, $nomValidite);
            $alpha = $salarie->getAlphaTango();
            if (!$alpha){
                $alpha = new AlphaTango();
                $salarie->setAlphaTango($alpha);
                $this->manager->persist($salarie);
            }
            $alpha->setAttestationFormation($nomValidite);
            $this->manager->persist($alpha);
        }
        $this->manager->flush();
        return new JsonResponse($type);
    }

    /**
     * @param string $type
     * @return JsonResponse
     * @Route ("/saveNumberLicence/{type}")
     * @Security ("is_granted('ROLE_SALARIE')")
     */
    public function saveNumberLicence(string $type):JsonResponse{
        $content = json_decode($this->request->getContent());

        $salarie = $this->salarieRepository->findOneBy(['id'=>$content->id]);
        if ($type ==='aptitutde'){
            $licence = $salarie->getLicenceDgac();
            if (!$licence){
                $licence = new LicenceDgac();
                $salarie->setLicenceDgac($licence);
                $this->manager->persist($salarie);
            }
            $licence->setNumeroDeLicence($content->donnee);
        }
        elseif ($type ==="exploitant"){
            $licence = $salarie->getLicenceDgac();
            if (!$licence){
                $licence = new LicenceDgac();
                $salarie->setLicenceDgac($licence);
                $this->manager->persist($salarie);
            }
            $licence->setExploitant($content->donnee);
        }
        elseif ($type ==="validite"){
            $alpha = $salarie->getAlphaTango();
            if (!$alpha){
                $alpha = new AlphaTango();
                $salarie->setAlphaTango($alpha);
                $this->manager->persist($salarie);
            }
            $date = \DateTime::createFromFormat("Y-m-d",$content->donnee);

            $alpha->setFinValidite($date);
            $this->manager->persist($alpha);
        }
        $this->manager->flush();
        return new JsonResponse($type);

    }
}
