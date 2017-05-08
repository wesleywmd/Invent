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
}