<?php

namespace App\Service;

use League\Flysystem\FilesystemOperator;

class FileUploader 
{
    private $storage;
    
    // The variable name $defaultStorage matters: it needs to be the camelized version
    // of the name of your storage. 
    //This service is in charge of uploading an image
    public function __construct(FilesystemOperator $defaultStorage)
    {
        $this->storage = $defaultStorage;
    }
    public function uploadBase64File(string $base64File): string 
    {
        $extension = explode('/',mime_content_type($base64File))[1];
        $data = explode(',', $base64File);
        $filename = sprintf('%s.%s',uniqid('post_',true),$extension);
        $this->storage->write($filename,base64_decode($data[1]));
        return $filename;
    }
   
}