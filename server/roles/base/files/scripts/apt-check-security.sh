#!/bin/bash
/usr/bin/apt-get --dry-run --show-upgraded upgrade 2> /dev/null | grep '-security' | grep \"^Inst\" | cut -d ' ' -f2 | sort -u