<?php

namespace App\Controller;

use App\Form\PaiementType;
use App\Repository\InterDiagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementDiagController extends AbstractController
{
    public function __construct(private InterDiagRepository $interDiagRepository,
                                private RequestStack $requestStack,private FormFactoryInterface $formFactory)
    {
        $this->interDiagRepository=$interDiagRepository;
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $uuid
     * @param string $type
     * @return Response
     * @Route("/paiement-diag/{type}/{uuid}",name="paiementDiag")
     */
    public function formulairePaiment(string $uuid,string $type): Response
    {
        $inter = $this->interDiagRepository->findOneBy(['identifiat'=>$uuid]);
        $form = $this->formFactory->createNamed('', PaiementType::class, [
            'action' => ' ',
            'method' => 'post'
        ]);
        $remise = ($inter->getRemiseTemps() !==0) ? 1+$inter->getRemiseTemps()/100 : 1;
        if ($type === 'acompte') {
            $montant = $inter->getAcompte()/$remise;
        }
        if ($type === 'intervention') {
            $montant = ($inter->getPrix() - $inter->getAcompte())/$remise;
        }
        $url = 'http' . (($this->requestStack->getCurrentRequest()->server->get('HTTPS') !== null) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $url .= substr($this->requestStack->getCurrentRequest()->server->get('REQUEST_URI'), 0, strrpos($this->requestStack->getCurrentRequest()->server->get('REQUEST_URI'), '/') + 1);
        $url .= $uuid;
        if ($this->requestStack->getCurrentRequest()->query->get('data')) {
            return $this->redirectToRoute('traitementPaiementDiag',[
                'data'=>$this->requestStack->getCurrentRequest()->query->get('data'),
                'uuid'=>$uuid,
                'type'=>$type,
            ]);
        }
        return $this->renderForm('paiement/paiementDiag.html.twig',[
            'form'=>$form,
            'montant'=>$montant,
            'url'=>$url,
            'inter'=>$inter,
            'type'=>$type
        ]);
    }
}