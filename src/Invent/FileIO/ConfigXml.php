<?php
namespace Invent\FileIO;

class ConfigXml extends AbstractXml implements FileIOInterface
{
    const CODE_CONFIG = "config";
    const CODE_MODULES = "modules";
    const CODE_MODULE = "module";
    const CODE_VERSION = "version";
    const CODE_GLOBAL = "global";
    const CODE_MODELS = "models";
    const CODE_MODELS_KEY = "models:key";
    const CODE_MODELS_CLASS = "models:class";
    const CODE_BLOCKS = "blocks";
    const CODE_BLOCKS_KEY = "blocks:key";
    const CODE_BLOCKS_CLASS = "blocks:class";
    const CODE_HELPERS = "helpers";
    const CODE_HELPERS_KEY = "helpers:key";
    const CODE_HELPERS_CLASS = "helpers:class";

    public function getXPathQuery($code)
    {
        switch($code) {
            case self::CODE_CONFIG:        return "//config";
            case self::CODE_MODULES:       return $this->getXPathQuery(self::CODE_CONFIG) . "/modules";
            case self::CODE_MODULE:        return $this->getXPathQuery(self::CODE_MODULES) . "/" . $this->module->getModule();
            case self::CODE_VERSION:       return $this->getXPathQuery(self::CODE_MODULE) . "/version";
            case self::CODE_GLOBAL:        return $this->getXPathQuery(self::CODE_CONFIG) . "/global";
            case self::CODE_MODELS:        return $this->getXPathQuery(self::CODE_GLOBAL) . "/models";
            case self::CODE_MODELS_KEY:    return $this->getXPathQuery(self::CODE_MODELS) . "/" . $this->module->getKey();
            case self::CODE_MODELS_CLASS:  return $this->getXPathQuery(self::CODE_MODELS_KEY) . "/class";
            case self::CODE_BLOCKS:        return $this->getXPathQuery(self::CODE_GLOBAL) . "/blocks";
            case self::CODE_BLOCKS_KEY:    return $this->getXPathQuery(self::CODE_BLOCKS) . "/" . $this->module->getKey();
            case self::CODE_BLOCKS_CLASS:  return $this->getXPathQuery(self::CODE_BLOCKS_KEY) . "/class";
            case self::CODE_HELPERS:       return $this->getXPathQuery(self::CODE_GLOBAL) . "/helpers";
            case self::CODE_HELPERS_KEY:   return $this->getXPathQuery(self::CODE_HELPERS) . "/" . $this->module->getKey();
            case self::CODE_HELPERS_CLASS: return $this->getXPathQuery(self::CODE_HELPERS_KEY) . "/class";


            default: throw new \Exception("InitXml XPath code [{$code}] not valid");
        }
    }

    protected function createQueryNode($code)
    {
        switch($code) {
            case self::CODE_CONFIG:        $this->dom->appendChild($this->createNode("config"));                                                 break;
            case self::CODE_MODULES:       $this->findQueryNode(self::CODE_CONFIG)->appendChild($this->createNode("modules"));                   break;
            case self::CODE_MODULE:        $this->findQueryNode(self::CODE_MODULES)->appendChild($this->createNode($this->module->getModule())); break;
            case self::CODE_VERSION:       $this->findQueryNode(self::CODE_MODULE)->appendChild($this->createNode("version"));                   break;
            case self::CODE_GLOBAL:        $this->findQueryNode(self::CODE_CONFIG)->appendChild($this->createNode("global"));                    break;
            case self::CODE_MODELS:        $this->findQueryNode(self::CODE_GLOBAL)->appendChild($this->createNode("models"));                    break;
            case self::CODE_MODELS_KEY:    $this->findQueryNode(self::CODE_MODELS)->appendChild($this->createNode($this->module->getKey()));     break;
            case self::CODE_MODELS_CLASS:  $this->findQueryNode(self::CODE_MODELS_KEY)->appendChild($this->createNode("class"));                 break;
            case self::CODE_BLOCKS:        $this->findQueryNode(self::CODE_GLOBAL)->appendChild($this->createNode("blocks"));                    break;
            case self::CODE_BLOCKS_KEY:    $this->findQueryNode(self::CODE_BLOCKS)->appendChild($this->createNode($this->module->getKey()));     break;
            case self::CODE_BLOCKS_CLASS:  $this->findQueryNode(self::CODE_BLOCKS_KEY)->appendChild($this->createNode("class"));                 break;
            case self::CODE_HELPERS:       $this->findQueryNode(self::CODE_GLOBAL)->appendChild($this->createNode("helpers"));                   break;
            case self::CODE_HELPERS_KEY:   $this->findQueryNode(self::CODE_HELPERS)->appendChild($this->createNode($this->module->getKey()));    break;
            case self::CODE_HELPERS_CLASS: $this->findQueryNode(self::CODE_HELPERS_KEY)->appendChild($this->createNode("class"));                break;

            default: throw new \Exception("InitXml XPath code [{$code}] not valid");
        }
    }

    public function setVersion($version="0.0.0.1")
    {
        $this->setQueryNodeValue(self::CODE_VERSION,$version);
    }

    public function registerModels()
    {
        $this->setQueryNodeValue(self::CODE_MODELS_CLASS,$this->module->getModule() . "_Model");
    }
    
    public function registerBlocks()
    {
        $this->setQueryNodeValue(self::CODE_BLOCKS_CLASS,$this->module->getModule() . "_Block");
    }
    
    public function registerHelpers()
    {
        $this->setQueryNodeValue(self::CODE_HELPERS_CLASS,$this->module->getModule() . "_Helper");
    }

    public function rewriteHelper($location,$rewriteName,$rewriteObject)
    {
        $xpath = $this->getXPathQuery(self::CODE_HELPERS) . "/". $location . "/rewrite/" . $rewriteName;
        if( ! $this->isNode($xpath) ) {
            $this->findQueryNode(self::CODE_HELPERS)->appendChild($this->createNodeWithChildren($location,[
                $this->createNodeWithChildren("rewrite",[
                    $this->createNode($rewriteName,$rewriteObject)
                ])
            ]));
        } else {
            $this->getNode($xpath)->nodeValue = $rewriteObject;
        }
    }

    public function getPath()
    {
        return $this->module->pathAppCode() . '/etc/config.xml';
    }

    public function getContents()
    {
        return $this->outputXML();
    }
}