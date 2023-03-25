<?php

namespace App\Form;

use App\Entity\Drone;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DroneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomFabriquant')
            ->add('typeDrone')
            ->add('numeroDgac')
            ->add('PoidDrone')
            ->add('captif', ChoiceType::class, [
                'choices' => [
                    'oui' => 'oui',
                    'non' => 'non'
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('classe', ChoiceType::class, [
                'choices' => [
                    'Voilure fixe' => 'Voilure fixe',
                    'Hélicoptère' => 'Hélicoptère',
                    'Mulitirotors' => 'Multirotors',
                    'Ballon' => 'Ballon',
                    'Dirigeable' => 'Dirigeable'
                ],
                'placeholder' => 'Classe du drone'
            ])
            ->add('trame', TextType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Drone::class,
        ]);
    }
}
