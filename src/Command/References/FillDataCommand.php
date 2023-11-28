<?php

namespace App\Command\References;

use App\Service\References\ReferencesFillDataService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Fill data command
 */
#[AsCommand(
    name: ReferencesCommandDef::APP_COMMAND_FILL_DATA,
    description: 'Fill data',
    hidden: false,
    aliases: [],
)]
class FillDataCommand extends Command
{
    public function __construct(
        private ReferencesFillDataService $service
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {

        return;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->addOption('truncate', '-t', InputOption::VALUE_NONE, 'Truncate table(s) before fill data')
            ->addOption('entity', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Entity name')
            // the command help shown when running the command with the "--help" option
            ->setHelp(<<<HELP
            The <info>%command.name%</info> command fill data:

              <info>php %command.full_name%</info> <comment>--truncate</comment>
            HELP);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start(ReferencesCommandDef::APP_COMMAND_FILL_DATA);

        $truncate = (bool) $input->getOption('truncate');
        $entities = (array) $input->getOption('entity');

        $result = $this->service->fill(truncate: $truncate, entities: $entities);

        $event = $stopwatch->stop(ReferencesCommandDef::APP_COMMAND_FILL_DATA);

        if ($output->isVerbose()) {
            $io = new SymfonyStyle($input, $output);
            $io->comment(sprintf(
                'Elapsed time: %.2f ms / Consumed memory: %.2f MB',
                $event->getDuration(),
                $event->getMemory() / (1024 ** 2),
            ));
        }

        return ($result) ? Command::SUCCESS : Command::FAILURE;
    }
}
