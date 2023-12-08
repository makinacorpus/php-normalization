<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\Command;

use MakinaCorpus\Normalization\NameMap;
use MakinaCorpus\Normalization\NameMapList;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @codeCoverageIgnore
 */
#[AsCommand(name: 'normalization:list', description: 'List known static aliases')]
final class ListCommand extends Command
{
    public function __construct(
        private NameMapList $nameMap
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addOption('tag', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, "Search in the given tag.");
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tags = (array) $input->getOption('tag');
        if (!$tags) {
            $tags = [NameMap::TAG_DEFAULT];
        }

        $io->section('Aliases');

        foreach ($tags as $tag) {
            $some = false;
            foreach ($this->nameMap->listAliases($tag) as $alias => $phpType) {
                $output->writeln(\sprintf("[%s] %s -> %s", $tag, $alias, $phpType));
                $some = true;
            }
            if (!$some) {
                $output->writeln(\sprintf("[%s] <nothing to display>", $tag));
            }
        }

        $io->writeln('');
        $io->section('PHP types');

        $some = false;
        foreach ($tags as $tag) {
            $some = false;
            foreach ($this->nameMap->listPhpTypes($tag) as $phpType => $alias) {
                $output->writeln(\sprintf("[%s] %s -> %s", $tag, $phpType, $alias));
                $some = true;
            }
            if (!$some) {
                $output->writeln(\sprintf("[%s] <nothing to display>", $tag));
            }
        }

        $io->writeln('');

        return self::SUCCESS;
    }
}
