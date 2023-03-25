<?php

namespace App\EventSubscriber;


use App\Event\NotifEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificationEvent implements EventSubscriberInterface
{

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
           NotifEvent::NAME=>"envoieNotif"
        ];
    }

    /**
     *
     * @Route("/envoieNotif")
     */
    public function envoieNotif(){
        date_default_timezone_set("America/New_York");
        header("Cache-Control: no-cache");
        header("Content-Type: text/event-stream");

        //$counter = rand(1, 10);

            // Chaque seconde, on envoie un évènement "ping".

            echo "event: ping\n";
            $curDate = date(DATE_ISO8601);
            echo 'data: {"time": "' . $curDate . '"}';
            echo "\n\n";

            // Envoi d'un message simple à fréquence aléatoire.



            ob_end_flush();
            flush();

            // On ferme la boucle si le client a interrompu la connexion
            // (par exemple en fermant la page)

        sleep(1);


    }
}