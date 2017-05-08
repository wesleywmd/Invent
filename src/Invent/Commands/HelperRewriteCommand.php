<?php

namespace Invent\Commands;

use Exception;
use Invent\Module;
use Invent\FileIO\HelperPhp;
use Invent\FIleIO\ConfigXml;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelperRewriteCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:helper:rewrite";
    const ARGUMENT_HELPER_HANDLE = "helper_handle";
    const OPTION_GUT = "gut";

    protected $moduleMustExist = true;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Create new helper")
            ->addModuleInputs()
            ->addArgument(
                self::ARGUMENT_HELPER_HANDLE,
                InputArgument::REQUIRED,
                'the helper\'s magento handle'
            )->addOption(
                self::OPTION_GUT, 'g',
                InputOption::VALUE_NONE,
                'Guts the rewritten method so that it is empty.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            $handle = $input->getArgument(self::ARGUMENT_HELPER_HANDLE);
            $helper = \Mage::helper($handle);
            $parent_module = Module::getModuleName($helper);
            // Helpers dir doesn't actually resolve
            var_dump(\Mage::getModuleDir("helper",$parent_module) . "/Helper/" . str_replace("_","/",Module::getHelperName($helper))); die(0);
            // get helper file name
           // var_dump(\Mage::getModuleDir("helper",))
            // copy file to our module

            // rename object and re extend object

            // update xml with rewrite blocks

            $parent = $helper;
            var_dump(get_class($parent));
            while($parent = get_parent_class($parent)) {
                var_dump($parent);
            }
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }

        die(0);

        $helperPhp = HelperPhpFactory::create($module);
        $helperPhp->setName($name);

        if( $this->getFileIO()->isFile($helperPhp->getFilePath()) ) {
            $this->getLogger()->error("Helper \"{$name}\" already exists in \"{$module->getModule()}[{$module->getLocale()}]\"");
            return;
        }

        if( $input->getOption(self::OPTION_EXTENDS) ) {
            $helperPhp->setExtends($input->getOption(self::OPTION_EXTENDS));
        } else {
            $helperPhp->setExtends();
        }
        $this->getFileIO()->writePhpFile($helperPhp);

        $configXml = ConfigXmlFactory::create($module);
        $configXml->registerHelpers();
        $this->getFileIO()->rewriteXmlFile($configXml);

        $this->getLogger()->info("Helper \"{$name}\" Created in \"{$module->getModule()}[{$module->getLocale()}]\"");
    }
}