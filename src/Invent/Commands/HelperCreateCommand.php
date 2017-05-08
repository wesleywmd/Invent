<?php

namespace Invent\Commands;

use Exception;
use Invent\FileIO;
use Invent\FileIO\HelperPhp;
use Invent\FileIO\ConfigXml;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelperCreateCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:helper:create";
    const ARGUMENT_HELPER_NAME = "helper_name";
    const OPTION_EXTENDS = "extends";

    protected $moduleMustExist = true;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Create new helper")
            ->addModuleInputs()
            ->addArgument(
                self::ARGUMENT_HELPER_NAME,
                InputArgument::REQUIRED,
                'the helper\'s name'
            )->addOption(
                self::OPTION_EXTENDS, 'e',
                InputOption::VALUE_REQUIRED,
                'optionally set what object your helper will extend'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try{

            $helper_name = $input->getArgument(self::ARGUMENT_HELPER_NAME);
            /** @var HelperPhp $helperPhp */
            $helperPhp = $this->getFileIO()->createFile(FileIO::PHP_HELPER,$this->getModule(),false);
            $helperPhp->setName($helper_name);
            $helperPhp->setExtends($input->getOption(self::OPTION_EXTENDS));
            $this->getFileIO()->writeFile($helperPhp);

            /** @var ConfigXml $configXml */
            $configXml = $this->getFileIO()->createFile(FileIO::XML_CONFIG,$this->getModule());
            $configXml->registerHelpers();
            $this->getFileIO()->writeFile($configXml);

            $this->getLogger()->info("Helper \"{$helper_name}\" Created in \"{$this->getModule()->getModule()}[{$this->getModule()->getLocale()}]\"");
        } catch( Exception $e ) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}