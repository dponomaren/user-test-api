<?php

namespace App\Controller\Api;

use App\Enum\Serialization\FormatEnum;
use App\Exception\Domain\Api\ApiException;
use App\Exception\Domain\Api\ValidationException;
use App\Exception\Domain\Json\MalformedJsonException;
use App\Model\Response\Api\ApiResponse;
use App\Service\Domain\Core\RestApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CoreRestApiController extends AbstractController
{
    /**
     * @var RestApiService
     */
    protected $restApiService;

    /**
     * @param RestApiService $restApiService
     */
    public function __construct(
        RestApiService $restApiService
    ) {
        $this->restApiService = $restApiService;
    }

    /**
     * Serialize an object, wrapping it within a data block.
     *
     * @param mixed  $dataToBeSerialized
     * @param int    $responseCode
     * @param array  $contexts
     * @param array  $extraHeaders
     * @param string $format
     *
     * @return ApiResponse
     */
    public function serialize(
        $dataToBeSerialized,
        int $responseCode   = Response::HTTP_OK,
        array $contexts     = [],
        array $extraHeaders = [],
        string $format      = FormatEnum::JSON_FORMAT
    ): ApiResponse {
        return $this->restApiService->serialize(
            $dataToBeSerialized,
            $responseCode,
            $contexts,
            $extraHeaders,
            $format
        );
    }

    /**
     * Serialize an object, without wrapping it.
     *
     * @param mixed  $dataToBeSerialized
     * @param int    $responseCode
     * @param array  $contexts
     * @param array  $extraHeaders
     * @param string $format
     *
     * @return ApiResponse
     */
    public function serializeWithoutWrapping(
        $dataToBeSerialized,
        int $responseCode   = Response::HTTP_OK,
        array $contexts     = [],
        array $extraHeaders = [],
        string $format      = FormatEnum::JSON_FORMAT
    ): ApiResponse {
        return $this->restApiService->serializeWithoutWrapping(
            $dataToBeSerialized,
            $responseCode,
            $contexts,
            $extraHeaders,
            $format
        );
    }

    /**
     * @param string $requestData
     * @param array  $contexts
     * @param string $className
     *
     * @return mixed
     *
     * @throws ApiException
     * @throws MalformedJsonException
     */
    public function deserialize(string $requestData, array $contexts = [], string $className = '')
    {
        return $this->restApiService->deserialize(
            $requestData,
            $contexts,
            $className
        );
    }

    /**
     * @param $instance
     *
     * @return bool|ConstraintViolationListInterface
     *
     * @throws ValidationException
     */
    public function validate($instance)
    {
        return $this->restApiService->validate($instance);
    }
}