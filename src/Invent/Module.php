<?php
namespace Invent;

use Exception;
use Invent\Commands\AbstractModuleCommand;
use Symfony\Component\Console\Input\InputInterface;

class Module
{
    const TYPE_XML = ['admin','config','system','init'];
    const DEFAULT_LOCALE = "local";

    private $locale;
    private $module;
    private $key;
    private $namespace;
    private $name;

    public static function mageBaseDir()
    {
        return \Mage::getBaseDir();
    }

    public static function getModuleName($class)
    {
        if( is_object($class) ) {
            $class = get_class($class);
        }
        $pos1 = strpos($class, "_");
        $pos2 = strpos($class, "_", $pos1 + strlen("_"));
        return substr($class, 0, $pos2);
    }

    public static function getHelperName($class)
    {
        if( is_object($class) ) {
            $class = get_class($class);
        }
        return substr($class,strpos($class,"_Helper_")+strlen("_Helper_"),strlen($class));
    }

    /**
     * Module constructor.
     * @param InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        $this->processModule($input);
        $this->processKey($input);
        $this->processLocale($input);
    }

    private function processModule(InputInterface $input)
    {
        $this->module = $input->getArgument(AbstractModuleCommand::ARGUMENT_MODULE);
        list($this->namespace,$this->name) = explode("_",$this->module);
    }

    private function processKey(InputInterface $input)
    {
        $key = $input->getOption(AbstractModuleCommand::OPTION_KEY);
        $this->key = ( is_null($key) ) ? strtolower($this->module) : $key;
    }

    private function processLocale(InputInterface $input)
    {
        $core = $input->getOption(AbstractModuleCommand::OPTION_CORE);
        $community = $input->getOption(AbstractModuleCommand::OPTION_COMMUNITY);
        $local = $input->getOption(AbstractModuleCommand::OPTION_LOCAL);
        if( $core + $community + $local > 1 ) {
            throw new Exception("Can only assign one locale flag");
        } elseif( $core ) {
            $this->locale = AbstractModuleCommand::OPTION_CORE;
        } elseif( $community ) {
            $this->locale = AbstractModuleCommand::OPTION_COMMUNITY;
        } elseif( $local ) {
            $this->locale = AbstractModuleCommand::OPTION_LOCAL;
        } else {
            $this->locale = self::DEFAULT_LOCALE;
        }
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getName()
    {
        return $this->name;
    }

    public function pathAppCode($path=null)
    {
        $appCode = self::mageBaseDir() . '/app/code/' . $this->locale . '/' . $this->namespace . '/' . $this->name;
        return ( is_null($path) ) ? $appCode : $appCode . "/" . $path;
    }
}