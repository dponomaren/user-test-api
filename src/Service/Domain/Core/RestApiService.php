<?php

namespace App\Service\Domain\Core;

use App\Enum\Serialization\FormatEnum;
use App\Exception\Domain\Api\ApiException;
use App\Exception\Domain\Api\ValidationException;
use App\Exception\Domain\Json\MalformedJsonException;
use App\Model\Response\Api\ApiResponse;
use App\Service\Domain\Validator\FactoryInterface as ValidatorFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RestApiService
{
    /**
     * @var ValidatorFactoryInterface
     */
    protected $validatorFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param ValidatorFactoryInterface $validatorFactory
     * @param SerializerInterface       $serializer
     * @param ValidatorInterface        $validator
     */
    public function __construct(
        ValidatorFactoryInterface $validatorFactory,
        SerializerInterface       $serializer,
        ValidatorInterface        $validator
    ) {
        $this->validatorFactory     = $validatorFactory;
        $this->serializer           = $serializer;
        $this->validator            = $validator;
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
        array $contexts     = ['get'],
        array $extraHeaders = [],
        string $format      = FormatEnum::JSON_FORMAT
    ): ApiResponse {
        $dataToBeSerialized = [
            'data' => $dataToBeSerialized,
        ];

        return $this->serializeWithoutWrapping($dataToBeSerialized, $responseCode, $contexts, $extraHeaders, $format);
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
    public function serializeErrors(
        $dataToBeSerialized,
        int $responseCode   = Response::HTTP_OK,
        array $contexts     = ['get'],
        array $extraHeaders = [],
        string $format      = FormatEnum::JSON_FORMAT
    ): ApiResponse {
        $dataToBeSerialized = [
            'data'   => [],
            'errors' => $dataToBeSerialized,
        ];

        return $this->serializeWithoutWrapping($dataToBeSerialized, $responseCode, $contexts, $extraHeaders, $format);
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
        int    $responseCode   = Response::HTTP_OK,
        array  $contexts       = ['get'],
        array  $extraHeaders   = [],
        string $format         = FormatEnum::JSON_FORMAT
    ): ApiResponse {
        $responseData = null;

        $responseData = $this->serializer->serialize(
            $dataToBeSerialized,
            $format,
            SerializationContext::create()->setGroups($contexts)
        );

        $mainHeaders   = [
            'Content-Type' => 'application/json',
        ];
        $actualHeaders = $mainHeaders + $extraHeaders;

        return new ApiResponse($responseData, $responseCode, $actualHeaders);
    }

    /**
     *
     * @param string $requestData
     * @param array  $contexts
     * @param string $className
     * @param string $type
     *
     * @return mixed
     *
     * @throws ApiException
     * @throws MalformedJsonException
     */
    public function deserialize(
        string $requestData,
        array $contexts = [],
        string $className = '',
        string $type = FormatEnum::JSON_FORMAT
    ) {
        if (false === $this->validatorFactory->getValidator($type)->isValid($requestData)) {
            throw new MalformedJsonException();
        }

        if (!class_exists($className)) {
            throw new ApiException('Class not exists', 500);
        }

        try {
            return $this->serializer->deserialize(
                $requestData,
                $className,
                FormatEnum::JSON_FORMAT,
                DeserializationContext::create()->setGroups($contexts)
            );
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Validate an instance
     *
     * @param $instance
     *
     * @return bool|ConstraintViolationListInterface
     *
     * @throws ValidationException
     */
    public function validate($instance)
    {
        $validationErrors = $this->validator->validate($instance, $constraints = null, $validationGroups = null);

        if (0 !== $validationErrors->count()) {
            throw new ValidationException($validationErrors);
        }

        return true;
    }
}