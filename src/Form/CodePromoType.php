<?php

namespace App\Form;

use App\Entity\CodePromo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CodePromoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('avantage')
            ->add('validite', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('cible', ChoiceType::class, [
                'choices' => [
                    'OTD concerné' => null,
                    'OTD sans abonnements' => 'OTD sans abonnements',
                    'Abonné So free' => 'So free',
                    'Abonné Access' => 'Access',
                    'Abonné Premuim' => 'Premium',
                    'Abonné Infinite' => 'Infinite',
                    'Toute les abonnés' => 'abonné'
                ],

                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CodePromo::class,
        ]);
    }
}
