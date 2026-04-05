<?php

namespace App\Entity;

use App\Repository\BonFideliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BonFideliteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class BonFidelite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bonFidelites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compte $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column]
    private bool $utilise = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;
    

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $utiliseAt = null;


    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
    $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Compte
    {
        return $this->user;
    }

    public function setUser(?Compte $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function isUtilise(): ?bool
    {
        return $this->utilise;
    }

    public function setUtilise(bool $utilise): static
    {
        $this->utilise = $utilise;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUtiliseAt(): ?\DateTimeImmutable
    {
        return $this->utiliseAt;
    }

    public function setUtiliseAt(?\DateTimeImmutable $utiliseAt): static
    {
        $this->utiliseAt = $utiliseAt;

        return $this;
    }
}
