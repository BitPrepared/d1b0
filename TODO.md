#TODO

[ ] rimuovere pacchetti compilazione alla fine del processo di instllazioni (di tutto e non singolo role!)

[ ] tolleranza validita token per utente

[ ] disabilitare TRACE su haproxy

[ ] haproxy gzip, https, anti ddos, anti info server (500 e header)

[ ] rivedere i gelf message verso l'utente

[ ] finire la review di schema e example

[ ] in ansible-role-nginx bug: https://github.com/jdauphant/ansible-role-nginx/issues/28 da risolvere per DO e co

[ ] tidy config: http://api.html-tidy.org/tidy/quickref_5.2.0.html#MarkupHeader

[ ] rsnapshot (log) ed eventualmente file caricati (=> crittografia)

[ ] automatic backup mysql

[ ] log rotation

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

[ ] su fail2ban inserire il controllo sui log di suhosin

[ ] https://github.com/sovereign/sovereign

[ ] http://petrovs.info/2015/12/27/My-way-to-auto-update-Lets-Encrypt/

[ ] artillery enviroment configuration dev/stage
