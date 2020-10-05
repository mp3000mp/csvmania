<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssignmentRepository")
 */
class Assignment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="assignedAssignments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $assigned_to;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Task", inversedBy="assignments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @ORM\Column(type="datetime")
     */
    private $assigned_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $done_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validated_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="validatedAssignments")
     */
    private $validated_by;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="assignment", orphanRemoval=true)
     */
    private $answers;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 0;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assigned_to;
    }

    public function setAssignedTo(?User $assigned_to): self
    {
        $this->assigned_to = $assigned_to;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getAssignedAt(): ?\DateTimeInterface
    {
        return $this->assigned_at;
    }

    public function setAssignedAt(\DateTimeInterface $assigned_at): self
    {
        $this->assigned_at = $assigned_at;

        return $this;
    }

    public function getDoneAt(): ?\DateTimeInterface
    {
        return $this->done_at;
    }

    public function setDoneAt(?\DateTimeInterface $done_at): self
    {
        $this->done_at = $done_at;

        return $this;
    }

    public function getValidatedAt(): ?\DateTimeInterface
    {
        return $this->validated_at;
    }

    public function setValidatedAt(?\DateTimeInterface $validated_at): self
    {
        $this->validated_at = $validated_at;

        return $this;
    }

    public function getValidatedBy(): ?User
    {
        return $this->validated_by;
    }

    public function setValidatedBy(?User $validated_by): self
    {
        $this->validated_by = $validated_by;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setAssignment($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getAssignment() === $this) {
                $answer->setAssignment(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }








}
