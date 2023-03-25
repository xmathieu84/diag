<?php

namespace App\Form;

use App\Entity\Dossier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type',ChoiceType::class,[
                'choices'=>[
                    'Patrimoine immobilier'=>'Patrimoine immobilier',
                    'Voirie , Espace vert , Parking'=>'Voirie , Espace vert , Parking',
                    "Réseaux"=>"Réseaux",
                    "Ouvrage d’art"=>"Ouvrage d’art",
                    "Evènement sportif ou culturel"=>"Evènement sportif ou culturel",
                    "Intervention diverse"=>"Intervention diverse"
                ],
                'placeholder'=>'Type de dossier'
            ])
            ->add('reference',TextType::class,[
                'required'=>false
            ])
            ->add('nom')

            ->add('adresse',AdresseType::class)
            ->add('dossierGen',ChoiceType::class,[
                'choices'=>[
                    'Je souhaite inclure un dossier général (Données générales relatives au dossier)'=>true
                ],
                'mapped'=>false,
                'expanded'=>true,
                'multiple'=>true,
                'required'=>false
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dossier::class,
        ]);
    }
}
