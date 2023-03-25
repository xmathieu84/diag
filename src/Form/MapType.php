<?php

namespace App\Form;

use App\Entity\MAP;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dureeVol', DateIntervalType::class, [
                'with_years'  => false,
                'with_months' => false,
                'with_days'   => false,
                'with_hours'  => true,
                'with_minutes' => true,
                'labels' => [
                    'invert' => null,
                    'years' => null,
                    'months' => null,
                    'weeks' => null,
                    'days' => null,
                    'hours' => 'Heures',
                    'minutes' => 'Minutes',
                    'seconds' => null,
                ]
            ])
            ->add('observation');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MAP::class,
        ]);
    }
}
