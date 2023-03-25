<?php

namespace App\Form;

use App\Form\UserType;
use App\Form\AdresseType;
use App\Entity\Consultant;
use App\Form\CiviliteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ConsultantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profil', ChoiceType::class, [
                'choices' => [
                    '-----------------------------------------Profil--------------------------------------------------' => null,
                    'Particulier propriétaire' => 'Particulier propriétaire',
                    'Syndic de co-propriété' => 'Syndic de co-propriété',
                    'Gestionnaire d\'immeuble' => 'Gestionnaire d\'immeuble',
                    'Commune' => 'Commune',
                    'Assureur' => 'Assureur',
                    'Entreprise' => 'Entreprise',
                    'Expert en assurance' => 'Expert en assurance',
                    'Agent Immobilier' => 'Agent Immobilier',
                    'Diagnostiqueur' => 'Diagnostiqueur',
                    'Notaire' => 'Notaire',

                ]
            ])
            ->add('telephone', TelType::class)
            ->add('siret', TextType::class, [
                'required' => false
            ])
            ->add('tva', TextType::class, [
                'required' => false
            ])

            ->add('adresse', AdresseType::class)
            ->add('civilite', CiviliteType::class)
            ->add('user', UserType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Consultant::class,
        ]);
    }
}
