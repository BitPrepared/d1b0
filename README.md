[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BitPrepared/d1b0/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BitPrepared/d1b0/?branch=master)

# d1b0
Progetto Segreto


#TODO

* [ ] tolleranza validita token per utente
* [ ] disabilitare TRACE su haproxy
* [ ] haproxy gzip, https, anti ddos, anti info server (500 e header)
* [ ] rivedere i gelf message verso l'utente
* [ ] finire la review di schema e example
* [ ] in ansible-role-nginx bug: https://github.com/jdauphant/ansible-role-nginx/issues/28 da risolvere per DO e co
* [ ] tidy config: http://api.html-tidy.org/tidy/quickref_5.2.0.html#MarkupHeader
* [ ] rsnapshot (log) ed eventualmente file caricati (=> crittografia)
* [ ] automatic backup mysql
* [ ] log rotation
* [ ] in test: mail catcher !!!! come config di base , in prod settare ssmtp o qualcosa per l'invio serio di mail! (mailgun??) MATRAIL?
* [ ] apt-check-security.sh forse e da mettere in un cron anche di root va bene
* [ ] delete nei vari elementi

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
