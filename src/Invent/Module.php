<?php
namespace Invent;

use Invent\Commands\AbstractModuleCommand;
use Symfony\Component\Console\Input\InputInterface;

class Module
{
    const TYPE_XML = ['admin','config','system','init'];

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
    public function __construct($input)
    {
        // process $module
        $this->module = $input->getArgument(AbstractModuleCommand::ARGUMENT_MODULE);
        list($this->namespace,$this->name) = explode("_",$this->module);

        // process $key
        $key = $input->getOption(AbstractModuleCommand::OPTION_KEY);
        $this->key = ( is_null($key) ) ? strtolower($this->module) : $key;

        // process $locale
        if( $input->getOption(AbstractModuleCommand::OPTION_LOCALE) ) {
            $this->locale = $input->getOption(AbstractModuleCommand::OPTION_LOCALE);
        } else {
            $this->locale = "local";
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

    public function pathAppCode()
    {
        return self::mageBaseDir() . '/app/code/' . $this->locale . '/' . $this->namespace . '/' . $this->name;
    }
}