<?php

namespace App\Entity;

use App\Repository\BorrowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowRepository::class)]
class Borrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'borrows')]
    private ?User $idUser = null;

    #[ORM\OneToMany(mappedBy: 'borrow', targetEntity: Book::class)]
    private Collection $idBook;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateBorrow = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable:true)]
    private ?\DateTimeInterface $dateReturn = null;

    public function __construct()
    {
        $this->idBook = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getIdBook(): Collection
    {
        return $this->idBook;
    }

    public function addIdBook(Book $idBook): self
    {
        if (!$this->idBook->contains($idBook)) {
            $this->idBook->add($idBook);
            $idBook->setBorrow($this);
        }

        return $this;
    }

    public function removeIdBook(Book $idBook): self
    {
        if ($this->idBook->removeElement($idBook)) {
            // set the owning side to null (unless already changed)
            if ($idBook->getBorrow() === $this) {
                $idBook->setBorrow(null);
            }
        }

        return $this;
    }

    public function getDateBorrow(): ?\DateTimeInterface
    {
        return $this->dateBorrow;
    }

    public function setDateBorrow(\DateTimeInterface $dateBorrow): self
    {
        $this->dateBorrow = $dateBorrow;

        return $this;
    }

    public function getDateReturn(): ?\DateTimeInterface
    {
        return $this->dateReturn;
    }

    public function setDateReturn(\DateTimeInterface $dateReturn): self
    {
        $this->dateReturn = $dateReturn;

        return $this;
    }
}
