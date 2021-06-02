<?php

namespace App\Model\Request\Api\Login;

interface LoginRequestInterface
{
    public function getLoginName(): string;

    public function getLoginPassword(): string;
}