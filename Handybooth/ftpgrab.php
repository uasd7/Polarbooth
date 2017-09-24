<?php
/**
 * Created by PhpStorm.
 * User: sts
 * Date: 23.09.17
 * Time: 08:36
 */

require_once('../config/init.php');

function getFtpFilesList($conn, $dir)
{
    $files = [];
    $list = ftp_nlist($conn, $dir);
    if (is_array($list)) {
        foreach ($list as $filename) {
            if (substr($filename, 0, 1) === '.') continue;

            $path = $dir . '/' . $filename;
            if (ftp_size($conn, $path) === -1) {
                $files = array_merge($files, getFtpFilesList($conn, $path));
            } else {
                $files[] = substr($path, strpos($path, '/') + 1);
            }
        }
    }

    return $files;
}

function processFile($conn, $config, $file)
{
    $newFileName = md5(time()).'.jpg';

    $fileNamePhoto = $config['folders']['full'].DIRECTORY_SEPARATOR.$newFileName;
    $fileNameThumb = $config['folders']['thumb'].DIRECTORY_SEPARATOR.$newFileName;

    $tmpFile = tempnam(sys_get_temp_dir(), 'handyBooth_');

    if (ftp_get($conn, $tmpFile, $file, FTP_BINARY)) {
        rename($tmpFile, $fileNamePhoto);
        chmod($fileNamePhoto, 0755);

        ftp_delete($conn, $file);

        // image scale
        list($width, $height) = getimagesize($fileNamePhoto);
        $newWidth = 1200;
        $newHeight = $newWidth / $width * $height;
        $source = imagecreatefromjpeg($fileNamePhoto);
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($thumb, $fileNameThumb);

        // insert into database
        $list = [];
        if (is_file($config['listFile'])) {
            $list = json_decode(file_get_contents($config['listFile']));
        }
        $list[] = $file;
        file_put_contents($config['listFile'], json_encode($list));
    }
}


$conn = ftp_connect($config['handyBooth']['ftp']['host'], $config['handyBooth']['ftp']['port']);
$loginResult = ftp_login($conn, $config['handyBooth']['ftp']['user'], $config['handyBooth']['ftp']['password']);

if (!$conn || !$loginResult) {
    echo 'FTP-Verbindung ist fehlgeschlagen.';
    exit;
}

$tree = getFtpFilesList($conn, '/');

foreach ($tree as $file) {
    processFile($conn, $config, $file);
}

ftp_close($conn);
exit;