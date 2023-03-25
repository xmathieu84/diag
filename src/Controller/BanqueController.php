<?php

namespace App\Controller;

use App\Entity\Banque;
use App\Entity\RcComplement;
use App\Form\RcPro2Type;
use App\Helper\BanqueRepoTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Repository\AssurancesRepository;
use App\Service\Fichier;
use App\Entity\Assurances;
use App\Form\AssuranceEntType;
use App\Helper\EntityManagerTrait;
use App\Helper\RequestTrait;
use App\Service\choixTemplate;
use App\Service\Mail;
use App\Service\MangoPayService;

use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Throwable;

/**
 * Class BanqueController
 * @package App\Controller
 */
class BanqueController extends AbstractController
{
    use EntityManagerTrait, RequestTrait,EntrepriseRepoTrait,SalarieRepoTrait,BanqueRepoTrait;


    /**
     * @Route("/assurance-rcp",name="finalisation")
     * @isGranted("ROLE_ENTREPRISE")
     *
     * @param Fichier $fichier
     * @param choixTemplate $choixTemplate
     * @param MangoPayService $mangoPayService
     * @return Response
     */
    public function finaliserInscription(Fichier $fichier, choixTemplate $choixTemplate, MangoPayService $mangoPayService):Response
    {
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $entreprise = $salarie->getEntreprise();
        $template = $choixTemplate->templateAE($entreprise);
        $assurance = new Assurances();
        $entreprise->setEntAss($assurance);

        $form = $this->createForm(AssuranceEntType::class, $assurance);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $assurancePro = $form['ass_pro_fichier']->getData();
                $nouveauAss = $fichier->moveFile($assurancePro, $this->getParameter('assurances_directory'),'assurance');
                $assurance->setAssProFichier($nouveauAss);
                $this->manager->persist($entreprise);
                $this->manager->persist($assurance);
                $this->manager->flush();
                return $this->redirectToRoute('voirAssu');
            }
            catch (\Exception $e){

                $this->addFlash('alerte', 'Un ou plusieurs champs ne sont pas correctement remplis');
            }


        }

        return $this->render('entreprise/finalisation.html.twig', [
            'template' => $template,
            'form' => $form->createView(),
            'entreprise' => $entreprise
        ]);
    }

    /**
     * @param int $id
     * @param AssurancesRepository $assurancesRepository
     * @param choixTemplate $choixTemplate
     * @return Response
     * @Route("/entreprise/assuranceComplementaire/{id}",name="assuranceComplementaire")
     */
    public function assuranceComplementaire(int $id,AssurancesRepository $assurancesRepository,choixTemplate $choixTemplate,Fichier $fichier):Response{
        $assurance = $assurancesRepository->findOneBy(['id'=>$id]);
        $entreprise = $this->getUser()->getSalarie()->getEntreprise();
        $complemnetaire = new RcComplement();
        $form = $this->createForm(RcPro2Type::class,$complemnetaire);
        $form->handleRequest($this->request);
        $template = $choixTemplate->templateAE($entreprise);
        if ($form->isSubmitted() && $form->isValid()){
                $assuranceComplemanteire = $form['fichier']->getData();
                $nom = $fichier->moveFile($assuranceComplemanteire,$this->getParameter("assurances_directory"),'assurance');
                $complemnetaire->setFichier($nom);
                $complemnetaire->setAssurance($assurance);
                $this->manager->persist($complemnetaire);
                $this->manager->flush();
                return $this->redirectToRoute("kyc_ubo");
        }
        return $this->render("entreprise/assuranceComplementaire.html.twig",[
            'form'=>$form->createView(),
            'template'=>$template,
            'entreprise'=>$entreprise
        ]);
    }

    /**
     * @return Response
     * @Route("/modifierBanqueotd",name="modifBanqueOtd")
     * @Security ("is_granted('ROLE_ENTREPRISE')")
     */
    public function changeBanqueOtd(){
        $entreprise = $this->getUser()->getSalarie()->getEntreprise();
        $banque = $this->banqueRepository->findOneBy(['entreprise'=>$entreprise,'actif'=>true]);
         return $this->render('entreprise/changerBanque.html.twig',[
             'banque'=>$banque
         ]);
    }

    /**
     * @param MangoPayService $mangoPayService
     * @return JsonResponse
     * @Route("/validerModifBanqueOtd")
     */
    public function validerModifBAnqueOtd(Mail $mail):JsonResponse{
        $content = $this->request->getContent();
        $expediteur = $this->getUser()->getEmail();
        $entreprise = $this->getUser()->getSalarie()->getEntreprise()->getDenomination().' '.$this->getUser()->getSalarie()->getEntreprise()->getEnseigne();
        $mail->mailChangeBanque($content,$expediteur,$entreprise);

        return new JsonResponse();

    }
}
