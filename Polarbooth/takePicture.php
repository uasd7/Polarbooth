<?php
/**
 * Created by PhpStorm.
 * User: sts
 * Date: 22.09.17
 * Time: 22:10
 */

require_once('../config/init.php');


$file = md5(time()).'.jpg';

$fileNamePhoto = $config['folders']['full'] . DIRECTORY_SEPARATOR . $file;
$fileNameThumb = $config['folders']['thumb'] . DIRECTORY_SEPARATOR . $file;

$shootimage = shell_exec('sudo gphoto2 --capture-image-and-download --filename=' . $fileNamePhoto . ' images');

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

