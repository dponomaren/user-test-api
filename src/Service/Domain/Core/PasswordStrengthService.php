<?php

namespace App\Service\Domain\Core;

use App\Exception\Domain\Api\PasswordStrengthException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PasswordStrengthService
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var int
     */
    protected $minLength;

    /**
     * @var int
     */
    protected $minLetters;

    /**
     * @var int
     */
    protected $minNumbers;

    /**
     * @var int
     */
    protected $minSpecialChars;

    /**
     * @var int
     */
    protected $minStrength;

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        $this->parameterBag    = $parameterBag;
        $this->minLength       = $this->parameterBag->get('app.password.min_length');
        $this->minLetters      = $this->parameterBag->get('app.password.min_letters');
        $this->minNumbers      = $this->parameterBag->get('app.password.min_numbers');
        $this->minSpecialChars = $this->parameterBag->get('app.password.min_special_chars');
        $this->minStrength     = $this->parameterBag->get('app.password.min_strength');
    }

    /**
     * @param string $password
     *
     * @return int Calculated password strength on a scale from 0 to 10
     */
    public function calculate(string $password): int
    {
        $result = 0;
        $result += $this->checkForLength($password);
        $result += $this->checkForLetters($password);
        $result += $this->checkForNumbers($password);
        $result += $this->checkForSpecialChars($password);

        return $result;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        $params = [];
        $params['password_min_length']        = $this->minLength;
        $params['password_min_letters']       = $this->minLetters;
        $params['password_min_numbers']       = $this->minNumbers;
        $params['password_min_special_chars'] = $this->minSpecialChars;
        $params['password_min_strength']      = $this->minStrength;

        return $params;
    }

    /**
     * @param string $password
     *
     * @return int
     */
    private function checkForLength(string $password): int
    {
        if (strlen($password) >= $this->minLength) {
            return 4;
        }

        if (strlen($password) >= ceil($this->minLength / 2) && strlen($password) < $this->minLength) {
            return 2;
        }

        return 0;
    }

    /**
     * @param string $password
     *
     * @return int
     */
    protected function checkForLetters(string $password): int
    {
        if (preg_match("/^(.*?[a-z]){{$this->minLetters},}/", $password)) {
            return 2;
        }

        if (preg_match("/[a-zA-Z]/", $password)) {
            return 1;
        }

        return 0;
    }

    /**
     * @param string $password
     *
     * @return int
     */
    protected function checkForNumbers(string $password): int
    {
        if (preg_match("/^(.*?[0-9]){{$this->minNumbers},}/", $password)) {
            return 2;
        }

        if (preg_match("/[0-9]/", $password)) {
            return 1;
        }

        return 0;
    }

    /**
     * @param string $password
     *
     * @return int
     */
    protected function checkForSpecialChars(string $password): int
    {
        if (preg_match("/^(.*\W+){{$this->minSpecialChars},}?/", $password)) {
            return 2;
        }

        if (preg_match("/^(.*\W+)/", $password)) {
            return 1;
        }

        return 0;
    }

    /**
     * @param string $password
     *
     * @throws PasswordStrengthException
     */
    public function checkStrength(string $password)
    {
        $result = $this->calculate($password);

        if ($result < $this->minStrength) {
            throw new PasswordStrengthException("The chosen password is too weak.");
        }
    }
}