<?php
//Класс для базової роботи с файлами.
namespace classes;

class File
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
    public function Copy(string $path)
    {
        copy($this->path, $path);
    }

    static public function createFile($path): bool
    {
        file_put_contents($path, "");
        return self::checkExists($path);

    }

    static public function delete(string $path): void
    {
        if(!file_exists($path)) {
            print_r("File not exists");
        } else {
            unlink($path);
        }
    }

    static function find(string $path, string $key): string
    {
        $path =glob($path . $key);
        if(empty($path)) {
            die('Файл відсутній: ' . $path[0]);
        } else {
            $path = $path[0];
        }
        return $path;
    }

    static function checkExists(string $path): bool
    {
        file_exists($path) ? $result = true : $result = false;
        return $result;
    }
    static function getFormatFile(string $path, string $type = 'xlsx')
    {
        if(self::checkExists($path)) {
            pathinfo($path, PATHINFO_EXTENSION) === $type ? $result=true : $result=false;
            return $result;
        }
    }

    static function checkSize($path, $size) {
        if(filesize($path) >= ($size * 1048576)) {
            return false;
        }
        return true;
    }

    static function moveUploadedFile(string $from, string $to)
    {
        if(!self::checkExists($from)){
            exit;
        }
        move_uploaded_file($from, $to);
    }


}