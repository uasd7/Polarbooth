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

    $fileNameWaiting = $config['folders']['full'].DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'waiting.txt';
    file_put_contents($fileNameWaiting, 'waiting');

    $fileNamePhoto = $config['folders']['full'].DIRECTORY_SEPARATOR.$file;
    $fileNameThumb = $config['folders']['thumb'].DIRECTORY_SEPARATOR.$file;
$start = microtime(true);
    $shootimage = shell_exec('sudo gphoto2 --capture-image-and-download --filename='.$fileNamePhoto.' images');
echo (microtime(true) - $start) . ' s';
    // image scale
    $cmd = 'epeg -m 1200 ' . $fileNamePhoto . ' ' . $fileNameThumb;
    shell_exec($cmd);
    unlink($fileNameWaiting);
} catch (Exception $e) {
    echo $e->getMessage();
}

