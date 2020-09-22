<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @ORM\Table(name="my_user",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="uer_email_idx",
 *            columns={"email"})
 *    }
 * )
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_that_rarely_changes")
 * @UniqueEntity(fields={"email"})
 */
class User implements UserInterface
{
    use TimestampableEntity, LoginTimestampable;
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="jsonb")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var ManuallyResourceJob[]|Collection
     * @ORM\OneToMany(targetEntity="ManuallyResourceJob",
     *      mappedBy="createdAtAdmin",fetch="LAZY")
     */
    private $resourceJobs;

    public function __construct()
    {
        $this->shippingAddress = new ArrayCollection();
        $this->resourceJobs = new ArrayCollection();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    /**
     * @return Collection|ManuallyResourceJob[]
     */
    public function getResourceJobs(): Collection
    {
        return $this->resourceJobs;
    }

    public function addResourceJob(ManuallyResourceJob $resourceJob): self
    {
        if (!$this->resourceJobs->contains($resourceJob)) {
            $this->resourceJobs[] = $resourceJob;
            $resourceJob->setCreatedAtAdmin($this);
        }

        return $this;
    }

    public function removeResourceJob(ManuallyResourceJob $resourceJob): self
    {
        if ($this->resourceJobs->contains($resourceJob)) {
            $this->resourceJobs->removeElement($resourceJob);
            // set the owning side to null (unless already changed)
            if ($resourceJob->getCreatedAtAdmin() === $this) {
                $resourceJob->setCreatedAtAdmin(null);
            }
        }

        return $this;
    }
}
