<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Multimedia;

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
    public function darLike(EntityManagerInterface $em)
    {
        $usuario = $this->security->getUser();

        if ($usuario) {
            $idMultimedia = $_GET["multimedia_id"];

            $multimedia_repositorio = $em->getRepository(Multimedia::class)->findOneBy(array("id" => $idMultimedia));
            $usuario_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("email" => $usuario->getUserIdentifier()));

            $multimedia_repositorio->addUsuario($usuario_repositorio);
            $usuario_repositorio->addGetLike($multimedia_repositorio);
            $em->persist($multimedia_repositorio);
            $em->persist($usuario_repositorio);
            
            $em->flush();
        }
    }
}
