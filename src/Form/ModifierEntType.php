<?php

namespace App\Form;

use App\Form\AdresseType;
use App\Entity\Entreprise;
use App\Form\AssuranceEntType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ModifierEntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder


            ->add('adresse', AdresseType::class)
            ->add('telephone', TelephoneType::class)

            ->add('logo', FileType::class, [
                'required' => false,

            ])



            ->add('dirigeant', CiviliteType::class);



        $builder->get('logo')->addModelTransformer(new CallbackTransformer(
            function ($logo) {
                return null;
            },
            function ($logo) {
                return $logo;
            }
        ));;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
