<?php

namespace Invent\Commands;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlockAuditCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:block:audit";
    const ARGUMENT_BLOCK_HANDLE = "block_handle";
    
    protected $moduleMustExist = true;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Print and audit of a block")
            ->addArgument(
                self::ARGUMENT_BLOCK_HANDLE,
                InputArgument::REQUIRED,
                'the block\'s magento handle'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            $block = \Mage::getBlockSingleton($input->getArgument(self::ARGUMENT_BLOCK_HANDLE));
            $this->getLogger()->displayObject($block,"info");
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}