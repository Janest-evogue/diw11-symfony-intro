<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * Clé primaire
     * @ORM\Id()
     * auto-increment
     * @ORM\GeneratedValue()
     * champ de type integer en bdd
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * varchar(100) NOT NULL en bdd
     * @ORM\Column(type="string", length=100)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstname;

    /**
     * varchar(255) NOT NULL unique en bdd
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * date NOT NULL en bdd
     * @ORM\Column(type="date")
     */
    private $birthdate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
