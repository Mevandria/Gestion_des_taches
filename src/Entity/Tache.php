<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\FichierTache;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * @ORM\Entity(repositoryClass=TacheRepository::class)
 */
class Tache
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
    private $Titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estFait;

    /**
     * @ORM\ManyToOne(inversedBy= "taches")
     * @ORM\JoinColumn(nullable= false)
     */
    private ?User $owner = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private Collection $partagerAvec;

    /**
     * @ORM\Column(type= "datetime", nullable= true)
     */
    private ?\DateTimeInterface $deadline = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FichierTache", mappedBy="tache", cascade={"persist", "remove"})
    */
    private collection $fichierTaches;



    public function __construct()
    {
        $this->partagerAvec = new ArrayCollection();
        $this->fichierTaches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): self
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getEstFait(): bool
    {
        return $this->estFait;
    }

    public function setEstFait(bool $estFait): self
    {
        $this->estFait = $estFait;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getPartagerAvec(): Collection
    {
        return $this->partagerAvec;
    }

    public function addPartagerAvec(User $user): self
    {
        if (!$this->partagerAvec->contains($user)) {
            $this->partagerAvec->add($user);
        }

        return $this;
    }

    public function removePartagerAvec(User $user): self
    {
        $this->partagerAvec->removeElement($user);
        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;
        return $this;
    }
    /**
     * @return Collection<int, FichierTache>
     */
    public function getFichierTaches(): Collection
    {
        return $this->fichierTaches;
    }

    public function addFichierTache(FichierTache $fichierTache): self
    {
        if (!$this->fichierTaches->contains($fichierTache)) {
            $this->fichierTaches[] = $fichierTache;
            $fichierTache->setTache($this);
        }

        return $this;
    }

    public function removeFichierTache(FichierTache $fichierTache): self
    {
        if ($this->fichierTaches->removeElement($fichierTache)) {
            if ($fichierTache->getTache() === $this) {
                $fichierTache->setTache(null);
            }
        }

        return $this;
    }

}
