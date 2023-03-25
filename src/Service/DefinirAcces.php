<?php

namespace App\Service;

use App\Entity\Entreprise;
use App\Repository\EtatAbonnementRepository;
use App\Repository\UserRepository;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class DefinirAcces
{
    /**
     * @var UserPasswordHasherInterface
     */
  private  UserPasswordHasherInterface $encoder;
    /**
     * @var EtatAbonnementRepository
     */
  private $etatAbonnementRepository;
    /**
     * @var UserRepository
     */
  protected UserRepository $userRepository;

    /**
     * DefinirAcces constructor.
     * @param UserPasswordHasherInterface $encoder
     * @param EtatAbonnementRepository $etatAbonnementRepository
     * @param UserRepository $userRepository
     */
  public function __construct(UserPasswordHasherInterface $encoder, EtatAbonnementRepository $etatAbonnementRepository, UserRepository $userRepository)
  {
    $this->encoder = $encoder;
    $this->etatAbonnementRepository = $etatAbonnementRepository;
    $this->userRepository = $userRepository;
  }

    /**
     * Génère un identifiant unique et hash le mot de passe
     *
     * @param  $user
     * @return string
     */
  public function identPass($user): string
  {

      return $this->encoder->hashPassword($user, $user->getPassword());
  }


    /**
     * Defini le nom de l'abonnement souscrit pour les différents acces aux pages
     *
     * @param Entreprise $entreprise
     * @return string|null
     */
  public function accesSite(Entreprise $entreprise):?string
  {
    $etat = $this->etatAbonnementRepository->findBy(['entreprise' => $entreprise, 'abonne' => true], ['id' => 'DESC']);
    if (!empty($etat)) {
      $nomAbonnement = $etat[0]->getAbonnement()->getNom();
    } else {
      $nomAbonnement = null;
    }

    return $nomAbonnement;
  }
}
