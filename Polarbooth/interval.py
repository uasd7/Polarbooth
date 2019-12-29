#!/usr/bin/python
# -*- coding: utf-8 -*-

import time
import subprocess

while True:
  subprocess.call("/usr/bin/php /var/www/html/Polarbooth/takePicture.php", shell=True)
  time.sleep(3)
