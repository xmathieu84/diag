<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

trait RequestTrait
{


    protected  $request;

    /**
     * @param RequestStack $request
     * @required
     */
    public function setRequest(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }
}
