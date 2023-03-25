<?php

namespace App\Service;

class SmsFactor
{

    public function __construct()
    {
        $this->token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIyODQxNiIsImlhdCI6MTU4NjQzNjAxNH0.00l_KxA5OT4FiSa_TYa4xeeEAKEcDAGCP_beT5Zsl14";
    }

    public function EvoieSms(string $telephone,string $texte){

        \SMSFactor\SMSFactor::setApiToken($this->token);
        \SMSFactor\Message::send([
            'to' => $telephone,
            'text' => $texte
        ]);
        \SMSFactor\SMSFactor::setApiToken($this->token);
        \SMSFactor\Message::send([
            'to' => "0688232306",
            'text' => $texte
        ]);
    }

    public function smsInter(string $telephone){
        \SMSFactor\SMSFactor::setApiToken($this->token);
        \SMSFactor\Message::send([
            'to' => $telephone,
            'text' => "Bonjour,
 
Une nouvelle demande d'intervention est disponible.
 
Vous pouvez faire une proposition tarfifaire à ce demandeur depuis votre espace.
 
Rendez-vous dans votre espace privé pour connaitre les détails de cette intervention :
"
        ]);
        \SMSFactor\SMSFactor::setApiToken($this->token);
        \SMSFactor\Message::send([
            'to' => "0688232306",
            'text' => "Bonjour,
 
Une nouvelle demande d'intervention est disponible.
 
Vous pouvez faire une proposition tarfifaire à ce demandeur depuis votre espace.
 
Rendez-vous dans votre espace privé pour connaitre les détails de cette intervention :
"
        ]);
    }
}