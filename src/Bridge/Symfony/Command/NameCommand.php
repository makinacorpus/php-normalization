<?php

declare(strict_types=1);

namespace MakinaCorpus\Normalization\Bridge\Symfony\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use MakinaCorpus\Normalization\NameMap;

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
        $this->addArgument('action', InputArgument::OPTIONAL, "What to do, can be 'list' or 'name'", 'list');
        $this->addArgument('target', InputArgument::OPTIONAL, "If 'name' is given, this must be a class PHP name or logical name");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        switch ($action = $input->getArgument('action')) {

            case 'list':
                throw new \RuntimeException("Not implemented yet.");

            case 'name':
                if (!$name = $input->getArgument('target')) {
                    throw new InvalidArgumentException("'target' argument is mandatory when action is 'name'");
                }
                $candidate = $this->nameMap->fromPhpType($name);
                if ($name === $candidate) {
                    $candidate = $this->nameMap->toPhpType($name);
                    if ($candidate === $name) {
                        $output->writeln("Could not find PHP class name or alias: " . $name);
                    } else {
                        $output->writeln(\sprintf("%s -> %s", $candidate, $name));
                    }
                } else {
                    $output->writeln(\sprintf("%s -> %s", $name, $candidate));
                }
                break;

            default:
                throw new InvalidArgumentException(\sprintf("'action' paramater is invalid, expected one of 'list' or 'name'", $action));
        }

        return 0;
    }
}
