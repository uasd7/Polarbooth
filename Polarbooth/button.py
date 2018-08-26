#!/usr/bin/python
# -*- coding: utf-8 -*-

import RPi.GPIO as GPIO
import time
import subprocess

GPIO.setmode(GPIO.BOARD)

GPIO.setup(40, GPIO.IN, pull_up_down = GPIO.PUD_DOWN)

try:
  while True:
    if GPIO.input(40):
      subprocess.call("/usr/bin/php /var/www/html/Polarbooth/takePicture.php", shell=True)
    else:
      time.sleep(0.2)

except KeyboardInterrupt:
  GPIO.cleanup()
