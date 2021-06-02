<?php

namespace App\Entity;

interface PersistableEntityInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime;
}