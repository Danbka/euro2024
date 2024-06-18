<?php

namespace App\Entity;

use App\Repository\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
class Prediction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'event')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'predictions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column]
    private ?int $hostTeamScores = null;

    #[ORM\Column]
    private ?int $guestTeamScores = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getHostTeamScores(): ?int
    {
        return $this->hostTeamScores;
    }

    public function setHostTeamScores(int $hostTeamScores): static
    {
        $this->hostTeamScores = $hostTeamScores;

        return $this;
    }

    public function getGuestTeamScores(): ?int
    {
        return $this->guestTeamScores;
    }

    public function setGuestTeamScores(int $guestTeamScores): static
    {
        $this->guestTeamScores = $guestTeamScores;

        return $this;
    }

    public function getPoints(): int
    {
        $event = $this->event;

        // unknown result
        if ($event->getHostTeamScores() === null || $event->getGuestTeamScores() === null) {
            return 0;
        }

        // match!
        if ($this->hostTeamScores === $event->getHostTeamScores() && $this->guestTeamScores === $event->getGuestTeamScores()) {
            return 5;
        }

        // draw
        if ($this->hostTeamScores === $this->guestTeamScores && $event->getHostTeamScores() === $event->getGuestTeamScores()) {
            return 3;
        }

        // trend
        if (
            $this->hostTeamScores > $this->guestTeamScores && $event->getHostTeamScores() > $event->getGuestTeamScores()
            || $this->hostTeamScores < $this->guestTeamScores && $event->getHostTeamScores() < $event->getGuestTeamScores()
        ) {
            return 3;
        }

        return 0;
    }
}
