#!/bin/bash
IP="10.0.9.17"
date=$(date +"%b %d %H:%M:%S")
echo "$date [762] ALERT - use of eval is forbidden by configuration (attacker '$IP', file '/usr/local/nginx/html/fail.php', line 2)" >> /var/www/jail/var/log/php/suhosin.log
