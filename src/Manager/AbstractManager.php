<?php

namespace App\Manager;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\Domain\Api\ValidationException;
use App\Exception\Domain\Api\NotFoundException;
use App\Entity\PersistableEntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Enum\PaginationDefaultEnum;
use Doctrine\ORM\EntityRepository;

abstract class AbstractManager
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param EntityRepository       $entityRepository
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface     $validator
     */
    public function __construct(
        EntityRepository       $entityRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface     $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator     = $validator;
        $this->repository    = $entityRepository;

    }

    protected function getManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function findAll(
        int $limit = PaginationDefaultEnum::ITEMS_PER_PAGE,
        int $offset = 0
    ): array {

        return $this->getRepository()->findBy(
            [],
            ['createdAt' => 'DESC'],
            $limit,
            $offset
        );
    }

    /**
     * @param string $id
     *
     * @return PersistableEntityInterface|object
     *
     * @throws NotFoundException
     */
    public function find(string $id)
    {
        $instance = $this->getRepository()->find($id);

        if (null === $instance) {
            throw new NotFoundException;
        }

        return $instance;
    }

    /**
     * @param PersistableEntityInterface $instance
     *
     * @return bool
     */
    public function delete($instance): bool
    {
        $this->getManager()->remove($instance);
        $this->getManager()->flush();

        return true;
    }

    /**
     * @param PersistableEntityInterface $instance
     *
     * @return PersistableEntityInterface
     *
     * @throws ValidationException
     */
    public function create($instance)
    {
        $this->validateAndSave($instance);

        return $instance;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws NotFoundException
     */
    public function deleteById(int $id): bool
    {
        $instance = $this->find($id);

        return $this->delete($instance);
    }

    /**
     * @param mixed $instance
     * @param null  $constraints
     * @param null  $validationGroups
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function validate($instance, $constraints = null, $validationGroups = null): bool
    {
        $validationErrors = $this->validator->validate($instance, $constraints, $validationGroups);

        if (0 !== $validationErrors->count()) {
            throw new ValidationException($validationErrors);
        }

        return true;
    }

    /**
     * @param PersistableEntityInterface $instance
     * @param null                       $constraints
     * @param null                       $validationGroups
     *
     * @return PersistableEntityInterface
     *
     * @throws ValidationException
     */
    public function validateAndSave($instance, $constraints = null, $validationGroups = null)
    {
        $this->validate($instance, $constraints, $validationGroups);

        return $this->saveManagerStatus($instance);
    }

    /**
     * @param PersistableEntityInterface $instance
     *
     * @return PersistableEntityInterface
     */
    protected function saveManagerStatus($instance)
    {
        $this->getManager()->persist($instance);
        $this->getManager()->flush();

        return $instance;
    }

    protected function getRepository(): EntityRepository
    {
        return $this->repository;
    }
}