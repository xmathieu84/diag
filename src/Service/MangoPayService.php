<?php

namespace App\Service;

use App\Entity\Adresse;
use App\Entity\Banque;
use App\Entity\Entreprise;
use DateTimeInterface;
use MangoPay\Address;
use MangoPay\BankAccount;
use MangoPay\BankAccountDetailsIBAN;
use MangoPay\Birthplace;
use MangoPay\BrowserInfo;
use MangoPay\Card;
use MangoPay\CardRegistration;
use MangoPay\Client;
use MangoPay\Hook;
use MangoPay\KycDocument;
use MangoPay\KycDocumentStatus;
use MangoPay\Libraries\Exception;
use MangoPay\Libraries\ResponseException;
use MangoPay\MangoPayApi;
use MangoPay\Money;
use MangoPay\PayIn;
use MangoPay\PayInExecutionDetailsDirect;
use MangoPay\PayInPaymentDetailsBankWire;
use MangoPay\PayInPaymentDetailsCard;
use MangoPay\PayInPaymentType;
use MangoPay\PayOut;
use MangoPay\PayOutPaymentDetailsBankWire;
use MangoPay\Transfer;
use MangoPay\Ubo;
use MangoPay\UboDeclaration;
use MangoPay\UboDeclarationStatus;
use MangoPay\User;
use MangoPay\UserLegal;
use MangoPay\UserNatural;
use MangoPay\Wallet;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MangoPayService.
 */
class MangoPayService
{
    /**
     * @var MangoPay\MangoPayApi
     */
    private $mangoPayApi;

    /**
     * MangoPayService constructor.
     */
    public function __construct()
    {
        $this->mangoPayApi = new MangoPayApi();
        /*
         * Dev
         */
        $this->mangoPayApi->Config->ClientId = 'diagdronetest2';
        $this->mangoPayApi->Config->ClientPassword = '6mixSvFewF1bsm1TkwjOF5wOey1ObUD6wz1fcON2yucYmcQZ6Y';
        $this->mangoPayApi->Config->BaseUrl = 'https://api.sandbox.mangopay.com';
        /*
         * Prod
         */
        // $this->mangoPayApi->Config->ClientId = 'diagdroneprod';
        // $this->mangoPayApi->Config->ClientPassword = 'RzYOz1ftKH2ME0rmSxAx1oPBdSu0e5e1oaqBg26EamweYr8dB8';
        // $this->mangoPayApi->Config->BaseUrl="https://api.mangopay.com";
        /*
         * Cache pour windows
         */
        $this->mangoPayApi->Config->TemporaryFolder = 'D:\\Travail\\diagDrone\\var';
        /*
         * Cache pour centos 7
         */
        // $this->mangoPayApi->Config->TemporaryFolder = '/var/www/vhosts/diag-drone.com/httpdocs/var/cache/mango';
    }

    /**
     * @param string $prenom
     * @param string $nom
     *
     * @throws Exception
     */
    public function getMangoUser($demandeur, string $mail): User
    {
        $mangoUser = new UserNatural();
        $mangoUser->PersonType = 'NATURAL';
        $mangoUser->FirstName = $demandeur->getCivilite()->getPrenom();
        $mangoUser->LastName = $demandeur->getCivilite()->getNom();
        $mangoUser->Birthday = 1409735187;
        $mangoUser->Nationality = 'FR';
        $mangoUser->CountryOfResidence = 'FR';
        $mangoUser->Email = $mail;
        // Send the request
        $mangoUser = $this->mangoPayApi->Users->Create($mangoUser);

        return $mangoUser;
    }

    /**
     * @param $nom
     * @param $prenom
     *
     * @throws Exception
     */
    public function getMangoUserHdd($nom, $prenom, string $mail): User
    {
        $mangoUser = new UserNatural();
        $mangoUser->PersonType = 'NATURAL';
        $mangoUser->FirstName = $prenom;
        $mangoUser->LastName = $nom;
        $mangoUser->Birthday = 1409735187;
        $mangoUser->Nationality = 'FR';
        $mangoUser->CountryOfResidence = 'FR';
        $mangoUser->Email = $mail;
        // Send the request
        $mangoUser = $this->mangoPayApi->Users->Create($mangoUser);

        return $mangoUser;
    }

    public function createWallet(string $IdUser): Wallet
    {
        $wallet = new Wallet();
        $wallet->Owners = [$IdUser];
        $wallet->Description = 'Mon portefeuille DiagDrone';
        $wallet->Currency = 'EUR';
        $wallet->Tag = 'custom meta';
        $Result = $this->mangoPayApi->Wallets->Create($wallet);

        return $Result;
    }

    /**
     * Undocumented function.
     *
     * @return UserLegal $mangoUserLegal
     *
     * @throws Exception
     */
    public function createLegalUser($salarie, Adresse $adresse): UserLegal
    {
        $UserLegal = new UserLegal();
        $UserLegal->HeadquartersAddress = new Address();
        $UserLegal->HeadquartersAddress->AddressLine1 = $adresse->getNumero().' '.$adresse->getNomVoie();
        // $UserLegal->HeadquartersAddress->AddressLine2 = "";
        $UserLegal->HeadquartersAddress->City = $adresse->getVille();
        $UserLegal->HeadquartersAddress->Region = '';
        $UserLegal->HeadquartersAddress->PostalCode = $adresse->getCodePostal();
        $UserLegal->HeadquartersAddress->Country = 'FR';
        if ('auto-entrepreneur' === $salarie->getEntreprise()->getFormJuridique()) {
            $UserLegal->LegalPersonType = 'SOLETRADER';
        } else {
            $UserLegal->LegalPersonType = 'BUSINESS';
        }
        if ('auto-entrepreneur' === $salarie->getEntreprise()->getFormJuridique()) {
            $UserLegal->Name = $salarie->getCivilite()->getPrenom().' '.$salarie->getCivilite()->getNom();
        } else {
            $UserLegal->Name = $salarie->getEntreprise()->getDenomination();
        }

        $UserLegal->LegalRepresentativeAddress = new Address();
        $UserLegal->LegalRepresentativeAddress->AddressLine1 = $adresse->getNumero().' '.$adresse->getNomVoie();
        // $UserLegal->LegalRepresentativeAddress->AddressLine2 = "";
        $UserLegal->LegalRepresentativeAddress->City = $adresse->getVille();
        // $UserLegal->LegalRepresentativeAddress->Region = "";
        $UserLegal->LegalRepresentativeAddress->PostalCode = $adresse->getCodePostal();
        $UserLegal->LegalRepresentativeAddress->Country = 'FR';
        $UserLegal->LegalRepresentativeBirthday = 1463496101;
        $UserLegal->LegalRepresentativeCountryOfResidence = 'FR';
        $UserLegal->LegalRepresentativeNationality = 'FR';
        $UserLegal->LegalRepresentativeEmail = $salarie->getUser()->getEmail();
        $UserLegal->LegalRepresentativeFirstName = $salarie->getEntreprise()->getDirigeant()->getPrenom();
        $UserLegal->LegalRepresentativeLastName = $salarie->getEntreprise()->getDirigeant()->getNom();
        $UserLegal->Email = $salarie->getUser()->getEmail();
        $UserLegal->CompanyNumber = $salarie->getEntreprise()->getSiretTva()->getSiret();
        $Result = $this->mangoPayApi->Users->Create($UserLegal);

        return $Result;
    }

    public function allUser(): User
    {
        $mangoUsers = $this->mangoPayApi->Users->getAll();

        return $mangoUsers;
    }

    public function oneUser(string $idUser): User
    {
        $mangoUser = $this->mangoPayApi->Users->get($idUser);

        return $mangoUser;
    }

    public function bankDD()
    {
        try {
            $bank = new BankAccount();
            $bank->Type = 'IBAN';
            $bank->UserId = 'dddev';
            $bank->OwnerName = 'diagDrone';
            $bank->OwnerAddress = new Address();
            $bank->OwnerAddress->AddressLine1 = '31 rue de la cerisaie';
            $bank->OwnerAddress->City = 'Gargas';
            $bank->OwnerAddress->PostalCode = '84400';
            $bank->OwnerAddress->Country = 'FR';
            $bank->Details = new BankAccountDetailsIBAN();
            $bank->Details->IBAN = 'FR7611306000844811719272861';
            $bank->Details->BIC = 'agrifrpp813';
            $bank->Active = true;
            $Result = $this->mangoPayApi->Users->CreateBankAccount('dddev', $bank);
        } catch (\Throwable $th) {
        }

        return $Result;
    }

    /**
     * @param string $idWallet
     */
    public function oneWallet($idWallet): Wallet
    {
        $wallet = $this->mangoPayApi->Wallets->get($idWallet);

        return $wallet;
    }

    /**
     * Undocumented function.
     *
     * @param $userId
     */
    public function createBanqueIban($userId, string $nom, Adresse $adresse, Banque $banque): BankAccount
    {
        $bank = new BankAccount();
        $bank->Type = 'IBAN';
        $bank->UserId = $userId;
        $bank->OwnerName = $nom;
        $bank->OwnerAddress = new Address();
        $bank->OwnerAddress->AddressLine1 = $adresse->getNumero().' '.$adresse->getNomVoie();
        $bank->OwnerAddress->City = $adresse->getVille();
        $bank->OwnerAddress->PostalCode = $adresse->getCodePostal();
        $bank->OwnerAddress->Country = 'FR';
        $bank->Details = new BankAccountDetailsIBAN();
        $bank->Details->IBAN = $banque->getIban();
        $bank->Details->BIC = $banque->getBic();
        $bank->Active = true;
        $Result = $this->mangoPayApi->Users->CreateBankAccount($userId, $bank);

        return $Result;
    }

    /**
     * Undocumented function.
     *
     * @param string $idBanque
     * @param string $idUser
     */
    public function getBanqueUser($idBanque, $idUser): BankAccount
    {
        return $this->mangoPayApi->Users->GetBankAccount($idUser, $idBanque);
    }

    /**
     * Enrgistre une carte de paiement. Etape 1 du module de paiement.
     */
    public function createCard(string $idUser, string $cardType): CardRegistration
    {
        $card = new CardRegistration();
        $card->UserId = $idUser;
        $card->Currency = 'EUR';
        $card->CardType = $cardType;
        $result = $this->mangoPayApi->CardRegistrations->Create($card);

        return $result;
    }

    /**
     * Créer une carte.
     *
     * @param string $cardId
     * @param string $RegistrationData
     *
     * @return CardRegistration
     */
    public function validateCard($cardId, $RegistrationData)
    {
        $card = new CardRegistration();
        $card->Id = $cardId;
        $card->RegistrationData = 'data='.$RegistrationData;
        $result = $this->mangoPayApi->CardRegistrations->update($card);

        return $result;
    }

    /**
     * Effectuer un paiement.
     *
     * @return PayIn
     */
    public function createDirectPayIn(Request $request,string $walletId,
                                      CardRegistration $card, float $montant,
                                      float $frais, string $url, array $content)
    {
        $directPayIn = new PayIn();
        $directPayIn->CreditedWalletId = $walletId;
        $directPayIn->AuthorId = $card->UserId;
        $directPayIn->DebitedFunds = new Money();
        $directPayIn->DebitedFunds->Amount = $montant * 100;
        $directPayIn->DebitedFunds->Currency = 'EUR';
        $directPayIn->Fees = new Money();
        $directPayIn->Fees->Amount = $frais * 100;
        $directPayIn->Fees->Currency = 'EUR';
        $directPayIn->PaymentDetails = new PayInPaymentDetailsCard();
        $directPayIn->PaymentDetails->CardId = $card->CardId;
        $directPayIn->PaymentDetails->CardType = $card->CardType;
        $directPayIn->PaymentDetails->BrowserInfo = new BrowserInfo();
        $directPayIn->PaymentDetails->BrowserInfo->AcceptHeader = $request->headers->get('accept');
        $directPayIn->PaymentDetails->BrowserInfo->Language = $content['langage'];
        $directPayIn->PaymentDetails->BrowserInfo->UserAgent = $content['userAgent'];
        $directPayIn->PaymentDetails->BrowserInfo->JavascriptEnabled = true;
        $directPayIn->PaymentDetails->BrowserInfo->JavaEnabled = $content['javaActif'];
        $directPayIn->PaymentDetails->BrowserInfo->ColorDepth = $content['color'];
        $directPayIn->PaymentDetails->BrowserInfo->ScreenHeight = $content['height'];
        $directPayIn->PaymentDetails->BrowserInfo->ScreenWidth = $content['width'];
        $directPayIn->PaymentDetails->BrowserInfo->TimeZoneOffset = $content['timeZone'];
        $directPayIn->PaymentDetails->IpAddress = $request->getClientIp();
        $directPayIn->ExecutionDetails = new PayInExecutionDetailsDirect();
        $directPayIn->ExecutionDetails->SecureMode = 'FORCE';
        $directPayIn->ExecutionDetails->SecureModeReturnURL = $url;

        try {
            $result = $this->mangoPayApi->PayIns->Create($directPayIn);
        } catch (\Exception $th) {
            $result = $th;
        }

        return $result;
    }

    /**
     * Desactive une carte après paiement.
     *
     * @return void
     */
    public function deactivateCard(string $idCard)
    {
        $card = new Card();
        $card->Id = $idCard;
        $card->Active = false;

        $this->mangoPayApi->Cards->Update($card);
    }

    /**
     * Information client DiagDrone.
     */
    public function createClient(): Client
    {
        $result = $this->mangoPayApi->Clients->Get();

        return $result;
    }

    public function creatBankWireDirectPayIn(int $idDemandeur, string $idWallet, float $montant, float $commission): PayIn
    {
        $bankwire = new PayIn();

        $bankwire->PaymentType = PayInPaymentType::BankWire;
        $bankwire->ExecutionType = 'DIRECT';
        $bankwire->PaymentDetails = new PayInPaymentDetailsBankWire();
        $bankwire->AuthorId = $idDemandeur;
        $bankwire->ExecutionDetails = new PayInExecutionDetailsDirect();
        $bankwire->CreditedWalletId = $idWallet;
        $bankwire->DeclaredDebitedFunds = new Money();
        $bankwire->DeclaredDebitedFunds->Currency = 'EUR';
        $bankwire->DeclaredDebitedFunds->Amount = $montant * 100;
        $bankwire->DeclaredFees = new Money();
        $bankwire->DeclaredFees->Currency = 'EUR';
        $bankwire->DeclaredFees->Amount = $commission;

        return $this->mangoPayApi->PayIns->Create($bankwire);
    }

    /**
     * Undocumented function.
     *
     * @param $user
     */
    public function createPayOut($user, float $montant): PayOut
    {
        $payOut = new PayOut();
        $payOut->AuthorId = $user->getMangoPayId();
        $payOut->DebitedFunds = new Money();
        $payOut->DebitedFunds->Currency = 'EUR';
        $payOut->DebitedFunds->Amount = $montant * 100;
        $payOut->Fees = new Money();
        $payOut->Fees->Currency = 'EUR';
        $payOut->Fees->Amount = 0;
        $payOut->DebitedWalletId = $user->getWalletMangoId();
        $payOut->MeanOfPaymentDetails = new PayOutPaymentDetailsBankWire();
        $payOut->MeanOfPaymentDetails->BankAccountId = $user->getBankMangoPay();
        try {
            $result = $this->mangoPayApi->PayOuts->create($payOut);
        } catch (\Exception $th) {
            $result = $th;
        }

        return $result;
    }

    /**
     * Recupere un virement vers le compte d'un OTD.
     *
     * @return void
     */
    public function getPayOut(string $payOut)
    {
        return $this->mangoPayApi->PayOuts->Get($payOut);
    }

    /**
     * Recupere les paiements en faveur d'un OTD.
     *
     * @return void
     */
    public function getPayIn(string $payIn)
    {
        return $this->mangoPayApi->PayIns->Get($payIn);
    }

    /**
     * Creation du KYC pour les OTD.
     *
     * @return KycDocument
     *
     * @throws Exception
     */
    public function createKYC(string $userId, File $file, string $type)
    {
        $kyc = new KycDocument();
        $kyc->Type = $type;
        $resultKyc = $this->mangoPayApi->Users->CreateKycDocument($userId, $kyc);
        $resultPage = $this->mangoPayApi->Users->CreateKycPageFromFile($userId, $resultKyc->Id, $file);

        $kycFinal = new KycDocument();
        $kycFinal->Id = $resultKyc->Id;
        $kycFinal->Status = KycDocumentStatus::ValidationAsked;
        try {
            $result = $this->mangoPayApi->Users->UpdateKycDocument($userId, $kycFinal);
        } catch (\Exception $e) {
            $result = $e;
        }
        try {
            $hookAccepte = new Hook();
            $hookAccepte->EventType = 'KYC_SUCCEEDED';
            $hookAccepte->Url = 'https://diag-drone.com/accordKyc';
            $this->mangoPayApi->Hooks->Create($hookAccepte);
            $hookRefused = new Hook();
            $hookRefused->EventType = 'KYC_FAILED';
            $hookRefused->Url = 'https://diag-drone.com/refusKyc';
            $this->mangoPayApi->Hooks->Create($hookRefused);
        } catch (\Exception $e) {
        }

        return $result;
    }

    /**
     * @return UboDeclaration
     *
     * @throws ResponseException
     */
    public function createUBO(string $userId, Entreprise $entreprise, Adresse $adresse, DateTimeInterface $birthday, string $villeNaissance)
    {
        $resultUboDeclaration = $this->mangoPayApi->UboDeclarations->Create($userId);
        $ubo = new Ubo();
        $ubo->FirstName = $entreprise->getDirigeant()->getPrenom();
        $ubo->LastName = $entreprise->getDirigeant()->getNom();
        $ubo->Address = new Address();
        $ubo->Address->AddressLine1 = $adresse->getNumero().' '.$adresse->getNomVoie();
        $ubo->Address->City = $adresse->getVille();
        $ubo->Address->PostalCode = $adresse->getCodePostal();
        $ubo->Address->Country = 'FR';
        $ubo->Nationality = 'FR';
        $ubo->Birthday = $birthday->getTimestamp();
        $ubo->Birthplace = new Birthplace();
        $ubo->Birthplace->Country = 'FR';
        $ubo->Birthplace->City = $villeNaissance;
        $resultUbo = $this->mangoPayApi->UboDeclarations->CreateUbo($userId, $resultUboDeclaration->Id, $ubo);
        $Ubo = new Ubo();
        $Ubo->Id = $resultUbo->Id;
        $Ubo->Status = UboDeclarationStatus::ValidationAsked;

        try {
            $HookRefused = new Hook();
            $HookRefused->EventType = 'UBO_DECLARATION_REFUSED';
            $HookRefused->Url = 'https://diag-drone.com/refusUbo';
            $this->mangoPayApi->Hooks->Create($HookRefused);
        } catch (\Exception $e) {
        }
        try {
            $hook = new Hook();
            $hook->EventType = 'UBO_DECLARATION_VALIDATED';
            $hook->Url = 'https://diag-drone.com/accordUbo';
            $this->mangoPayApi->Hooks->Create($hook);
        } catch (\Exception $e) {
        }

        try {
            $result = $this->mangoPayApi->UboDeclarations->SubmitForValidation($userId, $resultUboDeclaration->Id);
        } catch (Exception $e) {
        }

        return $result;
    }

    /**
     * Undocumented function.
     *
     * @param User $user
     */
    public function PayWithWallet(\App\Entity\User $user): Transfer
    {
        try {
            $Transfer = new Transfer();
            $Transfer->AuthorId = $user->getMangoPayId();
            $Transfer->CreditedUserId = $user->getMangoPayId();
            $Transfer->DebitedFunds = new Money();
            $Transfer->DebitedFunds->Currency = 'EUR';
            $Transfer->DebitedFunds->Amount = 3500;
            $Transfer->Fees = new Money();
            $Transfer->Fees->Currency = 'EUR';
            $Transfer->Fees->Amount = 0;
            $Transfer->DebitedWalletId = $user->getWalletMangoId();
            $Transfer->CreditedWalletId = '95908834';
            $Transfer->Tag = 'Paiment cerfa';
            $result = $this->mangoPayApi->Transfers->Create($Transfer);
        } catch (\Throwable $th) {
        }

        return $result;
    }

    public function viewPayIn($idTransaction)
    {
        return $this->mangoPayApi->PayIns->Get($idTransaction);
    }

    public function getKyc($idDoc, $idUser)
    {
        $doc = $this->mangoPayApi->Users->GetKycDocument($idUser, $idDoc);

        return $doc;
    }
}
