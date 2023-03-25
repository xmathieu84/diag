<?php

namespace App\Form;

use App\Entity\AppelOffre;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ordre',TextType::class,[
                'required'=>false
            ])
            ->add('reference',TextType::class,[
                'required'=>false
            ])
            ->add('denomination',TextType::class,[
                'required'=>false
            ])
            ->add('type',ChoiceType::class,[
                'choices'=>[
                    'Appel à concurence'=>'Appel à concurrence',
                    "Appel d'offre"=>"Appel d'offre"
                ],
                'placeholder'=>'Type'
            ])

            ->add('categorie',TextType::class,[
                'required'=>false
            ])
            ->add('ouvert',ChoiceType::class,[
                'choices'=>[
                    'ouvert'=>'ouvert',
                    'restreint'=>'restreint'
                ]
                ,
                'multiple'=>false,
                'expanded'=>true,
                'required'=>false,
                'mapped'=>false,
                'placeholder'=>null
            ])
            ->add('DateRemiseProp',DateType::class,[
                'widget'=>'single_text',

            ])
            ->add('datePriseDecision',DateType::class,[
                'widget'=>'single_text',

            ])
            ->add('datePassationCommande',DateType::class,[
                'widget'=>'single_text',

                'required'=>false
            ])
            ->add('dateMaxLivraison',DateType::class,[
                'widget'=>'single_text',

                'required'=>false
            ])
            ->add('critereSelection')
            ->add('presentation')
            ->add('restreint',RestreintType::class,[
                'required'=>false
            ])

            ->add('budgetExistant',ChoiceType::class,[
                'choices'=>[
                    'Oui'=>'oui',
                    'Non'=>'non'
                ],
                'multiple'=>false,
                'expanded'=>true,

                'mapped'=>false,
                'placeholder'=>null
            ])
            ->add('budget',BudgetType::class,['required'=>false])
        ;
            $builder->add('contacts',CollectionType::class,[
                'entry_type'=>ContactAoType::class,
                'entry_options' => ['label' => true],
                'label'=>' ',
                'allow_add'=>true,
                'allow_delete' => true,
                'mapped'=>false

            ]);
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent){
                $appel = $formEvent->getData();
                $form = $formEvent->getForm();
                if ($appel->getDossierAos()->IsEmpty()){
                    $form->add('dossier',FileType::class,[
                        'mapped'=>false,
                        'multiple'=>true,
                        'attr'=>[
                            'accept'=>'application/pdf,application/zip,application/rar,application/7zip'
                        ]
                    ]);
                }
                else{
                    $form->add('dossier',FileType::class,[
                        'mapped'=>false,
                        'multiple'=>true,
                        'required'=>false,
                        'attr'=>[
                            'accept'=>'application/pdf,application/zip,application/rar,application/7zip'
                        ]
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppelOffre::class,
        ]);
    }
}
