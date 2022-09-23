<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\ConfirmationToken;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Ramsey\Uuid\Uuid;

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


    public function testApiSuccessfullLogin(): void
    {

        $container = self::getContainer();

        $user = new User();
        $user->setName('test');
        $user->setEmail('test@example.com');
        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, '$3CR3T')
        );

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        $response = static::createClient()->request('POST', '/api/login', [
            'json' => [
                'email' => "test@example.com",
                'password' => '$3CR3T'
            ]
        ]);

        $json = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('refresh_token', $json);
        $this->assertArrayHasKey('token', $json);
    }

    public function testLoginBadCredentialsValidation(): void
    {

        $user = $this->createUser();

        $response = static::createClient()->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test',
                'password' => 'test'
            ]
        ]);

        $this->assertJsonContains([
            'code' => 401,
            'message' => 'Invalid credentials.'
        ]);
        $this->assertResponseStatusCodeSame(401);
    }


    public function testUserTokenRefresh(): void
    {
        $user = $this->createUser();

        $response = static::createClient()->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'test',
                'password' => 'test'
            ]
        ]);

        $token = $response->toArray()['refresh_token'];

        $response = static::createClient()->request('POST', '/api/refresh_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'refresh_token' => $token
            ]
        ]);

        $json = $response->toArray();

        $this->assertResponseIsSuccessful(200);
        $this->assertArrayHasKey('refresh_token', $json);
        $this->assertArrayHasKey('token', $json);
    }

    public function testSendUserPasswordReset(): void
    {
        $user = $this->createUser();

        $response = self::createClient()->request('POST', '/api/password_reset_send_link', [
            'json' => [
                'email' => 'test@test.com'
            ]
        ]);

        $this->assertResponseIsSuccessful(200);
        $this->assertJsonContains([
            'description' => 'Email with configured link will be send to given user.'
        ]);

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Outsource me - password reset');


    }

    public function testSendUserPasswordResetUserNotExists(): void
    {

        $response = self::createClient()->request('POST', '/api/password_reset_send_link', [
            'json' => [
                'email' => 'test@test.com'
            ]
        ]);

        $this->assertResponseIsSuccessful(401);

    }

    public function testUserPasswordResetExecute(): void
    {
        $user = $this->createUser();
        $token = $this->createToken($user);

        $response = self::createClient()->request('POST', '/api/password_reset_execute', [
            'json' => [
                'new_password' => 'TestPassword123',
                'confirmation_token' => $token->getToken()
            ]
        ]);

        $this->assertResponseIsSuccessful(200);
        $this->assertJsonContains([
            'description' => 'Password updated.'
        ]);

    }

    public function testUserPasswordResetExecuteValidPassword()
    {

        $user = $this->createUser();
        $token = $this->createToken($user);

        $response = self::createClient()->request('POST', '/api/password_reset_execute', [
            'json' => [
                'new_password' => 'test123',
                'confirmation_token' => $token->getToken()
            ]
        ]);

        $this->assertResponseIsSuccessful(400);
        $this->assertJsonContains([
            'description' => 'password: Password have to be minimum 8 characters and contains at least one letter and number.'
        ]);

    }

    public function testUserPasswordResetExecuteValidToken()
    {

        $user = $this->createUser();

        $response = self::createClient()->request('POST', '/api/password_reset_execute', [
            'json' => [
                'new_password' => 'PasswordTest123',
                'confirmation_token' => '123'
            ]
        ]);

        $this->assertResponseIsSuccessful(401);
        $this->assertJsonContains([
            'description' => 'Invalid or expired confirmation token.'
        ]);

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

    private function createToken(User $user): ConfirmationToken
    {
        $token = new ConfirmationToken();
        $token->setToken(Uuid::uuid4());
        $token->setExpiredAt(new \DateTime('+ 7 days'));
        $token->setType('password_reset');
        $token->setUser($user);

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($token);
        $em->flush();

        return $token;
    }

    private function createUser($email = 'test@test.com',
                                $name = 'Test',
                                $surname = 'Test',
                                $password = 'test123',
                                $accountType = 'Developer'): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setSurname($surname);
        $user->setEmailConfirmedAt(new \DateTime());

        $encoded = static::getContainer()->get('security.user_password_hasher')
            ->hashPassword($user, $password);
        $user->setPassword($encoded);

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

}
