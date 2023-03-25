<?php

namespace App\Form;

use App\Entity\Travaux;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheProBtpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codePostal',TextType::class)
            ->add('ville',TextType::class)
            ->add('travaux',EntityType::class,[
                'class'=>Travaux::class,
                'choice_label'=>'nom',
                'mapped'=>false,
                'expanded'=>true,
                'multiple'=>false,
                'row_attr' => ['class' => 'text-editor'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
