<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbPlaces = null;

    #[ORM\Column]
    private ?float $prixTotal = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compte $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seance $seance = null;

    #[ORM\Column(length: 255)]
    private ?string $ticketToken = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $gratuite = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
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

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): static
    {
        $this->seance = $seance;

        return $this;
    }

    public function getTicketToken(): ?string
    {
        return $this->ticketToken;
    }

    public function setTicketToken(string $ticketToken): static
    {
        $this->ticketToken = $ticketToken;

        return $this;
    }

    public function isGratuite(): bool { 
        return $this->gratuite; 
    }

    public function setGratuite(bool $gratuite): static
    {
    $this->gratuite = $gratuite;
    return $this;
    }
}
