<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Seance;
use Doctrine\ORM\EntityRepository; // <-- IMPORTANT : Import pour filtrer la base de données
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          
            ->add('date_part', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'label' => 'Jour de la séance',
                'attr' => ['class' => 'form-control date-picker', 'placeholder' => 'Choisir le jour']
            ])
            
            ->add('time_part', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'label' => 'Horaire',
                'attr' => ['class' => 'form-control time-picker', 'placeholder' => 'Choisir l\'heure']
            ])
            
       
            ->add('film', EntityType::class, [
                'class' => Film::class,
                'choice_label' => 'Nom',
                'label' => 'Film',
                
             
                'placeholder' => '--- Sélectionnez un film ---',
                
               
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->where('f.deletedAt IS NULL')
                        ->orderBy('f.Nom', 'ASC'); 
                },
                'attr' => ['class' => 'form-control']
            ])
            
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
                'label' => 'Salle',
                'attr' => ['class' => 'form-control'] 
            ])
            
           
            ->add('NbPlaceReservees', null, [
                'attr' => ['style' => 'display:none;'], 
                'data' => 0, 
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Seance::class]);
    }
}