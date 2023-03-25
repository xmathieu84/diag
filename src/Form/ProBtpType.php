<?php

namespace App\Form;

use App\Entity\AbonnementGci;
use App\Entity\ProBtp;
use App\Entity\Travaux;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProBtpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('siteWeb')
            ->add('distanceInter',RangeType::class,[
                'attr'=>[
                    'min'=>0,
                    'max'=>100,
                    'value'=>0,
                    'step'=>1
                ]
            ])

            ->add("departZoneInter",ChoiceType::class,[
                'choices'=>[
                    'Depuis votre siège social'=>'oui',
                    'Depuis une autre ville'=>'non'
                ],
                'expanded'=>true,
                'required'=>false,
                'multiple'=>false,
                "placeholder"=>false,
                'mapped'=>false
            ])
            ->add('villeDepart',TextType::class,[
                'required'=>false,
                'mapped'=>false
            ])
            ->add('travaux',EntityType::class,[
                'class'=>Travaux::class,
                'choice_label'=>'nom',
                'mapped'=>false,
                'expanded'=>true,
                'multiple'=>true,

            ])
            ->add('abonnement',EntityType::class,[
                'class'=>AbonnementGci::class,
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('ag')
                        ->andWhere('ag.profil =:profil')
                        ->setParameter('profil','Pro-Btp');
                },
                'choice_label'=>function(AbonnementGci $abonnementGci){
                    return sprintf($abonnementGci->getNom()." ". $abonnementGci->getPrix()." € HT par mois. Engagement :".$abonnementGci->getDuree()->format('%y')." année(s)");
                },
                'mapped'=>false,
                'placeholder'=>'Choisissez votre abonnement',
                'choice_attr' => function($choice, $key, $value) {

                    return ['data-possible' => $choice->getCible()];
                },

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProBtp::class,
        ]);
    }
}
