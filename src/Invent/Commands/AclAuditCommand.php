<?php

namespace Invent\Commands;

use Exception;
use Invent\MageService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AclAuditCommand extends AbstractModuleCommand
{
    const COMMAND_NAME = "invent:acl:audit";

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->setDescription("List the current acl's registered.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input,$output);
        try {
            $service = new MageService();
            var_dump($service->getRegisteredAcls());
            $this->getLogger()->info("Success!");
        } catch(Exception $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }
}