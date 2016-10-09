[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BitPrepared/d1b0/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BitPrepared/d1b0/?branch=master)

# d1b0
Progetto Segreto


# Vagrant image base

 * https://atlas.hashicorp.com/debian/boxes/jessie64


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

## COMANDI

  * **npm run init**: Prepara l'ambiente per sviluppo, (clean,install)

  * **npm install**: compile

  * **npm run build:favicon**: generazione favicon web

  * **npm test** (pretest, test, posttest)

  * **npm start** (prestart, start, poststart): server di prova in locale http://localhost:8080/workspace/

  * **npm stop** (prestart, start, poststart): stop server di prova

  * **npm restart** (prerestart, restart, postrestart): riavvio server di prova; Note: npm restart will run the stop and start scripts if no restart script is provided.

  * **npm run lint**: Check JS nel progetto

  * **npm run dist**: Prepara l'ambiente per un rilascio

  * **npm run open:dev**: Apre il browser sulla pagina di test


## TEMPISTICA CREAZIONE SERVER

  real	17m19.359s

## HOSTS config

127.0.0.1 localhost www.d1b0.local
