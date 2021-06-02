<?php

namespace App\Exception\Domain\Api;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends ApiException
{
    /**
     * @var int
     */
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * @var ConstraintViolationListInterface
     */
    protected $constraintViolations;

    /**
     * @param ConstraintViolationListInterface $constraintViolations
     */
    public function __construct(ConstraintViolationListInterface $constraintViolations)
    {
        parent::__construct();

        $this->constraintViolations = $constraintViolations;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolations(): ConstraintViolationListInterface
    {
        return $this->constraintViolations;
    }
}