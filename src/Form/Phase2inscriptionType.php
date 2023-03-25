<?php

namespace App\Form;

use App\Entity\Salarie;
use App\Entity\TypeDonnee;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Phase2inscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('peri_inter', ChoiceType::class, [
                'choices' => [
                    '-50 km' => '50',
                    '-100km' => '100',
                    '-150km' => '150',
                    '+150km' => '1200'
                ],
                'placeholder' => "Périmètre d'intervention (vol d'oiseau)"

            ])
            ->add('typeDonnees', EntityType::class, [
                'class' => TypeDonnee::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,




            ])
            ->add('licenceDgac', DgacType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salarie::class,
        ]);
    }
}
