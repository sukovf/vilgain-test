<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Security\UserRole;
use App\Type\UserRoleType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use JsonSerializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JsonSerializable
{
    #[Id]
    #[GeneratedValue]
    #[Column('id', Types::INTEGER)]
    private int $id;

    #[NotBlank, Email]
    #[Column('email', Types::STRING)]
    private string $email;

    #[Column('password', Types::STRING)]
    private string $password;

    #[NotBlank]
    #[Column('name', Types::STRING)]
    private string $name;

    #[Column('role', UserRoleType::NAME)]
    private UserRole $role;

    /** @var Collection<int, Article> */
    #[OneToMany(targetEntity: Article::class, mappedBy: 'author', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): self
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param Collection<int, Article> $articles
     */
    public function setArticles(Collection $articles): self
    {
        $this->articles = $articles;
        return $this;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setAuthor($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        $this->articles->removeElement($article);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        /**
         * I really hate to mute PHPStan here. I don't know how to tell the DB that the email property is a non-empty
         * string in a way that PHPStan understands and stops complaining about mismatch between the DB and entity
         * definitions.
         */
        /** @phpstan-ignore-next-line */
        return $this->email;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'name'      => $this->name,
            'role'      => $this->role->value
        ];
    }
}