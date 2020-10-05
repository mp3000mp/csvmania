<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $last_name;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="smallint")
     */
    private $nb_failed_connexion;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="created_by")
     */
    private $created_tasks;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Assignment", mappedBy="assigned_to", orphanRemoval=true)
     */
    private $assignedAssignments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Assignment", mappedBy="validated_by")
     */
    private $validatedAssignments;

    public function __construct()
    {
        $this->created_tasks = new ArrayCollection();
        $this->assignedAssignments = new ArrayCollection();
        $this->validatedAssignments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getNbFailedConnexion(): ?int
    {
        return $this->nb_failed_connexion;
    }

    public function setNbFailedConnexion(int $nb_failed_connexion): self
    {
        $this->nb_failed_connexion = $nb_failed_connexion;

        return $this;
    }
    public function addNbFailedConnexion(): self
    {
        $this->nb_failed_connexion++;

        return $this;
    }
    public function razNbFailedConnexion(): self
    {
        $this->nb_failed_connexion = 0;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getCreatedTasks(): Collection
    {
        return $this->created_tasks;
    }

    public function addCreatedTask(Task $createdTask): self
    {
        if (!$this->created_tasks->contains($createdTask)) {
            $this->created_tasks[] = $createdTask;
            $createdTask->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedTask(Task $createdTask): self
    {
        if ($this->created_tasks->contains($createdTask)) {
            $this->created_tasks->removeElement($createdTask);
            // set the owning side to null (unless already changed)
            if ($createdTask->getCreatedBy() === $this) {
                $createdTask->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }




    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->active,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->active,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return ($this->nb_failed_connexion <= 3);
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->active;
    }

    /**
     * @return Collection|Assignment[]
     */
    public function getAssignments(): Collection
    {
        return $this->assignedAssignments;
    }

    public function addAssignment(Assignment $assignment): self
    {
        if (!$this->assignedAssignments->contains($assignment)) {
            $this->assignedAssignments[] = $assignment;
            $assignment->setUser($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): self
    {
        if ($this->assignedAssignments->contains($assignment)) {
            $this->assignedAssignments->removeElement($assignment);
            // set the owning side to null (unless already changed)
            if ($assignment->getUser() === $this) {
                $assignment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Assignment[]
     */
    public function getValidatedAssignments(): Collection
    {
        return $this->validatedAssignments;
    }

    public function addValidatedAssignment(Assignment $validatedAssignment): self
    {
        if (!$this->validatedAssignments->contains($validatedAssignment)) {
            $this->validatedAssignments[] = $validatedAssignment;
            $validatedAssignment->setValidatedBy($this);
        }

        return $this;
    }

    public function removeValidatedAssignment(Assignment $validatedAssignment): self
    {
        if ($this->validatedAssignments->contains($validatedAssignment)) {
            $this->validatedAssignments->removeElement($validatedAssignment);
            // set the owning side to null (unless already changed)
            if ($validatedAssignment->getValidatedBy() === $this) {
                $validatedAssignment->setValidatedBy(null);
            }
        }

        return $this;
    }


}
