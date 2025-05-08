<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\AvionRepository")]
class Avion
    {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $immatriculation;

    #[ORM\Column(type: 'string', length: 255)]
    private $modele;

    #[ORM\Column(type: 'integer')]
    private $nombreDePlace;

    // Ajoutez les getters et setters pour toutes les propriétés
    public function getId(): ?int
    {
    return $this->id;
    }

    public function getImmatriculation(): ?string
    {
    return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
    $this->immatriculation = $immatriculation;
    return $this;
    }

    public function getModele(): ?string
    {
    return $this->modele;
    }

    public function setModele(string $modele): self
    {
    $this->modele = $modele;
    return $this;
    }

    public function getNombreDePlace(): ?int
    {
    return $this->nombreDePlace;
    }

    public function setNombreDePlace(int $nombreDePlace): self
    {
    $this->nombreDePlace = $nombreDePlace;
    return $this;
    }
}