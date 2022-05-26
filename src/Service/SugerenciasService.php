<?php

namespace App\Service;

use App\Entity\Multimedia;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Callable_;
use PhpParser\Node\Stmt\Foreach_;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SugerenciasService extends AbstractExtension
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function sugerenciasAleatorias()
    {
        $cantidad = 3;
        $repositorioMultimedia = $this->em->getRepository(Multimedia::class);

        $idsMultimedia = $repositorioMultimedia->createQueryBuilder('multimedia')
            ->select('multimedia.id')
            ->getQuery()
            ->getScalarResult();                // Devuelve array con todos los datos
        /* ->getSingleScalarResult(); */    // Devuelve un solo valor como string

        $aleatoriosImagenMultimedia = $repositorioMultimedia
            ->createQueryBuilder('multimedia')
            ->andWhere('multimedia.id IN (:ids)')
            ->andWhere('multimedia.formato IN (:png)')
            ->setParameter('ids', $idsMultimedia)
            ->setParameter('png', 'png')
            ->getQuery()
            ->getResult();


        shuffle($aleatoriosImagenMultimedia);

        $barajaImagenMultimedia = [];
        $contador = 0;
        foreach ($aleatoriosImagenMultimedia as $aleatoriosImagen) {
            if ($contador == $cantidad) {
                break;
            } else {
                $aux = base64_encode(stream_get_contents($aleatoriosImagen->getArchivo(), -1, -1));
                $aleatoriosImagen->setArchivo($aux);


                /* Coger foto del usuario de la publicacion para mostrarla en el modal */
                /* $aux2 = base64_encode(stream_get_contents($aleatoriosImagen->getUsuario()->getImagen(), -1, -1));
                $aleatoriosImagen->getUsuario()->setImagen($aux2); */

                array_push($barajaImagenMultimedia, $aleatoriosImagen);
            }
            $contador++;
        }



        return [
            new TwigFunction('sugeridos_imagen', [$this, ($barajaImagenMultimedia)])
        ];
    }
}
