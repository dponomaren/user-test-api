<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

abstract class AbstractTemporalEntity implements PersistableEntityInterface
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @JMS\Type("string")
     * @JMS\Groups({"get"})
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @JMS\Groups({"get"})
     * @JMS\Type("DateTime<'Y-m-d\TH:i:sP'>")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @JMS\Groups({"get"})
     * @JMS\Type("DateTime<'Y-m-d\TH:i:sP'>")
     */
    protected $updatedAt;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $currentTime = new \DateTime;

        $this->createdAt = $currentTime;
        $this->updatedAt = $currentTime;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $currentTime     = new \DateTime;
        $this->updatedAt = $currentTime;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}