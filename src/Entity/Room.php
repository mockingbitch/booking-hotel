<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=BookingDetail::class, mappedBy="room")
     */
    private $bookingDetails;

    /**
     * @ORM\OneToMany(targetEntity=Amount::class, mappedBy="room")
     */
    private $amounts;

    public function __construct()
    {
        $this->bookingDetails = new ArrayCollection();
        $this->amounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|BookingDetail[]
     */
    public function getBookingDetails(): Collection
    {
        return $this->bookingDetails;
    }

    public function addBookingDetail(BookingDetail $bookingDetail): self
    {
        if (!$this->bookingDetails->contains($bookingDetail)) {
            $this->bookingDetails[] = $bookingDetail;
            $bookingDetail->setRoom($this);
        }

        return $this;
    }

    public function removeBookingDetail(BookingDetail $bookingDetail): self
    {
        if ($this->bookingDetails->removeElement($bookingDetail)) {
            // set the owning side to null (unless already changed)
            if ($bookingDetail->getRoom() === $this) {
                $bookingDetail->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Amount[]
     */
    public function getAmounts(): Collection
    {
        return $this->amounts;
    }

    public function addAmount(Amount $amount): self
    {
        if (!$this->amounts->contains($amount)) {
            $this->amounts[] = $amount;
            $amount->setRoom($this);
        }

        return $this;
    }

    public function removeAmount(Amount $amount): self
    {
        if ($this->amounts->removeElement($amount)) {
            // set the owning side to null (unless already changed)
            if ($amount->getRoom() === $this) {
                $amount->setRoom(null);
            }
        }

        return $this;
    }
}
