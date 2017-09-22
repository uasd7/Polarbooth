<?php
/**
 * Created by PhpStorm.
 * User: sts
 * Date: 22.09.17
 * Time: 21:58
 */

require_once('config.inc.php');

foreach($config['folders'] as $directory) {
    if(!is_dir($directory)){
        mkdir($directory, 0777);
    }
}