#!/bin/bash


if [ ! -f ./server/plays/ssh/root.key ]; then
    ssh-keygen -t rsa -b 4096 -N "" -f ./server/plays/ssh/root.key
fi
if [ ! -f ./server/plays/ssh/developer.key ]; then
    ssh-keygen -t rsa -b 4096 -N "" -f ./server/plays/ssh/developer.key
fi
