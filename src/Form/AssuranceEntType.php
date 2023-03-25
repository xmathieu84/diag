<?php

namespace App\Form;

use App\Entity\Assurances;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AssuranceEntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder


            ->add('nom_compagnie')
            ->add('ass_pro_fichier', FileType::class, [
                'mapped' => true,

            ])

            ->add('ass_pro');

        $builder->get('ass_pro_fichier')->addModelTransformer(new CallbackTransformer(
            function ($ass_pro_fichier) {
                return null;
            },
            function ($ass_pro_fichier) {
                return $ass_pro_fichier;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Assurances::class,
        ]);
    }
}
