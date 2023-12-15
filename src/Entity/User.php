<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;          // On indique qu'on va utiliser Doctrine

#[ORM\Entity, ORM\Table(name: 'users')]     // On indique que cette classe est considérée comme une table 'users" dans la bdd
class User
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]    // On indique que cet attribut est clé primaire et auto-increment
    private int $id;

    #[ORM\Column(length: 255)]                   // On indique que cet attribut est une simple colonne de la table
    private string $email;

    #[ORM\Column(length: 255)]                  // On indique que cet attribut est une simple colonne de la table
    private string $password;

    public function getId(): int
    {
        return $this->id;
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
}
