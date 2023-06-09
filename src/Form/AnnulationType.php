<?php

namespace App\Form;

use App\Entity\Annulation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class AnnulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('raison')
            ->add('validation', ChoiceType::class, [
                'choices' => [
                    'oui' => 'oui',
                    'non' => 'non'
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annulation::class,
        ]);
    }
}
