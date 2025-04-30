<?php

namespace App\Entity;

use App\Repository\VolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VolRepository::class)]
class Vol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $villeDepart = null;

    #[ORM\Column(length: 50)]
    private ?string $villeArrive = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureDepart = null;

    #[ORM\Column]
    private ?float $prixBilletInitiale = null;

    #[ORM\ManyToOne(inversedBy: 'refVols')]
    private ?Avion $refAvion = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'refVol', orphanRemoval: true)]
    private Collection $refReservations;

    public function __construct()
    {
        $this->refReservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleDepart(): ?string
    {
        return $this->villeDepart;
    }

    public function setVilleDepart(string $villeDepart): static
    {
        $this->villeDepart = $villeDepart;

        return $this;
    }

    public function getVilleArrive(): ?string
    {
        return $this->villeArrive;
    }

    public function setVilleArrivee(string $villeArrive): static
    {
        $this->villeArrive = $villeArrive;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(\DateTimeInterface $heureDepart): static
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getPrixBilletInitiale(): ?float
    {
        return $this->prixBilletInitiale;
    }

    public function setPrixBilletInitiale(float $prixBilletInitiale): static
    {
        $this->prixBilletInitiale = $prixBilletInitiale;

        return $this;
    }

    public function getRefAvion(): ?Avion
    {
        return $this->refAvion;
    }

    public function setRefAvion(?Avion $refAvion): static
    {
        $this->refAvion = $refAvion;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getRefReservations(): Collection
    {
        return $this->refReservations;
    }

    public function addRefReservation(Reservation $refReservation): static
    {
        if (!$this->refReservations->contains($refReservation)) {
            $this->refReservations->add($refReservation);
            $refReservation->setRefVol($this);
        }

        return $this;
    }

    public function removeRefReservation(Reservation $refReservation): static
    {
        if ($this->refReservations->removeElement($refReservation)) {
            // set the owning side to null (unless already changed)
            if ($refReservation->getRefVol() === $this) {
                $refReservation->setRefVol(null);
            }
        }

        return $this;
    }
}
