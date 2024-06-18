<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hostTeam = null;

    #[ORM\Column(length: 255)]
    private ?string $guestTeam = null;

    #[ORM\Column(nullable: true)]
    private ?int $hostTeamScores = null;

    #[ORM\Column(nullable: true)]
    private ?int $guestTeamScores = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $matchDate = null;

    /**
     * @var Collection<int, Prediction>
     */
    #[ORM\OneToMany(targetEntity: Prediction::class, mappedBy: 'event', orphanRemoval: true)]
    private Collection $predictions;

    public function __construct()
    {
        $this->predictions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHostTeam(): ?string
    {
        return $this->hostTeam;
    }

    public function setHostTeam(string $hostTeam): static
    {
        $this->hostTeam = $hostTeam;

        return $this;
    }

    public function getGuestTeam(): ?string
    {
        return $this->guestTeam;
    }

    public function setGuestTeam(string $guestTeam): static
    {
        $this->guestTeam = $guestTeam;

        return $this;
    }

    public function getHostTeamScores(): ?int
    {
        return $this->hostTeamScores;
    }

    public function setHostTeamScores(?int $hostTeamScores): static
    {
        $this->hostTeamScores = $hostTeamScores;

        return $this;
    }

    public function getGuestTeamScores(): ?int
    {
        return $this->guestTeamScores;
    }

    public function setGuestTeamScores(?int $guestTeamScores): static
    {
        $this->guestTeamScores = $guestTeamScores;

        return $this;
    }

    public function getMatchDate(): ?\DateTimeInterface
    {
        return $this->matchDate;
    }

    public function setMatchDate(\DateTimeInterface $matchDate): static
    {
        $this->matchDate = $matchDate;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->hostTeam . ' - ' . $this->guestTeam;
    }

    /**
     * @return Collection<int, Prediction>
     */
    public function getPredictions(): Collection
    {
        return $this->predictions;
    }

    public function addPrediction(Prediction $prediction): static
    {
        if (!$this->predictions->contains($prediction)) {
            $this->predictions->add($prediction);
            $prediction->setEvent($this);
        }

        return $this;
    }

    public function removePrediction(Prediction $prediction): static
    {
        if ($this->predictions->removeElement($prediction)) {
            // set the owning side to null (unless already changed)
            if ($prediction->getEvent() === $this) {
                $prediction->setEvent(null);
            }
        }

        return $this;
    }

    public function isPredictable(): bool
    {
        return $this->matchDate->getTimestamp() > (new \DateTime('+1 hour'))->getTimestamp();
    }
}
