#!/bin/bash

# dns
nslookup $IP

# db interno 
geoiplookup $IP

# 1000 al giorno
curl ipinfo.io/$P

# or use CLI
# https://github.com/DavidePastore/ipinfo
