<?php
namespace Invent\FileIO;

use Invent\MageService;

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

    public function registerAclPath($path)
    {
        // ensure acl resources node is built
        $this->createQueryNode(self::CODE_ACL_RESOURCES);

        $path = explode("/",$path);
        $currentPath = "";
        $currentXPath = $this->getXPathQuery(self::CODE_ACL_RESOURCES);
        $service = new MageService();
        foreach( $path as $node ) {
            var_dump($currentXPath);

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








    public function registerTopMenu($code,$attr) {
        // main node
        // title
        // action
        // sort order
        // depends
        // children
    }

    private function createMenuItem($title,$action,$sortOrder,$children,$depends)
    {
        return [
            "title"=>$title,
            "action"=>$action,
            "sort_order"=>$sortOrder,
            "children"=>$children,

        ];
    }
}