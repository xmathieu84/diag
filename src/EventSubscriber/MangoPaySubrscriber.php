<?php


namespace App\EventSubscriber;


use App\Event\MangoPayUserEvent;
use App\Helper\EntityManagerTrait;
use App\Helper\EntrepriseRepoTrait;
use App\Repository\DemandeurRepository;
use App\Service\MangoPayService;
use MangoPay\Libraries\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MangoPaySubrscriber implements EventSubscriberInterface
{
    use EntityManagerTrait,EntrepriseRepoTrait;

    /**
     * @var MangoPayService
     */
    private MangoPayService $mangoPayService;



    /**
     * MangoPaySubrscriber constructor.
     * @param MangoPayService $mangoPayService
     */
    public function __construct(MangoPayService $mangoPayService)
    {
        $this->mangoPayService=$mangoPayService;

    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents():array
    {
        return [
            MangoPayUserEvent::MANGO=>'mangoPayUser'
        ];
    }

    /**
     * @param GenericEvent $event
     * @throws Exception
     */
    public function mangoPayUser(GenericEvent $event):void{

        $entreprise = $event->getSubject();
        $mangoUser = $this->mangoPayService->createLegalUser($entreprise, $entreprise->getAdresse());
        $wallet = $this->mangoPayService->createWallet($mangoUser->Id);
        $entreprise->getUser()->setWalletMangoID($wallet->Id)
            ->setMangoPayID($mangoUser->Id);
        $this->manager->persist($entreprise->getUser());
    }
}