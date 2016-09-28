# DEBUG

1) inspect work

fail2ban-client set loglevel 4

/var/log/fail2ban.log e' cosi verbose

2) check status

watch -n 1 "fail2ban-client status suhosin-php"
