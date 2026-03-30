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
            // Champ Date uniquement
            ->add('date_part', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'label' => 'Jour de la séance',
                'attr' => ['class' => 'form-control date-picker', 'placeholder' => 'Choisir le jour']
            ])
            // Champ Heure uniquement
            ->add('time_part', TimeType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'label' => 'Horaire',
                'attr' => ['class' => 'form-control time-picker', 'placeholder' => 'Choisir l\'heure']
            ])
            
            // --- LE CHAMP FILM UNIFIÉ ET OPTIMISÉ ---
            ->add('film', EntityType::class, [
                'class' => Film::class,
                'choice_label' => 'Nom',
                'label' => 'Film',
                
                // 1. Le texte par défaut (règle le problème de l'image qui s'affiche de suite)
                'placeholder' => '--- Sélectionnez un film ---',
                
                // 2. Le filtre "Soft Delete" (exclut les films supprimés)
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->where('f.deletedAt IS NULL')
                        ->orderBy('f.Nom', 'ASC'); // Tri alphabétique
                },
                'attr' => ['class' => 'form-control']
            ])
            
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
                'label' => 'Salle',
                'attr' => ['class' => 'form-control'] // Ajout direct de la classe CSS
            ])
            
            // On garde le champ réel masqué pour la réception des données
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