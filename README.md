[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BitPrepared/d1b0/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BitPrepared/d1b0/?branch=master)

# d1b0
Progetto Segreto


# Vagrant image base

 * https://atlas.hashicorp.com/debian/boxes/jessie64

## ENVIROMENT

  * dev

  * staging

  * production

## COMANDI

Base:

  * **npm install --production**: installazione dipendenze

  * **npm run initialize**: Prepara l'ambiente per sviluppo, (clean,install)

  * **npm run clean**: clean generated/compiled element

  * **npm run build**: creazione del server sull'ambiente designato (default: dev)

  * **npm deploy**: deploy sull'enviroment configurato (default: dev)

  * **npm test** (pretest, test, posttest)

  * **npm run open:dev**: Apre il browser sulla pagina di test

  * **npm run lint**: Check JS nel progetto

  * **npm run ssh:developer|root**: ssh as developer or root

  * **npm-check**: per vedere i package da aggiornae (serve "npm-check" installato globalmente)

Server:

  * **npm start** (prestart, start, poststart): server di prova in locale http://localhost:8080/workspace/

  * **npm stop** (prestart, start, poststart): stop server di prova

  * **npm restart** (prerestart, restart, postrestart): riavvio server di prova; Note: npm restart will run the stop and start scripts if no restart script is provided. [DA FARE]

  * **npm run clean:server**: Destroy del server

Distribuzione

  * **npm run dist**: Prepara l'ambiente per un rilascio

Setup setting override

  * **npm config set PARAM VALUE**: setto l'enviroment


## SETUP staging

```
npm config set impresa-luna:enviroment staging
npm config set impresa-luna:database_host localhost
npm config set impresa-luna:database_dbname workspace
npm config set impresa-luna:database_username workspaceUser
npm config set impresa-luna:database_password workspacePassword
```

## ADD CACHE APT

```
npm config set impresa-luna:cache enable
```

## CHECK

~~~
curl -kin https://www.d1b0.local:8443/workspace/
~~~

## TEMPISTICA CREAZIONE SERVER

  real	17m42.269s

## HOSTS config

127.0.0.1 localhost dev.d1b0.local
