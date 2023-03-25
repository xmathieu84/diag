<?php
namespace App\Helper;

use App\Repository\DocSousDossierRepository;

trait DocSouDossierRepoTrait{

    protected DocSousDossierRepository $docSousDossierRepository;

    /**
     * @param DocSousDossierRepository $docSousDossierRepository
     * @required
     */
    public function setDocRepo(DocSousDossierRepository $docSousDossierRepository){
        $this->docSousDossierRepository=$docSousDossierRepository;
    }
}