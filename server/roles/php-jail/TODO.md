[ ] https://github.com/nbs-system/naxsi/wiki/integration-fail2ban
[ ] https://www.nginx.com/resources/wiki/start/topics/tutorials/config_pitfalls/ check pitfalls
[ ] https://github.com/nbs-system/naxsi/wiki/integration-apparmor
[ ] https://geekflare.com/nikto-webserver-scanner/ test con nikto
[ ] https://www.digitalocean.com/community/tutorials/how-to-secure-nginx-on-ubuntu-14-04
[ ] https://geekflare.com/nginx-webserver-security-hardening-guide/
[ ] https://github.com/omega8cc/boa controllare le loro config
[ ] http://vujade.co/monitoring-nginx/
[ ] https://github.com/nbs-system/naxsi/wiki/naxsi-setup naxsi
[ ] http://www.madirish.net/199
[ ] http://www.cyberciti.biz/tips/php-security-best-practices-tutorial.html


MALDET CHECK UPLOAD FILE
[ ] https://github.com/stefanesser/suhosin/issues/43


[ ] Config NAXSI

LearningMode - Start Naxsi in learning mode. This means that no request will actually be blocked. Only security exceptions will be raised in the Nginx error log. Such a non-blocking initial behavior is important because the default rules are rather aggressive. Later, based on these exceptions, we will create whitelist for legitimate traffic.
SecRulesEnabled - Enable Naxsi for a server block / location. Similarly, you can disable Naxsi for a site or part of a site by uncommenting SecRulesDisabled.
DeniedUrl - URL to which denied requests will be sent internally. This is the only setting you should change. You can use the readily available 50x.html error page found inside the default document root (/usr/share/nginx/html/50x.html), or you can create your own custom error page.
CheckRule - Set the threshold for the different counters. Once this threshold is passed (e.g. 8 points for the SQL counter) the request will be blocked. To make these rules more aggressive, decrease their values and vice versa.


[ ] disabilitare l'autosessione sul php
