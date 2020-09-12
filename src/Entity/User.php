<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="User.with.this.email.is.already.registered")
 */
class User implements UserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const USER_STATUS_AWAITING = 'Awaiting Activation';
    public const USER_STATUS_ACTIVE = 'Active';
    public const USER_STATUS_BLOCKED = 'Blocked';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private string $status;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private string $role;

    /**
     * @ORM\OneToMany(targetEntity=Play::class, mappedBy="user", orphanRemoval=true)
     */
    private $plays;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $confirmationCode;

    public function __construct()
    {
        $this->plays = new ArrayCollection();
        $this->role = self::ROLE_USER;
        $this->status = self::USER_STATUS_AWAITING;
    }

    public function activate()
	{
		$this->status = self::USER_STATUS_ACTIVE;
	}

    public function block()
    {
        $this->status = self::USER_STATUS_BLOCKED;
    }

    public function setAdmin()
    {
        $this->role=self::ROLE_ADMIN;
    }

    public function setUser()
    {
        $this->role=self::ROLE_USER;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @return Collection|Play[]
     */
    public function getPlays(): Collection
    {
        return $this->plays;
    }

    public function addPlay(Play $play): self
    {
        if (!$this->plays->contains($play)) {
            $this->plays[] = $play;
            $play->setUser($this);
        }

        return $this;
    }

    public function removePlay(Play $play): self
    {
        if ($this->plays->contains($play)) {
            $this->plays->removeElement($play);
            if ($play->getUser() === $this) {
                $play->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
    }

    public function getSalt()
    {
    }

    public function getUsername(): string
    {
        return $this->name;
    }

    public function eraseCredentials()
    {
    }

    public function getConfirmationCode(): string
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode(string $confirmationCode): self
    {
        $this->confirmationCode = $confirmationCode;
        return $this;
    }
}
