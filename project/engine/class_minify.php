<?php

namespace Engine;

class Minify {

    const dirs = [
        'resource/js/',
        'resource/css/'
    ];

    public static function init() {

        foreach (self::dirs as $dir) {

            if (is_dir($dir)) {
                foreach (new \DirectoryIterator($dir) as $fileInfo) {
                    if ($fileInfo->isDot() || $fileInfo->isDir()) {
                        continue;
                    }

                    if ($fileInfo->getExtension() == 'css') {
                        self::minifyCSS('css/' . $fileInfo->getFilename());
                    } else if (($fileInfo->getExtension() == 'js')) {
                        self::minifyJS('js/' . $fileInfo->getFilename());
                    }
                }
            }
        }
    }

    private static function getMinFullPath($path) {
        $search = ['resource/css', 'resource/js'];
        $replace = ['resource/css/min', 'resource/js/min'];
        return str_replace($search, $replace, $path);
    }

    public static function minifyJS($file) {

        $fileFullPath = BASE_SERVER_URL . 'resource/' . $file;
        $data = file_get_contents($fileFullPath);
        $data = self::removeComments2($data);
        $data = self::removeWhitespace($data);
        $data = self::removeComments($data);
        $data = self::removeSpaceAfterFunctionArgs($data);
        touch(self::getMinFullPath($fileFullPath));
        file_put_contents(self::getMinFullPath($fileFullPath), $data);
    }

    public static function minifyCSS($file) {

        $fileFullPath = BASE_SERVER_URL . 'resource/' . $file;
        $data = file_get_contents($fileFullPath);
        $data = self::removeWhitespace($data);
        $data = self::removeSpaceAfterColons($data);
        $data = self::removeSpaceBeforeBrace($data);
        $data = self::removeComments($data);
        touch(self::getMinFullPath($fileFullPath));
        file_put_contents(self::getMinFullPath($fileFullPath), $data);
    }

    private static function removeWhitespace($txt) {
        return str_replace(["\r\n", "\r", "\n", "\t", '    ', '   ', '  '], '', $txt);
    }

    private static function removeSpaceAfterColons($txt) {
        return str_replace(': ', ':', $txt);
    }

    private static function removeSpaceBeforeBrace($txt) {
        return str_replace(' {', '{', $txt);
    }

    private static function removeSpaceAfterFunctionArgs($txt) {
        return str_replace(') {', '){', $txt);
    }

    private static function removeComments($txt) {
        return preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $txt);
    }

    private static function removeComments2($txt) {
        return preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $txt);
    }
}