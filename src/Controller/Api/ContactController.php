<?php
namespace App\Controller\Api;

use App\Entity\Contact;
use App\Form\Type\ContactFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



class ContactController extends AbstractFOSRestController
{
    /**
     * @Rest\Post(path="/registerContact")
     * @Rest\View(serializerGroups={"contact"}, serializerEnableMaxDepthChecks=true)
     */
    public function createContact(
        EntityManagerInterface $em, Request $request
    ){
        //this controller resgister the contact
        $contact = new Contact();
        $data = json_decode($request->getContent(), true);
            $contact->setNombres($data['nombres']);
            $contact->setEmail($data['email']);
            $contact->setComentario($data['comentario']);
            $em->persist($contact);
            $em->flush();
            $data = array(
                'status'=>'success',
                'contact'=>$contact
            );
        
        return $data;
    }

    
}