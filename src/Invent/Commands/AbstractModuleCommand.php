<?php

namespace Invent\Commands;

use Exception;
use Invent\FileIO;
use Invent\Logger;
use Invent\Module;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use N98\Magento\Command\AbstractMagentoCommand;

abstract class AbstractModuleCommand extends AbstractMagentoCommand
{
    const ARGUMENT_MODULE = "module";
    const OPTION_KEY = "key";
    const OPTION_LOCALE = "locale";

    /** @var  Logger */
    private $logger;

    /** @var Module */
    protected $module;
    
    /** @var FileIO */
    private $fileIO;

    protected $moduleMustExist;

    protected function addModuleInputs()
    {
        $this->addArgument(
            self::ARGUMENT_MODULE,
            InputArgument::REQUIRED,
            "Module Name"
        )->addOption(
            self::OPTION_KEY, "k",
            InputOption::VALUE_REQUIRED,
            "Module Key"
        )->addOption(
            self::OPTION_LOCALE, "l",
            InputOption::VALUE_REQUIRED,
            "Module Locale"
        );
        return $this;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->setLogger($output);
            $this->setFileIO();
            $this->resolveMagento($output);
            if( $input->hasArgument(self::ARGUMENT_MODULE) ) {
                $this->module = new Module($input);
                if( !is_null($this->moduleMustExist) ) {
                    if( $this->moduleMustExist && !$this->getFileIO()->moduleExists($this->module) ) {
                        throw new Exception("Module must exist");
                    } elseif( !$this->moduleMustExist && $this->getFileIO()->moduleExists($this->module) ) {
                        throw new Exception("Module must not exist");
                    }
                }
            }
        } catch( Exception $e) {
            $this->getLogger()->error($e->getMessage());
            exit(1);
        }
    }

    protected function getModule()
    {
        return $this->module;
    }

    /**
     * @param OutputInterface $output
     */
    protected function setLogger(OutputInterface $output)
    {
        $this->logger = new Logger($output);
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    protected function resolveMagento(OutputInterface $output)
    {
        $this->detectMagento($output);
        if( ! $this->initMagento() ) {
            throw new Exception("There seems to be a problem loading Magento.");
        }
    }

    protected function setFileIO()
    {
        $this->fileIO = new FileIO();
    }

    /**
     * @return FileIO
     */
    protected function getFileIO()
    {
        return $this->fileIO;
    }
}