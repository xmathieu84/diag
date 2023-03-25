<?php

namespace App\Form;

use App\Entity\Agent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutAgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cgv',ChoiceType::class,[
                'choices'=>[
                    "J'accepte les conditions générales d'utilisation"=>true
                ],
                'multiple'=>true,
                'expanded'=>true,
                'mapped'=>false
            ])
            ->add('user',UserType::class)

            ->add('civilite',CiviliteType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
        ]);
    }
}
