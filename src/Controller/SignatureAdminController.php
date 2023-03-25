<?php

namespace App\Controller;

use App\Entity\SignatureAdmin;
use App\Helper\EntityManagerTrait;

use App\Helper\RequestTrait;
use App\Repository\SignatureAdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SignatureAdminController extends AbstractController
{
    use RequestTrait, EntityManagerTrait;
    /**
     * @Route("/admin/signature", name="signature_admin")
     */
    public function index(SignatureAdminRepository $signatureAdmin)
    {
        $signature = $signatureAdmin->findOneBy(['nom' => 'administrateur']);
        return $this->render('administrateur/signature.html.twig', [
            'signature' => $signature,
        ]);
    }

    /**
     * @Route("/signatureAdmin")
     *
     * @return void
     */
    public function signatureAdmin()
    {
        $adresse = $this->request->getContent();
        $signature = new SignatureAdmin();
        $signature->setNom('administrateur')
            ->setAdresse($adresse);
        $this->manager->persist($signature);
        $this->manager->flush();
        return new JsonResponse();
    }
}
