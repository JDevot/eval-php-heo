<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DemandeRepository::class)
 */
class Demande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="demande")
     */
    private $tickets;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="demandes")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $idProprietaire;

    public function __toString()
    {
        return $this->getTitle();
    }
    
    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmail():?string
    {
        dd($this->$this->user->getEmail());
        return $this->$this->user->getEmail();
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setDemande($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getDemande() === $this) {
                $ticket->setDemande(null);
            }
        }

        return $this;
    }

    public function addUser(user $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(user $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    public function getIdProprietaire(): ?int
    {
        return $this->idProprietaire;
    }

    public function setIdProprietaire(int $idProprietaire): self
    {
        $this->idProprietaire = $idProprietaire;

        return $this;
    }
}
