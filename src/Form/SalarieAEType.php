<?php

namespace App\Form;

use App\Form\DgacType;
use App\Entity\Salarie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SalarieAEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('peri_inter', ChoiceType::class, [
                'choices' => [
                    '---' => null,
                    '-50 km' => '50',
                    '-100km' => '100',
                    '-150km' => '150',
                    '+150km' => '1200'
                ],
                'required' => false,

            ])
            ->add('licenceDgac',DgacType::class);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salarie::class,
        ]);
    }
}
