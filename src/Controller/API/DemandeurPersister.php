<?php


namespace App\Controller\API;


use App\Entity\Demandeur;
use App\Event\AdressEvent;
use App\Event\DemandeurEvent;
use App\Event\UserEvent;
use App\Helper\EntityManagerTrait;
use App\Service\Geoloc;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class DemandeurPersister implements \ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface
{
    use EntityManagerTrait;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    private Geoloc $geoloc;
    public function __construct(EventDispatcherInterface $eventDispatcher,Geoloc $geoloc)
    {
        $this->eventDispatcher=$eventDispatcher;
        $this->geoloc = $geoloc;
    }

    public function supports($data, array $context = []): bool
    {
       return $data instanceof Demandeur;
    }

    /**
     * @param Demandeur $data
     * @param array $context
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        $coordEvent = new GenericEvent($data);
        $this->eventDispatcher->dispatch($coordEvent,AdressEvent::NAME);
        $accesEvent = new GenericEvent($data->getUser());
        $this->eventDispatcher->dispatch($accesEvent,UserEvent::NAME);
        $mangoEvent = new GenericEvent($data);
        $this->eventDispatcher->dispatch($mangoEvent,DemandeurEvent::NAME);
        $data->setNom('demandeur')
            ->setCgv(false);
        $this->manager->persist($data);
        $this->manager->persist($data->getUser());
        $this->manager->flush();
        $response = new JsonResponse();
        return $response->setData('ok');
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}