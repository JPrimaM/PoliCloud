<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;
use Symfony\Component\Security\Core\Security;
use App\Form\SinginType;
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
    public function inicio(): Response
    {
        $usuario = $this->security->getUser();


        return $this->render('./acceso/inicio.html.twig');
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
                $imagen = $form->get("imagen")->getData();
                $usuario->setImagen(file_get_contents($imagen)); 

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
                'form' => $form->createView()
            ]);
        } else {
            return $this->redirectToRoute('app_inicio');
        }
    }

}
