<?php

namespace App\Form;

use App\Entity\Rapport;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RapportType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('photos', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'attr'     => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ],

            ])

            ->add('rap_fichier', FileType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('rap_resume', TextareaType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('rap_messhid', TextareaType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('donnees_telemetrique', FileType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('cerfa_inter', FileType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('confirmation', ChoiceType::class, [
                'choices' => [
                    'oui' => 'oui',
                    'non' => 'non',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,

            ])
            ->add('degatInter', ChoiceType::class, [
                'choices' => [
                    'oui' => 'oui',
                    'non' => 'non',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,

            ])
            ->add('degat',IncidentType::class,[
                'mapped'=>false,
                'required'=>false
            ])

            ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
            $rapport = $formEvent->getData();
            $form = $formEvent->getForm();
            if ($rapport->getIntervention()->getIntDem()->getUser()) {
                $form->add('paiement', ChoiceType::class, [
                    'choices' => [
                        'oui' => 'oui',
                        'non' => 'non',
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'mapped' => false,



                ])
                    ->add('date', DateType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'mapped' => false
                    ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rapport::class,
        ]);
    }
}
