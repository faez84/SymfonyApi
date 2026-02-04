<?php
namespace App\Tests\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Api extends WebTestCase
{
    protected ?KernelBrowser $client = null;
    protected EntityManagerInterface $em;

    private const TEST_USER_EMAIL = 'testuser@example.com';
    private const TEST_USER_PASSWORD = 'testpassword';
    private const TEST_USER_ROLE = 'ROLE_USER';
    private const TEST_USER_ADMIN_EMAIL = 'admin@example.com';
    private const TEST_USER_ADMIN_PASSWORD = 'adminpassword';
    private const TEST_ADMIN_ROLE = 'ROLE_ADMIN';

    public function setUp(): void
    {
        if (!$this->client) {
            $this->client = static::createClient();
        }
        $this->em = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->createUser();
        $this->createAdminUser();
        parent::setUp();
      
       
    }
    protected function tearDown(): void
    {
        $this->deleteAdminUser();
        $this->deleteUser();
        parent::tearDown();
    }
    
    public function getToken(): mixed
    {
        $this->client->request('POST', '/api/login_check', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'ACCEPT' => 'application/json',
        ], json_encode([
            'username' => self::TEST_USER_ADMIN_EMAIL,
            'password' => self::TEST_USER_ADMIN_PASSWORD,
        ]));

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        return $data['token'] ?? null;
    }

    public function createUser(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => self::TEST_USER_EMAIL]);
        if (!$user) {
            $user = new User();
            $user->setEmail(self::TEST_USER_EMAIL);
            $user->setPassword(password_hash(self::TEST_USER_PASSWORD, PASSWORD_BCRYPT));
            $user->setRoles([self::TEST_USER_ROLE]);
            $this->em->persist($user);
            $this->em->flush();
        }   
    }

    public function deleteUser(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => self::TEST_USER_EMAIL]);
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }   
    }

    public function createAdminUser(): void
    {
        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => self::TEST_USER_ADMIN_EMAIL]);
        if (!$admin) {
            $admin = new User();
            $admin->setEmail(self::TEST_USER_ADMIN_EMAIL);
            $admin->setPassword(password_hash(self::TEST_USER_ADMIN_PASSWORD, PASSWORD_BCRYPT));
            $admin->setRoles([self::TEST_ADMIN_ROLE]);
            $this->em->persist($admin);
            $this->em->flush();
        }   
    }

    public function deleteAdminUser(): void
    {
        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => self::TEST_USER_ADMIN_EMAIL]);
        if ($admin) {
            $this->em->remove($admin);
            $this->em->flush();
        }   
    }

        public function deleteUserWithEmail(string $email): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
        }   
    }
}