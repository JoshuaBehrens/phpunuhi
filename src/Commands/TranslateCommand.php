<?php

namespace PHPUnuhi\Commands;

use PHPUnuhi\Bundles\Storage\StorageFactory;
use PHPUnuhi\Bundles\Translation\TranslatorFactory;
use PHPUnuhi\Configuration\ConfigurationLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TranslateCommand extends Command
{

    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('translate')
            ->setDescription('Translate all your translations by using one of our translation services')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('deepl-key', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('json-intent', null, InputOption::VALUE_OPTIONAL, '', '')
            ->addOption('json-sort', null, InputOption::VALUE_NONE, '', null);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->showHeader();

        # -----------------------------------------------------------------

        $configFile = $this->getConfigFile($input);

        $service = (string)$input->getOption('service');
        $deeplApiKey = (string)$input->getOption('deepl-key');

        $intent = (string)$input->getOption('json-intent');
        $sort = (bool)$input->getOption('json-sort');

        if (empty($intent)) {
            $intent = 2;
        } else {
            $intent = (int)$intent;
        }

        # -----------------------------------------------------------------

        if (empty($service)) {
            throw new \Exception('No service provided for translation! Please set a service with argument --service=[deepl,...]');
        }

        $configLoader = new ConfigurationLoader();
        $config = $configLoader->load($configFile);


        $translator = TranslatorFactory::fromService($service, $deeplApiKey);


        $translatedCount = 0;

        foreach ($config->getTranslationSets() as $set) {

            $io->section('Translation Set: ' . $set->getName());

            foreach ($set->getLocales() as $locale) {
                foreach ($locale->getTranslations() as $translation) {

                    if (empty($translation->getValue())) {
                        # translate
                        $existingTranslation = $set->findAnyExistingTranslation($translation->getKey());

                        $newTranslation = $translator->translate(
                            $existingTranslation->getValue(),
                            '',
                            $locale->getName()
                        );

                        $translatedCount++;

                        $translation->setValue($newTranslation);
                    }
                }
            }

            $storageSaver = StorageFactory::getSaverFromFormat($set->getFormat(), $intent, $sort);

            $storageSaver->saveTranslations($set);
        }


        $io->success($translatedCount . ' translations are updated!');
        exit(0);
    }


}