<?php
namespace Invent\FileIO;

class AdminhtmlXml extends AbstractXml implements FileIOInterface
{
    const CODE_CONFIG = "config";
    const CODE_MENU = "menu";
    const CODE_ACL = "acl";
    const CODE_ACL_RESOURCES = "acl_resource";

    public function getXPathQuery($code)
    {
        switch($code) {
            case self::CODE_CONFIG:        return "//config";
            case self::CODE_MENU:          return $this->getXPathQuery(self::CODE_CONFIG) . "/menu";
            case self::CODE_ACL:           return $this->getXPathQuery(self::CODE_CONFIG) . "/acl";
            case self::CODE_ACL_RESOURCES: return $this->getXPathQuery(self::CODE_ACL) . "/resources";

            default: throw new \Exception("AdminhtmlXml XPath code [{$code}] not valid");
        }
    }

    protected function createQueryNode($code)
    {
        switch($code) {
            case self::CODE_CONFIG:        $this->dom->appendChild($this->createNode("config"));                             break;
            case self::CODE_MENU:          $this->findQueryNode(self::CODE_CONFIG)->appendChild($this->createNode("menu"));  break;
            case self::CODE_ACL:           $this->findQueryNode(self::CODE_CONFIG)->appendChild($this->createNode("acl"));   break;
            case self::CODE_ACL_RESOURCES: $this->findQueryNode(self::CODE_ACL)->appendChild($this->createNode("resources")); break;


            default: throw new \Exception("AdminhtmlXml XPath code [{$code}] not valid");
        }
    }

    public function getPath()
    {
        return $this->module->pathAppCode() . '/etc/adminhtml.xml';
    }

    public function getContents()
    {
        return $this->outputXML();
    }
}