<?php

namespace App\Service;
use App\Entity\PostsBlog;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostsBlogRepository;
class PostManager{
    //this service manage the creation and edition of posts in the database
    public function __construct()
    {
        
    }
    public function create($j_data,$filename,EntityManagerInterface $em,PostsBlog $post){
        $post->setTitle($j_data['title']);
        $post->setContent($j_data['content']);
        $post->setImage($filename);
        $post->setDateCreated(\DateTime::createFromFormat('Y-m-d', date("Y-m-d")));
        $post->setAutor($j_data['autor']);
        $post->setIdAutor($j_data['id_autor']); 
        $em->persist($post);
        $em->flush();
        return $post;
    }
    public function edit($j_data,$filename,EntityManagerInterface $em,
    PostsBlog $post){
        
        $post->setTitle($j_data['title']);
        $post->setContent($j_data['content']);
        $em->persist($post);
        $em->flush();
        if($filename){
            $post->setImage($filename);
        }
        return $post;
    }
    
    
}