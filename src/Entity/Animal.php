<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;



use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("rapportVet:read")]
    private ?int $id = null;

    #[ORM\Column(name: 'prenom', length: 50)]
    #[Assert\NotBlank]
    #[Groups(["animal:read", "habitat:read", "animal:write"])]
    private ?string $Prenom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(["rapportVet:read", "animal:read", "animal:write"])]
    private ?string $race = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups(["animal:read", "animal:write"])]
    private ?string $image = null;

    /**
     * @var Collection<int, RapporVeterinaire>
     */
    #[ORM\OneToMany(targetEntity: RapporVeterinaire::class, mappedBy: 'Rapports_animal')]
    private Collection $rapporVeterinaires;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[Assert\NotBlank]
    #[Groups("animal:write")]
    private ?Habitat $Habitat = null;

    public function __construct()
    {
        $this->rapporVeterinaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }
    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }
    public function setRace(string $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, RapporVeterinaire>
     */
    public function getRapporVeterinaires(): Collection
    {
        return $this->rapporVeterinaires;
    }

    public function addRapporVeterinaire(RapporVeterinaire $rapporVeterinaire): static
    {
        if (!$this->rapporVeterinaires->contains($rapporVeterinaire)) {
            $this->rapporVeterinaires->add($rapporVeterinaire);
            $rapporVeterinaire->setRapportsAnimal($this);
        }

        return $this;
    }

    public function removeRapporVeterinaire(RapporVeterinaire $rapporVeterinaire): static
    {
        if ($this->rapporVeterinaires->removeElement($rapporVeterinaire)) {
            // set the owning side to null (unless already changed)
            if ($rapporVeterinaire->getRapportsAnimal() === $this) {
                $rapporVeterinaire->setRapportsAnimal(null);
            }
        }

        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->Habitat;
    }
    public function setHabitat(?Habitat $Habitat): static
    {
        $this->Habitat = $Habitat;

        return $this;
    }
}
