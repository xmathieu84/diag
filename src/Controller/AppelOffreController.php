<?php


namespace App\Controller;




use App\Entity\AppelOffre;
use App\Entity\DossierAo;
use App\Entity\FichierInfoComplementaire;
use App\Entity\InfoComplementaire;

use App\Form\AppelType;
use App\Helper\AgentRepoTrait;
use App\Entity\Notice;
use App\Helper\AppelOffreRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;

use App\Repository\FichierInfoComplementaireRepository;
use App\Repository\InfoComplementaireRepository;
use App\Service\choixTemplate;
use App\Service\DefinirDate;
use App\Service\Fichier;

use DateInterval;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppelOffreController extends AbstractController
{
    use AgentRepoTrait, RequestTrait, EntityManagerTrait,AppelOffreRepoTrait,DemandeurRepoTrait;



    /**
     * @param choixTemplate $choixTemplate
     * @param int|null $id
     * @param Fichier $fichier
     * @return Response
     * @Route("/publier un appel d'offre/{code}",name="listeAppelOffre")
     * @Route("/modifier un appel d'offre/{id}/{code}",name="modifierAppelOffre")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     */
    public function listeAppelOffre(choixTemplate $choixTemplate,int $id=null,Fichier $fichier,string $code=null): Response
    {

        if(!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }
        else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        if ($id){
            $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);
        }
        else{
            $appel = new AppelOffre();
            $appel->setAgents($agent)
                ->setEtat('crée');
        }
        $form = $this->createForm(AppelType::class,$appel);

        $form->handleRequest($this->request);

        if ($form->isSubmitted()&&$form->isValid()){
            foreach ($form['contacts']->getData() as  $contact){
                $contact->setAppelOffre($appel);
                $this->manager->persist($contact);
            }
            foreach ($form['dossier']->getData() as $key=> $dossier){
                $nom = 'dossier'.$appel->getType().$appel->getDenomination().'n°'.($key+1);
                $filename = $fichier->saveFile($nom,$this->getParameter('appelOffre_directory'),$dossier);
                $dossierAo = new DossierAo();
                $dossierAo->setAppelOffre($appel)
                    ->setFichier($filename);
                $this->manager->persist($dossierAo);
            }

           $this->manager->persist($appel);
            $this->manager->flush();
            return $this->redirectToRoute('ajoutPiece',['id'=>$appel->getId(),'code'=>$code]);
        }

        return $this->render('institution/listeAppelOffre.html.twig', [
                'agent'=>$agent,
                'form'=>$form->createView(),
                'code'=>$code
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/ajouter des pièces/{id}/{code}",name="ajoutPiece")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1') ")
     */
    public function ajoutPiece(int $id,choixTemplate $choixTemplate,string $code =null):Response{

        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);


        return $this->render('institution/ajoutPiece.html.twig',[
            'appel'=>$appel,
            'code'=>$code
        ]);
    }

    /**
     * @param $type
     * @param $id
     * @return JsonResponse
     * @Route("/institution/saveInfo/{type}/{id}")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     */
    public function saveInfo($type,$id):JsonResponse{
        $content = $this->request->getContent();
        $appel  =$this->appelOffreRepository->findOneBy(['id'=>$id]);
        $info = new InfoComplementaire();
        $info->setType($type)
            ->setTexte($content)
            ->setAppelOffres($appel);
        $this->manager->persist($info);
        $this->manager->flush();
        $response  =new JsonResponse();
        return $response->setData($info->getId());
    }

    /**
     * @param $id
     * @param InfoComplementaireRepository $repository
     * @param Fichier $fichier
     * @return JsonResponse
     * @Route ("/insitutionnel/saveFile/{id}")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     */
    public function saveFile($id,InfoComplementaireRepository $repository,Fichier $fichier):JsonResponse{
        $file = $this->request->files->get('file');
        $info = $repository->findOneBy(['id'=>$id]);
        $nom = 'fichier n°'.($info->getFichierInfoComplementaires()->count()+1).$info->getType().$id;
        $fichierInfo  =new FichierInfoComplementaire();
        $filename = $fichier->saveFile($nom,$this->appelOffreDirectory,$file);
        $fichierInfo->setNom($filename)
            ->setInfoComplementaires($info);
        $this->manager->persist($fichierInfo);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/précision de l'appel/{id}/{code}",name="precisionAO")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     */
    public function precisionAO(int $id,string $code=null):Response{
    $appel  =$this->appelOffreRepository->findOneBy(['id'=>$id]);

    return $this->render('institution/precisionAO.html.twig',[
        'appel'=>$appel,
        'code'=>$code
    ]);

    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/institution/savePrecision/{id}")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1') ")
     */
    public function savePrecision(int $id):JsonResponse{
        $content = $this->request->getContent();
        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);

        $info = new InfoComplementaire();
        $info->setType('info complementaire')
            ->setTexte($content)
                ->setAppelOffres($appel);
        $this->manager->persist($info);
        $this->manager->flush();
        return new JsonResponse();
    }

    /**
     * @param string|null $etat
     * @param choixTemplate $choixTemplate
     * @param string|null $code
     * @return Response
     * @Route("/mes appels d'offre/{code}",name="mesAO")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     */
    public function mesAO(choixTemplate $choixTemplate,string $code=null){
        if (!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        $appels = $this->appelOffreRepository->findByInstitution($agent->getDemandeur());

        return $this->render('institution/mesAO.html.twig',[
            'appels'=>$appels,
            'code'=>$code,
            'demandeur'=>$agent->getDemandeur()->getId()
        ]);
    }

    /**
     *
     * @Route("/mes appels/supendu/{code}",name="mesAOsuspendu")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     * @param string|null $code
     * @return Response
     */
    public function appelSuspendu(string $code=null):Response{
        if (!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        $appels = $this->appelOffreRepository->findByEtat($agent->getDemandeur(),'suspendu');
        return $this->render('institution/mesAO.html.twig',[
            'appels'=>$appels,
            'code'=>$code,

        ]);
    }
    /**
     *
     * @Route("/mes appels/publie/{code}",name="mesAOPublie")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     * @param string|null $code
     * @return Response
     */
    public function appelPublie(string $code=null):Response{
        if (!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        $appels = $this->appelOffreRepository->findByEtat($agent->getDemandeur(),'publié');
        return $this->render('institution/mesAO.html.twig',[
            'appels'=>$appels,
            'code'=>$code,

        ]);
    }
    /**
     *
     * @Route("/mes appels/reponse/{code}",name="mesAOResponse")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     * @param string|null $code
     * @return Response
     */
    public function appelReponse(string $code=null):Response{
        if (!$code){
            $agent = $this->agentRepository->findOneBy(['user'=>$this->getUser()]);
        }else{
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$code]);
        }
        $appels = $this->appelOffreRepository->findByEtat($agent->getDemandeur(),'publié');
        return $this->render('institution/mesAO.html.twig',[
            'appels'=>$appels,
            'code'=>$code,

        ]);
    }

    /**
     * @param $id
     * @param $type
     * @param FichierInfoComplementaireRepository $repository
     * @return JsonResponse
     * @Route("/insitution/nobreFichier/{id}-{type}")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     */
    public function nombreFichier($id,$type,FichierInfoComplementaireRepository $repository):JsonResponse{
        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);
        $info = $repository->findByAoType($appel,$type);
        return new JsonResponse(count($info));
    }

    /**
     * @param int $id
     * @param string $action
     * @return RedirectResponse
     * @Route("/insitution/publierAO/{id}/{action}/{code}",name="actionAO")
     * @Security ("is_granted('ROLE_ABONNE') and is_granted('ROLE_NIVEAU1')")
     */
    public function publierAO(int $id,string $action,string $code=null):RedirectResponse{

        $appel = $this->appelOffreRepository->findOneBy(['id'=>$id]);
        $appel->setEtat($action);
        $this->manager->persist($appel);
        $this->manager->flush();
        return $this->redirectToRoute('mesAO',['code'=>$code]);
    }



}