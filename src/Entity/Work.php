<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\IsDeletedSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Interfaces\UserSettableInterface;
use App\Repository\WorkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=WorkRepository::class)
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put', 'delete'],
    denormalizationContext: ['groups' => ['work:write']],
    normalizationContext: ['groups' => ['work:read']],
)]

class Work implements
    UserSettableInterface,
    UpdatedAtSettableInterface,
    CreatedAtSettableInterface,
    IsDeletedSettableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['work:read'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    #[Groups(['work:read', 'work:write'])]
    private $theme;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    #[Groups(['work:read', 'work:write'])]
    private $text;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="works")
     */
    #[Groups(['work:read', 'work:write'])]
    private $tags;

    /**
     * @ORM\Column(type="bigint")
     * @Assert\NotBlank
     */
    #[Groups(['work:read', 'work:write'])]
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="works")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['work:read'])]
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['work:read'])]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[Groups(['work:read'])]
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDeleted = false;

    /**
     * @ORM\OneToMany(targetEntity=WorkComment::class, mappedBy="work")
     */
    #[Groups(['work:read'])]
    private $workComments;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->workComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return Collection|WorkComment[]
     */
    public function getWorkComments(): Collection
    {
        return $this->workComments;
    }

    public function addWorkComment(WorkComment $workComment): self
    {
        if (!$this->workComments->contains($workComment)) {
            $this->workComments[] = $workComment;
            $workComment->setWork($this);
        }

        return $this;
    }

    public function removeWorkComment(WorkComment $workComment): self
    {
        if ($this->workComments->removeElement($workComment)) {
            // set the owning side to null (unless already changed)
            if ($workComment->getWork() === $this) {
                $workComment->setWork(null);
            }
        }

        return $this;
    }
}
