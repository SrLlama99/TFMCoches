<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'usuario')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'id')]
    #[ORM\GeneratedValue]
    private $UserId;

    #[ORM\Column(type: 'string', name: 'nombre')]
    private $UserName;

    #[ORM\Column(type: 'string', name: 'correo')]
    private $UserMail;

    #[ORM\Column(type: 'string', name: 'UserPassword')]
    private $UserPassword;

    #[ORM\Column(type: 'string', name: 'admin')]
    private $Admin;

    public function getUserId()
    {
        return $this->UserId;
    }

    public function setUserId($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    public function getUserName()
    {
        return $this->UserName;
    }

    public function setUserName($UserName)
    {
        $this->UserName = $UserName;
        return $this;
    }

    public function getUserMail()
    {
        return $this->UserMail;
    }

    public function setUserMail($UserMail)
    {
        $this->UserMail = $UserMail;
        return $this;
    }

    public function getUserPassword()
    {
        return $this->UserPassword;
    }

    public function setUserPassword($UserPassword)
    {
        $this->UserPassword = $UserPassword;
    }

    public function getAdmin()
    {
        return $this->Admin;
    }

    public function setAdmin($Admin)
    {
        $this->Admin = $Admin;
    }

    public function getRoles(): array
    {
        if ($this->Admin === 1)
            return ['ROLE_USER', 'ROLE_ADMIN'];
        else
            return ['ROLE_USER'];
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function getPassword(): string
    {
        return $this->getUserPassword();
    }


    public function getUserIdentifier(): string
    {
        return $this->getUserMail();
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void {}

}