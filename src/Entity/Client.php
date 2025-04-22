<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $zoneIntervention = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer un numéro de téléphone')]
    #[Assert\Regex(
        pattern: '/^(\+33|0)[1-9](\d{2}){4}$/',
        message: 'Le format du numéro de téléphone n\'est pas valide. Utilisez le format 0XXXXXXXXX ou +33XXXXXXXXX'
    )]
    private ?string $telephone = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Prestation::class)]
    private Collection $prestations;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getZoneIntervention(): ?string
    {
        return $this->zoneIntervention;
    }

    public function setZoneIntervention(?string $zoneIntervention): void
    {
        $this->zoneIntervention = $zoneIntervention;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getPrestations(): Collection
    {
        return $this->prestations;
    }

    public function setPrestations(Collection $prestations): void
    {
        $this->prestations = $prestations;
    }

    public function __construct()
    {
        $this->prestations = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
}