#!/bin/bash

php -S 127.0.0.1:8080 &

raml2html -i api.raml > api-generated.html

killall php
