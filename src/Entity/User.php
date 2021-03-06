<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Component\User\Dtos\RefreshTokenRequestDto;
use App\Component\User\Dtos\UserAuthDto;
use App\Component\User\Dtos\UserDto;
use App\Controller\DeleteAction;
use App\Controller\UserAboutMeAction;
use App\Controller\UserAuthAction;
use App\Controller\UserAuthByRefreshTokenAction;
use App\Controller\UserChangePasswordAction;
use App\Controller\UserCreateAction;
use App\Controller\UserIsUniqueEmailAction;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\IsDeletedSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get'                => [
            'security'              => "is_granted('ROLE_ADMIN')",
            'normalization_context' => ['groups' => ['users:read']],
        ],
        'post'               => [
            'controller' => UserCreateAction::class,
            'input'      => UserDto::class,
        ],
        'aboutMe'            => [
            'controller'      => UserAboutMeAction::class,
            'method'          => 'get',
            'path'            => 'users/about_me',
            'openapi_context' => [
                'summary'    => 'Shows info about the authenticated user',
//                'parameters' => [
//                    [
//                        'in'       => 'query',
//                        'name'     => 'test',
//                        'type'     => 'string',
//                        'required' => true,
//                        'example'  => '{"id": 1, "hash": "e9151ae9de2d5a3cd1d16834431a0317"}',
//                    ],
//                ],
            ],
        ],
        'auth'               => [
            'controller'      => UserAuthAction::class,
            'input'           => UserAuthDto::class,
            'method'          => 'post',
            'path'            => 'users/auth',
            'openapi_context' => ['summary' => 'Authorization'],
        ],
        'authByRefreshToken' => [
            'controller'      => UserAuthByRefreshTokenAction::class,
            'method'          => 'post',
            'path'            => 'users/auth/refreshToken',
            'openapi_context' => ['summary' => 'Authorization by refreshToken'],
            'input'           => RefreshTokenRequestDto::class,
        ],
        'isUniqueEmail'      => [
            'controller'              => UserIsUniqueEmailAction::class,
            'method'                  => 'post',
            'path'                    => 'users/is_unique_email',
            'openapi_context'         => ['summary' => 'Checks email for uniqueness'],
            'denormalization_context' => ['groups' => ['user:isUniqueEmail:write']],
        ],
    ],
    itemOperations: [
        'get'            => [
            'security' => "object == user || is_granted('ROLE_ADMIN')",
        ],
        'put'            => [
            'security'                => "object == user || is_granted('ROLE_ADMIN')",
            'denormalization_context' => ['groups' => ['user:put:write']],
        ],
        'delete'         => [
            'controller' => DeleteAction::class,
            'security'   => "object == user || is_granted('ROLE_ADMIN')",
        ],
        'changePassword' => [
            'controller'              => UserChangePasswordAction::class,
            'method'                  => 'put',
            'path'                    => 'users/{id}/password',
            'security'                => "object == user || is_granted('ROLE_ADMIN')",
            'openapi_context'         => ['summary' => 'Changes password'],
            'denormalization_context' => ['groups' => ['user:changePassword:write']],
        ],
    ],
    denormalizationContext: ['groups' => ['user:write']],
    normalizationContext: ['groups' => ['user:read', 'users:read']],
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'createdAt', 'updatedAt', 'email'])]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'email' => 'partial'])]
#[UniqueEntity('email', message: 'Bu email band')]
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @see OrderFilter
 * @see SearchFilter
 * @see UserCreateAction
 * @see UserAboutMeAction
 * @see UserIsUniqueEmailAction
 * @see UserChangePasswordAction
 * @see UserAuthAction
 * @see DeleteAction
 * @see UserAuthByRefreshTokenAction
 * @see RefreshTokenRequestDto
 */
class User implements
    UserInterface,
    UpdatedAtSettableInterface,
    CreatedAtSettableInterface,
    IsDeletedSettableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['users:read'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\Email]
    #[Assert\NotBlank]
    #[Groups(['user:read', 'users:read', 'user:write', 'user:put:write', 'user:isUniqueEmail:write'])]
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Groups(['user:write', 'user:changePassword:write'])]
    private $password;

    /**
     * @ORM\Column(type="array")
     */
    #[Groups(['user:read'])]
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['user:read'])]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"user:read"})
     */
    #[Groups(['user:read'])]
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted = false;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="users")
     */
    #[Groups(['user:read'])]
    private $tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Groups(['user:write', 'user:read', 'users:read', 'user:put:write'])]
    private $givenName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Groups(['user:write', 'user:read', 'users:read', 'user:put:write'])]
    private $familyName;

    /**
     * @ORM\OneToMany(targetEntity=Work::class, mappedBy="user")
     */
    #[Groups(['user:read', 'users:read'])]
    private $works;

    /**
     * @ORM\OneToMany(targetEntity=WorkComment::class, mappedBy="user")
     */
    #[Groups(['user:read', 'users:read'])]
    private $workComments;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->works = new ArrayCollection();
        $this->workComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function deleteRole(string $role): self
    {
        $roles = $this->roles;

        foreach ($roles as $roleKey => $roleName) {
            if ($roleName === $role) {
                unset($roles[$roleKey]);
                $this->setRoles($roles);
            }
        }

        return $this;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->getEmail();
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

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

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
            $tag->addUser($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeUser($this);
        }

        return $this;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName): self
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * @return Collection|Work[]
     */
    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(Work $work): self
    {
        if (!$this->works->contains($work)) {
            $this->works[] = $work;
            $work->setUser($this);
        }

        return $this;
    }

    public function removeWork(Work $work): self
    {
        if ($this->works->removeElement($work)) {
            // set the owning side to null (unless already changed)
            if ($work->getUser() === $this) {
                $work->setUser(null);
            }
        }

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
            $workComment->setUser($this);
        }

        return $this;
    }

    public function removeWorkComment(WorkComment $workComment): self
    {
        if ($this->workComments->removeElement($workComment)) {
            // set the owning side to null (unless already changed)
            if ($workComment->getUser() === $this) {
                $workComment->setUser(null);
            }
        }

        return $this;
    }
}
