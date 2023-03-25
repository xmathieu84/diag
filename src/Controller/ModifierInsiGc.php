<?php


namespace App\Controller;


use App\Entity\Banque;
use App\Helper\AboTotalInstiRepoTrait;
use App\Helper\AgentRepoTrait;
use App\Helper\BanqueRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\etatAboRepoTrait;
use App\Helper\RequestTrait;
use App\Repository\PackSupRepository;
use App\Repository\TravauxRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Fichier;
use App\Service\Geoloc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class ModifierInsiGc extends AbstractController
{
    use AgentRepoTrait,RequestTrait,EntityManagerTrait,AboTotalInstiRepoTrait,BanqueRepoTrait;


    /**
     * @param choixTemplate $choixTemplate
     * @param PackSupRepository $supRepository
     * @param DefinirDate $definirDate
     * @return Response
     * @throws \Exception
     * @Route("/mes informations",name="infoInsti")
     *
     * @Security("is_granted('ROLE_MANITOU') or is_granted('ROLE_BTP')")
     */
    public function informationCompteGcInsti(choixTemplate $choixTemplate,PackSupRepository $supRepository,DefinirDate $definirDate){
        $template = $choixTemplate->templateDem($this->getUser());
        $agentI = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $responsable = $this->agentRepository->findResponsable($template[1]);
        $packs = $supRepository->findByInstitution($template[1]);
        $abonnement = $this->aboTotalInstiRepository->findAbonnement($template[1],$definirDate->aujourdhuiImmutable());
        $nombreAgent=0;
        $nombre = 0;
        foreach ($agentI->getDemandeur()->getAgents() as $agent){
            $nombreAgent ++;
            foreach ($agent->getResponsable() as $responsable){
                $nombreAgent= $responsable->getChef()->count() +$nombreAgent;
            }
        }
        foreach ($abonnement as $state){
            $nombre=$state->getAbonnement()->getUtlisateur();
        }

        foreach ($packs as $pack){
            $nombre += $pack->getEmploye();
        }

        return $this->render('institution/information.html.twig',[
            'template'=>$template[0],
            'institution'=>$template[1],
            'responsable'=>$responsable,
            'nombre'=>$nombre,
            'nombreA'=>$nombreAgent
        ]);

    }

    /**
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/changer mes informations",name="modifInfoInsti")
     *
     * @Security("is_granted('ROLE_MANITOU') or is_granted('ROLE_BTP') ")
     */
    public function infoModifCompte(choixTemplate $choixTemplate,TravauxRepository $travauxRepository){
        $template = $choixTemplate->templateDem($this->getUser());
        $responsable = $this->agentRepository->findResponsable($template[1]);
        $travaux = $travauxRepository->findAll();
        return $this->render('institution/infoModifCompte.html.twig',[
            'template'=>$template[0],
            'responsable'=>$responsable,
            'travaux'=>$travaux
        ]);
    }

    /**
     * @param string $type
     * @param UserPasswordHasherInterface $encoder
     * @param Geoloc $geoloc
     * @param Fichier $fichier
     * @param TravauxRepository $travauxRepository
     * @return JsonResponse
     * @Route("/modifChamp/{type}")
     * @Security("is_granted('ROLE_MANITOU') or is_granted('ROLE_BTP') ")
     */
    public function modifierChamp(string $type,UserPasswordHasherInterface $encoder,Geoloc $geoloc,Fichier $fichier,TravauxRepository $travauxRepository):JsonResponse{
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        if ($type ==='email'){
            $content = $this->request->getContent();
            $user = $this->getUser();
            $user->setEmail($content);
            $this->manager->persist($user);
            $retour = $content;
        }
        elseif ($type ==='password'){
            $content = $this->request->getContent();
            $user = $this->getUser();
            $user->setPassword($encoder->hashPassword($user,$content));
            $this->manager->persist($user);
            $retour = '********';
        }
        elseif ($type ==='telephone'){
            $content = $this->request->getContent();

            $agent->getDemandeur()->getTelephon()->setNumero($content);
            $this->manager->persist($agent);
            $retour = $content;
        }
        elseif ($type ==='siteWeb'){
            $content = $this->request->getContent();

            $agent->getDemandeur()->getProBtp()->setSiteWeb($content);
            $this->manager->persist($agent);
            $retour = $content;
        }
        elseif ($type ==='distanceInter'){
            $content = $this->request->getContent();
            $coordonnnee = $geoloc->distance($agent->getDemandeur()->getAdresse()->getCoordonnees()->getLatitude(),$agent->getDemandeur()->getAdresse()->getCoordonnees()->getLongitude(),$content);
            $agent->getDemandeur()->getAdresse()->getCoordonnees()->setLatMinInter($coordonnnee[0])
                                                                ->setLatMaxInter($coordonnnee[1])
                                                                ->setLonMaxInter($coordonnnee[3])
                                                                ->setLonMinInter($coordonnnee[2]);
            $this->manager->persist($agent);
            $retour = $content;
        }
        elseif ($type ==='adresse'){
            $content = json_decode($this->request->getContent());
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
            $agent->getDemandeur()->getAdresse()->setNumero($content->numero)
                ->setNomVoie($content->voie)
                ->setCodePostal($content->cp)
                ->setVille($content->ville);
            $coordonnnee = $geoloc->geolocalisation($agent->getDemandeur());
            $agent->getDemandeur()->getAdresse()->getCoordonnees()->setLatitude($coordonnnee[0])
                ->setLongitude($coordonnnee[1]);
            $this->manager->persist($agent);
            $retour = $content;
        }
        elseif ($type ==='logoInsti'){
            $logo = $this->request->files->get('logo');

            $nom = 'logo'.$agent->getDemandeur()->getNom().time();
            $fileName = $fichier->saveFile($nom,$this->getParameter('logo_directory'),$logo);
            $agent->getDemandeur()->setLogo($fileName);
            $this->manager->persist($agent);
            $retour  =$fileName;
        }
        elseif ($type ==='bandeau'){
            $logo = $this->request->files->get('bandeau');

            $nom = 'logo'.$agent->getDemandeur()->getNom().time();
            $fileName = $fichier->saveFile($nom,$this->logoDirectory,$logo);
            $agent->getDemandeur()->getProBtp()->setBandeauPub($fileName);
            $this->manager->persist($agent);
            $retour  =$fileName;
        }
        $this->manager->flush();
        return new JsonResponse($retour);
    }

    /**
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/liste des authorisations",name="listeAutorisation")
     */
    public function modifAutorisation(choixTemplate $choixTemplate){
        $template = $choixTemplate->templateDem($this->getUser());
        $agents = $template[1]->getAgents();

        return $this->render('institution/modifAutorisation.html.twig',[
            'agents'=>$agents,
            'template'=>$template[0]
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/institution/changerRole")
     */
    public function changeRole():JsonResponse{
        $content = json_decode($this->request->getContent());
        $role = [];
        $roleOldAdmin = [];
        $agent = $this->agentRepository->findOneBy(['id'=>$content->agent]);
        $admin = $this->agentRepository->findResponsable($agent->getDemandeur());
        if ($content->type ==='gc'){
            array_push($role,"ROLE_GRANDCOMPTE","ROLE_ABONNE");
            array_push($roleOldAdmin,"ROLE_GRANDCOMPTE","ROLE_ABONNE");

        }
        else{
            array_push($role,"ROLE_INSTITUTION","ROLE_ABONNE");
            array_push($roleOldAdmin,"ROLE_INSTITUTION","ROLE_ABONNE");

        }
        switch ($content->role){
            case  "NIVEAU3":
                $role[] = "ROLE_NIVEAU3";
                break;
            case  "NIVEAU2":
                $role[] = "ROLE_NIVEAU2";
                break;
            case  "NIVEAU1":
                $role[] = "ROLE_NIVEAU1";
                break;
            case "RESPONSABLE":
                $role[]= "ROLE_RESPONSABLE";
                break;
            case"MANITOU":
                $roleOldAdmin[]= "ROLE_RESPONSABLE";
                $role[] = "ROLE_MANITOU";
                
        }
        $agent->getUser()->setRoles($role);
        if ($agent !== $admin && $content->role ==='MANITOU'){
            $admin->getUser()->setRoles($roleOldAdmin);
            $this->manager->persist($admin);
        }
        $this->manager->persist($agent);
        $this->manager->flush();

        return new JsonResponse();
    }

    /**
     * @return Response
     * @Route ("/changer banque",name="changerBanque")
     * @Security ("is_granted('ROLE_MANITOU')")
     */
    public function changerBanque(){
        $grandCompte = $this->getUser()->getAgent()->getDemandeur();
        $banque = $this->banqueRepository->findOneBy(['institution'=>$grandCompte,'actif'=>true]);

        return $this->render('institution/modifierBanque.html.twig',[
            'banque'=>$banque
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/modifRouteGc")
     */
    public function modifBanqueGc():JsonResponse{
        $grandCompte = $this->getUser()->getAgent()->getDemandeur();
        $oldBanque = $this->banqueRepository->findOneBy(['institution'=>$grandCompte,'actif'=>true]);
        $content = json_decode($this->request->getContent());
        if ($content != $oldBanque->getIban()){
            $banque = new Banque();
            $banque->setIban($content->iban)
                ->setBic($content->bic)
                ->setActif(true)
                ->setInstitution($grandCompte)
                ->setNom($content->nom)
                ->setAdresse($content->adresse);

            $oldBanque->setActif(false);
            $this->manager->persist($banque);
            $this->manager->persist($oldBanque);
            $this->manager->flush();
            return  new JsonResponse();
        }else{
            return new JsonResponse('non valide');
        }
    }

    /**
     * @return JsonResponse
     * @Route("/modifier/recupererTravaux")
     * @Security ("is_granted('ROLE_BTP')")
     */
    public function recupTravaux():JsonResponse{
        $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        $travaux = $agent->getDemandeur()->getProBtp()->getTravaux();
        $liste = [];
        foreach ($travaux as $travail){
            $liste[]=$travail->getId();
        }
        return new JsonResponse($liste);
    }

    /**
     * @param TravauxRepository $travauxRepository
     * @return JsonResponse
     * @Route("/modifier/ChangeTravaux")
     */
    public function changeTravaux(TravauxRepository $travauxRepository){

        $content = json_decode($this->request->getContent());
        $travaux = $travauxRepository->findOneBy(['id'=>$content->id]);
       $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        if ($content->changement ==='retrait'){
            $agent->getDemandeur()->getProBtp()->removeTravaux($travaux);
        }
        else{
            $agent->getDemandeur()->getProBtp()->addTravaux($travaux);
        }
        $this->manager->persist($agent);
        $this->manager->flush();
        return new JsonResponse();
    }


}