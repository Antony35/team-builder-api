<?php

namespace App\Entity;

use App\Repository\PeopleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PeopleRepository::class)]
class People
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('people')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['people', 'team'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['people', 'team'])]
    private ?string $lastname = null;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'people', cascade:['persist'])]
    #[ORM\JoinTable(name:'people_team')]
    #[Groups('people')]
    private Collection $team;

    public function __construct()
    {
        $this->team = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeam(): Collection
    {
        return $this->team;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->team->contains($team)) {
            $this->team->add($team);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        $this->team->removeElement($team);

        return $this;
    }

    public function getFullName(): string
    {
        return sprintf("%s %s", $this->getFirstname(), $this->getLastname());
    }
}
