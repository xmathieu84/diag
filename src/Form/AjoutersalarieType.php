<?php

namespace App\Form;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Salarie;

use App\Form\AdresseType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;


use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AjoutersalarieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder

            ->add('adresse', AdresseType::class)
            ->add('telephone', TelephoneType::class)
            ->add('civilite', CiviliteType::class)
            ->add('user', UserType::class);
            if ($options['user']->hasRole('ROLE_ODI')){

                $builder->add('copier',ChoiceType::class,[
                    'choices'=>[
                        'Oui'=>'Oui',
                        'Non'=>'Non'
                    ],'expanded'=>true,'multiple'=>false,'mapped'=>false
                ])
                    ->add('mission',CheckboxType::class,[
                        'label'=>'Missions',
                        'mapped'=>false
                    ])
                    ->add('tarif',CheckboxType::class,[
                        'label'=>'Tarif',
                        'mapped'=>false,
                        'required'=>false
                    ])
                    ->add('pack',CheckboxType::class,[
                        'label'=>'Packs',
                        'mapped'=>false,
                        'required'=>false
                    ])
                    ->add('salarie',EntityType::class,[
                        'class'=>Salarie::class,
                        'query_builder'=>function(EntityRepository $er) use ($user){
                            return $er->createQueryBuilder('s')
                                ->andWhere('s.entreprise = :entreprise')
                                ->setParameter('entreprise',$user->getSalarie()->getEntreprise())
                                ;
                        },
                        'mapped'=>false,
                        'choice_label'=> function($salarie){
                            return $salarie->getCivilite()->getPrenom().' '.$salarie->getCivilite()->getNom();
                        },
                        'placeholder'=>'Choisissez votre ODI'
                    ])
                    ;
            }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Salarie::class,
            'user'=>User::class
        ]);
    }
}
