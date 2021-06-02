<?php

namespace App\Service\Domain\Core;

use App\Exception\Domain\Api\TokenExtractionException;
use Symfony\Component\HttpFoundation\Request;

class TokenExtractionService
{
    /**
     * @param string $authorizationHeader
     *
     * @return string
     * @throws TokenExtractionException
     */
    public function extract(string $authorizationHeader): string
    {
        if (false === strstr($authorizationHeader, 'Bearer')) {
            throw new TokenExtractionException('Bearer must be provided as the authorisation format');
        }

        $parts = explode(' ', $authorizationHeader);

        if (count($parts) < 2) {
            throw new TokenExtractionException('Token must be formed out of two parts');
        }

        return $parts[1];
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    public function extractAuthorizationHeader(Request $request)
    {
        if (false === $request->headers->has('Authorization')) {
            return null;
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (empty($authorizationHeader)) {
            return null;
        }

        return $authorizationHeader;
    }

    /**
     * @param Request $request
     *
     * @return string
     *
     * @throws TokenExtractionException
     */
    public function extractTokenFromRequest(Request $request): string
    {
        $authorizationHeader = $this->extractAuthorizationHeader($request);

        if (empty($authorizationHeader)) {
            throw new TokenExtractionException('Token not found.');
        }

        return $this->extract($authorizationHeader);
    }
}