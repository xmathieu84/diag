<?php

namespace App\Form;

use App\Entity\Demandeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class DemandeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite', CiviliteType::class)
            ->add('cpAmbassadeur',HiddenType::class,[
                'required'=>false,
                'mapped'=>false
            ])
            ->add('adresse', AdresseType::class)
            ->add('telephon', TelephoneType::class)
            ->add('profilInsti', ChoiceType::class, [
                'placeholder' => "Type d'institution",
                'choices' => [
                    'Commune' => 'Commune',
                    'Communauté de communes (ou équivalent)' => 'Communauté de communes',
                    'Département' => 'Département',
                    'Région' => 'Région',
                    'Communautés religieuses '=>'Communautés religieuses',
                    'Établissement public (musée, parc naturel...)'=>"Établissement public",
                    "Société d'Économie Mixte (SEM)"=>"Société d'Économie Mixte",
                    "Société Coopérative d'Intérêt Collectif (SCIC)"=>"Société Coopérative d'Intérêt Collectif"
                ]
                , 'mapped' => false
            ])
            ->add('codePromo',TextType::class,[
                'required'=>false,
                'mapped'=>false
            ])
            ->add('infoCodeProm',ChoiceType::class,[
                'choices'=>[
                    'Je possède un code promotionnel'=>true
                ],
                'mapped'=>false,
                'required'=>false,
                'expanded'=>true,
                'multiple'=>true

            ])
            ->add('profilGc', ChoiceType::class, [
                'placeholder'=>'Profil',
                'choices' => [
                    'Notaire' => 'Notaire',
                    "Syndicat de copropriété"=>"Syndicat de co-propriété",
                    'Syndic de co-propriété' => 'Syndic de co-propriété',
                    'Gestionnaire d\'immeuble' => 'Gestionnaire d\'immeuble',
                    'Agent Immobilier' => 'Agent Immobilier',
                    'Compagnie d’assurance, agent général, courtier en assurance ' => 'Compagnie d’assurance, agent général, courtier en assurance ',
                    "Bureau d’étude "=>"Bureau d’étude ",
                    "Entreprise BTP et autres "=>"Entreprise BTP et autres",
                    "Huissier"=>"Huissier",
                    "Association de droit privé"=>"Association de droit privé",
                    "Bailleurs privés - SCI et autres (Moins de 50 lots-logement-batiments)"=>"Bailleurs privés - SCI et autres (Moins de 50 lots-logement-batiments)",
                    "Bailleurs pro importants-foncières... (de 50 à 200 lots)"=>"Bailleurs pro importants-foncières... (de 50 à 200 lots)",
                    "Bailleurs pro importants-foncières... (plus de 200 lots)"=>"Bailleurs pro importants-foncières... (plus de 200 lots)",
                    "Bailleurs sociaux (moins de 200 lots)"=>"Bailleurs sociaux (moins de 200 lots)",
                    "Bailleurs sociaux (plus de 200 lots)"=>"Bailleurs sociaux (plus de 200 lots)"
                ],
                'mapped' => false
            ])
            ->add('habitant',ChoiceType::class,[
                'choices'=>[
                    "Jusqu'à 300 habitants"=>"200",
                    "Jusqu'à 500 habitants"=>"499",
                    "Jusqu'à 1 000 habitants"=>"999",
                    "Jusqu'à 5 000 habitants"=>"4999",
                    "Jusqu'à 10 000 habitants"=>"9999",
                    "Jusqu'à 50 000 habitants"=>"49999",
                    "Jusqu'à 100 000 habitants"=>"99999",
                    "Jusqu'à 500 000 habitants"=>"499999",
                    "Plus de 500 000 habitants"=>"500001",
                ],
                'required'=>false,
                'mapped'=>false
            ])
            ->add('utilisateur',ChoiceType::class,[
                'placeholder'=>'Utlisateur',
                'choices'=>[

                    '1 utilisateur'=>1,
                    '2 utilisateurs'=>2
                ],
                'mapped'=>false,
                'required'=>false

            ])
            ->add('profil', ChoiceType::class, [
                'choices' => [
                    'Profil' => null,
                    'Particulier propriétaire' => 'Particulier propriétaire',
                    'Entreprise' => 'Entreprise',
                    'Institutionnel (Mairie, agglomération, département) '=>'institutionnel  (Mairie, agglomération, département)',
                    'Notaire' => 'Notaire',
                    'Syndic de co-propriété' => 'Syndic de co-propriété',
                    'Gestionnaire d\'immeuble' => 'Gestionnaire d\'immeuble',
                    'Agent Immobilier' => 'Agent Immobilier',
                    'Diagnostiqueur, opérateur télé-pilote '=>'Diagnostiqueur, opérateur télé-pilote ',
                    'Compagnie d’assurance, agent général, courtier en assurance ' => 'Compagnie d’assurance, agent général, courtier en assurance ',
                    "Bureau d’étude "=>"Bureau d’étude ",
                    "Entreprise BTP et autres "=>"Entreprise BTP et autres",
                    "Huissier"=>"Huissier",
                    "Association de droit privé"=>"Association de droit privé"


                ],
                'mapped' => false
            ])
            ->add('nom', TextType::class, [
                'required' => false
            ])

            ->add('siretTva', SiretTvaType::class, ['required' => false])

            ->add('user', UserType::class,['mapped'=>false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Demandeur::class,
        ]);
    }
}
