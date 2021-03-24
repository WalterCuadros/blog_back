<?php
namespace App\Controller\Api;

use App\Entity\Users;
use App\Form\Type\UserFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



class UsersController extends AbstractFOSRestController
{
    
    /**
     * @Rest\Post(path="/user")
     * @Rest\View(serializerGroups={"users"}, serializerEnableMaxDepthChecks=true)
     */
    public function login(Request $request,UsersRepository $usersRepository){ 
        //this controller consults the user in the database
        $response = new JsonResponse();
        $data_request = json_decode($request->getContent(), true);
        $email = $data_request['email'];
        $password = $data_request['password'];
         if((empty($email) || !isset($email)) || (empty($password) || !isset($password))){
            $data = array(
                'status'=>'error',
                'mensaje'=>'No se pudo traer datos'
            );
        }else{
            //find user by id in the bd
             $user = $usersRepository->findOneBy([
                'email' => $email
            ]);
                
            if(!empty($user)){
                //valid if match password
                if($user->getPassword() == $password){
                    $user_data = array(
                        'id'=>$user->getId(),
                        'nombres'=>$user->getNombres(),
                        'apellidos'=>$user->getApellidos(),
                        'email'=>$user->getEmail()
                    );
                    $data = array(
                        'code'=>200,
                        'status'=>'success',
                        'user'=>$user_data
                    );
                }else{
                    $data = array(
                        'code'=>500,
                        'status'=>'error',
                        'message'=>'Password equivocado'
                    );
                }
            }else{
                $data = array(
                    'code'=>500,
                    'status'=>'error',
                    'message'=>'No existe usuario'
                );
            }
        }
        $response->setData($data); 
        return $response;
    }


    /**
     * @Rest\Post(path="/registerUser")
     * @Rest\View(serializerGroups={"users"}, serializerEnableMaxDepthChecks=true)
     */
    public function createUser(
        EntityManagerInterface $em, Request $request
    ){
        //this controller create user in the bd
        $user = new Users();
        $form =$this->createForm(UserFormType::class,$user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($user);
            $em->flush();
            $data = array(
                'status'=>'success',
                'user'=>$user
            );
        }else{
            $data = array(
                'status'=>'error',
                'user'=>$form
            );
        }
        return $data;
    }
}