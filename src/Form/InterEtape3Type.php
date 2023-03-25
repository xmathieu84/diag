<?php

namespace App\Form;

use App\Entity\Intervention;
use App\Entity\Travaux;
use App\Repository\TravauxRepository;
use App\Form\PhotoInterType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InterEtape3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intemp', ChoiceType::class, [
                'mapped' => false,
                'placeholder' => false,
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],

                'expanded' => true,
                'multiple' => false,
                'required'=>true
            ])
            ->add('besoinBudget',ChoiceType::class,[
                'mapped'=>false,
                'choices'=>[
                    "Oui"=>"Oui",
                    'Non'=>'Non'
                ],
                'placeholder' => false,
                'multiple'=>false,
                'expanded'=>true
            ])
            ->add('budgetInter',BudgetInterType::class,[
                'required'=>false,
                'mapped'=>false,
                'label'=>" "
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
            ->add('inter_precision', TextareaType::class, [

            ])
        ->add('photoOnly',CheckboxType::class,[
                'label'=>"Photos",
                'mapped'=>false,
                'required'=>false
                ])
            ->add('videoOnly',CheckboxType::class,[
                'label'=>"Vidéos",
                'mapped'=>false,
                'required'=>false
            ])
            ->add('nbrePhoto',NumberType::class,[
                'required'=>false
            ])
            ->add('nbreVideo',NumberType::class,[
                'required'=>false,
                'attr'=>[
                    'max'=>2
                ]
            ])
            ->add('photos', FileType::class, [
                'mapped' => false,
                'required'=>false,
                'multiple' => true,
                'attr'     => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ]
            ])
            ->add('travaux',EntityType::class,[
                'class'=>Travaux::class,
                'choice_label'=>'nom',
                'mapped'=>false,
                'multiple'=>true,
                'expanded'=>true,
                'row_attr'=>[
                    'class'=>'test'
                ]


            ])
            ->add('autreType',TextType::class,[
                'required'=>false,
                'mapped'=>false
            ])


            ->add('autreIntemp',TextType::class,[
                'mapped'=>false,
                "required"=>false
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
        ]);
    }
}
