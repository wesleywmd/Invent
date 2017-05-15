<?php

namespace Invent\Commands;

use Exception;
use Invent\FileIO;
use Invent\Service\Version;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetupSqlCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:setup:sql";
    const OPTION_RESOURCE = "resource";

    protected $moduleMustExist = true;

    private $help = <<<HELP
Creates an install script if no install script is found, otherwise it creates an upgrade script. The config xml will 
have its resources registered properly and the version will be increased.
HELP;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Creates an install script or upgrade script")
            ->setHelp($this->help)
            ->addModuleInputs()
            ->addOption(
                self::OPTION_RESOURCE, "r",
                InputOption::VALUE_REQUIRED,
                "Type of setup resource to create"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            /** @var FileIO\ConfigXml $configXml */
            $configXml = $this->getFileIO()->createFile(FileIO::XML_CONFIG,$this->getModule());
            $configXml->registerSetupResource();
            /** @var string $sqlPath */
            $sqlPath = $this->getModule()->pathAppCode("sql/". $this->getModule()->getKey() . "_setup");
            if( ! $this->getFileIO()->isDir($sqlPath) ) {
                $this->getFileIO()->makeDir($sqlPath);
            }
            if( $this->getFileIO()->findFileByPattern("install-",$sqlPath) ) {
                /** @var FileIO\SetupScriptPhp $script */
                $script = $this->getFileIO()->createFile(FileIO::PHP_SETUP_UPGRADE,$this->getModule());
                $script->setCurrentVersion($configXml->getVersion());
            } else {
                /** @var FileIO\SetupScriptPhp $script */
                $script = $this->getFileIO()->createFile(FileIO::PHP_SETUP_INSTALL, $this->getModule());
            }
            /** @var Version $version */
            $version = new Version($configXml->getVersion());
            $version->increment();
            $script->setNewVersion($version->getString());
            $this->getFileIO()->writeFile($script);
            $configXml->setVersion($version->getString());
            $this->getFileIO()->writeFile($configXml);
            $this->getLogger()->info("New installer created: " . $script->getPath());
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}