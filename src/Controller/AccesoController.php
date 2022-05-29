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

class AccesoController extends AbstractController
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
     * @Route("/", name="app_inicio")
     */
    public function inicio(Request $request, EntityManagerInterface $em): Response
    {

        $usuario = $this->security->getUser();

        if ($usuario) {
            $rol = $usuario->getRoles();
            $apodo = $usuario->getApodo();
            $likes = $usuario->getGetLikes();

            if ($usuario->getImagen()) {
                $imagen = base64_encode(stream_get_contents($usuario->getImagen(), -1, -1));
            } else {
                $imagen = 0;
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

                    if($formato_portada == "jpg") {
                        if(in_array($formato_archivo, $formatos)) {

                            $multimedia->setPortada(file_get_contents($portada));
                                        
                            $multimedia->setArchivo(file_get_contents($archivo));
                            $multimedia->setFormato($archivo->guessExtension());
        
                            $multimedia->setUsuario($usuario_repositorio);

                            try {
                                $em->persist($multimedia);
                                $em->flush();
                            } catch (\Exception $e) {
                                return new Response("Esto no va, no no no.");
                            }
                            return $this->redirectToRoute('app_inicio');
                        } else {
                            $formato_archivo_valido = false; /* En caso de que el archivo no tenga formato .jpg, .mp3, .mp4 */
                        }
                    } else {
                        $formato_portada_valido = false; /* En caso de que la portada no tenga formato .jpg */
                    }
                    if (in_array('ROLE_RESIDENTE', $rol)) {
                        return $this->render('acceso/inicio.html.twig', [
                            'rol' => $rol[0],
                            'apodo' => $apodo,
                            'imagen' => $imagen,
                            'likes' => $likes,
                            'form' => $form->createView(),
                            'formato_portada_valido' => $formato_portada_valido,
                            'formato_archivo_valido' => $formato_archivo_valido
                        ]);
                    }
                }
            }
            if (in_array('ROLE_RESIDENTE', $rol)) {
                return $this->render('acceso/inicio.html.twig', [
                    'rol' => $rol[0],
                    'apodo' => $apodo,
                    'imagen' => $imagen,
                    'likes' => $likes,
                    'form' => $form->createView(),
                    'formato_portada_valido' => $formato_portada_valido,
                    'formato_archivo_valido' => $formato_archivo_valido
                ]);
            }
        }
        return $this->render('./acceso/inicio.html.twig');
    }

    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $usuario = $this->getUser();
        if (!$usuario) {
            return $this->render('acceso/inicio.html.twig', [
                'error' => $error,
                'lastUsername' => $lastUsername
            ]);
        } else {
            return $this->redirectToRoute('app_singin');
        }
    }

    /**
     * @Route("/singin", name="app_singin")
     */
    public function singin(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $em): Response
    {
        $usuario = $this->getUser();

        if (!$usuario) {

            $usuario = new Usuario();
            $form = $this->createForm(SinginType::class, $usuario);
            $form->handleRequest($request);
            $pass_coinciden = true;
            $email_libre = true;

            if ($form->isSubmitted() && $form->isValid()) {
                $a = ['ROLE_RESIDENTE'];
                $usuario->setRoles($a);
                if($form->get('password')->getData() == $form->get('password2')->getData()) {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $usuario,
                        $form->get('password')->getData()
                    );
                    $usuario->setPassword($hashedPassword);
    
                    if ($imagen = $form->get("imagen")->getData()) {
                        $usuario->setImagen(file_get_contents($imagen));
                    }
    
                    try {
                        $em->persist($usuario);
                        $em->flush();
                    } catch (\Exception $e) {
                        $email_libre = false; /* En caso de que el email ya esté en uso */
                        return $this->render('./acceso/singin.html.twig', [
                            'controller_name' => 'SinginController',
                            'form' => $form->createView(),
                            'nuevo' => 'nuevo',
                            'pass_coinciden' => $pass_coinciden,
                            'email_libre' => $email_libre
                        ]);
                    }
                    return $this->redirectToRoute('app_inicio');
                } else {
                    $pass_coinciden = false; /* En caso de que las contraseñas no coincidan */
                    return $this->render('./acceso/singin.html.twig', [
                        'controller_name' => 'SinginController',
                        'form' => $form->createView(),
                        'nuevo' => 'nuevo',
                        'pass_coinciden' => $pass_coinciden,
                        'email_libre' => $email_libre
                    ]);
                }
            }
            /* Render por defecto, primera vez dentro */
            return $this->render('./acceso/singin.html.twig', [
                'controller_name' => 'SinginController',
                'form' => $form->createView(),
                'nuevo' => "nuevo",
                'pass_coinciden' => true,
                'email_libre' => true
            ]);
        } else { /* Todo OK, usuario registrado */
            return $this->redirectToRoute('app_inicio');
        }
    }


    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        throw new \Exception('No te olvides de activar el logut en security.yaml');
    }
}
