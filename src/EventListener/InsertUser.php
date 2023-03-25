<?php


namespace App\EventListener;


use App\Entity\User;
use App\Helper\AgentRepoTrait;
use App\Helper\DemandeurRepoTrait;
use App\Helper\SalarieRepoTrait;
use App\Service\Mail;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class InsertUser
{
    use AgentRepoTrait,SalarieRepoTrait,DemandeurRepoTrait;
    /**
     * @var Mail
     */
    protected $mail;

    /**
     * InsertUser constructor.
     * @param Mail $mail
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     * @throws TransportExceptionInterface
     */
    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        if ($entity instanceof User && $entity->getEmail()) {

            $agent = $this->agentRepository->findOneBy(['user'=>$entity]);
            $salarie = $this->salarieRepository->findOneBy(['user'=>$entity]);

            if ($entity->hasRole('ROLE_INSTITUTION')||$entity->hasRole('ROLE_GRANDCOMPTE')){
                $this->mail->mailInscriptionAgent($user->getAgent());
            }
            if ($entity->hasRole('ROLE_ENTREPRISE')||$entity->hasRole('ROLE_SALARIE')){
                $this->mail->mailInscriptionSalarie($salarie);
            }
            else{
                $this->mail->confirmerMail($entity->getCodeActivation(), $entity->getEmail());
            }


        }

    }
}