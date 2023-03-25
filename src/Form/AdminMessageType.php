<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('selectionDestinataire', ChoiceType::class, [
                'choices' => [
                    '-----' => null,
                    'tout les membres' => 'all',
                    'entreprise premium' => 'premium',
                    'une entreprise' => 'entreprise'
                ],
                'mapped' => false,

            ])
            ->add('mail', EmailType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('contenu')

            ->add('sujet');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
