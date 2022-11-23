<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,)]
    #[Assert\NotBlank(message:"la sortie doit avoir un nom svp") ]
    #[Assert\Length( min:"4" , max:"255")]

    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message:" veuillez saisir date et heure svp") ]
   // #[Assert\GreaterThanOrEqual(propertyPath: "") ]

    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:" combien de temps va durer cette super sortie !") ]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Jusqu'a quand  peut-on  s'incrire à votre super activité!") ]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Plus on est de fou plus on rit! Mais combien vous serrez au Max!! ") ]
    private ?int $nbParticipantMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $infoSortie = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campusOrganisateur = null;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrganise')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message:"Ou aurra lieu l'activité ! ") ]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sortiesOrganise')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $organisateur = null;

    #[ORM\OneToMany(mappedBy: 'sortie', targetEntity: Inscription::class)]
    private Collection $inscriptions;



    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbParticipantMax(): ?int
    {
        return $this->nbParticipantMax;
    }

    public function setNbParticipantMax(int $nbParticipantMax): self
    {
        $this->nbParticipantMax = $nbParticipantMax;

        return $this;
    }

    public function getInfoSortie(): ?string
    {
        return $this->infoSortie;
    }

    public function setInfoSortie(?string $infoSortie): self
    {
        $this->infoSortie = $infoSortie;

        return $this;
    }

    public function getCampusOrganisateur(): ?Campus
    {
        return $this->campusOrganisateur;
    }

    public function setCampusOrganisateur(?Campus $campusOrganisateur): self
    {
        $this->campusOrganisateur = $campusOrganisateur;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function sortieComplete():bool
    {
        if ($this ->getNbParticipantMax()&& $this ->getInscriptions()->count()>=$this->getNbParticipantMax()){
            return true;
        }
        return false;
    }


    /**
    * @param UserInterface $user
    * @return bool
    */
    public function estinscrit(UserInterface $user): bool
    {
        foreach($this->getInscriptions() as $sub){
            if ($sub->getParticipants() === $user){
                return true;
            }
        }

        return false;
    }


}
