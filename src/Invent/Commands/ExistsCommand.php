<?php

namespace Invent\Commands;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExistsCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:exists";
    const OPTION_SIMPLE = "simple";

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Test if a module files already exist")
            ->setHelp("Test whether the module xml and module config xml exist since these ar ethe 2 parts a module needs to exist.")
            ->addModuleInputs()
            ->addOption(
                self::OPTION_SIMPLE, 's',
                InputOption::VALUE_NONE,
                "Get the simple output of 1 or 0"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            if( $input->getOption(self::OPTION_SIMPLE) ) {
                $output->writeln( (int) $this->getFileIO()->moduleExists($this->getModule()) );
            } else {
                $msg = ($this->getFileIO()->moduleExists($this->getModule())) ? " exists in " : " does not exist in ";
                $msg = $this->getModule()->getModule() . $msg . $this->getModule()->getLocale() . "!";
                $this->getLogger()->info($msg);
            }
        } catch( Exception $e ) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}