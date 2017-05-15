<?php
namespace Invent\FileIO;

use Invent\Module;

class SetupScriptPhp extends AbstractPhp implements FileIOInterface
{
    const TYPE_INSTALL = "install-";
    const TYPE_UPGRADE = "upgrade-";
    protected $vars = [];

    protected $type;
    protected $currentVersion = "";
    protected $newVersion = "";

    public function __construct($type, Module $module)
    {
        parent::__construct($module);
        $this->type = $type;
    }

    public function setCurrentVersion($version)
    {
        $this->currentVersion = $version;
    }

    public function setNewVersion($version)
    {
        $this->newVersion = $version;
    }

    public function getTemplatePath()
    {
        return __DIR__ . "/Template/SetupScript.tpl";
    }

    public function getPath()
    {
        $versions = $this->newVersion;
        if( $this->type === self::TYPE_UPGRADE ) {
            $versions = $this->currentVersion . "-" . $versions;
        }
        return $this->module->pathAppCode("sql/" . $this->module->getKey() . "_setup/" . $this->type . $versions . ".php");
    }

    public function getContents()
    {
        return $this->outputPHP();
    }
}