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
use phpDocumentor\Reflection\Types\Mixed_;
use Symfony\Bundle\TwigBundle\DependencyInjection\Compiler\TwigEnvironmentPass;
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

            if ($usuario->getImagen()) {
                $imagen = base64_encode(stream_get_contents($usuario->getImagen(), -1, -1));
            } else {
                $imagen = 0;
            }

            $usuario_repositorio = $em->getRepository(Usuario::class)->findOneBy(array("email" => $usuario->getUserIdentifier()));
            $multimedia = new Multimedia();
            $form = $this->createForm(PublicarType::class, $multimedia);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if ($archivo = $form->get("archivo")->getData()) {

                    $multimedia->setArchivo(file_get_contents($archivo));
                    $multimedia->setFormato($archivo->guessExtension());
                    $multimedia->setUsuario($usuario_repositorio);
                }

                try {
                    $em->persist($multimedia);
                    $em->flush();
                } catch (\Exception $e) {
                    return new Response("Esto no va, no no no.");
                }
                return $this->redirectToRoute('app_inicio');
            }

            if (in_array('ROLE_RESIDENTE', $rol)) {
                return $this->render('acceso/inicio.html.twig', [
                    'rol' => $rol[0],
                    'apodo' => $apodo,
                    'imagen' => $imagen,
                    'form' => $form->createView()
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

            if ($form->isSubmitted() && $form->isValid()) {
                $a = ['ROLE_RESIDENTE'];
                $usuario->setRoles($a);
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
                    return new Response("Esto no va, no no no.");
                }
                return $this->redirectToRoute('app_inicio');
            }
            return $this->render('./acceso/singin.html.twig', [
                'controller_name' => 'SinginController',
                'form' => $form->createView(),
                'nuevo' => "nuevo"
            ]);
        } else {
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
