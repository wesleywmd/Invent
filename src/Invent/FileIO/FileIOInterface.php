<?php
namespace Invent\FileIO;

interface FileIOInterface
{
    const FILE_TYPE = "type";
    public function getPath();
    public function getContents();
}