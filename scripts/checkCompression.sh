#!/bin/bash
# Stefanos-MacBook-Pro-2:~ stamagnini$ curl -k -o/dev/null -D - https://dev.d1b0.local:8443/workspace/index.php
#   % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
#                                  Dload  Upload   Total   Spent    Left  Speed
#   0     0    0     0    0     0      0      0 --:--:-- --:--:-- --:--:--     0HTTP/1.1 200 OK
# Date: Thu, 06 Oct 2016 13:45:22 GMT
# Content-Type: text/html; charset=UTF-8
# Transfer-Encoding: chunked
# X-XSS-Protection: 1; mode=block
# X-Content-Type-Options: nosniff
#
# 100  1126    0  1126    0     0  24976      0 --:--:-- --:--:-- --:--:-- 25590
# Stefanos-MacBook-Pro-2:~ stamagnini$ curl -k -o/dev/null -D - https://dev.d1b0.local:8443/workspace/index.php -H "Accept-Encoding: gzip"
#   % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
#                                  Dload  Upload   Total   Spent    Left  Speed
#   0     0    0     0    0     0      0      0 --:--:-- --:--:-- --:--:--     0HTTP/1.1 200 OK
# Date: Thu, 06 Oct 2016 13:45:37 GMT
# Content-Type: text/html; charset=UTF-8
# Transfer-Encoding: chunked
# Content-Encoding: gzip
# X-XSS-Protection: 1; mode=block
# X-Content-Type-Options: nosniff
#
# 100   520    0   520    0     0  10767      0 --:--:-- --:--:-- --:--:-- 10833


nogzip=$(curl -k -o/dev/null -D - https://dev.d1b0.local:8443/workspace/ | grep gzip | wc -l)

if [ $nogzip -ne 0 ]; then
  exit 2
fi

gzip=$(curl -k -o/dev/null -D - https://dev.d1b0.local:8443/workspace/ -H "Accept-Encoding: gzip" | grep gzip | wc -l)

if [ $gzip -ne 1 ]; then
  exit 3
fi
