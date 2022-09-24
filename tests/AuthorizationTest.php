<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\ConfirmationToken;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthorizationTest extends ApiTestCase
{
    use ReloadDatabaseTrait;


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

    public function testIfConfirmationEmailWasResend()
    {
        $client = static::createClient();
        $user = $this->createUser();
        $user->setEmailConfirmedAt(null);

        $response = $client->request('POST', '/api/resend_email_verification_link', [
            'json' => [
                'email' => 'test@test.com'
            ]
        ]);

        $this->assertJsonContains([
            'description' => 'Email with configured link will be send to given user.',
        ]);
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Outsource me - registration confirmation');

    }

    public function testIfConfirmationEmailWasResendWhenUserEmailIsConfirmed()
    {
        $client = static::createClient();
        $user = $this->createUser();

        $response = $client->request('POST', '/api/resend_email_verification_link', [
            'json' => [
                'email' => 'test@test.com'
            ]
        ]);

        $this->assertJsonContains([
            'hydra:description' => 'Email for this user is confirmed.',
        ]);
        $this->assertEmailCount(0);

    }

    public function testIfConfirmationEmailWasResendWhenInvalidEmailPassed()
    {
        $client = static::createClient();
        $user = $this->createUser();

        $response = $client->request('POST', '/api/resend_email_verification_link', [
            'json' => [
                'email' => 'test@t.com'
            ]
        ]);

        $this->assertJsonContains([
            'hydra:description' => 'Invalid email address.',
        ]);
        $this->assertEmailCount(0);

    }


    public function testApiSuccessfullLogin(): void
    {
        $response = $this->loginRequest()->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('refresh_token', $response);
        $this->assertArrayHasKey('token', $response);
    }

    public function testLoginBadCredentialsValidation(): void
    {
        $response = $this->loginRequest('test123', 'test');

        $this->assertJsonContains([
            'code' => 401,
            'message' => 'Invalid credentials.'
        ]);
        $this->assertResponseStatusCodeSame(401);
    }


    public function testUserTokenRefresh(): void
    {
        $client = static::createClient();
        $response = $this->loginRequest()->toArray();

        $token = $response['refresh_token'];

        $response = $client->request('POST', '/api/refresh_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'refresh_token' => $token
            ]
        ]);

        $json = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('refresh_token', $json);
        $this->assertArrayHasKey('token', $json);
    }

    public function testSendUserPasswordReset(): void
    {
        $client = self::createClient();
        $user = $this->createUser();

        $response = $client->request('POST', '/api/reset_password_send_link', [
            'json' => [
                'email' => 'test@test.com'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'description' => 'Email with configured link will be send to given user.'
        ]);

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailHasHeader($email, 'subject', 'Outsource me - password reset');


    }

    public function testSendUserPasswordResetUserNotExists(): void
    {

        $client = self::createClient();
        $response = $client->request('POST', '/api/reset_password_send_link', [
            'json' => [
                'email' => 'test@est.com'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);

    }

    public function testUserPasswordResetExecute(): void
    {
        $client = self::createClient();
        $user = $this->createUser();
        $token = $this->createToken($user);

        $response = $client->request('POST', '/api/reset_password_execute', [
            'json' => [
                'password' => 'TestPassword123',
                'confirmation_token' => $token->getToken()
            ]
        ]);


        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['description' => 'Password reset successfully.'
        ]);


    }

    public function testUserPasswordResetExecuteValidPassword()
    {

        $client = self::createClient();
        $user = $this->createUser();
        $token = $this->createToken($user);

        $response = $client->request('POST', '/api/reset_password_execute', [
            'json' => [
                'password' => 'test123',
                'confirmation_token' => $token->getToken()
            ]
        ]);

        $this->assertJsonContains([
            'hydra:description' => 'Password have to be minimum 8 characters and contains at least one letter and number.'
        ]);
        $this->assertResponseStatusCodeSame(400);

    }

    public function testUserPasswordResetExecuteValidToken()
    {

        $client = self::createClient();
        $user = $this->createUser();

        $response = $client->request('POST', '/api/reset_password_execute', [
            'json' => [
                'password' => 'PasswordTest123',
                'confirmation_token' => '123'
            ]
        ]);


        $this->assertJsonContains([
            'hydra:description' => 'Invalid or expired confirmation token.'
        ]);
        $this->assertResponseStatusCodeSame(401);

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
        $token->setType(ConfirmationToken::RESET_PASSWORD_EXECUTE_TYPE);
        $token->setUser($user);

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->persist($token);
        $em->flush();

        return $token;
    }

    private function loginRequest(string $password = 'test123', string $email = 'test@test.com'): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $client = static::createClient();
        $user = $this->createUser();

        return $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password
            ]
        ]);

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
