<?php

namespace Invent\Commands;

use Exception;
use Invent\MageService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AclExistsCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:acl:exists";
    const ARGUMENT_PATH = "path";

    protected $moduleMustExist = null;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("Check if acl path exists")
            ->addArgument(
                self::ARGUMENT_PATH,
                InputArgument::REQUIRED,
                'Acl path to check.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            $service = new MageService();
            $path = $input->getArgument(self::ARGUMENT_PATH);
            if( $service->hasAcl($path) ) {
                $this->getLogger()->info("Found valid acl path \"{$path}\"");
            } else {
                $this->getLogger()->comment("Did not find valid acl path \"{$path}\"");
            }
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}