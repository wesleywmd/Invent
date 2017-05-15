<?php

namespace Invent\Commands;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModelAuditCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:model:audit";
    const ARGUMENT_MODEL_HANDLE = "model_handle";

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Print an audit of a model")
            ->setHelp("Displays a formatted audit of a model based on the provide model handle. This displays class ancestry, fields, and methods for the given model.")
            ->addArgument(
                self::ARGUMENT_MODEL_HANDLE,
                InputArgument::REQUIRED,
                "Model handle to look up"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            $model = \Mage::getModel($input->getArgument(self::ARGUMENT_MODEL_HANDLE));
            $this->getLogger()->displayObject($model,"info");
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}