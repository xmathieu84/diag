<?php

namespace App\Form;

use App\Entity\Salarie;
use App\Form\AdresseType;
use App\Entity\TypeDonnee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ModifSalairieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite',CiviliteType::class)
            ->add('adresse', AdresseType::class)
            ->add('tel_salarie', TextType::class)
            ->add('peri_inter', ChoiceType::class, [
                'choices' => [
                    "Distance d'intervention" => null,
                    '-50 km' => '50',
                    '-100km' => '100',
                    '-150km' => '150',
                    '+150km' => '1200'
                ]

            ])
            ->add('licenceDgac',DgacType::class,[
                'required'=>false
            ])
            ->add('TypeDonnees',EntityType::class,[
                'class'=>TypeDonnee::class,
                'choice_label'=>'nom',
                'multiple'=>true,
                'expanded'=>true,
                
                
            ])

            
            ->add('user', ModifierUserType::class);
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salarie::class,
        ]);
    }
}
