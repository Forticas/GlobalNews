<?php

namespace App\Entity;


use App\Repository\KeywordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KeywordRepository::class)]
class Keyword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'keywords')]
    private Collection $posts;

    #[ORM\Column(length: 255)]
    private ?string $locale = 'en';

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'keywords')]
    private ?self $mainKeyword = null;

    #[ORM\OneToMany(mappedBy: 'mainKeyword', targetEntity: self::class)]
    private Collection $keywords;

    public function __toString(): string
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->keywords = new ArrayCollection();
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
            $post->addKeyword($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            $post->removeKeyword($this);
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

    public function getMainKeyword(): ?self
    {
        return $this->mainKeyword;
    }

    public function setMainKeyword(?self $mainKeyword): self
    {
        $this->mainKeyword = $mainKeyword;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(self $keyword): self
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->setMainKeyword($this);
        }

        return $this;
    }

    public function removeKeyword(self $keyword): self
    {
        if ($this->keywords->removeElement($keyword)) {
            // set the owning side to null (unless already changed)
            if ($keyword->getMainKeyword() === $this) {
                $keyword->setMainKeyword(null);
            }
        }

        return $this;
    }
}
