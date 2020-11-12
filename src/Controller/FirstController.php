<?php

namespace App\Controller;
use App\Entity\Usuario;
use App\service\NewMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    /** @var NewMessage $men */
    private $message;
    /** @var Usuario $us*/
    private  $us;

    /**
     * FirstController constructor.
     * @param $message
     * @param $us
     */
    public function __construct(NewMessage $message)
    {
        $this->message = $message;
        $this->us=new Usuario();
        $this->us->setNombre("Javier");
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
            'message' => $m,'nombre'=>$this->us->getNombre()
        ]);
    }
    /**
     * @Route("/downloadFile/", name="downloadFile")
     */
    public function downloadFile(){
        $archivo = new File('../composer.json');
        return $this->file($archivo,'Fichero Json');
    }
    /**
     * @Route("/sendMail", name="sendMail")
     */
    public function sendMail(){
        $send=$this->message->sendMail();
        return $this->redirectToRoute('first',['envio'=>$send]);
    }
    /**
     * @Route("/serializer/", name="serializer")
     */
    public function serializer(){
        $encoder = new JsonEncoder();
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format(\DateTime::ISO8601) : '';
        };
        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
            ],
        ];
        $normalizer = new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json=$serializer->serialize($this->us, 'json');
        return $this->render('first/serializer.html.twig',['serializer'=>$json]);

    }

}
