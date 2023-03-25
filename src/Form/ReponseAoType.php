<?php

namespace App\Form;

use App\Entity\ReponseAo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseAoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presentation')
            ->add('qualification')
            ->add('contexte',TextareaType::class,[
                'required'=>false
            ])
            ->add('contacts',CollectionType::class, [
                'entry_type'=>ContactAoType::class,
                'entry_options' => ['label' => true],
                'label'=>' ',
                'allow_add'=>true,
                'allow_delete' => true,
                'mapped'=>false
            ])
            ->add('prix')
            ->add('details',TextareaType::class,[
                'required'=>false
            ])
            ->add('precision',ChoiceType::class,[
                'mapped'=>false,
                'choices'=>[
                    'oui'=>'oui',
                    'non'=>'non'
                ],'multiple'=>false,
                'expanded'=>true
            ])
            ->add('precisionCom',TextareaType::class,[
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReponseAo::class,
        ]);
    }
}
