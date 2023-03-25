<?php


namespace App\EventSubscriber;
use App\Event\DemandeurEvent;
use App\Helper\EntityManagerTrait;
use App\Repository\DemandeurRepository;
use App\Service\MangoPayService;
use \Symfony\Component\EventDispatcher\EventSubscriberInterface ;
use Symfony\Component\EventDispatcher\GenericEvent;

class DemandeurMangoPaySubsciber implements EventSubscriberInterface
{
    use EntityManagerTrait;
    /**
     * @var MangoPayService
     */
    private MangoPayService $mangoPayService;
    /**
     * @var DemandeurRepository
     */
    private DemandeurRepository $demandeurRepository;

    /**
     * @return string[]
     */
    public static function getSubscribedEvents():array
    {
        return [DemandeurEvent::NAME=>'demandeurMango'];
    }

    public function __construct(DemandeurRepository $demandeurRepository,MangoPayService $mangoPayService)
    {
        $this->mangoPayService=$mangoPayService;
        $this->demandeurRepository=$demandeurRepository;
    }

    public function demandeurMango(GenericEvent $event){
        $demandeur = $event->getSubject();
        $mangoUser = $this->mangoPayService->getMangoUser($demandeur, $demandeur->getUser()->getEmail());

        $wallet = $this->mangoPayService->createWallet($mangoUser->Id);
        $demandeur->getUser()->setWalletMangoID($wallet->Id)
                    ->setMangoPayID($mangoUser->Id);
        $this->manager->persist($demandeur->getUser());
    }
}