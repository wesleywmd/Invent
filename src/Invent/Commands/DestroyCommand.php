<?php

namespace Invent\Commands;

use Exception;
use Invent\FileIO;
use Invent\FileIO\HelperPhp;
use Invent\FileIO\InitXml;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DestroyCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:destroy";
    const OPTION_DESTROY = "destroy";

    protected $moduleMustExist = true;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Destroy a module")
            ->setHelp("Permanently change ")
            ->addModuleInputs()
            ->addOption(
                self::OPTION_DESTROY, null,
                InputOption::VALUE_NONE,
                'Confirmation flag. You MUST use this flag to destroy a module.'
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
            if( ! $input->getOption(self::OPTION_DESTROY) ) {
                throw new Exception("Must submit the destroy flag");
            }
            $this->getFileIO()->destroyFile($this->getFileIO()->createFile(FileIO::XML_INIT,$this->getModule(),true)->getPath());
            $this->getFileIO()->destroyDir($this->getModule()->pathAppCode());

            $this->getLogger()->info(sprintf("Module %s Destroy in %s",$this->getModule()->getModule(),$this->getModule()->getLocale()));
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}