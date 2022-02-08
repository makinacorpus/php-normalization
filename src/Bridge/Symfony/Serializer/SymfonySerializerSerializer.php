<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\Serializer;

use MakinaCorpus\Normalization\MimeTypeConverter;
use MakinaCorpus\Normalization\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Brings a custom serializer interface implementation that internally uses
 * the symfony/serializer package for doing the job.
 */
final class SymfonySerializerSerializer implements Serializer
{
    private SerializerInterface $symfonySerializer;

    public function __construct(SerializerInterface $symfonySerializer)
    {
        $this->symfonySerializer = $symfonySerializer;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, string $format, ?string $forceType = null): string
    {
        return $this->symfonySerializer->serialize($data, MimeTypeConverter::mimetypeToFormat($format));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize(string $type, string $format, string $data)
    {
        return $this->symfonySerializer->deserialize($data, $type, MimeTypeConverter::mimetypeToFormat($format));
    }
}
