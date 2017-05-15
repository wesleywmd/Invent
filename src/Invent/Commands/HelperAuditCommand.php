<?php

namespace Invent\Commands;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelperAuditCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:helper:audit";
    const ARGUMENT_HELPER_HANDLE = "helper_handle";

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Print an audit of a helper")
            ->setHelp("Displays a formatted audit of a helper based on the provide helper handle. This displays class ancestry, fields, and methods for the given helper.")
            ->addArgument(
                self::ARGUMENT_HELPER_HANDLE,
                InputArgument::REQUIRED,
                "Helper handle to look up"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            $helper = \Mage::helper($input->getArgument(self::ARGUMENT_HELPER_HANDLE));
            $this->getLogger()->displayObject($helper,"info");
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}