<?php

namespace App\Form;

use App\Entity\Restreint;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestreintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delaiDepotCandidature',TextType::class,[
                'mapped'=>false
            ])
            ->add('delaiReponseCandidature',TextType::class,[
                'mapped'=>false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restreint::class,
        ]);
    }
}
