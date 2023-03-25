<?php

namespace App\Helper;

use Knp\Component\Pager\PaginatorInterface;

trait PaginatorTrait
{

    protected $paginator;

    /**
     * @required
     *
     * @param PaginatorInterface $paginator
     * @return void
     */
    public function FunctionName(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }
}
