#TODO

[ ] tolleranza validita token per utente

[ ] disabilitare TRACE su haproxy (manca il test)

[ ] rivedere i gelf message verso l'utente

[ ] finire la review di schema e example

[ ] per l'enviroment valutare: https://github.com/weareinteractive/ansible-environment

[ ] in ansible-role-nginx bug: https://github.com/jdauphant/ansible-role-nginx/issues/28 da risolvere per DO e co

[ ] tidy config: http://api.html-tidy.org/tidy/quickref_5.2.0.html#MarkupHeader

[ ] rsnapshot (log) ed eventualmente file caricati (=> crittografia)

[ ] automatic backup mysql

[ ] in test: mail catcher !!!! come config di base , in prod settare ssmtp o qualcosa per l'invio serio di mail! (mailgun??) MATRAIL?

[ ] apt-check-security.sh forse e da mettere in un cron anche di root va bene

[ ] delete nei vari elementi

[ ] validazione json schema API con https://github.com/epoberezkin/ajv

[ ] goaccess :
      - https://www.webfoobar.com/index.php/node/53
      - https://pantheon.io/docs/nginx-access-log/
      - http://vujade.co/monitoring-nginx-traffic-using-goaccess/
[ ] aide, integrit or samhain

[ ] Please consider removing these system accounts.
Check to see if you need them for your system applications before removing.
Also, consult the securitylinks.txt file for more information.
  sync
  man
  lp
  news
  uucp

 [ ] https://www.scalescale.com/tips/nginx/monitor-nginx-munin/

[ ] check
        user 'lp': directory '/var/spool/lpd' does not exist
        user 'news': directory '/var/spool/news' does not exist
        user 'uucp': directory '/var/spool/uucp' does not exist
        user 'www-data': directory '/var/www' does not exist
        user 'list': directory '/var/list' does not exist
        user 'irc': directory '/var/run/ircd' does not exist
        user 'gnats': directory '/var/lib/gnats' does not exist
        user 'nobody': directory '/nonexistent' does not exist
        user 'systemd-resolve': directory '/run/systemd/resolve' does not exist

[ ] Checks for sticky bits on tmp files
      report.html is not chmod 644.
      scripts is not chmod 644.
      /var/log/wtmp is not chmod 644.
      /etc/init.d/motd is not chmod 644.
      /etc/mtab is not chmod 644.
      report.html is not chmod 644.
      scripts is not chmod 644.
      Check above files for chmod 644.
      Check above dirs to ensure root ownership.

[ ] Protcol 2 not found in sshd config, or you are doing 1,2.

[ ] https://github.com/sovereign/sovereign

[ ] http://petrovs.info/2015/12/27/My-way-to-auto-update-Lets-Encrypt/

[ ] artillery enviroment configuration dev/stage

[ ] piwiki analisi https://github.com/piwik/piwik-log-analytics

[ ] nginx firewall https://www.rtcx.net/nginx-application-firewall.html

[ ] configurazione ntop "dpkg-reconfigure ntop" che di default non legge l'interfaccia e la porta 3000 non è aperta (giustamente)

[ ] http://dev.maxmind.com/geoip/legacy/geolite/ e http://dev.maxmind.com/geoip/geoip2/geolite2/ e https://github.com/maxmind/GeoIP2-php

[ ] pimpa analisys :

    - http://engineering.dailymotion.com/php-7-deployment-at-dailymotion/

    - https://github.com/tony2001/pinba_engine

    - http://emeryberger.github.io/Hoard/

    - https://howto.biapy.com/en/debian-gnu-linux/system/software/install-hoard-on-debian

 [ ] digital ocean integration

     - https://github.com/Wiredcraft/dopy/issues/41

     - https://github.com/phillbaker/digitalocean-node

     - https://github.com/matt-major/do-wrapper

     - https://www.digitalocean.com/community/tutorials/how-to-use-doctl-the-official-digitalocean-command-line-client

[ ] https://github.com/Stevie-Ray/referrer-spam-blocker

[ ] php security

    - https://github.com/Maikuolan/phpMussel

    - http://nintechnet.com/ninjafirewall/pro-edition/?download

    - https://websectools.com/

    - https://github.com/psecio/versionscan

    - https://github.com/psecio/iniscan

    - ELENCO: https://wwphp-fb.github.io/faq/security/php-security-issues/

    - https://github.com/koalaman/shellcheck

[ ] letsencrypt

apt install letsencrypt=0.9.3-1~bpo8+1 certbot=0.9.3-1~bpo8+1 python-certbot=0.9.3-1~bpo8+1 python-acme=0.9.3-1~bpo8+1 python-cryptography=1.3.4-1~bpo8+2 python-pyasn1=0.1.9-1~bpo8+1 python-setuptools=20.10.1-1.1~bpo8+1 python-openssl=16.0.0-1~bpo8+1 python-pkg-resources=20.10.1-1.1~bpo8+1
