<?php

namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanningRepository::class)
 */
class Planning
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $horaires = [];





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoraires(): ?array
    {
        return $this->horaires;
    }

    public function setHoraires(array $horaires): self
    {
        $this->horaires = $horaires;

        return $this;
    }




}
