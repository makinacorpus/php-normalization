<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\Command;

use MakinaCorpus\Normalization\NameMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * @codeCoverageIgnore
 */
final class NameCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'normalization:name';

    /**
     * {@inheritdoc}
     */
    protected static $defaultDescription = 'Name things.';

    private NameMap $nameMap;

    public function __construct(NameMap $nameMap)
    {
        parent::__construct();

        $this->nameMap = $nameMap;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addArgument('target', InputArgument::OPTIONAL, "If 'list', then list all, otherwise this must be a class PHP name or logical name.");
        $this->addOption('tag', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, "Search in the given tag.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tags = (array) $input->getOption('tag');
        if (!$tags) {
            $tags = [NameMap::TAG_DEFAULT];
        }

        switch ($name = $input->getArgument('target')) {

            case 'list':
                throw new \RuntimeException("Not implemented yet.");

            default:
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
                break;
        }

        return 0;
    }
}
