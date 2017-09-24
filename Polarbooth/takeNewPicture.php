<?php
/**
 * Created by PhpStorm.
 * User: sts
 * Date: 22.09.17
 * Time: 22:10
 */

require_once('init.php');


$file = md5(time()).'.jpg';

$filenamePhoto = $config['folders']['full'] . DIRECTORY_SEPARATOR . $file;
$filenameThumb = $config['folders']['thumb'] . DIRECTORY_SEPARATOR . $file;

$shootimage = shell_exec('sudo gphoto2 --capture-image-and-download --filename=' . $filenamePhoto . ' images');

// image scale
list($width, $height) = getimagesize($filenamePhoto);
$newWidth = 1200;
$newHeight = $newWidth / $width * $height;
$source = imagecreatefromjpeg($filenamePhoto);
$thumb = imagecreatetruecolor($newWidth, $newHeight);
imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
imagejpeg($thumb, $filenameThumb);

// insert into database

$list = [];
if (is_file($config['listFile'])) {
    $list = json_decode(file_get_contents($config['listFile']));
}

$list[] = $file;

file_put_contents($config['listFile'], json_encode($list));
