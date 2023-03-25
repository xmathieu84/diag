<?php

namespace App\Service;

use App\Entity\Rapport;
use App\Entity\Video;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class Fichier
 * @package App\Service
 */
class Fichier
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Fichier constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {

        $this->manager = $manager;
    }
    /**
     * Deplace et remplace le nom d'un fichier
     *
     * @param [type] $file
     * @param [type] $destination
     * @return string
     */
    public function moveFile($file, $destination,$nom): string
    {
        $fileName = $nom.time(). '.' . $file->guessExtension();
        $file->move($destination, $fileName);

        return $fileName;
    }

    /**
     * @param $video
     * @param Rapport $rapport
     * @param $dossier
     */
    public function saveVideo($video, Rapport $rapport, $dossier,$nomVideoSauve):void
    {
        $nom = $video->getClientOriginalName();
        $nouveauNomVideo = $nomVideoSauve . '-' . time() . '.' . $video->guessExtension();
        $result = $video->move($dossier, $nouveauNomVideo);
        $result = move_uploaded_file($nom, $dossier);
        $video = new Video();
        $video->setNom($nouveauNomVideo)
            ->setRapport($rapport);
        $this->manager->persist($video);
        $this->manager->flush();
    }

    /**
     * @param string $nomFichier
     * @param string $destination
     * @param $file
     * @return string
     */
    public function saveFile(string $nomFichier,string $destination, $file): string
    {
        $fileName = $nomFichier . time().'.' . $file->guessExtension();
        $file->move($destination, $fileName);

        return $fileName;
    }

    /**
     * @param string $base64
     *
     * @return false|resource
     */
    public function decodeBase64(string $base64,$destination){
        $filename_path = time().".jpg";
        $base64_string = str_replace('data:image/png;base64,', '', $base64);
        $base64_string = str_replace(' ', '+', $base64_string);
        $decoded = base64_decode($base64_string);

         file_put_contents($destination.'/'.$filename_path,$decoded,FILE_APPEND);

        return $filename_path;
    }

    public function deplacer($file,$destination){
            copy($file,$destination);
            unlink($file);
    }

    public function convertImage($source, $dst, $width, $height, $quality){
        $image = getImageSize($source);

       if ($image['mime']==='image/jpg' || $image['mime']==='image/jpeg'){
           $tranform = imagecreatefromjpeg($source);
       }else{
           $tranform =imageCreateFromPng($source);
       }
        $imageSize = getimagesize($source) ;
        $imageRessource= @imagecreatefromjpeg($source) ;
        $imageFinal = imagecreatetruecolor($width, $height) ;
        $final = imagecopyresampled($imageFinal, $imageRessource, 0,0,0,0, $width, $height, $imageSize[0], $imageSize[1]) ;
        imagejpeg($imageFinal, $dst, $quality) ;
        if ($image['mime']==='image/jpg' || $image['mime']==='image/jpeg'){
            imagejpeg($imageFinal, $dst, $quality) ;
        }else{
            imagepng($imageFinal, $dst, $quality) ;
        }
    }


}
