<?php

namespace App\Controller;
use App\service\NewMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    /**
     * @Route("/", name="first")
     */
    public function index(NewMessage $message): Response
    {
        $t=$this->json(['username' => 'jane.doe','apellidos'=>'orti']);
        $this->addFlash('mensaje',$t);
        $m=$message->getHappyMessage();
        return $this->render('first/index.html.twig', [
            'message' => $m,
        ]);

    }
    /**
     * @Route("downloadFile/", name="downloadFile")
     */
    public function downloadFile(){
        $archivo = new File('../composer.json');
        return $this->file($archivo,'Fichero Json');

    }
    /**
     * @Route("sendMail/", name="sendMail")
     */
    public function sendMail(NewMessage $mail){
        $send=$mail->sendMail();
        return $this->redirectToRoute('first',['envio'=>$send]);

    }
}
