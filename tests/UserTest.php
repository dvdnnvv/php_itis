<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\User;

final class UserTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $user = new User('Иван', 'ivan@example.com');
    
        $this->assertEquals('Иван', $user->getName());
        $this->assertEquals('ivan@example.com', $user->getEmail());
    }
    public function testSetId(): void
    {
        $user = new User('Иван', 'ivan@example.com');
        $user->setId(5);
        $this->assertEquals(5, $user->getId());
    }

    public function testSetIdThrowsExceptionForInvalidId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ID must be positive');

        $user = new User('Иван', 'ivan@example.com');
        $user->setId(-1);
    }
    public function testSetName(): void
    {
        $user = new User('Иван', 'ivan@example.com');
        $user->setName('Пётр');
        $this->assertEquals('Пётр', $user->getName());
    }

    
    public static function validEmailProvider(): array
    {
        return [
            ['user@example.com'],
            ['ivan@mail.ru'],
            ['test@gmail.com'],
        ];
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['not-an-email'],
            ['missing@domain'],
            ['@no-name.com'],
            ['', 'empty string'],
        ];
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testSetEmailWithValidEmail(string $email): void
    {
        $user = new User('Иван', 'ivan@example.com');
        $user->setEmail($email);
        $this->assertEquals($email, $user->getEmail());
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testSetEmailThrowsExceptionForInvalidEmail(string $email): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        $user = new User('Иван', 'ivan@example.com');
        $user->setEmail($email);
    }
    public function testAddRoleAndHasRole(): void
    {
        $user = new User('Иван', 'ivan@example.com');
        $user->addRole('editor');

        $this->assertTrue($user->hasRole('editor'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertEquals(['editor'], $user->getRoles());
    }

    public function testAddRoleNoDuplicates(): void
    {
        $user = new User('Иван', 'ivan@example.com');
        $user->addRole('admin');
        $user->addRole('admin');

        $this->assertEquals(['admin'], $user->getRoles());
    }

    public function testAddRoleThrowsExceptionForInvalidRole(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid role');

        $user = new User('Иван', 'ivan@example.com');
        $user->addRole('superuser');
    }

    public function testIsAdmin(): void
    {
        $user = new User('Иван', 'ivan@example.com');
        $this->assertFalse($user->isAdmin());

        $user->addRole('admin');
        $this->assertTrue($user->isAdmin());
    }
}