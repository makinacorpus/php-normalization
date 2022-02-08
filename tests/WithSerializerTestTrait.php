<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Tests;

use MakinaCorpus\Normalization\Serializer;
use MakinaCorpus\Normalization\Bridge\Symfony\Serializer\RamseyUuidNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

trait WithSerializerTestTrait
{
    /**
     * Create serializer.
     */
    final protected function createSerializer(): Serializer
    {
        return new SymfonySerializer(
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
