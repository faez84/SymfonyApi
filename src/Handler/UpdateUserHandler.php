<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdateUserHandler implements UpdateUserHandlerInterface
{
    public function __construct(
        private UserRepository $users,
        private EntityManagerInterface $em
    ) {
    }

    public function handle(int $id, User $input): User
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
        }
        $user->setEmail($input->getEmail());
        $this->em->flush();

        return $user;
    }
}
