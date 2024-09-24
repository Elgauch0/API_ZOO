<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RapporVeterinaireRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RapporVeterinaireRepository::class)]
class RapporVeterinaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("rapportVet:read")]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups("rapportVet:read", "rapportVet:write")]
    private ?string $etat = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups("rapportVet:write")]
    private ?string $nourriture = null;

    #[ORM\Column]
    #[Groups("rapportVet:read")]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(inversedBy: 'rapporVeterinaires')]
    #[Groups("rapportVet:read", "rapportVet:write")]
    private ?Animal $Rapports_animal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getNourriture(): ?string
    {
        return $this->nourriture;
    }

    public function setNourriture(string $nourriture): static
    {
        $this->nourriture = $nourriture;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getRapportsAnimal(): ?Animal
    {
        return $this->Rapports_animal;
    }

    public function setRapportsAnimal(?Animal $Rapports_animal): static
    {
        $this->Rapports_animal = $Rapports_animal;

        return $this;
    }
}
