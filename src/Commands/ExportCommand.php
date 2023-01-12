<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Exchange\ExchangeFactory;
use PHPUnuhi\Bundles\Exchange\ExchangeFormat;
use PHPUnuhi\Configuration\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends Command
{

    use \PHPUnuhi\Traits\CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('export')
            ->setDescription('Exports all or specific translations into an exchange file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('set', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('dir', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, '', ExchangeFormat::CSV)
            ->addOption('csv-delimiter', null, InputOption::VALUE_REQUIRED, '', '');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PHPUnuhi Export');
        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);
        $exportExchangeFormat = (string)$input->getOption('format');
        $setName = (string)$input->getOption('set');
        $outputDir = (string)$input->getOption('dir');

        # arguments for individual exchange exporters
        $delimiter = (string)$input->getOption('csv-delimiter');

        if (empty($delimiter)) {
            $delimiter = ',';
        }


        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];
        $outputDir = $workingDir . '/' . $outputDir;


        # -----------------------------------------------------------------

        $configLoader = new ConfigurationLoader();

        $config = $configLoader->load($configFile);

        $exporter = ExchangeFactory::getExporterFromFormat($exportExchangeFormat, $delimiter);

        foreach ($config->getTranslationSets() as $set) {

            # if we have configured to only export a single suite then skip all others
            if (!empty($setName) && $setName !== $set->getName()) {
                continue;
            }

            $io->section('Translation Set: ' . $set->getName());

            $exporter->export($set, $outputDir);
        }

        $io->success('All translations exported!');
        exit(0);
    }

}
