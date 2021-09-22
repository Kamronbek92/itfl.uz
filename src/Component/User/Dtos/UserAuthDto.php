<?php
declare(strict_types=1);

namespace App\Component\User\Dtos;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AuthenticationTokenDto
 *
 * @package App\Component\User\Dtos
 */
class UserAuthDto
{
    /**
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    #[Groups(['user:read', 'user:write', 'user:isUniqueEmail:write'])]
    private string $email;

    /**
     * @Assert\Length(min="6")
     * @Assert\NotBlank
     */
    #[Groups(['user:write'])]
    private string $password;


    public function __construct(string $email, string $password,  /*, App $app */)
    {
        $this->email = $email;
        $this->password = $password;
//        $this->app = $app;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }


//    public function getApp(): App
//    {
//        return $this->app;
//    }
}
