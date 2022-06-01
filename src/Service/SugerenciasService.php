<?php

namespace App\Service;

use App\Entity\Multimedia;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use phpDocumentor\Reflection\Types\Callable_;
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
            ->andWhere('multimedia.formato IN (:jpg)')
            ->setParameter('ids', $idsMultimedia)
            ->setParameter('jpg', 'jpg')
            ->getQuery()
            ->getResult();


        shuffle($aleatoriosImagenMultimedia);

        $portadasImagen = array();
        $perfilImagen = array();
        $barajaImagenMultimedia = array();
        $contador = 0;
        foreach ($aleatoriosImagenMultimedia as $aleatoriosImagen) {
            if ($contador == $cantidad) {
                break;
            } else {
                $aux = base64_encode(stream_get_contents($aleatoriosImagen->getArchivo(), -1, -1));
                $aleatoriosImagen->setArchivo($aux);

                $aux2 = base64_encode(stream_get_contents($aleatoriosImagen->getPortada(), -1, -1));
                if($aux2 != "") {
                    $aleatoriosImagen->setPortada($aux2);

                    array_push($portadasImagen, $aux2); // Se almacena y se globaliza para que en caso de que el usaurio consulte su propio 
                                                        // contenido se puedan incorporar las imagenes, ya que de normal elimina el contenido de la portada
                    
    
                    /* Coger foto del usuario de la publicacion para mostrarla en el modal */
                    try {   // En caso de que el usuario tenga la imagen null no salta error
                        if($aleatoriosImagen->getUsuario()->getImagen()) {
                            $aux3 = base64_encode(stream_get_contents($aleatoriosImagen->getUsuario()->getImagen(), -1, -1));
                            $aleatoriosImagen->getUsuario()->setImagen($aux3);
    
                            array_push($perfilImagen, $aux3);   // Se almacena y se globaliza para que en caso de que el usaurio consulte su propio 
                                                                // contenido se puedan incorporar las imagenes, ya que de normal elimina el contenido del perfil
                        }
                    } catch(Exception $e) {
                        
                    }                
                    array_push($barajaImagenMultimedia, $aleatoriosImagen);
                } else {
                    $contador--;
                    
                }
                
            }
            $contador++;
        }
 
        //var_dump(base64_encode(stream_get_contents($aleatoriosImagenMultimedia[0]->getUsuario()->getImagen(), -1, -1)));
        //var_dump($aux);

        return [
            new TwigFunction('sugeridos_imagen', [$this, ($barajaImagenMultimedia)]),
            new TwigFunction('sugeridos_perfil_imagen', [$this, ($aleatoriosImagenMultimedia)]),
            new TwigFunction('sugeridos_portada_imagen', [$this, ($portadasImagen)])
        ];
    }



    /* MUSICA */
    public function sugerenciasMusicaAleatorias()
    {
        $cantidad = 3;
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
                if($aux2 != "") {
                    $aleatoriosMusica->setPortada($aux2);


                    /* Coger foto del usuario de la publicacion para mostrarla en el modal */
                    try {   // En caso de que el usuario tenga la Imagen null no salta error
                        if($aleatoriosMusica->getUsuario()->getImagen()) {
                            $aleatoriosMusica->getUsuario()->setImagen(base64_encode(stream_get_contents($aleatoriosMusica->getUsuario()->getImagen(), -1, -1)));
                        }
                    } catch(Exception $e) {
                        
                    }                
                    array_push($barajaMusicaMultimedia, $aleatoriosMusica);
                } else {
                    $contador--;
                    
                }
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
        $cantidad = 3;
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

                $aux2 = base64_encode(stream_get_contents($aleatoriosVideo->getPortada(), -1, -1));
                if($aux2 != "") {
                $aleatoriosVideo->setPortada($aux2);


                /* Coger foto del usuario de la publicacion para mostrarla en el modal */
                try {   // En caso de que el usuario tenga la Imagen null no salta error
                    if($aleatoriosVideo->getUsuario()->getImagen()) {
                        $aleatoriosVideo->getUsuario()->setImagen(base64_encode(stream_get_contents($aleatoriosVideo->getUsuario()->getImagen(), -1, -1)));
                    }
                } catch(Exception $e) {
                    
                }                
                array_push($barajaVideoMultimedia, $aleatoriosVideo);
                } else {
                    $contador--;
                    
                }
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