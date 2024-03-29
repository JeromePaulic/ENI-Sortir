<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $rue = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Sortie::class)]
    private Collection $sortiesOrganise;

    #[ORM\ManyToOne(inversedBy: 'lieus')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ville $ville = null;

    public function __construct()
    {
        $this->sortiesOrganise = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganise(): Collection
    {
        return $this->sortiesOrganise;
    }

    public function addSortiesOrganise(Sortie $sortiesOrganise): self
    {
        if (!$this->sortiesOrganise->contains($sortiesOrganise)) {
            $this->sortiesOrganise->add($sortiesOrganise);
            $sortiesOrganise->setLieu($this);
        }

        return $this;
    }

    public function removeSortiesOrganise(Sortie $sortiesOrganise): self
    {
        if ($this->sortiesOrganise->removeElement($sortiesOrganise)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganise->getLieu() === $this) {
                $sortiesOrganise->setLieu(null);
            }
        }

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
}
