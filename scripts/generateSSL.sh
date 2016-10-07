#!/bin/bash

if [ ! -f ./server/plays/ssl/selfsigned.crt ]; then
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 -subj "/C=US/ST=Denial/L=Springfield/O=Dis/CN=www.d1b0.local" -keyout ./server/plays/ssl/selfsigned.key -out ./server/plays/ssl/selfsigned.crt
    # RICHIEDE MOLTO TEMPO!
    #openssl dhparam -out ./server/plays/ssl/dhparam.pem 4096
    cat ./server/plays/ssl/selfsigned.crt >> ./server/plays/ssl/selfsigned.combo.pem
    cat ./server/plays/ssl/selfsigned.key >> ./server/plays/ssl/selfsigned.combo.pem
    # INTERMEDIO
    #cat ./server/plays/ssl/dhparam.pem >> ./server/plays/ssl/selfsigned.combo.pem
fi
