<?php

namespace Invent\Commands;

use DOMDocument;
use Invent\FileIO;
use Invent\MageService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends AbstractModuleCommand
{
    protected $_command_name = "invent:test";
    protected $_command_description = "New Test command registered in a module";

    protected function configure()
    {
        $this->setName("invent:test")
            ->setDescription("New Test command registered in a module")
            ->addModuleInputs()
            ->addOption(
                "names", null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                "names"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        var_dump($input->getOption("names")); die(0);




        $adminhtmlXml = $this->getFileIO()->createFile(FileIO::XML_ADMINHTML,$this->getModule());
        $adminhtmlXml->registerAclPath("admin/system/testconfig/catalog/test");
        echo $adminhtmlXml->outputXML();
    }
}