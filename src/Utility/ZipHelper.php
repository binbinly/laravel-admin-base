<?php


namespace AdminBase\Utility;

use ZipArchive;

class ZipHelper
{
    /**
     * 解压zip文件
     * @param $zipFile
     * @param $targetDir
     */
    public static function extract($zipFile, $targetDir){
        if(!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $zip = new ZipArchive();
        $openRes = $zip->open($zipFile);
        if ($openRes === TRUE) {
            $zip->extractTo($targetDir);
            $zip->close();
        }
    }
}