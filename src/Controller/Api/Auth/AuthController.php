<?php

namespace App\Controller\Api\Auth;

use App\Controller\Api\CoreRestApiController;
use App\Manager\Core\UserManager;
use App\Model\Request\Api\Login\RegisterSimpleUserRequest;
use App\Model\Response\Api\ApiResponse;
use App\Service\Domain\Core\AuthenticationService;
use App\Service\Domain\Core\RestApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\User;
use App\Model\Request\Api\Login\SimpleLoginRequest;

/**
 * @Route("/api/auth")
 */
class AuthController extends CoreRestApiController
{
    /**
     * @var AuthenticationService
     */
    protected $authService;
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param RestApiService        $restApiService
     * @param AuthenticationService $authenticationService
     * @param UserManager           $userManager
     */
    public function __construct(
        RestApiService        $restApiService,
        AuthenticationService $authenticationService,
        UserManager           $userManager
    ) {
        parent::__construct($restApiService);

        $this->authService = $authenticationService;
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     *
     * @return ApiResponse
     *
     * @throws \App\Exception\Domain\Api\ApiException
     * @throws \App\Exception\Domain\Json\MalformedJsonException
     *
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login User",
     *     tags={"User"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(ref=@Model(type=SimpleLoginRequest::class, groups={"create"}))
     *     ),
     *     @OA\Response(response="200", description="An example resource")
     * )
     *
     * @Route("/login", name="auth_login", methods={"POST"})
     */
    public function login(Request $request): ApiResponse
    {
        $loginRequest = $this->deserialize($request->getContent(), ['get'], SimpleLoginRequest::class);

        $userToken = $this->authService->loginWithUsernameOrEmail($loginRequest);

        return $this->serialize($userToken, ApiResponse::HTTP_OK, ['get']);
    }

    /**
     * @param Request $request
     *
     * @return ApiResponse
     *
     * @throws \App\Exception\Domain\Api\ApiException
     * @throws \App\Exception\Domain\Json\MalformedJsonException
     *
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register new simple User",
     *     tags={"User"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(ref=@Model(type=RegisterSimpleUserRequest::class, groups={"create"}))
     *     ),
     *     @OA\Response(response="200", description="User", @Model(type=User::class, groups={"get"}))
     * )
     *
     * @Route("/register", name="auth_register", methods={"POST"})
     */
    public function register(Request $request)
    {
        $userSimpleRequest = $this->deserialize(
            $request->getContent(),
            ['create'],
            RegisterSimpleUserRequest::class
        );

        $this->validate($userSimpleRequest);

        $user = $this->userManager->createUser($userSimpleRequest);
        $this->userManager->validateAndSave($user);

        return $this->serialize($user, ApiResponse::HTTP_OK, ['get']);
    }
}