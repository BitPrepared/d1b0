#!/bin/sh


rm workspace.sqlite
cat sqlite_raw.txt|sqlite3 workspace.sqlite
