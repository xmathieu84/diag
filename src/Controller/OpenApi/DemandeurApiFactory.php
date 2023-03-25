<?php

namespace App\Controller\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

class DemandeurApiFactory implements \ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated=$decorated;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $pathItem = $openApi->getPaths()->getPath('/api/infoDemandeur');


        $operation = $pathItem->getGet();

        $openApi->getPaths()->addPath('/api/infoDemandeur', $pathItem->withGet(
            $operation->withParameters(array_merge(
                $operation->getParameters(),
                [new Model\Parameter('id', 'query', 'Fields to remove of the output')]
            ))
        ));


        $openApi = $openApi->withInfo((new Model\Info('DIAG-DRONE', 'v2', 'Api DIAG-DRONE'))->withExtensionProperty('info-key', 'Info value'));
        $openApi = $openApi->withExtensionProperty('key', 'Custom x-key value');
        $openApi = $openApi->withExtensionProperty('x-value', 'Custom x-value value');
        return $openApi;
    }
}