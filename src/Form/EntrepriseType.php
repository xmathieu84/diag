<?php

namespace App\Form;

use App\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\AdresseType;
use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('denomination', TextType::class, ['required' => true])
            ->add('enseigne')
            ->add('form_juridique', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Forme juridique *' => "",
                    'SARL' => 'SARL',
                    'SA' => 'SA',
                    'SAS' => 'SAS',
                    'SASU' => 'SASU',
                    'EURL' => 'EURL',
                    'EIRL' => 'EIRL',
                    'Auto-entrepreneur' => 'auto-entrepreneur',

                ]
            ])
            ->add('siretTva', SiretTvaType::class, ['required' => true])


            ->add('adresse', AdresseType::class)
            ->add('telephone', TelephoneType::class, [
                'attr' => [
                    'pattern' => '/[0-9]{10}/'
                ]
            ])

            ->add('logo', FileType::class, [
                'required' => false
            ])



            ->add('dirigeant', CiviliteType::class)


            ->add('user', UserType::class,['mapped'=>false]);
        $builder->get('logo')->addModelTransformer(new CallbackTransformer(
            function ($logo) {
                return null;
            },
            function ($logo) {
                return $logo;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
