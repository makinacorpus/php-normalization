<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Testing;

use MakinaCorpus\Normalization\Serializer;
use MakinaCorpus\Normalization\Bridge\Symfony\Serializer\RamseyUuidNormalizer;
use MakinaCorpus\Normalization\Bridge\Symfony\Serializer\SymfonySerializerSerializer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * For other component to be able to use.
 */
trait WithSerializerTestTrait
{
    /**
     * Create serializer.
     */
    final protected function createSerializer(): Serializer
    {
        return new SymfonySerializerSerializer(
            new SymfonySerializer(
                [
                    new ArrayDenormalizer(),
                    new RamseyUuidNormalizer(),
                    new ObjectNormalizer(),
                ],
                [
                    new XmlEncoder(),
                    new JsonEncoder(),
                ]
            )
        );
    }
}
