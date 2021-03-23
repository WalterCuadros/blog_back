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
use League\Flysystem\FilesystemOperator;


class PostsController extends AbstractFOSRestController
{
    
 
    /**
     * @Rest\Post(path="/createPost")
     * @Rest\View(serializerGroups={"postsBlog"}, serializerEnableMaxDepthChecks=true)
     */
    public function createPost(
        EntityManagerInterface $em, Request $request ,FilesystemOperator $defaultStorage
    ){
        $response = new JsonResponse();
        $post = new PostsBlog();
        $form =$this->createForm(PostFormType::class,$post);
        $j_data = json_decode($request->getContent(), true);
        $form->submit($j_data);
        if($form->isSubmitted() && $form->isValid()){
            $post->setTitle($j_data['title']);
            $post->setContent($j_data['content']);
            $extension = explode('/',mime_content_type($j_data['image']))[1];
            $data = explode(',', $j_data['image']);
            $filename = sprintf('%s.%s',uniqid('post_',true),$extension);
            $defaultStorage->write($filename,base64_decode($data[1]));
            $post->setImage($filename);
            $post->setDateCreated(\DateTime::createFromFormat('Y-m-d', date("Y-m-d")));
            $post->setAutor($j_data['autor']);
            $post->setIdAutor($j_data['id_autor']); 
            $em->persist($post);
            $em->flush();
            $postArray[]=[
                'id'=>$post->getId(),
                'title'=>$post->getTitle(),
                'content'=>$post->getContent(),
                'autor'=>$post->getAutor(),
                'date_created'=>date_format($post->getDateCreated(),"Y/m/d"),
                'id_autotor'=>$post->getIdAutor()
            ];
            
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
        EntityManagerInterface $em, Request $request ,FilesystemOperator $defaultStorage , PostsBlogRepository $PostsBlogRepository
    ){
       
        $post = new PostsBlog();
        $form =$this->createForm(PostFormType::class,$post);
        $j_data = json_decode($request->getContent(), true);
       /*  $form->submit($j_data); */
        
            $post = $PostsBlogRepository->findOneBy(['id'=>$j_data['id']]);
            $post->setTitle($j_data['title']);
            $post->setContent($j_data['content']);
            if(!empty($j_data['image'])){
                $extension = explode('/',mime_content_type($j_data['image']))[1];
                $data = explode(',', $j_data['image']);
                $filename = sprintf('%s.%s',uniqid('post_',true),$extension);
                $defaultStorage->write($filename,base64_decode($data[1]));
                $post->setImage($filename);
            }
            $em->persist($post);
            $em->flush();
            $postArray[]=[
                'id'=>$post->getId(),
                'title'=>$post->getTitle(),
                'content'=>$post->getContent(),
                'autor'=>$post->getAutor(),
                'image'=>$post->getImage(),
                'date_created'=>date_format($post->getDateCreated(),"Y/m/d"),
                'id_autor'=>$post->getIdAutor()
            ];
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
        EntityManagerInterface $em, Request $request ,FilesystemOperator $defaultStorage , PostsBlogRepository $PostsBlogRepository
    ){
        
            $id  = $request->get('id');
            $response = new JsonResponse();
            $data_request = json_decode($request->getContent(), true);
            $id = $data_request['id'];
            if(empty($id) || !isset($id)){
                $data = array(
                    'status'=>'error',
                    'mensaje'=>'No se pudo traer datos'
                );
            }else{
                $post = $PostsBlogRepository->findOneBy(['id'=>$id]);
                $postArray[]=[
                    'id'=>$post->getId(),
                    'title'=>$post->getTitle(),
                    'content'=>$post->getContent(),
                    'autor'=>$post->getAutor(),
                    'image'=>$post->getImage(),
                    'date_created'=>date_format($post->getDateCreated(),"Y/m/d"),
                    'id_autor'=>$post->getIdAutor()
                ];
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
        EntityManagerInterface $em, Request $request ,FilesystemOperator $defaultStorage , PostsBlogRepository $PostsBlogRepository
    ){
        
            
                $post = $PostsBlogRepository->findAll();
                if(empty($post)){
                    $data = array(
                        'status'=>'error',
                        'post'=>'Datos vacios'
                    );
                }else{
                    $postArray=[];
                    foreach ($post as $item) {
                        $postsArray[]=[
                            'id'=>$item->getId(),
                            'title'=>$item->getTitle(),
                            'date'=>date_format($item->getDateCreated(),"Y/m/d"),
                            'autor'=>$item->getAutor(),
                            'image'=>$item->getImage()
                        ];
                    }
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
        EntityManagerInterface $em, Request $request ,FilesystemOperator $defaultStorage , PostsBlogRepository $PostsBlogRepository
    ){

            $id  = $request->get('id_autor');
            $data_request = json_decode($request->getContent(), true);
            $id = $data_request['id'];
            if(empty($id) || !isset($id)){
                $data = array(
                    'status'=>'error',
                    'mensaje'=>'No se pudo traer datos'
                );
            }else{
                $post = $PostsBlogRepository->findBy(['id_autor'=>$id]);
                $postArray=[];
                foreach ($post as $item) {
                    $postsArray[]=[
                        'id'=>$item->getId(),
                        'title'=>$item->getTitle(),
                        'date'=>date_format($item->getDateCreated(),"Y/m/d")
                    ];
                }
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
        EntityManagerInterface $em, Request $request ,FilesystemOperator $defaultStorage , PostsBlogRepository $PostsBlogRepository
    ){
        
           
            
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