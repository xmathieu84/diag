<?php

namespace App\Form;


use App\Entity\TypInter;

use App\Entity\TypeDonnee;

use App\Entity\Intervention;
use App\Entity\SecteurIntervention;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\AbstractType;
use Proxies\__CG__\App\Entity\ListeInter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InterventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder



            ->add('typeDeBien',ChoiceType::class,[
                'placeholder'=>'Type de bien',

                'choices'=>[
                    'Maison individuelle'=>'Maison individuelle',
                    'Bâtiment'=>'Bâtiment',
                    'Immeuble'=>'Immeuble',
                    'Terrain'=>'Terrain',
                    'Autre'=>'Autre'
                ],

                ])
            ->add('autre',TextType::class,[
                'mapped'=>false,
                'required'=>false
            ])
            ->add('decollage', ChoiceType::class, [
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false,
            ]);
        if ($options['profil']==='Particulier propriétaire'){
            $builder->add('renoncementDelaiRetract',ChoiceType::class,[
                'choices'=>[
                    'Oui je renonce'=>true,
                    'Non je ne renonce pas à mon droit de rétractation'=>false
                ],
                'expanded'=>true,
                'multiple'=>false,

            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervention::class,
            'profil'=>null
        ]);
    }
}
