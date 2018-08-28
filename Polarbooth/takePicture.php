<?php
/**
 * Created by PhpStorm.
 * User: sts
 * Date: 22.09.17
 * Time: 22:10
 */

require_once('../config/init.php');

try {
    $file = md5(time()).'.jpeg';

    $fileNamePhoto = $config['folders']['full'].DIRECTORY_SEPARATOR.$file;
    $fileNameThumb = $config['folders']['thumb'].DIRECTORY_SEPARATOR.$file;
    $shootimage = shell_exec('sudo gphoto2 --capture-image-and-download --filename='.$fileNamePhoto.' images');

// image scale
    list($width, $height) = getimagesize($fileNamePhoto);
    $newWidth = 1200;
    $newHeight = $newWidth / $width * $height;
    $source = imagecreatefromjpeg($fileNamePhoto);
    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagejpeg($thumb, $fileNameThumb);
} catch (Exception $e) {
    echo $e->getMessage();
}

