<?php

namespace core;

class File
{
    private string $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }
    public function Copy($path)
    {
        copy($this->fileName, $path);
    }

    public function Delete($path): void
    {
        if(!file_exists($path)) {
            print_r("File not exists");
        } else {
            unlink($path);
        }
    }

    static function Find($path, $key): string
    {
        $path =glob($path . $key);
        if(empty($path)) {
            die('File not exists');
        } else {
            $path = $path[0];
        }
        return $path;
    }

}