<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerTrait{
    /**
     * @var EntityManagerInterface $manager
     */

    protected EntityManagerInterface $manager;

    /**
     * @required
     * @param EntityManagerInterface $manager
     */

    public function setEntityManager(EntityManagerInterface $manager):void
    {
        $this->manager=$manager;
    }
}