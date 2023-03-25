<?php


namespace App\Service;


use http\Client;

class Yousign
{
    protected $urlBase;
    protected  $token;
    protected $lienWebHook;
    public function __construct()
    {
        //$this->urlBase = 'https://staging-api.yousign.com';
        //$this->token = '4bb9755a0f3c6b28a4bef18581fa7b21';
        //$this->lienWebHook = "https://diag-drone.com/retourSepa";

        $this->urlBase = "https://api.yousign.com";
        $this->token = "1f13cb0a00576036557ece302a0e20cd";
        $this->lienWebHook = "https://diag-drone.xyz/retourSepa";
    }

    public function getUser(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase.'/users',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ),
        ));

       $response =  curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function notifEmailSepa($idUser,$nom,$prenom,$mail,$telephone,$file)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->urlBase.'/procedures',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
    "name": "signature SEPA",
    "description": "Mail de signature",
    "members": [
        {
            "firstname": "'.$prenom.'",
            "lastname": "'.$nom.'",
            "email": "'.$mail.'",
            "phone": "'.$telephone.'",
            "fileObjects": [
                {
                    "file": "'.$file.'",
                    "page": 1,
                    "position": "300,90,550,150",
                    "mention": "Lu et approuvé",
                    "mention2": "Signed par '.$nom.' '.$prenom.'"
                }
            ]
        }
    ],
    "config": {
        "email": {
            "member.started": [
                {
                    "subject": "Signature éléctronique de votre Mandat SEPA",
                    "message": "Bonjour <tag data-tag-type=\\"string\\" data-tag-name=\\"recipient.firstname\\"></tag> <tag data-tag-type=\\"string\\" data-tag-name=\\"recipient.lastname\\"></tag>, <br><br>Vous êtes invités à signer votre mandat de prélèvement SEPA, cliquez sur le lien suivant pour y accéder: <tag data-tag-type=\\"button\\" data-tag-name=\\"url\\" data-tag-title=\\"Signer le mandat\\">Signer le mandat</tag>",
                    "to": ["@member"]
                }
            ]
            },
            "webhook":{
                "member.finished": [
                    {
                        "url": "https://diag-drone.com/retourSepa/'.$idUser.'",
                        "method": "GET",
                        "headers": {
                            "X-Custom-Header": "Yousign Webhook - Test value"
                        }
                    }
                ]
            }
        
    }
}',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /**
     * @throws \JsonException
     */
    public function createFile($file, $filename){
        $curl = curl_init();
        $result = file_get_contents($file);
        $data = base64_encode($result);
        $content = [
            'name'=>$filename,
            'content'=>$data
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase.'/files',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($content, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    public function telechargerFichier($idFile){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->urlBase.$idFile.'/download',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function notifEmailMandatCerfa($idMandat,$nom,$prenom,$mail,$telephone,$file){
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->urlBase.'/procedures',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
    "name": "signature SEPA",
    "description": "Mail de signature",
    "members": [
        {
            "firstname": "'.$prenom.'",
            "lastname": "'.$nom.'",
            "email": "'.$mail.'",
            "phone": "'.$telephone.'",
            "fileObjects": [
                {
                    "file": "'.$file.'",
                    "page": 4,
                    "position": "351,553,552,670",
                    "mention": "Lu et approuvé",
                    "mention2": "Signed par '.$nom.' '.$prenom.'"
                }
            ]
        }
    ],
    "config": {
        "email": {
            "member.started": [
                {
                    "subject": "Signature éléctronique de votre contra de mandat",
                    "message": "Bonjour <tag data-tag-type=\\"string\\" data-tag-name=\\"recipient.firstname\\"></tag> <tag data-tag-type=\\"string\\" data-tag-name=\\"recipient.lastname\\"></tag>, <br><br>Vous êtes invités à signer votre mandat de prélèvement SEPA, cliquez sur le lien suivant pour y accéder: <tag data-tag-type=\\"button\\" data-tag-name=\\"url\\" data-tag-title=\\"Signer le mandat\\">Signer le contrat de mandat</tag>",
                    "to": ["@member"]
                }
            ]
            },
            "webhook":{
                "member.finished": [
                    {
                        "url": "https://diag-drone.com/retourMandatCerfa/'.$idMandat.'",
                        "method": "GET",
                        "headers": {
                            "X-Custom-Header": "Yousign Webhook - Test value"
                        }
                    }
                ]
            }
        
    }
}',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$this->token,
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

}