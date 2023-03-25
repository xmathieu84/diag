<?php


namespace App\Controller;


use App\Helper\EntityManagerTrait;
use App\Helper\InterRepoTrait;
use App\Helper\RapportRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\codeActivation;
use App\Service\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RapportEntrepriseController extends AbstractController
{
    use InterRepoTrait,SalarieRepoTrait,RapportRepoTrait,EntityManagerTrait;

    /**
     * @return Response
     * @Route ("/entreprise/mes rapports",name="rapportEnt")
     */
    public function rapportEnt():Response{
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $interventions = $this->interventionRepository->findForEntreprise($salarie->getEntreprise());

        return $this->render('entreprise/mesRapport.html.twig',[
            'interventions'=>$interventions
        ]);
    }

    /**
     * @return Response
     * @Route("/liste rapport",name="listeRapport")
     */
    public function envoieRapport(){
        $salarie = $this->salarieRepository->findOneBy(['user'=>$this->getUser()]);
        $rapport = $this->rapportRepository->findHdd($salarie->getEntreprise());

        return $this->render('entreprise/envoieRapport.html.twig',[
            'rapports'=>$rapport
        ]);
    }

    public function envoieMAilRapport(codeActivation $codeActivation,Mail $mail,$id){
        $rapport = $this->rapportRepository->findOneBy(['id'=>$id]);
        $content = json_decode($this->request->getContent(),true);
        $codeRecherche = $codeActivation->codeAcces();
        $rapport->setCodeRecherche($codeRecherche);

        foreach ($content as $email){
            $codeUnique = $codeActivation->codeRapport();
            $consultant = new ConsultantHDD();
            $consultant->setMail($email)
                ->setCodeUnique($codeUnique)
                ->setRapport($rapport);
            $this->manager->persist($consultant);
            $this->manager->flush();
            $mail->mailConsultant($email,$codeRecherche,$codeUnique);

        }
        $this->manager->persist($rapport);
        $this->manager->flush();
        return new JsonResponse();
    }
}