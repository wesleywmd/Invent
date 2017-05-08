<?php

namespace Invent\Commands;

use DOMDocument;
use Invent\FileIO;
use Invent\MageService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends AbstractModuleCommand
{
    protected $_command_name = "invent:test";
    protected $_command_description = "New Test command registered in a module";

    protected function configure()
    {
        $this->setName("invent:test")
            ->setDescription("New Test command registered in a module")
            ->addModuleInputs();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        $service = new MageService();
        echo $service->inspectSystemXml("sections");
//        $configXml = $this->getFileIO()->createFile(FileIO::XML_CONFIG,$this->getModule());
//        $configXml->setVersion();
//        $configXml->rewriteHelper("catalog","product_price","Beta_Test_Helper_Product_Price");
//        echo $configXml->outputXML();
    }
}