<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DepartType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('debut', DateTimeType::class, [
                'help' => "Ceci est une estimation de l'heure de départ pour votre rendez-vous.",
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'input_format' => 'jj-MM-aaaa H:i',
                'placeholder' => [
                    'day' => 'Jour', 'month' => 'Mois', 'year' => 'Année', 'hour' => 'Heure', 'minute' => 'Minute'
                ],

            ])
            ->add('depart', DateTimeType::class, [
                'help' => "Estimation de l'heure départ du lieu de votre intervention",
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'input_format' => 'jj-MM-aaaa H:i',
                'placeholder' => [
                    'day' => 'Jour', 'month' => 'Mois', 'year' => 'Année', 'hour' => 'Heure', 'minute' => 'Minute'
                ],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
