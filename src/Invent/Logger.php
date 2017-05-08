<?php
namespace Invent;

use Symfony\Component\Console\Output\OutputInterface;

class Logger
{
    const PRINT_NEW_LINE = "\n";
    const PRINT_END_LINE = "|";
    const PRINT_SPACE = " ";
    const PRINT_LINE = "-";
    const PRINT_CROSS = "+";
    const PRINT_WIDTH = 75;
    const PRINT_MARGIN = 2;
    const PRINT_PADDING = 4;
    const PRINT_HEADER_PRE = "*~ ";
    const PRINT_HEADER_POST = " ~*";

    /** @var  OutputInterface */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Splits message string into array of proper length lines
     * @param string $msg
     * @return array
     */
    private function splitMessage($msg)
    {
        $msg = trim($msg);
        $return = [];
        while( strlen($msg) ) {
            if( strlen($msg) < self::PRINT_WIDTH ) {
                $return[] = $msg;
                $msg = "";
            } else {
                $line = preg_replace('/\s+?(\S+)?$/','',substr($msg,0,self::PRINT_WIDTH));
                $msg = trim(substr($msg,strlen($line)));
                $return[] = $line;
            }
        }
        return $return;
    }

    public function margin() { $this->output->writeln(""); }

    public function wrap($msg,$type=false)
    {
        return ( $type ) ? "<{$type}>{$msg}</{$type}>" : $msg;
    }
    public function wrapError($msg) { return $this->wrap($msg,"error"); }
    public function wrapInfo($msg) { return $this->wrap($msg,"info"); }
    public function wrapComment($msg) { return $this->wrap($msg,"comment"); }
    public function wrapQuestion($msg) { return $this->wrap($msg,"question"); }

    public function display($msg,$type=false)
    {
        foreach( $this->splitMessage($msg) as $line) {
            $this->output->writeln($this->wrap($line,$type));
        }
    }
    public function error($msg) { $this->display($msg, "error"); }
    public function info($msg) { $this->display($msg, "info"); }
    public function comment($msg) { $this->display($msg, "comment"); }
    public function question($msg) { $this->display($msg, "question"); }

    public function displayMessage($msg,$type=false)
    {
        $this->displayMargin($type);
        $this->displayBorder($type);
        $this->displaySpacer($type);
        foreach( $this->splitMessage($msg) as $line ) {
            $this->displayLine($line,$type);
        }
        $this->displaySpacer($type);
        $this->displayBorder($type);
        $this->displayMargin($type);
    }
    public function errorMessage($msg) { $this->displayMessage($msg, "error"); }
    public function infoMessage($msg) { $this->displayMessage($msg, "info"); }
    public function commentMessage($msg) { $this->displayMessage($msg, "comment"); }
    public function questionMessage($msg) { $this->displayMessage($msg, "question"); }

    public function displayBlock($header,$msg,$type=false)
    {
        $this->displayMargin($type);
        $this->displayBorder($type);
        $this->displayLine(self::PRINT_HEADER_PRE . " " . $header . " " . self::PRINT_HEADER_POST, $type);
        $this->displayBorder($type);
        $this->displaySpacer($type);
        foreach( $this->splitMessage($msg) as $line ) {
            $this->displayLine($line,$type);
        }
        $this->displaySpacer($type);
        $this->displayBorder($type);
        $this->displayMargin($type);
    }
    public function errorBlock($header, $msg) { $this->displayBlock($header, $msg, "error"); }
    public function infoBlock($header, $msg) { $this->displayBlock($header, $msg, "info"); }
    public function commentBlock($header, $msg) { $this->displayBlock($header, $msg, "comment"); }
    public function questionBlock($header, $msg) { $this->displayBlock($header, $msg, "question"); }

    public function displayMultiBlock($header,$blocks,$type=false)
    {
        $this->displayMargin($type);
        $this->displayBorder($type);
        $this->displayLine(self::PRINT_HEADER_PRE . " " . self::PRINT_HEADER_PRE . " " . $header . " " . self::PRINT_HEADER_POST . " " . self::PRINT_HEADER_POST, $type);
        foreach( $blocks as $header=>$body) {
            $this->displayBorder($type);
            $this->displayLine(self::PRINT_HEADER_PRE . " " . $header . " " . self::PRINT_HEADER_POST, $type);
            $this->displayBorder($type);
            $this->displaySpacer($type);
            if( is_string($body) ) {
                $body = $this->splitMessage($body);
            }
            foreach( $body as $line ) {
                $this->displayLine($line,$type);
            }
            $this->displaySpacer($type);
        }
        $this->displayBorder($type);
        $this->displayMargin($type);
    }

    public function displayObject($obj,$type=false)
    {
        $class = $parent = get_class($obj);
        $blocks = [ 'Parents'=>[], 'Variables'=>[], 'Methods'=>[] ];
        while($parent = get_parent_class($parent)) {
            $blocks['Parents'][] = $parent;
        }
        foreach( get_class_vars($obj) as $var ) {
            $blocks['Variables'][] = "- ".$var;
        }
        foreach( get_class_methods($obj) as $method ) {
            $blocks['Methods'][] = "- " . $method . "()";
        }
        $this->displayMultiBlock($class,$blocks,$type);
    }

    private function pad($pad,$msg,$char=" ")
    {
        return str_pad("",$pad,$char) . $msg . str_pad("",$pad,$char);
    }

    private function displayMargin($type=false)
    {
        $print = str_pad("",self::PRINT_WIDTH," ");
        $print = $this->pad(self::PRINT_PADDING,$print);
        $print = $this->pad(strlen(self::PRINT_END_LINE),$print);
        $print = $this->pad(self::PRINT_MARGIN,$print);
        $print = $this->wrap($print,$type);
        $this->output->writeln($print);
    }

    private function displayBorder($type=false)
    {
        $print = str_pad("",self::PRINT_WIDTH+self::PRINT_PADDING*2,self::PRINT_LINE);
        $print = $this->pad(strlen(self::PRINT_CROSS),$print,self::PRINT_CROSS);
        $print = $this->pad(self::PRINT_MARGIN,$print);
        $print = $this->wrap($print,$type);
        $this->output->writeln($print);
    }

    private function displaySpacer($type=false)
    {
        $print = str_pad("",self::PRINT_WIDTH," ");
        $print = $this->pad(self::PRINT_PADDING,$print);
        $print = $this->pad(strlen(self::PRINT_END_LINE),$print,self::PRINT_END_LINE);
        $print = $this->pad(self::PRINT_MARGIN,$print);
        $print = $this->wrap($print,$type);
        $this->output->writeln($print);
    }

    private function displayLine($msg,$type=false)
    {
        $msg = str_pad($msg,self::PRINT_WIDTH," ");
        $msg = $this->pad(self::PRINT_PADDING,$msg);
        $msg = $this->pad(strlen(self::PRINT_END_LINE),$msg,self::PRINT_END_LINE);
        $msg = $this->pad(self::PRINT_MARGIN,$msg);
        $msg = $this->wrap($msg,$type);
        $this->output->writeln($msg);
    }
}

