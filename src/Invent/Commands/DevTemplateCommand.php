<?php

namespace Invent\Commands;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DevTemplateCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "command:name";

    protected $moduleMustExist = null;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Create new module")
            ->addModuleInputs();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {

            $this->getLogger()->info("Success!");
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}