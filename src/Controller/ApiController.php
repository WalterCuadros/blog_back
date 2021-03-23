<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    
    /**
     * @Route("/api/login", name="login")
     */
    public function login(Request $request, LoggerInterface $logger){
        
        $logger->info('I love Tony Vairelles\' hairdresser.');
        $response = new JsonResponse();
        $data = array(
            'code' => 200,
            'status' => 'success',
            'message' =>"mensaje"
        );
        $response->setData($data);
        return $response;

    }
    /**
     * @Route("/api/createUser", name="createUser")
     */
    public function createUser(Request $request,EntityManagerInterface $em){
        
        $user = new Users();
        $response = new JsonResponse();
        $nombre = $request->get('nombre',null);
        $apellidos = $request->get('apellidos',null);
        $email = $request->get('email',null);
        $password = $request->get('password',null);
        if(empty($nombre) || empty($apellidos) || empty($email) || empty($password)){
            $data = array(
                'code' => '500',
                'status' => 'error',
                'message' =>'No se pudo guardar el registro faltan datos'
            );
        }else{
            $user->setNombres($nombre);
            $user->setApellidos($apellidos);
            $user->setEmail($email);
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $res = array(
                'id'=>$user->getId(),
                'nombres'=>$user->getNombres()
            );
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' =>$res
            );
        }

        $response->setData($data);
        return $response;

    }
    /**
     * @Route("/listUser", name="listUser")
     */
    public function listUser(Request $request, UsersRepository $usersRepository){
        $users = $usersRepository->findAll();
        $usersArray = [];
        foreach ($users as $user) {
            $usersArray[]=[
                'id'=>$user->getId(),
                'nombre'=>$user->getNombres()
            ];
        }
        $response = new JsonResponse();
        $data = array(
            'code' => 200,
            'status' => 'success',
            'message' =>$usersArray
        );
        $response->setData($data);
        return $response;
    }
}