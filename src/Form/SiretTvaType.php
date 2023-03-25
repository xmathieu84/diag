<?php

namespace App\Form;

use App\Entity\TvaSiret;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SiretTvaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tva', TextType::class, [
                'required' => false,
                'attr' => [
                    'pattern' => '[0-9A-Za-z]{2}[0-9]{11}'
                ]
            ])
            ->add('siret', TextType::class, [
                'required' => true,
                'attr' => [
                    'pattern' => '[0-9]{14}'
                ]
            ])
            ->add('assujeti',ChoiceType::class,[
                'choices'=>[
                    'Oui'=>true,
                    'Non'=>false
                ],
                'expanded'=>true,
                'multiple'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TvaSiret::class,
        ]);
    }
}
