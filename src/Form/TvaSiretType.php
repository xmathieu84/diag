<?php

namespace App\Form;

use App\Entity\TvaSiret;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TvaSiretType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tva', TextType::class, [
                'required' => false
            ])
            ->add('siret')
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TvaSiret::class,
        ]);
    }
}
