<?php

namespace Invent\Commands;

use Exception;
use Invent\FileIO;
use Invent\FileIO\BlockPhp;
use Invent\FileIO\ConfigXml;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BlockCreateCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:block:create";
    const ARGUMENT_BLOCK_NAME = "block_name";
    const OPTION_EXTENDS = "extends";

    protected $moduleMustExist = true;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Create new block class")
            ->setHelp("Creates a new block class and ensures that blocks are enabled in the config xml for the module.")
            ->addModuleInputs()
            ->addTestOption()
            ->addArgument(
                self::ARGUMENT_BLOCK_NAME,
                InputArgument::REQUIRED,
                "Block Name"
            )->addOption(
                self::OPTION_EXTENDS, 'e',
                InputOption::VALUE_REQUIRED,
                "optionally set what object your block will extend"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try{

            $block_name = $input->getArgument(self::ARGUMENT_BLOCK_NAME);
            /** @var BlockPhp $blockPhp */
            $blockPhp = $this->getFileIO()->createFile(FileIO::PHP_BLOCK,$this->getModule(),false);
            $blockPhp->setBlockName($block_name);
            $blockPhp->setExtends($input->getOption(self::OPTION_EXTENDS));
            $this->getFileIO()->writeFile($blockPhp);

            /** @var ConfigXml $configXml */
            $configXml = $this->getFileIO()->createFile(FileIO::XML_CONFIG,$this->getModule());
            $configXml->registerBlocks();
            $this->getFileIO()->writeFile($configXml);

            $this->getLogger()->info("Block \"{$block_name}\" Created in \"{$this->getModule()->getModule()}[{$this->getModule()->getLocale()}]\"");
        } catch( Exception $e ) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}