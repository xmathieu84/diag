<?php


namespace App\Controller\OpenApi;


use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;



class OpenApiFactory implements \ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface
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

        $pathItem = $openApi->getPaths()->getPath('/api/matchOtd');


        $operation = $pathItem->getPost();

        $openApi->getPaths()->addPath('/api/matchOtd',$pathItem->withPost(
            $operation->withParameters(array_merge(
                $operation->getParameters(),
                [new Model\Parameter('address', 'query', 'Adresse complète')],
                [new Model\Parameter('dateInter', 'query', 'timestamp javascript')],
                [new Model\Parameter('idTypeInter', 'query', "id type d'intervention(aérienne terrestre aquatique)")],
                [new Model\Parameter('idListeInter', 'query', "id liste interventiontion a obtenir avec le listeInterTypeInter->getListeInter")],
            ))

        ));


        $openApi = $openApi->withInfo((new Model\Info('DIAG-DRONE', 'v2', 'Api DIAG-DRONE'))->withExtensionProperty('info-key', 'Info value'));
        $openApi = $openApi->withExtensionProperty('key', 'Custom x-key value');
        $openApi = $openApi->withExtensionProperty('x-value', 'Custom x-value value');
        return $openApi;

    }
}