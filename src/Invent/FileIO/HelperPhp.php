<?php
namespace Invent\FileIO;

use Invent\Module;

class HelperPhp extends AbstractPhp implements FileIOInterface
{
    const DEFAULT_EXTENDS = "Mage_Core_Helper_Abstract";
    const VAR_MODULE = "MODULE";
    const VAR_HELPER_NAME = "HELPER_NAME";
    const VAR_EXTENDS = "EXTENDS";

    protected $vars = [
        self::VAR_EXTENDS=>null,
        self::VAR_HELPER_NAME=>null,
        self::VAR_MODULE=>null,
s    ];

    public function __construct(Module $module)
    {
        parent::__construct($module);
        $this->setVar(self::VAR_MODULE,$module->getModule());
    }

    public function getTemplatePath()
    {
        return __DIR__ . "/Template/Helper.tpl";
    }

    protected function setModule($module)
    {
        //@TODO validate MODULE var
        $this->setVar(self::VAR_MODULE,$module);
    }

    public function setHelperName($name)
    {
        //@TODO validate HELPER_NAME var
        $this->setVar(self::VAR_HELPER_NAME,$name);
    }

    public function setName($name)
    {
        //@TODO deprecate
        $this->setHelperName($name);
    }

    public function setExtends($extends=null)
    {
        //@TODO validate EXTENDS var
        $extends = ( $extends === null ) ? self::DEFAULT_EXTENDS : $extends;
        $this->setVar(self::VAR_EXTENDS,$extends);
    }

    public function getPath()
    {
        return $this->module->pathAppCode() . '/Helper/' . str_replace("_","/",$this->getVar(self::VAR_HELPER_NAME)) . ".php";
    }

    public function getContents()
    {
        return $this->outputPHP();
    }
}