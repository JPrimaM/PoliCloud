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


        $aleatoriosImagenMultimedia = $repositorioMultimedia    //Muestra las 3 primeras (cambiar)
            ->createQueryBuilder('multimedia')
            ->andWhere('multimedia.id IN (:ids)')
            ->andWhere('multimedia.formato IN (:png)')
            ->setParameter('ids', $idsMultimedia)
            ->setParameter('png', 'png')
            ->setFirstResult(0)
            ->setMaxResults($cantidad)
            ->getQuery()
            ->getResult();

        shuffle($aleatoriosImagenMultimedia);

        $aleatoriosImagenMultimediaFinal = [] ;

        foreach($aleatoriosImagenMultimedia as $aleatoriosImagen) { 
            $aux = base64_encode(stream_get_contents($aleatoriosImagen->getArchivo(), -1, -1));
            array_push($aleatoriosImagenMultimediaFinal, $aleatoriosImagen->setArchivo($aux));
        }
    
        return [
            new TwigFunction('sugeridos_imagen', [$this, ($aleatoriosImagenMultimedia)])
        ]; 
    }
}