<?php

namespace App\Controller;

use App\Entity\Multimedia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;
use App\Form\PublicarType;
use Symfony\Component\Security\Core\Security;
use App\Form\SinginType;
use App\Service\SugerenciasService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PerfilController extends AbstractController
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
     * @Route("/perfil/{idPerfil}", name="app_perfil")
     */
    public function perfil(Request $request, EntityManagerInterface $em, $idPerfil): Response
    {
        /** Perfil visitado **/
        $perfil_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("id" => $idPerfil));
        $multimedia_perfil_repositorio = $perfil_repositorio->getMultimedia();
        $imagen_perfil = base64_encode(stream_get_contents($perfil_repositorio->getImagen(), -1, -1));

        $portadasCodigicadas = array();
        foreach($multimedia_perfil_repositorio as $multimedia_perfil) {
            array_push($portadasCodigicadas, base64_encode(stream_get_contents($multimedia_perfil->getPortada(), -1, -1)));
        }
        //array_push($portadasAuxiliar, $multimedia_perfil->setPortada($portadaCodigicada));

        /** Fin apartado perfil visitado **/

        /** En caso de tener sesión iniciada **/

        $usuario = $this->security->getUser();

        if ($usuario) {
            $rol = $usuario->getRoles();
            $id = $usuario->getId();
            $apodo = $usuario->getApodo();
            $likes = $usuario->getGetLikes();

  

            $imagen = base64_encode(stream_get_contents($usuario->getImagen(), -1, -1));
            
            /** Esto es porque en caso de que el usuario visite su propio perfil, se chafa el objeto y vacía el campo imagen y no se ve img de perfil en header. Con esto se le vuelve dar el valor. **/
            if($usuario->getId() == $perfil_repositorio->getId()) {
                $imagen = $imagen_perfil;
            }

            $usuario_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("email" => $usuario->getUserIdentifier()));
            $multimedia = new Multimedia();
            $form = $this->createForm(PublicarType::class, $multimedia);
            $form->handleRequest($request);

            $formato_portada_valido = true;
            $formato_archivo_valido = true;

            if ($form->isSubmitted() && $form->isValid()) {
                $portada = $form->get("portada")->getData();
                $archivo = $form->get("archivo")->getData();

                if ($archivo && $portada) {

                    $formato_portada = $portada->guessExtension();
                    $formato_archivo = $archivo->guessExtension();

                    $formatos = array("jpg", "mp3", "mp4");

                    if ($formato_portada == "jpg") {
                        if (in_array($formato_archivo, $formatos)) {

                            $multimedia->setPortada(file_get_contents($portada));

                            $multimedia->setArchivo(file_get_contents($archivo));
                            $multimedia->setFormato($archivo->guessExtension());

                            $multimedia->setUsuario($usuario_repositorio);

                            try {
                                $em->persist($multimedia);
                                $em->flush();
                            } catch (\Exception $e) {
                                return new Response("Esto no va, no no no. Posiblemente haya que modificar el tamaño de paquete en MySQL");
                            }
                            return $this->redirectToRoute('app_inicio');
                        } else {
                            $formato_archivo_valido = false; /* En caso de que el archivo no tenga formato .jpg, .mp3, .mp4 */
                        }
                    } else {
                        $formato_portada_valido = false; /* En caso de que la portada no tenga formato .jpg */
                    }
                    if (in_array('ROLE_RESIDENTE', $rol)) {
                        return $this->render('perfil.html.twig', [
                            'rol' => $rol[0],
                            'id' => $id,
                            'apodo' => $apodo,
                            'imagen' => $imagen,
                            'likes' => $likes,
                            'form' => $form->createView(),
                            'formato_portada_valido' => $formato_portada_valido,
                            'formato_archivo_valido' => $formato_archivo_valido,
                            'perfil_repositorio' => $perfil_repositorio,
                            'imagen_perfil' => $imagen_perfil,
                            'multimedia_perfil' => $multimedia_perfil_repositorio,
                            'portadas_multimedia_perfil' => $portadasCodigicadas
                        ]);
                    }
                }
            }
            if (in_array('ROLE_RESIDENTE', $rol)) {
                return $this->render('perfil.html.twig', [
                    'rol' => $rol[0],
                    'id' => $id,
                    'apodo' => $apodo,
                    'imagen' => $imagen,
                    'likes' => $likes,
                    'form' => $form->createView(),
                    'formato_portada_valido' => $formato_portada_valido,
                    'formato_archivo_valido' => $formato_archivo_valido,
                    'perfil_repositorio' => $perfil_repositorio,
                    'imagen_perfil' => $imagen_perfil,
                    'multimedia_perfil' => $multimedia_perfil_repositorio,
                    'portadas_multimedia_perfil' => $portadasCodigicadas
                ]);
            }
        } else {
            return $this->render('perfil.html.twig', [
                    'rol' => "",
                    'id' => "",
                    'apodo' => "",
                    'imagen' => $imagen_perfil,
                    'likes' => "",
                    'form' => "",
                    'formato_portada_valido' => "",
                    'formato_archivo_valido' => "",
                    'perfil_repositorio' => $perfil_repositorio,
                    'imagen_perfil' => $imagen_perfil,
                    'multimedia_perfil' => $multimedia_perfil_repositorio,
                    'portadas_multimedia_perfil' => $portadasCodigicadas
                ]);
        }
    }
}
