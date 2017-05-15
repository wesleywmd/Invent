<?php

namespace Invent\Commands;

use Exception;
use Invent\FileIO;
use Invent\FileIO\HelperPhp;
use Invent\FileIO\InitXml;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModuleCreateCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:module:create";
    const OPTION_DISABLED = "disabled";
    const OPTION_HELPER = "data-helper";

    protected $moduleMustExist = false;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Create new module")
            ->setHelp("Creates a new module xml and config xml if the module files do not already exist")
            ->addModuleInputs()
            ->addTestOption()
            ->addOption(
                self::OPTION_DISABLED, null,
                InputOption::VALUE_NONE,
                'Set the active node of the newly created module to false'
            )->addOption(
                self::OPTION_HELPER, 'd',
                InputOption::VALUE_NONE,
                "Include a data helper."
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
            $initXml->setActive( ($input->getOption(self::OPTION_DISABLED)) ? "false" : "true" );
            $initXml->setCodePool($this->getModule()->getLocale());
            $this->getFileIO()->writeFile($initXml);

            // create config xml
            /** @var FileIO\ConfigXml $configXml */
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