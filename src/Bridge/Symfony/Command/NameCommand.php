<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\Command;

use MakinaCorpus\Normalization\NameMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * @codeCoverageIgnore
 */
#[AsCommand(name: 'normalization:name', description: 'Name things')]
final class NameCommand extends Command
{
    public function __construct(
        private NameMap $nameMap
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('target', InputArgument::OPTIONAL, "A PHP class name or an alias.");
        $this->addOption('tag', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, "Search in the given tag.");
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tags = (array) $input->getOption('tag');
        if (!$tags) {
            $tags = [NameMap::TAG_DEFAULT];
        }

        $name = $input->getArgument('target');

        foreach ($tags as $tag) {
            $candidate = $this->nameMap->fromPhpType($name, $tag);
            if ($name === $candidate) {
                $candidate = $this->nameMap->toPhpType($name, $tag);
                if ($candidate === $name) {
                    $output->writeln(\sprintf("[%s] Could not find PHP class name or alias: '%s'", $tag, $name));
                } else {
                    $output->writeln(\sprintf("[%s] %s -> %s", $tag, $candidate, $name));
                }
            } else {
                $output->writeln(\sprintf("[%s] %s -> %s", $tag, $name, $candidate));
            }
        }

        return self::SUCCESS;
    }
}
