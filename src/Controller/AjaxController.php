<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Usuario;
use App\Entity\Multimedia;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;

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

            $idMultimedia = $request->query->get("multimedia_id");

            try {
                /* Relaciona al Usuario con la Multimedia seleccionada */
                $multimedia_repositorio = $em->getRepository(Multimedia::class)->findOneBy(array("id" => $idMultimedia));
                $usuario_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("email" => $usuario->getUserIdentifier()));
                $multimedia_repositorio->addUsuario($usuario_repositorio);
                $em->persist($multimedia_repositorio);
                $em->flush();

                /* Cuanta los Usuarios con LIKE a esta Multimedia y lo asigna a la columna Likes de Multimedia */
                $multimedia_repositorio->setLikes(count($multimedia_repositorio->getUsuarios()));
                $em->persist($multimedia_repositorio);
                $em->flush();

                $json = json_encode(count($multimedia_repositorio->getUsuarios()));
                header('Content-Type: application/json');
                echo $json;
                
            } catch (Exception $e) {
            }


            return new Response(null);
        }
    }


    /**
     * @Route("quitar-like", name="app_quitarLike")
     */
    public function quitarLike(Request $request, EntityManagerInterface $em)
    {
        $usuario = $this->security->getUser();

        if ($usuario) {

            $idMultimedia = $request->query->get("multimedia_id");

            try {
                /* Relaciona al Usuario con la Multimedia seleccionada */
                $multimedia_repositorio = $em->getRepository(Multimedia::class)->findOneBy(array("id" => $idMultimedia));
                $usuario_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("email" => $usuario->getUserIdentifier()));
                $multimedia_repositorio->removeUsuario($usuario_repositorio);
                $em->persist($multimedia_repositorio);
                $em->flush();

                /* Cuanta los Usuarios con LIKE a esta Multimedia y lo asigna a la columna Likes de Multimedia */
                $multimedia_repositorio->setLikes(count($multimedia_repositorio->getUsuarios()));
                $em->persist($multimedia_repositorio);
                $em->flush();

                $json = json_encode(count($multimedia_repositorio->getUsuarios()));
                header('Content-Type: application/json');
                echo $json;

            } catch (Exception $e) {
            }


            return new Response(null);
        }
    }

    /**
     * @Route("eliminar-publicacion", name="app_eliminarPublicacion")
     */
    public function eliminarPublicacion(Request $request, EntityManagerInterface $em)
    {
        $usuario = $this->security->getUser();

        if ($usuario) {

            $idMultimedia = $request->query->get("multimedia_id");

            try {
                /* Relaciona al Usuario con la Multimedia seleccionada */
                $multimedia_repositorio = $em->getRepository(Multimedia::class)->findOneBy(array("id" => $idMultimedia));
                $usuario_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("email" => $usuario->getUserIdentifier()));

                if($multimedia_repositorio->getUsuario()->getId() == $usuario_repositorio->getId()) {
                    $em->remove($multimedia_repositorio);
                    $em->flush();
                    
                    //$json = json_encode(count($multimedia_repositorio->getUsuarios()));
                    header('Content-Type: application/json');
                    //echo $json;
                }
               

            } catch (Exception $e) {
            }


            return new Response(null);
        }
    }
}
