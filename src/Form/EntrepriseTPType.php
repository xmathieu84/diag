<?php

namespace App\Form;

use App\Entity\AbonnementGci;
use App\Entity\Demandeur;
use App\Helper\AbonnementGcirepoTrait;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrepriseTPType extends AbstractType
{
    use AbonnementGcirepoTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom')
            ->add('logo',FileType::class,[
                'mapped'=>false,
                'required'=>false
            ])

            ->add('abonnementPub',ChoiceType::class,[
                'choices'=>[
                    'oui'=>'oui',
                    'non'=>'non',
                ],
                'mapped'=>false,
                'expanded'=>true,
                'multiple'=>false
            ])
            ->add('siretTva',TvaSiretType::class)
            ->add('telephon',TelephoneType::class)
            ->add('adresse',AdresseType::class)
            ->add('civilite',CiviliteType::class)
            ->add('user',UserType::class)
            ->add('proBtp',ProBtpType::class,[
                'required'=>false
            ])

        ;
        if ($options['profil']==='proBtp'){
            $builder->add('abonnement',EntityType::class,[
                'class'=>AbonnementGci::class,
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('ag')
                        ->andWhere('ag.profil =:cible')
                        ->setParameter('cible','Entreprise');
                },
                'choice_label'=>function(AbonnementGci $abonnementGci){
                    return sprintf($abonnementGci->getNom()." ".$abonnementGci->getUtlisateur()." utlisateur(s) ".$abonnementGci->getPrix()." € HT par mois. Engagement : 12 mois");
                },
                'mapped'=>false,
                'placeholder'=>'Choisissez votre abonnement',
                'required'=>false

            ]);
        }
        else{
            $builder->add('abonnement',EntityType::class,[
                'class'=>AbonnementGci::class,
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('ag')
                        ->andWhere('ag.profil =:cible')
                        ->setParameter('cible','Entreprise');
                },
                'choice_label'=>function(AbonnementGci $abonnementGci){
                    return sprintf($abonnementGci->getNom()." ".$abonnementGci->getUtlisateur()." utlisateur(s) ".$abonnementGci->getPrix()." € HT par mois. Engagement : 12 mois");
                },
                'mapped'=>false,
                'placeholder'=>'Choisissez votre abonnement'

            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demandeur::class,
            'profil'=>null
        ]);
    }
}
