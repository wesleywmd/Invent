<?php
namespace Invent;

use DOMDocument;

class MageService
{
    private function washXmlOutput($xmlString)
    {
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        $xml->loadXML($xmlString);
        return $xml->saveXML();
    }

    private function compileModuleXml($type)
    {
        \Mage::app('admin');

        return \Mage::getConfig()
            ->loadModulesConfiguration($type . '.xml')
            ->getNode()
            ->asXML();
    }

    public function inspectSystemXml($area=false)
    {
        $dom = new DOMDocument();
        $dom->loadXML($this->washXmlOutput($this->compileModuleXml("system")));
        $xpath = new \DOMXPath($dom);
        switch($area) {
            case false: break;
            case "sections":
                $xml = $xpath->query("//config/tabs")->item(0);
                break;
        }
        return $dom->saveXML($xml);
    }

    public function inspectConfigXml()
    {
        return $this->washXmlOutput($this->compileModuleXml("config"));
    }

    public function inspectAdminHtmlXml()
    {
        return $this->washXmlOutput($this->compileModuleXml("adminhtml"));
    }

    public function getRegisteredAcls()
    {
        $dom = new DOMDocument();
        $dom->loadXML($this->washXmlOutput($this->compileModuleXml("config")));
        $xpath = new \DOMXPath($dom);
        $xml = $xpath->query("//config/adminhtml/acl")->item(0);
        return $dom->saveXML($xml);
    }

    public function getAcls()
    {
        echo $this->washXmlOutput(\Mage::getConfig()
       //     ->loadModulesConfiguration('config.xml')
            ->getNode("adminhtml/acl")->asXml());
    }

    public function hasAcl($aclPath)
    {
        $resources = \Mage::getModel('admin/roles')->getResourcesTree();
        $nodes = $resources->xpath("//*[@aclpath='{$aclPath}']");
        return !is_null($nodes[0]);
    }

    public function auditAclPath($path)
    {
        $path = explode("/",$path);
        $currentPath = "";
        $service = new MageService();
        foreach( $path as $node ) {
            if( $currentPath === "" ) {
                $currentPath = $node;
            } else {
                $currentPath .= "/" . $node;
            }

            if( $currentXPath !== $this->getXPathQuery(self::CODE_ACL_RESOURCES) ) {
                if( !$this->isNode($currentXPath . "/children") ) {
                    $this->getNode($currentXPath)->appendChild($this->createNode("children"));
                }
                $currentXPath .= "/children";
            }

            if( $service->hasAcl($currentPath) ) {
                if( !$this->isNode($currentXPath . "/" . $node) ) {
                    $this->getNode($currentXPath)->appendChild($this->createNodeWithChildren($node,[]));
                }
                $currentXPath .= "/" . $node;
            } else {
                if( !$this->isNode($currentXPath . "/" . $node) ) {
                    $domNode = $this->createNodeWithChildren($node,[
                        $this->createNode("title","TITLE"),
                        $this->createNode("sort_order","SORT_ORDER"),
                    ]);
                    $domNode->setAttribute("translate","title");
                    $domNode->setAttribute("module",$this->module->getKey());
                    $this->getNode($currentXPath)->appendChild($domNode);
                }
                $currentXPath .= "/" . $node;
            }
        }
    }
}