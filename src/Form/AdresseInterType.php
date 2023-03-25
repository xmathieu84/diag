<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseInterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero')
            ->add('nomVoie')
            ->add('codePostal')
            ->add('ville')

        ;
        if ($options['user']){
            $builder->add('sameFact',ChoiceType::class,[
                'choices'=>[
                    'Oui'=>'Oui',
                    'Non'=>'Non'
                ],
                'expanded'=>true,
                'multiple'=>false,
                'mapped'=>false,

            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
            'user'=>null
        ]);
    }
}
