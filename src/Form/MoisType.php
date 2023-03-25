<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('duree', ChoiceType::class, [
                'choices' => [
                    'Choisir la durée' => null,
                    '1 mois' => '1',
                    '2 mois' => '2',
                    '3 mois' => '3',
                    '4 mois' => '4',
                    '5 mois' => '5',
                    '6 mois' => '6',
                    '7 mois' => '7',
                    '8 mois' => '8',
                    '9 mois' => '9',
                ]
            ])
        
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
