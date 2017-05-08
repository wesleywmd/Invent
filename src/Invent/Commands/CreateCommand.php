<?php

namespace Invent\Commands;

use Exception;
use Invent\FileIO;
use Invent\FileIO\HelperPhp;
use Invent\FileIO\InitXml;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:create";
    const OPTION_OFF = "off";
    const OPTION_HELPER = "helper";

    protected $moduleMustExist = false;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Create new module")
            ->addModuleInputs()
            ->addOption(
                self::OPTION_OFF, 'o',
                InputOption::VALUE_OPTIONAL,
                'Set the active node of the newly created module to false when present'
            )->addOption(
                self::OPTION_HELPER, 'd',
                InputOption::VALUE_NONE,
                'Also generate your module\'s Data helper.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            // create init Xml
            /** @var InitXml $initXml */
            $initXml = $this->getFileIO()->createFile(FileIO::XML_INIT,$this->getModule());
            $initXml->setActive( ($input->getOption(self::OPTION_OFF)) ? "false" : "true" );
            $initXml->setCodePool($this->getModule()->getLocale());
            $this->getFileIO()->writeFile($initXml);

            // create config xml
            $configXml = $this->getFileIO()->createFile(FileIO::XML_CONFIG,$this->getModule());
            $configXml->setVersion("0.0.1");
            if( $input->getOption(self::OPTION_HELPER) ) {
                /** @var HelperPhp $dataHelperPhp */
                $dataHelperPhp = $this->getFileIO()->createFile(FileIO::PHP_HELPER,$this->getModule(),false);
                $dataHelperPhp->setName("Data");
                $dataHelperPhp->setExtends();
                $this->getFileIO()->writeFile($dataHelperPhp);
                $configXml->registerHelpers();
            }
            $this->getFileIO()->writeFile($configXml);

            $this->getLogger()->info(sprintf("Module %s Created in %s",$this->getModule()->getModule(),$this->getModule()->getLocale()));
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}