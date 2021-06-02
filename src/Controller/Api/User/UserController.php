<?php

namespace App\Controller\Api\User;

use App\Controller\Api\CoreRestApiController;
use App\Entity\User;
use App\Manager\Core\UserManager;
use App\Model\Request\Api\Login\RegisterSimpleUserRequest;
use App\Model\Response\Api\ApiResponse;
use App\Security\Voter\UserVoter;
use App\Service\Domain\Core\RestApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

/**
 * @Route("/api/users")
 */
class UserController extends CoreRestApiController
{
    /**
     * @var UserManager
     */
    protected $userManager;

    public function __construct(
        RestApiService $restApiService,
        UserManager    $userManager
    ) {
        parent::__construct($restApiService);

        $this->userManager = $userManager;
    }

    /**
     * @return ApiResponse
     *
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get all users",
     *     tags={"User"},
     *     @OA\Response(response="200", description="An example resource"),
     *     @Security(name="Bearer")
     * )
     *
     * @Route("", name="users_get", methods={"GET"})
     */
    public function getUsers()
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW_ALL);

        return $this->serialize(
            $this->userManager->findAll(),
            ApiResponse::HTTP_OK,
            ['get']
        );
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
     *     path="/api/users",
     *     summary="Create new User",
     *     tags={"User"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(ref=@Model(type=RegisterSimpleUserRequest::class, groups={"create"}))
     *     ),
     *     @OA\Response(response="200", description="User", @Model(type=User::class, groups={"get"}))
     * )
     *
     * @Route("", name="users_create_user", methods={"POST"})
     */
    public function createUser(Request $request)
    {
        $userSimpleRequest = $this->deserialize(
            $request->getContent(),
            ['create'],
            RegisterSimpleUserRequest::class
        );

        $this->validate($userSimpleRequest);

        $user = $this->userManager->createUser($userSimpleRequest);

        $this->denyAccessUnlessGranted(UserVoter::CREATE);
        $this->userManager->validateAndSave($user);

        return $this->serialize($user, ApiResponse::HTTP_OK, ['get']);
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return ApiResponse
     *
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="View User",
     *     tags={"User"},
     *     @OA\Response(response="200", description="User", @Model(type=User::class, groups={"get"}))
     * )
     *
     * @Route("/{id}", name="users_view_user", methods={"GET"})
     */
    public function viewUser(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);

        return $this->serialize(
            $user,
            ApiResponse::HTTP_OK,
            ['get']
        );
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return ApiResponse
     *
     * @throws \App\Exception\Domain\Api\ApiException
     * @throws \App\Exception\Domain\Api\ValidationException
     * @throws \App\Exception\Domain\Json\MalformedJsonException
     *
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Edit User",
     *     tags={"User"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(ref=@Model(type=RegisterSimpleUserRequest::class, groups={"edit"}))
     *     ),
     *     @OA\Response(response="200", description="User", @Model(type=User::class, groups={"edit"}))
     * )
     *
     * @Route("/{id}", name="users_edit_user", methods={"PUT"})
     */
    public function editUser(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        /** @var RegisterSimpleUserRequest $userSimpleRequest */
        $userSimpleRequest = $this->deserialize(
            $request->getContent(),
            ['edit'],
            RegisterSimpleUserRequest::class
        );

        $this->userManager->update($user, $userSimpleRequest->getUser());
        $this->userManager->validateAndSave($user);

        return $this->serialize($user, ApiResponse::HTTP_OK, ['get']);
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return ApiResponse
     *
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="View User",
     *     tags={"User"},
     *     @OA\Response(response="200", description="User", @Model(type=User::class, groups={"get"}))
     * )
     *
     * @Route("/{id}", name="users_delete_user", methods={"DELETE"})
     */
    public function deleteUser(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted(UserVoter::REMOVE, $user);
        $this->userManager->removeAndSave($user);

        return $this->serialize([], ApiResponse::HTTP_OK, ['delete']);
    }
}
