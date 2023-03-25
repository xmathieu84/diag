<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardNumber',NumberType::class,[
                'required'=>true
            ])
            ->add('cardExpirationDate',NumberType::class,[
                'required'=>true])
            ->add('cardCvx',NumberType::class,['required'=>true])
            ->add('returnURL', HiddenType::class,['required'=>true])
            ->add('accessKeyRef', HiddenType::class,['required'=>true])
            ->add('data', HiddenType::class,['required'=>true])



        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
