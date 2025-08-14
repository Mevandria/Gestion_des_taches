<?php

namespace App\Entity;

use App\Repository\FichierTacheRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Tache;
/**
 * @ORM\Entity(repositoryClass=FichierTacheRepository::class)
 */
class FichierTache
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
    private $nomFichier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $routeFichier;

    /**
     * @ORM\ManyToOne(targetEntity=Tache::class, inversedBy="fichierTaches")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?Tache $tache = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(string $nomFichier): self
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    public function getRouteFichier(): ?string
    {
        return $this->routeFichier;
    }

    public function setRouteFichier(string $routeFichier): self
    {
        $this->routeFichier = $routeFichier;

        return $this;
    }

    public function getTache(): ?Tache
    {
        return $this->tache;
    }

    public function setTache(?Tache $tache): self
    {
        $this->tache = $tache;

        return $this;
    }
}
