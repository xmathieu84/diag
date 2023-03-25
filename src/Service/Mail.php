<?php

namespace App\Service;

use App\Entity\Abonnements;
use App\Entity\Agent;
use App\Entity\AppelOffre;
use App\Entity\Intervention;
use App\Entity\Salarie;
use App\Repository\SalarieRepository;

use DateTimeInterface;
use phpDocumentor\Reflection\File;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;

/**
 * Class Mail
 * @package App\Service
 */
class Mail
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var SalarieRepository
     */
    private $salarieRepository;
    private $retourMail;

    /**
     * Mail constructor.
     * @param MailerInterface $mailer
     * @param SalarieRepository $salarieRepository
     */
    public function __construct(MailerInterface $mailer, SalarieRepository $salarieRepository)
    {
        $this->mailer = $mailer;
        $this->salarieRepository = $salarieRepository;
        $this->retourMail = "coquard.dominique@gmail.com";
    }

    /**
     * Envoie du mail de confirmation de l'inscription
     *
     *
     *
     * @param string $activation
     * @param string $adresseMail
     * @return void
     * @throws TransportExceptionInterface
     */
    public function confirmerMail(string $activation,string $adresseMail):void
    {
        $email = (new TemplatedEmail())
            ->from('inscription@diag-drone.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject('Activation de votre compte')
            ->htmlTemplate("email/templateMail/confirmEmail.html.twig")
            ->context([
                'activation' => $activation
            ]);


        $this->mailer->send($email);
    }

    /**
     * Envoie du mail de refus d'une intervention
     *
     *
     * @param array $adresseMail
     * @return void
     * @throws TransportExceptionInterface
     */
    public function mailInter(array $adresseMail):void
    {
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to(...$adresseMail)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject("DIAG DRONE. Une proposition tarifaire est en attente.")
            ->htmlTemplate("email/templateMail/mailInter.html.twig");
        $this->mailer->send($email);
    }

    /**
     *
     * @throws TransportExceptionInterface
     */
    public function mailDemandeIntervention( $adresseMail):void{
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject("DIAG DRONE. Demande d'intervention.")
            ->htmlTemplate("email/templateMail/mailDemandeInter.html.twig");
        $this->mailer->send($email);
    }
    /**
     * Undocumented function
     *
     * @param array $adresseMail
     * @return void
     * @throws TransportExceptionInterface
     */
    public function mailRapport(array $adresseMail)

    {
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to(...$adresseMail)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject("DIAG DRONE. Rapport d'intervention disponible.")
            ->htmlTemplate("email/templateMail/mailRapport.html.twig");
        $this->mailer->send($email);
    }

    /**
     * Undocumented function
     *
     * @param string $adresseMail
     * @param string $codePromo
     * @param string $avantage
     * @param DateTimeInterface $dateFin
     * @return void
     * @throws TransportExceptionInterface
     */
    public function mailPromo(string $adresseMail, string $codePromo, string $avantage, DateTimeInterface $dateFin)
    {
        $email = (new TemplatedEmail())
            ->from('contact@diag-drone.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject('Code Promotionnel')
            ->htmlTemplate("email/templateMail/mailPromo.html.twig")
            ->context([
                'codePromo' => $codePromo,
                'activation' => $avantage,
                'dateFin' => $dateFin
            ]);


        $this->mailer->send($email);
    }

    /**
     * Recuperation d'une adresse mail dans le cas d'un auto-entrepreneur
     *
     * @param int $id
     * @return string
     */
    public function getMail(int $id): string

    {
        $salarie = $this->salarieRepository->findOneBy(['id'=>$id]);

        $adresseMail = $salarie->getUser()->getEmail();


        return $adresseMail;
    }

    /**
     * @param string $adresseMail
     * @param string $codeRecherche
     * @param string $codeUnique
     * @throws TransportExceptionInterface
     */
    public function mailConsultant(string $adresseMail, string $codeRecherche,string $codeUnique,$refDossier = null)
    {

        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject('DIAG DRONE : NOUVEAU DOSSIER DISPONIBLE')
            ->htmlTemplate("email/templateMail/mailConsultant.html.twig")
            ->context(['codeRecherche' => $codeRecherche, 'codeUnique' => $codeUnique,'refDossier'=>$refDossier]);
        try {
            $this->mailer->send($email);
        }
        catch (\Exception $e){

        }
    }

    /**
     * @param string $mailOTD
     * @param string $mailPrefecture
     * @param File $pieceJointe
     * @throws TransportExceptionInterface
     */
    public function mailPJ(string $mailOTD,string $mailPrefecture,string $pieceJointe)
    {

        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($mailPrefecture)
            ->bcc($this->retourMail)
            ->addCc($mailOTD)
            ->attachFromPath($pieceJointe)
            ->subject("Demande d'autorisation de vol")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailPrefecture.html.twig");
        $this->mailer->send($email);
    }

    /**
     * @param string $amilDemandeur
     * @param string $montant
     * @param string $lien
     * @throws TransportExceptionInterface
     */
    public function mailPaiement(string $amilDemandeur, string $montant, string $lien)
    {
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($amilDemandeur)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject("Demande paiement d'une intervention")
            ->htmlTemplate("email/templateMail/mailPaiement.html.twig")
            ->context(['montant' => $montant, 'lien' => $lien]);
        $this->mailer->send($email);
    }

    /**
     * @param string $code
     * @param string $mailEntreprise
     * @param float $montant
     * @throws TransportExceptionInterface
     */
    public function validationAbonnnement(string $code, string $mailEntreprise, float $montant)
    {
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($mailEntreprise)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject('Validation du montant de votre abonnement')
            ->htmlTemplate("email/templateMail/validerAbonnement.html.twig")
            ->context(['montant' => $montant, 'code' => $code]);
        $this->mailer->send($email);
    }

    public function mailPrelevement($pieceJointe,$mois)
    {

        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to('mathieumiranda@hotmail.com')
            ->attach($pieceJointe,'prelevement.pdf','application/pdf')
            ->subject("Liste prelevement")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailPrelevement.html.twig")
            ->context(['mois'=>$mois])      ;

        $this->mailer->send($email);
    }

    /**
     * @param $pieceJointe
     * @param $mail
     * @throws TransportExceptionInterface
     */
    public function mailFactureOTD($pieceJointe,$mail)
    {

        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($mail)
            ->bcc($this->retourMail)
            ->attach($pieceJointe,'facture.pdf','application/pdf')
            ->subject("Facture d'abonnement")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailFactureOTD.html.twig")
            ->context(['mois'=>strftime('%B')]) ;

        $this->mailer->send($email);
    }

    /**
     * @param string $pieceJointe
     * @param string $mail
     * @param Intervention $intervention
     * @throws TransportExceptionInterface
     */
    public function mailFactureInter(string $pieceJointe,string $mail, $intervention,string $type)
    {

        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($mail)
            ->bcc($this->retourMail)
            ->attach($pieceJointe,'facture.pdf','application/pdf')
            ->subject("Facture de commission d'intervention")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/factureInter.html.twig")
            ->context(['intervention'=>$intervention,'type'=>$type]) ;

        $this->mailer->send($email);
    }

    /**
     * @param $mail
     * @throws TransportExceptionInterface
     */
    public function mailPropNonChoisie($mail){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($mail)
            ->bcc($this->retourMail)
            ->subject("DIAG DRONE : proposition non retenue.")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/propNonChoisie.html.twig");

        $this->mailer->send($email);
    }

    /**
     * @param $mail
     * @throws TransportExceptionInterface
     */
    public function mailPropChoisie($mail){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($mail)
            ->bcc($this->retourMail)
            ->subject("DIAG DRONE : votre intervention est confirmée")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/propChoisie.html.twig");

        $this->mailer->send($email);
    }

    /**
     * @param Intervention $intervention
     * @return array
     */
    public function retourneMailInter(Intervention $intervention):array{
        $adresseMail=[];
        if ($intervention->getIntDem()->getUser()){
            $adresseMail[] = $intervention->getIntDem()->getUser()->getEmail();
        } else{
            foreach ($intervention->getIntDem()->getAgents() as $agent) {
                foreach ($agent->getUser()->getRoles() as $role) {
                    if ($role === 'ROLE_MANITOU' || $role === 'ROLE_RESPONSABLE' || $role === 'ROLE_MANITOUGC' || $role === 'ROLE_RESPONSABLEGC') {
                        $adresseMail[] = $agent->getUser()->getEmail();
                    }
                }
            }
        }
        return $adresseMail;
    }

    /**
     * @param string $adresseMail
     * @param  $agent
     * @throws TransportExceptionInterface
     */
    public function mailInterInsti(string $adresseMail, $agent): void
    {
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->subject("Nouvelle demande d'intervention")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/interInsti.html.twig")
            ->context(['agent' => $agent]);

        $this->mailer->send($email);
    }


    public function mailOtdHDD(string $adressseMail,Intervention $intervention){
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adressseMail)
            ->bcc($this->retourMail)
            ->subject("Nouvelle demande d'intervention")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailOtdHdd.html.twig");


        $this->mailer->send($email);
    }

    public function mailAppelOffre(string $adresseMail,AppelOffre $appelOffre){
        $email = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->subject("Votre réponse à un appel d'offre")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailAo.html.twig")
            ->context(['appel' => $appelOffre]);


        $this->mailer->send($email);
    }

    public function mailBank(array $adresseMail,$pieceJointe,$date){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to(...$adresseMail)

            ->attachFromPath($pieceJointe)
            ->subject("Fichier de prélevement DIAG-DRONE")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailBanque.html.twig")
            ->context(['date'=>$date]) ;

        $this->mailer->send($email);
    }

    public function mailChangementABonnement(string $adresseMail,float $montant,Abonnements $abonnements,string $lien){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->subject("Changement d'abonnement")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailChangementAbonnement.html.twig")
            ->context(['montant'=>$montant,'abonnement'=>$abonnements,'activation'=>$lien]) ;

        $this->mailer->send($email);
    }

    public function mailPerdu( string $adresseMail,string $lien){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->subject("Mot de passe perdu")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailPerdu.html.twig")
            ->context(['lien'=>$lien]) ;

        $this->mailer->send($email);
    }

    public function mailDocumentEnt(string $adresseMail){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->subject("Documents nécessaire à l'inscription")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailDocument.html.twig");


        $this->mailer->send($email);
    }

    public function mailFactureAbonnementInsti(string $adresseMail,$pieceJointe):void{
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->attachFromPath($pieceJointe, time(), 'application/pdf')

            //->attach($pieceJointe,time(),'application/pdf')
            ->subject("Facture d'abonnement")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailFatcureInsti.html.twig")
            ;

        $this->mailer->send($email);
    }

    public function mailInscriptionAgent(Agent $agent){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($agent->getUser()->getEmail())
            ->bcc($this->retourMail)
            ->subject("Confirmation de votre inscription")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailInscriptionAgent.html.twig")
            ->context(['activation' => $agent->getUser()->getCodeActivation(),'agent'=>$agent])
        ;

        $this->mailer->send($email);
    }

    public function mailInscriptionSalarie(Salarie $salarie){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($salarie->getUser()->getEmail())
            ->bcc($this->retourMail)
            ->subject("Confirmation de votre inscription")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailInscriptionSalarie.html.twig")
            ->context(['activation' => $salarie->getUser()->getCodeActivation(),'salarie'=>$salarie])
        ;

        $this->mailer->send($email);
    }

    public function accordMangoPay(string $adresseMail){
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to($adresseMail)
            ->subject("Confirmation de votre inscription")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailInscriptionSalarie.html.twig")

        ;

        $this->mailer->send($email);
    }

    public function mailAlerteInsti(array $adresseMail):void{
        $email  = (new TemplatedEmail())
            ->from('contact@drone-diag.com')
            ->to(...$adresseMail)
            ->bcc($this->retourMail)
            ->subject("DIAG DRONE vous alerte")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailAlerteInsti.html.twig")

        ;

        $this->mailer->send($email);
    }

    /**
     * @param array $adresseMail
     * @param string $titre
     * @param array $contenu
     * @param string|null $codePromo
     * @throws TransportExceptionInterface
     */
    public function mailLancement(string $adresseMail,string $titre,array $contenu ,string $codePromo =null ){
        $email  = (new TemplatedEmail())
            ->from('contact@diag-drone.com')
            ->to($adresseMail)
            ->subject($titre)
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailLancement.html.twig")
            ->context(['titre' => $titre,'contenu'=>$contenu,'codePromo'=>$codePromo])


        ;

        $this->mailer->send($email);
    }

    /**
     * @param string $adresseMail
     * @param string $titre
     * @param array $contenu
     * @param string|null $codePromo
     * @param $id
     * @param $type
     * @return void
     * @throws TransportExceptionInterface
     */
    public function mailLancementAutre(string $adresseMail,string $titre,array $contenu ,string $codePromo =null,$id,$type ){

        $email  = (new TemplatedEmail())
            ->from('contact@diag-drone.com')
            ->to($adresseMail)
            ->subject($titre)
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailLancementAutre.html.twig")
            ->context(['titre' => $titre,'contenu'=>$contenu,'codePromo'=>$codePromo,'idMail'=>$id,'type'=>$type])

        ;

        $this->mailer->send($email);

    }


    public function mailuniqueAmba(string $adresseMail){
        $email  = (new TemplatedEmail())
            ->from('contact@diag-drone.com')
            ->to($adresseMail)
            ->subject("Découvrez la 1ère plateforme interactive de réservation de drone !")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailuniqueAmba.html.twig")


        ;
        $this->mailer->send($email);


    }

    public function mailTest($adresseMail){
        $email  = (new TemplatedEmail())
            ->from('contact@diag-drone.com')
            ->to($adresseMail)
            ->subject("Découvrez la 1ère plateforme interactive de réservation de drone !")
            ->priority(TemplatedEmail::PRIORITY_HIGHEST)
            ->htmlTemplate("email/templateMail/mailuniqueAmba.html.twig")


        ;
        $this->mailer->send($email);
    }

    /**
     * @param $adresseMail
     * @param string $sujet
     * @param string $message
     * @throws TransportExceptionInterface
     */
    public function mailContact($adresseMail,string $sujet,string $message){
        $email = (new TemplatedEmail())
            ->from($adresseMail)
            ->bcc($this->retourMail)
            ->to("contact@diag-drone.com")
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject($sujet)
            ->htmlTemplate("email/templateMail/mailContact.html.twig")
            ->context(["message"=>$message,"sujet"=>$sujet]);
        $this->mailer->send($email);
    }

    /**
     * @param string $titre
     * @param string $texte
     * @param string $expediteur
     * @param string $telephone
     * @param string $mailExpe
     * @param string $mailDest
     * @throws TransportExceptionInterface
     */
    public function mailToPartenairePro(string $titre, string $texte ,string $expediteur ,string $telephone , string $mailExpe, string $mailDest ){

        $email = (new TemplatedEmail())
            ->from('contact@diag-drone.com')
            ->to($mailDest)
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject($titre)
            ->htmlTemplate("email/templateMail/mailToParteairePro.html.twig")
            ->context(["message"=>$texte,"sujet"=>$titre,"telephone"=>$telephone,"expe"=>$expediteur,"mail"=>$mailExpe]);
        $this->mailer->send($email);
    }

    public function mailChangeBanque(string $contenu,string $expediteur,string $nomEntreprise){
        $email = (new TemplatedEmail())
            ->from($expediteur)
            ->to("contact@diag-drone.com")
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject("Changement de coordonnées bancaires")
            ->htmlTemplate("email/templateMail/mailChangeBanque.html.twig")
            ->context(["contenu"=>$contenu,"expediteur"=>$expediteur,"entreprise"=>$nomEntreprise]);
        $this->mailer->send($email);
    }

    public function mailDemandeContrainte(){
        $email = (new TemplatedEmail())
            ->from("contact@diag-drone.com")
            ->to('mathieumiranda@hotmail.com', 'marinvincent84000@hotmail.fr', "axel.cloux@gmail.com")
            ->bcc($this->retourMail)
            ->priority(TemplatedEmail::PRIORITY_HIGH)
            ->subject("Nouvelle demande de contrainte pour intervention")
            ->htmlTemplate("email/templateMail/mailContrainte.html.twig");
        $this->mailer->send($email);
    }

    /**
     * @param string $adresseMail
     * @return void
     * @throws TransportExceptionInterface
     */
    public function mailConfirmationInter(string $adresseMail){
        $email = (new TemplatedEmail())
            ->from("contact@diag-drone.com")
            ->to($adresseMail)
            ->bcc($this->retourMail)
            ->priority(Email::PRIORITY_HIGH)
            ->subject("Votre demande d'intervention par drone est confirmée")
            ->htmlTemplate("email/templateMail/mailConfirmationInter.html.twig");
        $this->mailer->send($email);
    }

    public function mailPropAdmin(string $mail,string $reponse):void{
        if ($reponse ==="refusé"){
            $template = "email/templateMail/mailPropAdminRefus.html.twig";
        }
        if ($reponse ==="publié"){
            $template = "email/templateMail/mailPropAdminAccord.html.twig";
        }
        $email = (new TemplatedEmail())
            ->from("contact@diag-drone.com")
            ->to($mail)
            ->bcc($this->retourMail)
            ->priority(Email::PRIORITY_HIGH)
            ->subject("Votre demande")
            ->htmlTemplate($template);
        $this->mailer->send($email);
    }

    public function mailConfirmationProposition(string $mail, string $contenu){
        $email = (new TemplatedEmail())
            ->from("contact@diag-drone.com")
            ->to($mail)
            ->bcc($this->retourMail)
            ->priority(Email::PRIORITY_HIGH)
            ->subject("Confirmation de votre demande")
            ->htmlTemplate("email/templateMail/confirmationDemandeProp.html.twig");
        $this->mailer->send($email);
    }
}
