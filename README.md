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

  * **npm run initialize**: Prepara l'ambiente per sviluppo, (clean,install)

  * **npm clean**: clean generated/compiled element

  * **npm install**: compile e installazione sul server vagrant di sviluppo

  * **npm deploy**: deploy sull'enviroment configurato (default: dev)

  * **npm test** (pretest, test, posttest)

  * **npm run open:dev**: Apre il browser sulla pagina di test

  * **npm run lint**: Check JS nel progetto

  * **npm run ssh:developer|root**: ssh as developer or root

Server:

  * **npm start** (prestart, start, poststart): server di prova in locale http://localhost:8080/workspace/

  * **npm stop** (prestart, start, poststart): stop server di prova

  * **npm restart** (prerestart, restart, postrestart): riavvio server di prova; Note: npm restart will run the stop and start scripts if no restart script is provided. [DA FARE]

  * **npm run clean:server**: Destroy del server 

Distribuzione

  * **npm run dist**: Prepara l'ambiente per un rilascio


## TEMPISTICA CREAZIONE SERVER

  real	17m19.359s

## HOSTS config

127.0.0.1 localhost www.d1b0.local
