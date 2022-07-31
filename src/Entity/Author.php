<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\Column(length: 255)]
    private ?string $locale = 'en';

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'authors')]
    private ?self $mainAuthor = null;

    #[ORM\OneToMany(mappedBy: 'mainAuthor', targetEntity: self::class)]
    private Collection $authors;

    public function __toString(): string
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->authors = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getMainAuthor(): ?self
    {
        return $this->mainAuthor;
    }

    public function setMainAuthor(?self $mainAuthor): self
    {
        $this->mainAuthor = $mainAuthor;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(self $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
            $author->setMainAuthor($this);
        }

        return $this;
    }

    public function removeAuthor(self $author): self
    {
        if ($this->authors->removeElement($author)) {
            // set the owning side to null (unless already changed)
            if ($author->getMainAuthor() === $this) {
                $author->setMainAuthor(null);
            }
        }

        return $this;
    }
}
