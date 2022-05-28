<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Multimedia;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("dar-like", name="app_darLike")
     */
    public function darLike(Request $request, EntityManagerInterface $em)
    {
        $usuario = $this->security->getUser();

        if ($usuario) {

            //Recoger GET
            $idMultimedia=$request->query->get("multimedia_id");
            //var_dump("GET:".$var);
            //$idMultimedia = $_GET["multimedia_id"];

            /* Relaciona al Usuario con la Multimedia seleccionada */
            $multimedia_repositorio = $em->getRepository(Multimedia::class)->findOneBy(array("id" => $idMultimedia));
            $usuario_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("email" => $usuario->getUserIdentifier()));
            $multimedia_repositorio->addUsuario($usuario_repositorio);

            /* Suma +1 a los Likes de la Multimedia seleccionada */
            $likesMultimedia = $multimedia_repositorio->getLikes();
            $multimedia_repositorio->setLikes($likesMultimedia+1);


            $em->persist($multimedia_repositorio);
            $em->flush();

            return new Response(null);
        }
    }
}
