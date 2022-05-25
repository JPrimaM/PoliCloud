<?php

namespace App\Service;

use App\Entity\Multimedia;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Foreach_;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SugerenciasService extends AbstractExtension {

    private $em;
    

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function sugerenciasAleatorias(): array {

        $repositorioMultimedia = $this->em->getRepository(Multimedia::class);

        $cantidadMultimedia = $repositorioMultimedia->createQueryBuilder('multimedia')
                                                    ->select('count(multimedia.id)')
                                                    ->getQuery()
                                                    ->getSingleScalarResult();


        $numeros = range(1, $cantidadMultimedia);
        shuffle($numeros);

        $aleatoriosIdsMultimedia = array_slice($numeros, 0, 6);

        $aleatoriosMultimedia = $repositorioMultimedia->createQueryBuilder('multimedia')
                                                      ->where('multimedia.id IN (:ids)')
                                                      ->setParameter('ids', $aleatoriosIdsMultimedia)
                                                      ->setMaxResults(1)
                                                      ->getQuery()
                                                      ->getResult();

        $aux = "";
        foreach ($aleatoriosMultimedia as $aleatorioM) {

                $aux = $aleatorioM;
            
        }


        return [
            new TwigFunction('sugeridos_multimedia', [ $this, $aux])
        ];
    }
















    /*     public function filaAleatoria($min, $max, $numFilas) {
        $numbers = range($min, $max);
        shuffle($numbers);

        return array_slice($numbers, 0, $numFilas);
    } */

    

}

?>