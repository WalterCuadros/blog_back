<?php

namespace App\Service\User;

use App\Entity\User;
use App\Model\Exception\User\UserNotFound;
use App\Repository\UserRepository;
use Ramsey\Uuid\Nonstandard\Uuid;

class GetUser
{
    private $UserRepository;

    public function __construct(UserRepository $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }

    public function __invoke(string $id): User
    {
        $User = $this->UserRepository->find(Uuid::fromString($id)); 
        if (!$User) {
            UserNotFound::throwException();
        }
        return $User;
    }
}