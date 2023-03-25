<?php

namespace App\Security;



use App\Entity\User;
use App\Event\FactureDiagEvent;
use App\Repository\InterDiagRepository;
use App\Repository\PourcentageRepository;
use App\Service\FacturePdfService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class CustomAuthenticator  extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private AuthorizationCheckerInterface $authChecker;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager,
                                UrlGeneratorInterface $urlGenerator,AuthorizationCheckerInterface $authChecker,
                                UserPasswordHasherInterface $passwordHasher,private InterDiagRepository $interDiagRepository,
                                private EventDispatcherInterface $eventDispatcher,
                                private PourcentageRepository $pourcentageRepository,private FacturePdfService $facturePdfService)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->authChecker = $authChecker;
        $this->passwordHasher = $passwordHasher;
        $this->interDiagRepository=$interDiagRepository;
        $this->pourcentageRepository=$pourcentageRepository;
        $this->facturePdfService = $facturePdfService;

    }


    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('Email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);
      
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        try {
            if ($token->getUser()->getActive()){
                $user = $token->getUser();
                $user->setIsConnect(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                if ($this->authChecker->isGranted('ROLE_INSTITUTION') || $this->authChecker->isGranted('ROLE_BTP') ) {
                    return new RedirectResponse($this->urlGenerator->generate('homeInsti', ["code" => null]));
                }elseif ($this->authChecker->isGranted('ROLE_DEMANDEUR') || $this->authChecker->isGranted('ROLE_CONSULTANT')) {
                    if ($request->query->get('id')){
                        $taux = $this->pourcentageRepository->findOneBy(['nom'=>"acompte"]);
                        $interDiag = $this->interDiagRepository->find($request->query->get('id'));
                        $factureAcompte = $this->facturePdfService->createFactureDiag($interDiag,'acompte');
                        $factureInter = $this->facturePdfService->createFactureDiag($interDiag,'inter');
                        $interDiag->setDemandeur($user->getDemandeur())
                                ->setAcompte($interDiag->getPrix()*($taux->getTaux()/100))
                                ->setFacture($factureInter)
                                ->setStatut('en attente de paiement')
                                ->setFactureAcompte($factureAcompte);
                        $this->entityManager->persist($interDiag);
                        $this->entityManager->flush();
                    }
                    return new RedirectResponse($this->urlGenerator->generate('demandeur'));
                }elseif ($this->authChecker->isGranted('ROLE_ENTREPRISE') || $this->authChecker->isGranted('ROLE_SALARIE')) {
                    return new RedirectResponse($this->urlGenerator->generate('entreprise'));
                }elseif ($this->authChecker->isGranted('ROLE_ADMIN')) {
                    return new RedirectResponse($this->urlGenerator->generate('administrateur'));
                }elseif ($this->authChecker->isGranted('ROLE_MILITAIRE')) {
                    return new RedirectResponse($this->urlGenerator->generate('accueilMilitaire'));
                }elseif ($this->authChecker->isGranted('ROLE_GRANDCOMPTE')) {
                    if ($this->authChecker->isGranted('ROLE_SYNDIC')){
                        return new RedirectResponse($$this->urlGenerator->generate('homeSyndic'));
                    }
                    else{
                        return new RedirectResponse($this->urlGenerator->generate('homeInsti'));
                    }
                }
            }
            else{
                return new RedirectResponse($this->urlGenerator->generate('compteNonActif'));
            }
        }
        catch (\Exception $e){
            
        }

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
    private function isPasswordValid(User $user, $password)
    {
        return $this->passwordHasher->isPasswordValid($user, $password);
    }


}
