<?php

namespace App\Form;

use App\Entity\Intervention;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class InterInstiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('photos', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'required'=>false,
                'attr'     => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ]
            ])
            ->add('agglo', ChoiceType::class, [

                'required' => true,
                'placeholder' => false,
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],

                'expanded' => true,
                'multiple' => false,
            ])
            ->add('intemp', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],

                'expanded' => true,
                'multiple' => false,
                'mapped'=>false
            ])
            ->add('inter_precision', TextareaType::class, [
                'required' => false
            ])
            ->add('dateIntemperie', DateType::class, [
                'mapped'=>false,
                'required' => false,
                'widget' => 'single_text',
                'input_format' => 'jj-MM-aaaa',
                'empty_data' => '',
                'placeholder' => [
                    'day' => 'Jour', 'month' => 'Mois', 'year' => 'Année',
                ]
            ])
            ->add('toiture',ChoiceType::class,[
                'choices'=>[
                    'Toiture/Couverture/Etanchéité'=>'Toiture/Couverture/Etanchéité',

                ],
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true
            ])
            ->add('gros',ChoiceType::class,[
                'choices'=>[

                    'Gros œuvre (Maçonnerie, Façade)'=>'Gros œuvre (Maçonnerie, Façade)',

                ],
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true
            ])
            ->add('menuiserie',ChoiceType::class,[
                'choices'=>[
                    'Menuiserie extérieur, ouverture'=>'Menuiserie extérieur, ouverture',
                ],
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true
            ])
            ->add('electricite',ChoiceType::class,[
                'choices'=>[
                    'Electricité/Plomberie/Plâtrerie/Peinture'=>'Electricité/Plomberie/Plâtrerie/Peinture',
                ],
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true
            ])
            ->add('reseaux',ChoiceType::class,[
                'choices'=>[
                    'Réseaux et alimentation (TP)'=>'Réseaux et alimentation (TP)',
                ],
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true
            ])
            ->add('exterieur',ChoiceType::class,[
                'choices'=>[
                    'Elément extérieur (végétale, piscine...)'=>'Elément extérieur (végétale, piscine...)',
                ],
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true
            ])
            ->add('autreType',TextType::class,[
                'required'=>false,
                'mapped'=>false
            ])
            ->add('decollage', ChoiceType::class, [


                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],


                'expanded' => true,
                'multiple' => false,
            ])
            ->add('intemperie', ChoiceType::class, [
                'choices'=>[
                    'Neige'=>'Neige',
                    'Vent'=>'Vent',
                    'Grêle'=>'Grêle',
                    'Pluie'=>'Pluie',
                    'Canicule'=>'Canicule',
                    'Grand froid'=>'Grand froid',

                ],
                'required' => false,
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true
            ])

            ->add('adresse', AdresseType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}
