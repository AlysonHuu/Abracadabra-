<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\Film;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Affiche', FileType::class, [
                'label' => 'Affiche du film (Fichier image)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M', // On enlève les ' ' autour de la clé et la flèche =>
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        mimeTypesMessage: 'Veuillez uploader une image valide (JPG, PNG ou WEBP)'
                    )
                ],
            ])

            ->add('Nom')
            ->add('Description')
            ->add('Genre')
            ->add('estDispo')
            ->add('dateSortie')
            ->add('Duree')
            ->add('Note')
            ->add('Commentaire')
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updatedAt', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('deletedAt', null, [
            //     'widget' => 'single_text',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
