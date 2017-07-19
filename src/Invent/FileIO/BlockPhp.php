<?php
namespace Invent\FileIO;

use Invent\Module;

class BlockPhp extends AbstractPhp implements FileIOInterface
{
    const DEFAULT_EXTENDS = "Mage_Core_Block_Abstract";
    const VAR_MODULE = "MODULE";
    const VAR_BLOCK_NAME = "BLOCK_NAME";
    const VAR_EXTENDS = "EXTENDS";

    protected $vars = [
        self::VAR_EXTENDS=>null,
        self::VAR_BLOCK_NAME=>null,
        self::VAR_MODULE=>null,
s    ];

    public function __construct(Module $module)
    {
        parent::__construct($module);
        $this->setVar(self::VAR_MODULE,$module->getModule());
    }

    public function getTemplatePath()
    {
        return __DIR__ . "/Template/Block.tpl";
    }

    protected function setModule($module)
    {
        $this->setVar(self::VAR_MODULE,$module);
    }

    public function setBlockName($name)
    {
        $this->setVar(self::VAR_BLOCK_NAME,$name);
    }

    public function setExtends($extends=null)
    {
        $this->setVar(self::VAR_EXTENDS,( ($extends===null)?self::DEFAULT_EXTENDS:$extends) );
    }

    public function getPath()
    {
        return $this->module->pathAppCode() . '/Block/' . str_replace("_","/",$this->getVar(self::VAR_BLOCK_NAME)) . ".php";
    }

    public function getContents()
    {
        return $this->outputPHP();
    }
}