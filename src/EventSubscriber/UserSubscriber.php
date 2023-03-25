<?php


namespace App\EventSubscriber;


use App\Event\UserEvent;
use App\Helper\EntityManagerTrait;
use App\Service\codeActivation;
use App\Service\DefinirAcces;
use App\Service\DefinirDate;
use Exception;
use Symfony\Component\EventDispatcher\GenericEvent;

class UserSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    use EntityManagerTrait;
    /**
     * @var DefinirAcces
     */
    protected $definirAcces;
    /**
     * @var codeActivation
     */
    protected $codeActivation;
    /**
     * @var DefinirDate
     */
    protected $definirDate;

    /**
     * UserSubscriber constructor.
     * @param DefinirAcces $definirAcces
     * @param codeActivation $codeActivation
     * @param DefinirDate $definirDate
     */
    public function __construct(DefinirAcces $definirAcces,codeActivation $codeActivation,DefinirDate $definirDate)
    {
        $this->codeActivation=$codeActivation;
        $this->definirAcces=$definirAcces;
        $this->definirDate=$definirDate;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
          UserEvent::NAME=>'accessUser'
        ];
    }

    /**
     * @param GenericEvent $event
     * @return void
     * @throws Exception
     */
    public function accessUser(GenericEvent $event):void
    {
        $user= $event->getSubject();
        $acces = $this->definirAcces->identPass($user);
        $user->setPassword($acces);
        $activation = $this->codeActivation->generer();
        $user->setCodeActivation($activation);
        $this->manager->persist($user);
    }
}