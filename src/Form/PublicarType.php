<?php

namespace App\Form;

use App\Entity\Multimedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PublicarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('portada', FileType::class, array(
                "required" => true
            ))
            ->add('archivo', FileType::class, array(
                "required" => true
            ))
            ->add('nombre', TextType::class, array(
                "required" => true
            ))
            ->add('categoria', TextType::class, array(
                "required" => false
            ))
            ->add('descripcion', TextType::class, array(
                "required" => false
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Publicar'
            ));;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Multimedia::class,
        ]);
    }
}
