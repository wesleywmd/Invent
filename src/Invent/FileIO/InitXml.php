<?php
namespace Invent\FileIO;

class InitXml extends AbstractXml implements FileIOInterface
{
    const CODE_CONFIG = "config";
    const CODE_MODULES = "modules";
    const CODE_MODULE = "module";
    const CODE_ACTIVE = "active";
    const CODE_CODEPOOL = "codepool";

    public function getXPathQuery($code)
    {
        switch($code) {
            case self::CODE_CONFIG:   return "//config";
            case self::CODE_MODULES:  return $this->getXPathQuery(self::CODE_CONFIG) . "/modules";
            case self::CODE_MODULE:   return $this->getXPathQuery(self::CODE_MODULES) . "/" . $this->module->getModule();
            case self::CODE_ACTIVE:   return $this->getXPathQuery(self::CODE_MODULE) . "/active";
            case self::CODE_CODEPOOL: return $this->getXPathQuery(self::CODE_MODULE) . "/codePool";

            default: throw new \Exception("InitXml XPath code [{$code}] not valid");
        }
    }

    protected function createQueryNode($code)
    {
        switch($code) {
            case self::CODE_CONFIG:   $this->dom->appendChild($this->createNode("config"));                                                 break;
            case self::CODE_MODULES:  $this->findQueryNode(self::CODE_CONFIG)->appendChild($this->createNode("modules"));                   break;
            case self::CODE_MODULE:   $this->findQueryNode(self::CODE_MODULES)->appendChild($this->createNode($this->module->getModule())); break;
            case self::CODE_ACTIVE:   $this->findQueryNode(self::CODE_MODULE)->appendChild($this->createNode("active"));                    break;
            case self::CODE_CODEPOOL: $this->findQueryNode(self::CODE_MODULE)->appendChild($this->createNode("codePool"));                  break;

            default: throw new \Exception("InitXml XPath code [{$code}] not valid");
        }
    }

    public function setActive($active="true")
    {
        $this->setQueryNodeValue(self::CODE_ACTIVE,$active);
    }

    public function setCodePool($codePool="local")
    {
        $this->setQueryNodeValue(self::CODE_CODEPOOL,$codePool);
    }

    public function getPath()
    {
        return $this->module->mageBaseDir() . '/app/etc/modules/' .  $this->module->getModule() . '.xml';
    }

    public function getContents()
    {
        return $this->outputXML();
    }
}