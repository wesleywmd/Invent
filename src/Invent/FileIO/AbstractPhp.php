<?php
namespace Invent\FileIO;

use Exception;
use Invent\Module;

abstract class AbstractPhp
{
    protected $module;
    protected $vars;
    
    public function __construct(Module $module,$fileExists=null)
    {
        $this->module = $module;
    }

    abstract public function getTemplatePath();

    public function isVar($key)
    {
        return (bool) key_exists($key,$this->vars);
    }

    public function getVar($key)
    {
        if( $this->isVar($key) ) {
            return $this->vars[$key];
        } else {
            throw new Exception("Key does not exist in Php template");
        }
    }

    public function setVar($key,$value)
    {
        if( $this->isVar($key) ) {
            $this->vars[$key] = $value;
        } else {
            throw new Exception("Key does not exist in Php template");
        }
    }

    public function outputPHP()
    {
        $template = file_get_contents($this->getTemplatePath());
        foreach( $this->vars as $key=>$var) {
            $template = str_replace("{{".$key."}}",$var,$template);
        }
        return $template;
    }
}