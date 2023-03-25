<?php

namespace App\Form;

use App\Entity\TarifAdmin;
use App\Entity\TypeDonnee;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prix')
            ->add('nom')
            ->add('typeDonnee', EntityType::class, [
                'class' => TypeDonnee::class,
                'choice_label' => 'nom',
                'choice_value'=>'nom', 
                'mapped'=>false               
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TarifAdmin::class,
        ]);
    }
}
