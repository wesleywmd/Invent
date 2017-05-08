<?php
namespace Invent\FileIO;

use DOMDocument;
use DOMXPath;
use Exception;
use Invent\Module;

abstract class AbstractXml
{
    /**
     * @var DOMDocument
     */
    protected $dom;
    protected $type;
    protected $module;
    
    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->dom = new DOMDocument();
    }

    public function loadFromPath()
    {
        if( ! $this->dom->load($this->getPath()) ) {
            throw new Exception("Invalid Xml File [{$this->getPath()}]");
        }
    }

    abstract public function getPath();
    abstract public function getXPathQuery($code);
    abstract protected function createQueryNode($code);

    protected function queryXPath($query)
    {
        $xpath = new DOMXPath($this->dom);
        return $xpath->query($query);
    }

    public function getNode($query)
    {
        $found = $this->queryXPath($query);
        if( $found->length === 1 ) {
            return $found->item(0);
        }
        return false;
    }
    
    protected function getQueryNode($code)
    {
        return $this->getNode($this->getXPathQuery($code));
    }

    public function findQueryNode($code)
    {
        if( ! $this->isQueryNode($code) ) {
            $this->createQueryNode($code);
        }
        return $this->getQueryNode($code);
    }

    public function getNodeValue($query)
    {
        if( $this->isNode($query) ) {
            return $this->getNode($query)->nodeValue;
        }
        return false;
    }

    public function getQueryNodeValue($code)
    {
        return $this->findQueryNode($code)->nodeValue;
    }

    public function setQueryNodeValue($code,$value)
    {
        $this->findQueryNode($code)->nodeValue = $value;
    }

    public function isNode($query)
    {
        $found = $this->queryXPath($query);
        return (bool) ($found->length > 0);
    }

    public function isQueryNode($code)
    {
        return $this->isNode($this->getXPathQuery($code));
    }

    public function createNode($name,$value=null)
    {
        if( is_null($value) ) {
            return $this->dom->createElement($name);
        } else {
            return $this->dom->createElement($name,$value);
        }
    }

    public function createNodeWithChildren($name,$children=[])
    {
        $return = $this->createNode($name);
        foreach( $children as $child ) {
            $return->appendChild($child);
        }
        return $return;
    }

    public function outputXML()
    {
        // save output of our xml
        $outXML = $this->dom->saveXML();

        // re-instantiate the DOM Object to clean formatting of the outputted Xml
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        $xml->loadXML($outXML);

        // return newly formatted xml output
        return $xml->saveXML();
    }

}