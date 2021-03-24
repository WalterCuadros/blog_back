<?php

namespace App\Service;
use App\Entity\PostsBlog;
class ResponsePost{
    //this service register new contacts in the database
    public function __construct()
    {
        
    }
    public function returnData(PostsBlog $post)
    {
        $postArray[]=[
            'id'=>$post->getId(),
            'title'=>$post->getTitle(),
            'content'=>$post->getContent(),
            'image'=>$post->getImage(),
            'autor'=>$post->getAutor(),
            'date_created'=>date_format($post->getDateCreated(),"Y/m/d"),
            'id_autor'=>$post->getIdAutor()
        ];
        
        return $postArray;
    }
    public function returnRowsData($post){
        $postsArray=[];
        foreach ($post as $item) {
            $postsArray[]=[
                'id'=>$item->getId(),
                'title'=>$item->getTitle(),
                'date'=>date_format($item->getDateCreated(),"Y/m/d"),
                'autor'=>$item->getAutor(),
                'image'=>$item->getImage()
            ];
        }
        return $postsArray;
    }
}