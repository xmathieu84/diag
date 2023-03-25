<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TvaSiretRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TvaSiretRepository::class)
 *
 */
class TvaSiret
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le n° de TVA est invalide")
     * @Groups ({"demandeurInfo","demandeur:write"})
     */
    private $tva;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,-]/",message="Le siret est invalide")
     * @Groups ({"demandeurInfo","demandeur:write"})
     */
    private $siret;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $assujeti;






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAssujeti(): ?bool
    {
        return $this->assujeti;
    }

    public function setAssujeti(?bool $assujeti): self
    {
        $this->assujeti = $assujeti;

        return $this;
    }






}
