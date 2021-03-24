<?php
namespace App\Controller\Api;

use App\Entity\PostsBlog;
use App\Form\Model\PostDto;
use App\Form\Type\PostFormType;
use App\Repository\PostsBlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\FileUploader;
use App\Service\ResponsePost;
use App\Service\PostManager;
class PostsController extends AbstractFOSRestController
{
    /**
     * @Rest\Post(path="/createPost")
     * @Rest\View(serializerGroups={"postsBlog"}, serializerEnableMaxDepthChecks=true)
     */
    public function createPost(
        EntityManagerInterface $em, Request $request ,
        FileUploader $fileUploader,ResponsePost $responsePost,
        PostManager $postManager
    ){
        //this controller resgister the new post
        $response = new JsonResponse();
        $post = new PostsBlog();
        //create the form for date new post
        $form =$this->createForm(PostFormType::class,$post);
        $j_data = json_decode($request->getContent(), true);
        $form->submit($j_data);
        if($form->isSubmitted() && $form->isValid()){
            //upload the image to db
            $filename = $fileUploader->uploadBase64File($j_data['image']);
            //create post with service postManager
            $post_new = $postManager->create($j_data,$filename,$em,$post);
            //generate response
            $postArray = $responsePost->returnData($post_new);
            $data = array(
                'status'=>'success',
                'post'=>$postArray
            );
        }else{
            $data = array(
                'status'=>'error',
                'post'=>$form
            );
        }
        $response->setData($data); 
        return $response;
    }
    /**
     * @Rest\Post(path="/updatePost")
     * @Rest\View(serializerGroups={"posts"}, serializerEnableMaxDepthChecks=true)
     */
    public function editPost(
        EntityManagerInterface $em, Request $request ,
        FileUploader $fileUploader , PostsBlogRepository $PostsBlogRepository,
        ResponsePost $responsePost,PostManager $postManager
    ){
        //this controller edit post by id
        $j_data = json_decode($request->getContent(), true);
        //Find post by id in the bd
        $post = $PostsBlogRepository->findOneBy(['id'=>$j_data['id']]);
        $filename =false;  
        //valid if image is empty
        if(!empty($j_data['image'])){
            $filename = $fileUploader->uploadBase64File($j_data['image']);
        }
        //edit post with service postManager
        $post_new = $postManager->edit($j_data,$filename,$em,$post);
        //generate response
        $postArray = $responsePost->returnData($post_new);
        $data = array(
            'status'=>'success',
            'post'=>$postArray
        );
        return $data;
    }
    /**
     * @Rest\Post(path="/viewPostbyId")
     * @Rest\View(serializerGroups={"posts"}, serializerEnableMaxDepthChecks=true)
     */
    public function viewPost(
        EntityManagerInterface $em, Request $request , 
        PostsBlogRepository $PostsBlogRepository,ResponsePost $responsePost
    ){
        //this controller view post by id
        $data_request = json_decode($request->getContent(), true);
        $id = $data_request['id'];
        if(empty($id) || !isset($id)){
            $data = array(
                'status'=>'error',
                'mensaje'=>'No se pudo traer datos'
            );
        }else{
            //find post by id in the bd
            $post = $PostsBlogRepository->findOneBy(['id'=>$id]);
            //generate response
            $postArray = $responsePost->returnData($post);
            $data = array(
                'status'=>'success',
                'post'=>$postArray
            );
        }
        return $data;
    }

    /**
     * @Rest\Post(path="/viewAll")
     * @Rest\View(serializerGroups={"posts"}, serializerEnableMaxDepthChecks=true)
     */
    public function viewAll(
        EntityManagerInterface $em, Request $request , 
        PostsBlogRepository $PostsBlogRepository,ResponsePost $responsePost
    ){
        //get all post of the bd
        $post = $PostsBlogRepository->findAll();
        if(empty($post)){
            $data = array(
                'status'=>'error',
                'post'=>'Datos vacios'
            );
        }else{
            //generate response
            $postsArray = $responsePost->returnRowsData($post);
            $data = array(
                'status'=>'success',
                'posts'=>$postsArray
            );
        }
        return $data;
    }
    /**
     * @Rest\Post(path="/viewPostbyUser")
     * @Rest\View(serializerGroups={"posts"}, serializerEnableMaxDepthChecks=true)
     */
    public function viewPostbyUser(
        EntityManagerInterface $em, Request $request, 
        PostsBlogRepository $PostsBlogRepository,ResponsePost $responsePost
    ){
        //this controller view post by id user
        $data_request = json_decode($request->getContent(), true);
        $id = $data_request['id'];
        if(empty($id) || !isset($id)){
            $data = array(
                'status'=>'error',
                'mensaje'=>'No se pudo traer datos'
            );
        }else{
            //find posts by id user of the bd
            $post = $PostsBlogRepository->findBy(['id_autor'=>$id]);
            $postArray=[];
            //generate response
            $postsArray = $responsePost->returnRowsData($post);
            $data = array(
                'status'=>'success',
                'posts'=>$postsArray
            );
        
        }
        return $data;
    }
    /**
     * @Rest\Post(path="/deletePostbyId")
     * @Rest\View(serializerGroups={"posts"}, serializerEnableMaxDepthChecks=true)
     */
    public function deletePost(
        EntityManagerInterface $em, Request $request, PostsBlogRepository $PostsBlogRepository
    ){
        //this controller delete post by id
        $data_request = json_decode($request->getContent(), true);
        $id = $data_request['id'];
        if(empty($id) || !isset($id)){
            $data = array(
                'status'=>'error',
                'mensaje'=>'No se pudo eliminar datos'
            );
        }else{
            $post = $PostsBlogRepository->findOneBy(['id'=>$id]);
            $em->remove($post);
            $em->flush();
            $data = array(
            'status'=>'success'
            );
        }
        return $data;
    }   
}