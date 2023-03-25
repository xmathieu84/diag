<?php

namespace App\Service;

use App\Repository\SalarieRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class SalarieClassIndent
{
    public function __construct(private SalarieRepository $salarieRepository,private TokenStorageInterface $tokenStorage)
    {
        $this->salarieRepository = $salarieRepository;
        $this->tokenStorage=$tokenStorage;

    }

    public function getSalarie(int $id=null){
        if ($id){
            return $this->salarieRepository->find($id);
        }
        else{
            return $this->tokenStorage->getToken()->getUser()->getSalarie();
        }

    }
}