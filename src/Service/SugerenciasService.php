<?php

namespace App\Service;

use App\Entity\Multimedia;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    public function sugerenciasImagenAleatorias()
    {
        $cantidad = 5;
        $repositorioMultimedia = $this->em->getRepository(Multimedia::class);

        $idsMultimedia = $repositorioMultimedia->createQueryBuilder('multimedia')
            ->select('multimedia.id')
            ->getQuery()
            ->getScalarResult();                // Devuelve array con todos los datos
        /* ->getSingleScalarResult(); */    // Devuelve un solo valor como string

        $aleatoriosImagenMultimedia = $repositorioMultimedia
            ->createQueryBuilder('multimedia')
            ->andWhere('multimedia.id IN (:ids)')
            ->andWhere('multimedia.formato IN (:jpg)')
            ->setParameter('ids', $idsMultimedia)
            ->setParameter('jpg', 'jpg')
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

                $aux2 = base64_encode(stream_get_contents($aleatoriosImagen->getPortada(), -1, -1));
                $aleatoriosImagen->setPortada($aux2);

                /* Coger foto del usuario de la publicacion para mostrarla en el modal */
                try {   // En caso de que el usuario tenga la imagen null no salta error
                    if($aleatoriosImagen->getUsuario()->getImagen()) {
                        $aleatoriosImagen->getUsuario()->setImagen(base64_encode(stream_get_contents($aleatoriosImagen->getUsuario()->getImagen(), -1, -1)));
                    }
                } catch(Exception $e) {
                    
                }                
                array_push($barajaImagenMultimedia, $aleatoriosImagen);
            }
            $contador++;
        }
 
        //var_dump(base64_encode(stream_get_contents($aleatoriosImagenMultimedia[0]->getUsuario()->getImagen(), -1, -1)));
        //var_dump($aux);

        return [
            new TwigFunction('sugeridos_imagen', [$this, ($barajaImagenMultimedia)])
        ];
    }



    /* AUDIO */
    public function sugerenciasMusicaAleatorias()
    {
        $cantidad = 5;
        $repositorioMultimedia = $this->em->getRepository(Multimedia::class);

        $idsMultimedia = $repositorioMultimedia->createQueryBuilder('multimedia')
            ->select('multimedia.id')
            ->getQuery()
            ->getScalarResult();                // Devuelve array con todos los datos
        /* ->getSingleScalarResult(); */    // Devuelve un solo valor como string

        $aleatoriosMusicaMultimedia = $repositorioMultimedia
            ->createQueryBuilder('multimedia')
            ->andWhere('multimedia.id IN (:ids)')
            ->andWhere('multimedia.formato IN (:mp3)')
            ->setParameter('ids', $idsMultimedia)
            ->setParameter('mp3', 'mp3')
            ->getQuery()
            ->getResult();


        shuffle($aleatoriosMusicaMultimedia);

        $barajaMusicaMultimedia = [];
        $contador = 0;
        foreach ($aleatoriosMusicaMultimedia as $aleatoriosMusica) {
            if ($contador == $cantidad) {
                break;
            } else {
                $aux = base64_encode(stream_get_contents($aleatoriosMusica->getArchivo(), -1, -1));
                $aleatoriosMusica->setArchivo($aux);

                $aux2 = base64_encode(stream_get_contents($aleatoriosMusica->getPortada(), -1, -1));
                $aleatoriosMusica->setPortada($aux2);


                /* Coger foto del usuario de la publicacion para mostrarla en el modal */
                try {   // En caso de que el usuario tenga la Imagen null no salta error
                    if($aleatoriosMusica->getUsuario()->getImagen()) {
                        $aleatoriosMusica->getUsuario()->setImagen(base64_encode(stream_get_contents($aleatoriosMusica->getUsuario()->getImagen(), -1, -1)));
                    }
                } catch(Exception $e) {
                    
                }                
                array_push($barajaMusicaMultimedia, $aleatoriosMusica);
            }
            $contador++;
        }
 
        //var_dump(base64_encode(stream_get_contents($aleatoriosMusicaMultimedia[0]->getUsuario()->getMusica(), -1, -1)));
        //var_dump($aux);

        return [
            new TwigFunction('sugeridos_musica', [$this, ($barajaMusicaMultimedia)])
        ];
    }


    /* VIDEO */
    public function sugerenciasVideoAleatorias()
    {
        $cantidad = 5;
        $repositorioMultimedia = $this->em->getRepository(Multimedia::class);

        $idsMultimedia = $repositorioMultimedia->createQueryBuilder('multimedia')
            ->select('multimedia.id')
            ->getQuery()
            ->getScalarResult();                // Devuelve array con todos los datos
        /* ->getSingleScalarResult(); */    // Devuelve un solo valor como string

        $aleatoriosVideoMultimedia = $repositorioMultimedia
            ->createQueryBuilder('multimedia')
            ->andWhere('multimedia.id IN (:ids)')
            ->andWhere('multimedia.formato IN (:mp4)')
            ->setParameter('ids', $idsMultimedia)
            ->setParameter('mp4', 'mp4')
            ->getQuery()
            ->getResult();


        shuffle($aleatoriosVideoMultimedia);

        $barajaVideoMultimedia = [];
        $contador = 0;
        foreach ($aleatoriosVideoMultimedia as $aleatoriosVideo) {
            if ($contador == $cantidad) {
                break;
            } else {
                $aux = base64_encode(stream_get_contents($aleatoriosVideo->getArchivo(), -1, -1));
                $aleatoriosVideo->setArchivo($aux);


                /* Coger foto del usuario de la publicacion para mostrarla en el modal */
                try {   // En caso de que el usuario tenga la Imagen null no salta error
                    if($aleatoriosVideo->getUsuario()->getImagen()) {
                        $aleatoriosVideo->getUsuario()->setImagen(base64_encode(stream_get_contents($aleatoriosVideo->getUsuario()->getImagen(), -1, -1)));
                    }
                } catch(Exception $e) {
                    
                }                
                array_push($barajaVideoMultimedia, $aleatoriosVideo);
            }
            $contador++;
        }
 
        //var_dump(base64_encode(stream_get_contents($aleatoriosVideoMultimedia[0]->getUsuario()->getVideo(), -1, -1)));
        //var_dump($aux);

        return [
            new TwigFunction('sugeridos_video', [$this, ($barajaVideoMultimedia)])
        ];
    }
}