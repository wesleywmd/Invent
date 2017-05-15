<?php
namespace Invent;

use Exception;
use Invent\FileIO\AbstractXml;
use Invent\FileIO\AdminhtmlXml;
use Invent\FileIO\ConfigXml;
use Invent\FileIO\FileIOInterface;
use Invent\FileIO\HelperPhp;
use Invent\FileIO\InitXml;
use Invent\FileIO\SetupScriptPhp;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Finder\SplFileInfo;

class FileIO
{
    const PHP_HELPER = "helper";
    const PHP_SETUP_INSTALL = "setup_install";
    const PHP_SETUP_UPGRADE = "setup_upgrade";
    const XML_ADMINHTML = "adminhtml";
    const XML_CONFIG = "config";
    const XML_INIT = "init";


    public function isFile($path)
    {
        return (bool) file_exists($path);
    }

    public function isDir($path)
    {
        return (bool) is_dir($path);
    }

    public function makeDir($path)
    {
        mkdir($path,0777,true);
    }

    public function findFileByPattern($pattern,$path)
    {
        $dir_iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if( substr($file->getFilename(), 0, strlen($pattern)) === $pattern ) {
                return true;
            }
        }
        return false;
    }

    public function moduleExists(Module $module)
    {
        $initXml = $this->createFile(self::XML_INIT,$module);
        $configXml = $this->createFile(self::XML_CONFIG,$module);
        return (bool) ( $this->isFile($initXml->getPath()) && $this->isFile($configXml->getPath()) );
    }

    public function writeFile(FileIOInterface $file)
    {
        if( ! self::isDir(dirname($file->getPath())) ) {
            self::makeDir(dirname($file->getPath()));
        }
        $handle = fopen($file->getPath(),"w");
        fwrite($handle,$file->getContents());
        fclose($handle);
    }

    public function createFile($type,Module $module,$fileExists=null) {
        /** @var FileIOInterface $file */
        switch( $type ) {
            case self::PHP_HELPER:
                /** @var HelperPhp $file */
                $file = new HelperPhp($module);
                break;
            case self::PHP_SETUP_INSTALL:
                /** @var SetupScriptPhp $file */
                $file = new SetupScriptPhp(SetupScriptPhp::TYPE_INSTALL,$module);
                break;
            case self::PHP_SETUP_UPGRADE:
                /** @var SetupScriptPhp $file */
                $file = new SetupScriptPhp(SetupScriptPhp::TYPE_UPGRADE,$module);
                break;
            case self::XML_ADMINHTML:
                /** @var AdminhtmlXml $file */
                $file = new AdminhtmlXml($module);
                break;
            case self::XML_CONFIG:
                /** @var ConfigXml $file */
                $file = new ConfigXml($module);
                break;
            case self::XML_INIT:
                /** @var InitXml $file */
                $file = new InitXml($module);
                break;
            default:
                throw new Exception("File Type ". $type . " is not registered.");
        }
        if( ! is_null($fileExists) ) {
            if( $fileExists && !$this->isFile($file->getPath()) ) {
                throw new Exception("File must exist");
            } elseif( !$fileExists && $this->isFile($file->getPath()) ) {
                throw new Exception("File must not exist");
            }
        }
        if( $file instanceof AbstractXml && $this->isFile($file->getPath()) ) {
            /** @var AbstractXml $file */
            $file->loadFromPath();
        }
        return $file;
    }

    public function destroyFile($path)
    {
        unlink($path);
    }

    public function destroyDir($dir)
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

}