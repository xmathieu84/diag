<?php

namespace App\Form;

use App\Entity\Salarie;
use App\Entity\TypeDonnee;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ModifInterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('periInter', ChoiceType::class, [
                'choices' => [
                    
                    '-50 km' => '50',
                    '-100km' => '100',
                    '-150km' => '150',
                    '+150km' => '1200'
                ],
                'placeholder'=>"distance d'intervention",
                'required'=>false

            ])      
           
            
            ->add('typeInters',EntityType::class,[
                'class'=>TypeDonnee::class,
                'choice_label'=>'nom',
                'expanded'=>true,
                'multiple'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salarie::class,
        ]);
    }
}
