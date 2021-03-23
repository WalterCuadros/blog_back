<?php 
namespace App\Service;

use App\Entity\User;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;

class UManager{

    private $em;
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UsersRepository $userRepository){
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function find(int $id ): ?User
    {
        return $this->userRepository->find($id);
    }
    public function create(): User
    {
        $user = new User();
        return $user;
    } 
    public function save(User $user): User
    {
        $this->em->persist();
        $this->em->flush();
        return $user;
    }
    public function reload(User $user):User
    {
        $this->em->refresh($user);
        return $user;
    }
}
