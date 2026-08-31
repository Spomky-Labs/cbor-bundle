<?php

declare(strict_types=1);

namespace SpomkyLabs\CborBundle\Tests\Functional;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\Test;
use SpomkyLabs\CborBundle\Tests\Fixtures\Person;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Tests for object serialization/deserialization with CBOR format.
 *
 * @internal
 */
final class ObjectSerializationTest extends KernelTestCase
{
    #[Test]
    public function itCanSerializeAndDeserializeObjects(): void
    {
        // Given
        static::bootKernel();

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $person = new Person();
        $person->setName('foo');
        $person->setAge(99);
        $person->setSportsperson(false);
        // Leave createdAt as null

        // When - Serialize to CBOR
        $cborContent = $serializer->serialize($person, 'cbor');

        // Then - Should produce valid CBOR
        static::assertIsString($cborContent);
        static::assertNotEmpty($cborContent);

        // Verify the CBOR content matches expected structure
        // A4 = map with 4 items
        // 646E616D65 = text "name"
        // 63666F6F = text "foo"
        // 63616765 = text "age"
        // 1863 = integer 99
        // 6C73706F727473706572736F6E = text "sportsperson"
        // F4 = false
        // 69637265617465644174 = text "createdAt"
        // F6 = null
        static::assertSame(
            'A4646E616D6563666F6F6361676518636C73706F727473706572736F6EF469637265617465644174F6',
            strtoupper(bin2hex($cborContent))
        );

        // When - Deserialize back to object
        $deserializedPerson = $serializer->deserialize($cborContent, Person::class, 'cbor');

        // Then - Should reconstruct the object correctly
        static::assertInstanceOf(Person::class, $deserializedPerson);
        static::assertSame('foo', $deserializedPerson->getName());
        static::assertSame(99, $deserializedPerson->getAge());
        static::assertFalse($deserializedPerson->isSportsperson());
        static::assertNull($deserializedPerson->getCreatedAt());
    }

    #[Test]
    public function itCanHandleComplexObjects(): void
    {
        // Given
        static::bootKernel();

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $person = new Person();
        $person->setName('Alice');
        $person->setAge(30);
        $person->setSportsperson(true);
        $person->setCreatedAt(new DateTimeImmutable('2024-01-15T10:30:00+00:00'));

        // When - Serialize and deserialize
        $cborContent = $serializer->serialize($person, 'cbor');
        $deserializedPerson = $serializer->deserialize($cborContent, Person::class, 'cbor');

        // Then - All properties should be preserved
        static::assertInstanceOf(Person::class, $deserializedPerson);
        static::assertSame('Alice', $deserializedPerson->getName());
        static::assertSame(30, $deserializedPerson->getAge());
        static::assertTrue($deserializedPerson->isSportsperson());
        static::assertInstanceOf(DateTimeInterface::class, $deserializedPerson->getCreatedAt());
        static::assertSame(
            '2024-01-15T10:30:00+00:00',
            $deserializedPerson->getCreatedAt()
                ->format(DateTimeInterface::ATOM)
        );
    }

    #[Test]
    public function itCanHandleArraysOfObjects(): void
    {
        // Given
        static::bootKernel();

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $person1 = new Person();
        $person1->setName('Alice')
            ->setAge(30)
            ->setSportsperson(true);

        $person2 = new Person();
        $person2->setName('Bob')
            ->setAge(25)
            ->setSportsperson(false);

        $people = [$person1, $person2];

        // When - Serialize array of objects
        $cborContent = $serializer->serialize($people, 'cbor');

        // Then - Should be valid CBOR
        static::assertIsString($cborContent);
        static::assertNotEmpty($cborContent);

        // When - Deserialize back
        $deserializedPeople = $serializer->deserialize($cborContent, Person::class . '[]', 'cbor');

        // Then - Should reconstruct all objects
        static::assertIsArray($deserializedPeople);
        static::assertCount(2, $deserializedPeople);

        static::assertInstanceOf(Person::class, $deserializedPeople[0]);
        static::assertSame('Alice', $deserializedPeople[0]->getName());
        static::assertSame(30, $deserializedPeople[0]->getAge());
        static::assertTrue($deserializedPeople[0]->isSportsperson());

        static::assertInstanceOf(Person::class, $deserializedPeople[1]);
        static::assertSame('Bob', $deserializedPeople[1]->getName());
        static::assertSame(25, $deserializedPeople[1]->getAge());
        static::assertFalse($deserializedPeople[1]->isSportsperson());
    }

    #[Test]
    public function itHandlesEmptyObjects(): void
    {
        // Given
        static::bootKernel();

        /** @var SerializerInterface $serializer */
        $serializer = static::getContainer()->get(SerializerInterface::class);

        $person = new Person();
        // All properties are null

        // When - Serialize and deserialize
        $cborContent = $serializer->serialize($person, 'cbor');
        $deserializedPerson = $serializer->deserialize($cborContent, Person::class, 'cbor');

        // Then - Should handle null values correctly
        static::assertInstanceOf(Person::class, $deserializedPerson);
        static::assertNull($deserializedPerson->getName());
        static::assertNull($deserializedPerson->getAge());
        static::assertNull($deserializedPerson->isSportsperson());
        static::assertNull($deserializedPerson->getCreatedAt());
    }
}
