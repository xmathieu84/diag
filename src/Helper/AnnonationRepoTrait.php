<?php

namespace App\Helper;


use App\Repository\AnnotationRepository;

trait AnnonationRepoTrait{

    protected AnnotationRepository $annotationRepository;

    /**
     * @param AnnotationRepository $annotationRepository
     * @required
     */
    public function setAnnotationRepo(AnnotationRepository $annotationRepository){
        $this->annotationRepository=$annotationRepository;
    }
}
