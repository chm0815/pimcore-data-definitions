<?php

declare(strict_types=1);

/*
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - Data Definitions Commercial License (DDCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    GPLv3 and DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ConvertImportDefinitionsToYaml extends Command
{
    protected function configure(): void
    {
        $this->setName('data-definition:configuration:importer:convert-to-yaml')
            ->setDescription('Convert convert import file definitions to YAML files')
            ->setHelp('This command converts convert import file definitions file to YAML files')
            ->addArgument('file', InputArgument::OPTIONAL, 'Path to the PHP file', 'var/config/importdefinitions.php')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $data = require $filePath;

        $fs = new Filesystem();
        if (!$fs->exists('var/config/import-definitions')) {
            $fs->mkdir('var/config/import-definitions');
        }

        foreach ($data as $entry) {
            $fileName = $entry['id'] . '.yaml';
            $yamlData = [
                'data_definitions' => [
                    'import_definitions' => [
                        $entry['id'] => $entry,
                    ],
                ],
            ];

            $yaml = Yaml::dump($yamlData, 4, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

            file_put_contents("var/config/import-definitions/{$fileName}", $yaml);
        }
        $output->writeln('YAML import definitions configurations are generated under: var/config/import-definitions');

        return Command::SUCCESS;
    }
}
