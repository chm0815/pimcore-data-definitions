<?php

namespace Instride\Bundle\DataDefinitionsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ConvertExportDefinitionsToYaml extends Command
{

    protected function configure(): void
    {
        $this
            ->setName('data-definition:configuration:exporter:convert-to-yaml')
            ->setDescription('Convert export definitions file to YAML files')
            ->setHelp('This command converts export definitions file to YAML')
            ->addArgument('file', InputArgument::OPTIONAL, 'Path to the PHP file', 'var/config/exportdefinitions.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $data = require $filePath;

        $fs = new Filesystem();
        if (!$fs->exists('var/config/export-definitions')) {
            $fs->mkdir('var/config/export-definitions');
        }

        foreach ($data as $entry) {

            $fileName = $entry['id'] . '.yaml';

            $yamlData = [
                'data_definitions' => [
                    'export_definitions' => [
                        $entry['id'] => $entry
                    ]
                ]
            ];

            $yaml = Yaml::dump($yamlData, 4, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
            file_put_contents("var/config/export-definitions/{$fileName}", $yaml);
        }
        $output->writeln('YAML export definitions are generated under: var/config/export-definitions');

        return Command::SUCCESS;
    }
}
