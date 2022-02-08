# Normalization helpers

PHP class name to business domain name naming strategies and normalization helpers.

Main feature is the *name map*, which provide a reversible and predictible
*class name to business name* and a *business name to class name* conversion
whose goal is to be put in front of any component that exposes PHP class name
to the outside world, in order to be able to alias your internal type names
to business domain names.

This provides three different class naming strategies:

 - *Passthrough* name conversion which doesn't convert anything. exposed names
   are your PHP class names.

 - *Prefix based* name conversion which will convert a string such as
   `Foo\Shop\Domain\Order\Command\BasketProductAdd` to `FooShop.Order.Command.BasketProductAdd`
   considering that the `Foo\Shop\Domain` namespace prefix will be always
   statically converted to `FooShop` and replacing separators using `.`.

 - *Static map* name conversion which uses a user-provided static map.

You can configure the name map to hold an infinite number of strategies,
each one identified by a *tag*, which permits to each service using this
API to have its own naming strategy.

The name map allows user-defined aliases map, which can hold a infinite
number of name aliases for a single PHP class name, allowing your project
to be resilient to past conventions. One use case for this is, for example,
when you change your naming convention while being plugged over a message
bus: your application may continue to consume older messages while being
upgraded.

Additionnaly, it provides a few other helpers:

 - A custom `Serializer` interface with a default implementation which uses
   the `symfony/serializer` component. This allows code using this API to
   benefit from a replacable serializer.

 - A `ramsey/uuid` normalizer and denormalizer for `symfony/serializer`.

 - More are likely to be added in the future.

# Setup

Install this package:

```sh
composer req makinacorpus/normalization
```

If you are using Symfony, add the bundle in your `config/bundles.php`:

```php
<?php

return [
    // ... Your other bundles.
    MakinaCorpus\Normalization\Bridge\Symfony\NormalizationBundle::class => ['all' => true],
];
```

Or you can do a standalone setup of the name map:

```php
use MakinaCorpus\Normalization\NameMap\DefaultNameMap;
use MakinaCorpus\Normalization\NameMap\PrefixNameMappingStrategy;

$nameMap = new DefaultNameMap(
    new PrefixNameMappingStrategy(
        'MyApp',
        'My\\Namespace\\Prefix',
    )
);
```

# Symfony bundle configuration

Here is an example `config/packages/normalization.yaml` file:

```yaml
#
# Sample configuration
#
normalization:
    default_strategy:
        #
        # Default name mapping strategy configuration.
        #
        # Per default the "PrefixNameMappingStrategy" is used, which means
        # that you need to give an application name prefix string, which will
        # be all normalized names prefix, and a PHP class namespace prefix
        # that will identify which PHP classes belongs to you or not.
        #
        # Per default the app name is "App" and the namespace prefix is
        # "App" as well, to mimic default Symfony skeleton app.
        #
        app_name: MyApp
        class_prefix: MyVendor\\MyApp

    strategy:
        #
        # Keys here are arbitrary user-defined tags.
        #
        # Tags purpose is to allow API user to define different strategies
        # for different contextes.
        # 
        # See \MakinaCorpus\Normalization\NameMap::TAG_* constants which
        # provides a few samples values.
        #
        # Values must be container services identifiers.
        #
        command: \App\Infra\Normalization\CustomCommandNameMappingStrategy
        event: \App\Infra\Normalization\CustomEventNameMappingStrategy

    static:
        #
        # Keys here are arbitrary user-defined tags.
        #
        # Tags purpose is to allow API user to define different strategies
        # for different contextes.
        # 
        # See \MakinaCorpus\Normalization\NameMap::TAG_* constants which
        # provides a few samples values.
        #
        command:
            #
            # Actual business domain name to PHP class name conversion.
            #
            map:
                Php\Native\Type: my_app.normalized_name
                Php\Other\Native\Type: my_app.other_normalized_name

            #
            # Legacy aliases to PHP class name conversion.
            #
            aliases:
                Php\Legacy\Name: Php\Native\Type
                Php\EvenMoreLegacy\Name: Php\Native\Type
                my_app.legacy_normalized_name: Php\Native\Type
                my_app.other_legacy_normalized_name: my_app.normalized_name
```

# Usage

In order to use the name map, simply inject the service into the service
needing it:

```php
namespace App\Infra\Bus;

use MakinaCorpus\Normalization\NameMap;

class SomeBus
{
    public function __construct(
        private NameMap $nameMap
    {
    }

    /**
     * This is fictional pseudo-code.
     */
    public function getClassBusinessName(object $message): void
    {
        return $this
            ->nameMap
            ->fromPhpType(
                \get_class($message),
                'some_tag'
            )
        ;
    }
}
```

If you are working in a Symfony application, you may simply use the
`MakinaCorpus\Normalization\NameMap\NameMapAware` interface on the object
in order for it to be automatically populated by the container:

```php
namespace App\Infra\Bus;

use MakinaCorpus\Normalization\NameMap\NameMapAware;
use MakinaCorpus\Normalization\NameMap\NameMapAwareTrait;
use MakinaCorpus\Normalization\Serializer;
use SomeVendor\SomePackage\MessageBroker;

class SomeBus implements NameMapAware
{
    use NameMapAwareTrait;

    /**
     * This is fictional pseudo-code.
     */
    public function send(object $message): void
    {
        return $this
            ->getNameMap()
            ->fromPhpType(
                \get_class($message),
                'some_tag'
            )
        ;
    }
}
```

# Testing

A docker environement with various containers for various PHP versions is
present in the `sys/` folder. For tests to work in all PHP versions, you
need to run `composer update --prefer-lowest` otherwise PHP 7.4 tests will
fail.

```sh
composer install
composer update --prefer-lowest
cd sys/
./docker-rebuild.sh # Run this only once
./docker-run.sh
```
