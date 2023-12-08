<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization;

/**
 * List known static aliases.
 *
 * Warning: this is not meant to be used at runtime, but only for debugging
 * or cache building purpose.
 */
interface NameMapList
{
    /**
     * List known tags.
     *
     * @return string[]
     */
    public function listTags(): array;

    /**
     * Return known static aliases, ordered by priority.
     *
     * @param array<string,string>
     *   Keys are aliases, values are PHP type names.
     */
    public function listAliases(string $tag = NameMap::TAG_DEFAULT): array;

    /**
     * Return known static PHP types, ordered by priority.
     *
     * @param array<string,string>
     *   Keys are PHP type names, values are aliases.
     */
    public function listPhpTypes(string $tag = NameMap::TAG_DEFAULT): array;
}
