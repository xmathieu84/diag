<?php

namespace App\Entity;

use App\Repository\MangoPayInRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MangoPayInRepository::class)
 */
class MangoPayIn
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[0-9]/",message="Le message est invalide")
     */
    private $walletId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Type invalide")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float", length=255)
     * @Assert\Regex(pattern="/[0-9.]/",message="Montant invalide")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity=Intervention::class, inversedBy="mangoPayIns")
     */
    private $intervention;

    /**
     * @ORM\OneToOne(targetEntity=InterDiag::class, mappedBy="mangoPayIn", cascade={"persist", "remove"})
     */
    private $interDiag;





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWalletId(): ?string
    {
        return $this->walletId;
    }

    public function setWalletId(string $walletId): self
    {
        $this->walletId = $walletId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    public function getInterDiag(): ?InterDiag
    {
        return $this->interDiag;
    }

    public function setInterDiag(?InterDiag $interDiag): self
    {
        // unset the owning side of the relation if necessary
        if ($interDiag === null && $this->interDiag !== null) {
            $this->interDiag->setMangoPayIn(null);
        }

        // set the owning side of the relation if necessary
        if ($interDiag !== null && $interDiag->getMangoPayIn() !== $this) {
            $interDiag->setMangoPayIn($this);
        }

        $this->interDiag = $interDiag;

        return $this;
    }
}
