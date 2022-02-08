<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization;

interface NameMap
{
    const TAG_AGGREGATE = 'aggregate';
    const TAG_COMMAND = 'command';
    const TAG_DEFAULT = 'default';
    const TAG_ENTITY = 'entity';
    const TAG_EVENT = 'event';
    const TAG_MESSAGE = 'message';
    const TAG_MODEL = 'model';

    /**
     * From business domain name, return corresponding PHP type.
     *
     * @param string $tag
     *   Arbitrary string which tells in which context we are forging the name.
     */
    public function toPhpType(string $name, string $tag = self::TAG_DEFAULT): string;

    /**
     * From PHP type name, return corresponding business domain name.
     *
     * @param string $tag
     *   Arbitrary string which tells in which context we are forging the name.
     */
    public function fromPhpType(string $phpType, string $tag = self::TAG_DEFAULT): string;
}
