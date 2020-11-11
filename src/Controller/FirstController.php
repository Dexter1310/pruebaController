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
    private $message;

    /**
     * FirstController constructor.
     * @param $message
     */
    public function __construct(NewMessage $message)
    {
        $this->message = $message;
    }

    /**
     * @Route("/", name="first")
     */
    public function index(): Response
    {
        $t=$this->json(['username' => 'jane.doe','apellidos'=>'orti']);
        $this->addFlash('success',$t);
        $m=$this->message->getHappyMessage();
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
    public function sendMail(){
        $send=$this->message->sendMail();
        return $this->redirectToRoute('first',['envio'=>$send]);

    }
}
