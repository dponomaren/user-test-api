<?php

namespace App\Listener\Domain;

use App\Service\Domain\Core\RestApiService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DomainApiListener
{
    /**
     * @var RestApiService
     */
    protected $restApiService;

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @param RestApiService        $restApiService
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        RestApiService        $restApiService,
        ParameterBagInterface $parameterBag
    ) {
        $this->restApiService = $restApiService;
        $this->parameterBag   = $parameterBag;
    }

    public function isProduction(): bool
    {
        return $this->parameterBag->get('app.env') == 'prod';
    }

    public function isDocEnable(): bool
    {
        return (bool)$this->parameterBag->get('app.doc');
    }
}