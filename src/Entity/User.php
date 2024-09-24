<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: ' email {{ value }} n\'est pas  valide .',
    )]
    #[Groups("user:read", "user:create")]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups("user:read", "user:create")]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups("user:read", "user:create")]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Groups("user:create")]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    private Collection $userRoles;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    #[Groups("user:read")]
    public function getRoles(): array
    {
        $roles = $this->userRoles->map(function (Role $role) {
            return $role->getLabel();
        })->toArray();

        // Etre sur  que chaque utilisateur a au moins ROLE_USER
        $roles[] = 'ROLE_EMPLOYE';

        return array_unique($roles);
    }


    public function addUserRole(Role $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->addUser($this);
        }
        return $this;
    }


    public function removeUserRole(Role $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            $userRole->removeUser($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }
}
