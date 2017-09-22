<?php
/**
 * Created by PhpStorm.
 * User: sts
 * Date: 22.09.17
 * Time: 21:24
 */

chdir('/home/pi/Pictures');

function ftpFilesList($conn, $dir)
{
    $files = [];
    $list = ftp_nlist($conn, $dir);
    if (is_array($list)) {
        foreach ($list as $filename) {
            if (substr($filename, 0, 1) === '.') continue;

            $path = $dir . '/' . $filename;
            if (ftp_size($conn, $path) === -1) {
                $files = array_merge($files, ftpFilesList($conn, $path));
            } else {
                $files[] = substr($path, strpos($path, '/') + 1);
            }
        }
    }

    return $files;
}

function getNextFileName()
{
    $files = scandir('.');
    $lastFile = array_slice($files, -1);

    if ($lastFile === '..' || $lastFile === '.') {
        $nextFile = '000001.jpg';
    } else {
        $nextFile = substr('0000000' . (intval(explode('.', $lastFile)[0], 10) + 1) . '.jpg', -10);
    }

    return $nextFile;
}

$conn = ftp_connect('192.168.178.30', 2221);
$loginResult = ftp_login($conn, 'android', 'android');

if (!$conn || !$loginResult) {
    echo 'FTP-Verbindung ist fehlgeschlagen.';
    exit;
}

$tree = ftpFilesList($conn, '/');

foreach ($tree as $file) {
    $tmpFile = tempnam(sys_get_temp_dir(), 'photobox_');

    if (ftp_get($conn, $tmpFile, $file, FTP_BINARY)) {
        $newFile = getNextFileName();
        rename($tmpFile, $newFile);
        chmod($newFile, 0755);
        ftp_delete($conn, $file);
    }
}

ftp_close($conn);
sleep(0.3);
exit;