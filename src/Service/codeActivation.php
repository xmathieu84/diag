<?php

namespace App\Service;


use App\Helper\AgentRepoTrait;
use App\Repository\UserRepository;
use Exception;

/**
 * Class codeActivation
 * @package App\Service
 */
class codeActivation
{
    use AgentRepoTrait;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * codeActivation constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository=$userRepository;
    }

    /**
     * Génère un code alpha-numerique de vérification d'adresse mail
     * 
     *
     * @return string
     */
    public function generer(): string
    {
        for ($activation = '', $i = 0, $z = strlen($a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') - 1; $i != 32; $x = rand(0, $z), $activation .= $a[$x], $i++);

        return $activation;
    }

    /**
     * @return string
     */
    public function codeRapport(): string
    {
        for ($codeRapport = '', $i = 0, $z = strlen($a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') - 1; $i != 10; $x = rand(0, $z), $codeRapport .= $a[$x], $i++);

        return $codeRapport;
    }

    /**
     * @return string
     */
    public function codeAcces(): string
    {

        for ($codeRapport = '', $i = 0, $z = strlen($a = '0123456789') - 1; $i != 6; $x = rand(0, $z), $codeRapport .= $a[$x], $i++);

        return $codeRapport;
    }

    /**
     * @throws Exception
     */
    public function identifiantDemandeur(){
        do{
            for ($identifiant = '', $i = 0, $z = strlen($a = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') - 1; $i !== 8; $x = random_int(0, $z), $identifiant .= $a[$x], $i++);
            $agent = $this->agentRepository->findOneBy(['identifiant'=>$identifiant]);
        } while($agent);
        return $identifiant;
    }
}
