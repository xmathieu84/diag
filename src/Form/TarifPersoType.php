<?php

namespace App\Form;



use App\Entity\TarifPerso;
use App\Entity\TypeDonnee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifPersoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prix')
            ->add('TypeDonnee', EntityType::class, [
                'class' => TypeDonnee::class,
                'choice_label' => 'nom',
                'choice_value'=>'nom',
                'placeholder'=>"Type de données produite"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TarifPerso::class,
        ]);
    }
}
