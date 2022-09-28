<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\ConfirmationToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityTest extends ApiTestCase
{
    use ReloadDatabaseTrait;


    public function testUserRegistration(): void
    {

        $this->createRegistrationRequest();

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@type' => 'User',
            'email' => 'test_2@test.com',
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

        $response = $client->request('POST', '/api/resend_email_verification_link', [
            'json' => [
                'email' => 'test_not_confirmed@test.com'
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

    public function testTokenPayloadAttributesAfterLogin(): void
    {
        $client = static::createClient();
        $encoder = $client->getContainer()->get(JWTEncoderInterface::class);
        $token = $this->loginRequest()->toArray()['token'];

        $payload = $encoder->decode($token);

        $this->assertArrayHasKey('name', $payload);
        $this->assertArrayHasKey('surname', $payload);
        $this->assertArrayHasKey('fullname', $payload);
        $this->assertArrayHasKey('username', $payload);
        $this->assertArrayHasKey('accountType', $payload);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
        $this->assertArrayHasKey('roles', $payload);

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
        $encoder = $client->getContainer()->get(JWTEncoderInterface::class);
        $response = $this->loginRequest()->toArray();

        $token = $response['refresh_token'];

        $response = $client->request('POST', '/api/refresh_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'refresh_token' => $token
            ]
        ]);

        $json = $response->toArray();

        $payload = $encoder->decode($json['token']);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('refresh_token', $json);
        $this->assertArrayHasKey('token', $json);
        $this->assertArrayHasKey('name', $payload);
        $this->assertArrayHasKey('surname', $payload);
        $this->assertArrayHasKey('fullname', $payload);
        $this->assertArrayHasKey('username', $payload);
        $this->assertArrayHasKey('accountType', $payload);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
        $this->assertArrayHasKey('roles', $payload);

    }


    public function testSendUserPasswordReset(): void
    {
        $client = self::createClient();

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
        $token = $this->createToken();

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
        $token = $this->createToken();

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

    private function createRegistrationRequest($email = 'test_2@test.com',
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

    public function testChangePasswordValidResponse(): void
    {
        $token = $this->loginRequest()->toArray()['token'];
        $client = static::createClient();
        $em = $client->getContainer()->get(EntityManagerInterface::class);
        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $response = $client->request('POST', '/api/change_password', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'oldPassword' => 'test123',
                'newPassword' => 'testPassword123'
            ]
        ])->toArray();


        $user = $em->getRepository(User::class)->findOneBy(['email' => 'test@test.com']);


        $this->assertTrue($passwordHasher->isPasswordValid($user, 'testPassword123'));
        $this->assertResponseIsSuccessful();
        $this->assertArraySubset(['description' => 'Password changed successfully.'], $response);

    }

    public function testChangePasswordInValidOldPassword(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = static::createClient()->request('POST', '/api/change_password', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'oldPassword' => 'test',
                'newPassword' => 'testPassword123'
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['hydra:description' => 'Invalid old password.']);

    }

    public function testChangePasswordInValidNewPassword(): void
    {
        $token = $this->loginRequest()->toArray()['token'];

        $response = static::createClient()->request('POST', '/api/change_password', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'oldPassword' => 'test123',
                'newPassword' => 'test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['hydra:description' => 'Password have to be minimum 8 characters and contains at least one letter and number.']);

    }

    private function createToken(): ConfirmationToken
    {

        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->find(1);

        $token = new ConfirmationToken();
        $token->setToken(Uuid::uuid4());
        $token->setExpiredAt(new \DateTime('+ 7 days'));
        $token->setType(ConfirmationToken::RESET_PASSWORD_EXECUTE_TYPE);
        $token->setUser($user);

        $em->persist($token);
        $em->flush();

        return $token;
    }


    private function loginRequest(string $password = 'test123', string $email = 'test@test.com'): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        $client = static::createClient();

        return $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password
            ]
        ]);

    }


}
