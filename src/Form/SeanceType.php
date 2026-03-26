<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Seance;
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
            ->add('film', EntityType::class, [
                'class' => Film::class,
                'choice_label' => 'Nom',
                'label' => 'Film'
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
                'label' => 'Salle'
            ])
            // On garde le champ réel masqué pour la réception des données
            ->add('NbPlaceReservees', null, ['attr' => ['style' => 'display:none;'], 'data' => 0, 'label' => false])
            ->add('film', EntityType::class, [
                'class' => Film::class,
                'choice_label' => 'Nom',
                'label' => 'Film',
                'attr' => ['class' => 'form-control film-select'], // On ajoute une classe pour le JS
                'choice_attr' => function($film) {
                    // On stocke le nom de l'affiche dans un attribut "data-poster"
                    return ['data-poster' => $film->getAffiche()];
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Seance::class]);
    }
}