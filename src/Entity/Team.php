<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['team'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['team', 'people'])]
    private ?string $name = null;

    /**
     * @var Collection<int, People>
     */
    #[ORM\ManyToMany(targetEntity: People::class, mappedBy: 'team')]
    #[Groups(['team'])]
    private Collection $people;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, People>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(People $person): static
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->addTeam($this);
        }

        return $this;
    }

    public function removePerson(People $person): static
    {
        if ($this->people->removeElement($person)) {
            $person->removeTeam($this);
        }

        return $this;
    }
}
