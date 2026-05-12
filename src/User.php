<?php

declare(strict_types=1);

namespace App;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private array $roles = [];

    public function __construct(string $name, string $email)
    {
        $this->setName($name);
        $this->setEmail($email);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('ID must be positive');
        }
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Name must be at least 2 characters');
        }
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, ['admin', 'editor', 'viewer'])) {
            throw new \InvalidArgumentException('Invalid role');
        }
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}