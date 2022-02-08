<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization;

/**
 * Converts PHP type (classes mostly) names into business domain names that
 * will be saved into the event store, and exposed into the bus, or can be
 * used in any other context where you need to name things.
 *
 * Having a naming strategy that differs from using raw PHP classes names
 * ensures many things:
 *
 *  - we output names that are not class names, we can reduce allowed character
 *    set in those names and ensure it'll be compatible in external tooling,
 * 
 *  - we can shorter drastically those names while keeping then unique and
 *    human readable: believe it or not, but on some projects, the "name" field
 *    in event store may take up to 5% of disk space,
 *
 *  - by using business domain oriented names, we can keep a map of class name
 *    changes when that happens while retaining the name, and remain resilient
 *    to internal PHP code changes: we don't need to update stored events or
 *    change message names which may still be ongoing into the bus.
 *
 * This is important to be able to produce a stable API and call convention
 * throught the message bus, stable in both various technological components
 * and in time.
 *
 * It is expected that conversion works both ways and is predictable.
 */
interface NameMappingStrategy
{
    /**
     * From business domain name, return corresponding PHP type.
     *
     * If given input is a PHP type, return it unmodified.
     */
    public function toPhpType(string $name): string;

    /**
     * From PHP type name, return corresponding business domain name.
     *
     * If given input is an existing alias, return it unmodified.
     */
    public function fromPhpType(string $phpType): string;
}
