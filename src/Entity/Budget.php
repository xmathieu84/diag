<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BudgetRepository::class)
 */
class Budget
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prevu;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $minimum;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $maximum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrevu(): ?float
    {
        return $this->prevu;
    }

    public function setPrevu(?float $prevu): self
    {
        $this->prevu = $prevu;

        return $this;
    }

    public function getMinimum(): ?float
    {
        return $this->minimum;
    }

    public function setMinimum(?float $minimum): self
    {
        $this->minimum = $minimum;

        return $this;
    }

    public function getMaximum(): ?float
    {
        return $this->maximum;
    }

    public function setMaximum(?float $maximum): self
    {
        $this->maximum = $maximum;

        return $this;
    }
}
