<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Fixtures;

use DateTimeInterface;

/**
 * Test fixture for object serialization/deserialization.
 */
final class Person
{
    private ?string $name = null;

    private ?int $age = null;

    private ?bool $sportsperson = null;

    private ?DateTimeInterface $createdAt = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function isSportsperson(): ?bool
    {
        return $this->sportsperson;
    }

    public function setSportsperson(?bool $sportsperson): self
    {
        $this->sportsperson = $sportsperson;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
