<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AuthorizationTest extends ApiTestCase
{
    use RefreshDatabaseTrait;


    public function testUserRegistration(): void
    {

        $this->createRegistrationRequest();

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@type' => 'User',
            'email' => 'test@test.com',
            'message' => 'Rejestracja powiodła się! Sprawdź podany adres email.',

        ]);
    }

    public function testUserRegistrationValidationEmail(): void
    {
        $this->createRegistrationRequest(email: 'test.com');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'email: This value is not a valid email address.'
        ]);
    }

    public function testUserRegistrationValidationName(): void
    {
        $this->createRegistrationRequest(name: 'a');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'name: Name have to be at least 2 characters'
        ]);

        $this->createRegistrationRequest(name: str_repeat('a', 55));
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'name: Name have to be no longer than 50 characters'
        ]);

    }

    public function testUserRegistrationValidationSurname(): void
    {
        $this->createRegistrationRequest(surname: 'a');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'surname: Surname have to be at least 2 characters'
        ]);

        $this->createRegistrationRequest(surname: str_repeat('a', 55));
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'surname: Surname have to be no longer than 50 characters'
        ]);

    }

    public function testUserRegistrationValidationPassword(): void
    {
        $this->createRegistrationRequest(password: 'Password', passswordConfirmation: 'Password');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'password: Password have to be minimum 8 characters and contains at least one letter and number.'
        ]);

    }

    public function testUserRegistrationValidationPasswordAndConfirmationPassword(): void
    {
        $this->createRegistrationRequest(password: 'Password1', passswordConfirmation: 'Password');

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'The password and confirmation fields are not equal.'
        ]);

    }

    public function testIfConfirmationEmailWasSend()
    {
        $this->createRegistrationRequest();

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Outsource me - registration confirmation');

    }

    private function createRegistrationRequest($email = 'test@test.com',
                                               $name = 'Test',
                                               $surname = 'Test',
                                               $password = 'PasswordTest123',
                                               $passswordConfirmation = 'PasswordTest123',
                                               $accountType = 'Developer'): Client
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', ['json' => [
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
            'password' => $password,
            'passwordConfirmation' => $passswordConfirmation,
            'accountType' => $accountType
        ]]);

        return $client;

    }


    private function createUser($email = 'test@test.com',
                                $name = 'Test',
                                $surname = 'Test',
                                $password = 'PasswordTest123',
                                $accountType = 'Developer'): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setEmailConfirmedAt(new \DateTime());

        $encoded = self::getContainer()->get('security.user_password_hasher')
            ->hashPassword($user, $password);
        $user->setPassword($encoded);

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

}
