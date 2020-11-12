<?php

namespace App\Controller;
use App\Entity\Usuario;
use App\service\NewMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
        $this->us->setId(23280369);

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
            'message' => $m,'nombre'=>$this->us->getNombre(),'id'=>$this->us->getId(),
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
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
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
//      TODO:formato XML:
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $xml=$serializer->serialize($this->us,'xml');
//      TODO:deseralizer:
        $serializer2 = new Serializer([new GetSetMethodNormalizer(), new ArrayDenormalizer()], [new JsonEncoder(),new XmlEncoder()]
        );
        $repository = $serializer2->deserialize($xml, Usuario::class, 'xml');
        $nom=$repository->getNombre();$id=$repository->getId();
        return $this->render('first/serializer.html.twig',['serializer'=>$json,'xml'=>$xml,'deserializer'=>"\tnombre: ".$nom. "\n\nID: ".$id ]);
    }

}
