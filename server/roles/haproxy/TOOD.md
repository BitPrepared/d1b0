[ ] add_header Pragma private; ... impedire i proxy in mezzo


[ ] GOACCESS:

goaccess -f prova.log --log-format='%^  %^ %^:%^:%^ %^ %^[%^]: %h:%^ [%d/%b/%Y:%H:%M:%S.%^] %^ %^ %^/%^/%^/%^/%L %s %b %^ %^ %^ %^/%^/%^/%^/%^ %^/%^ "%r"' --date-format='%b %d' --time-format='%H:%M:%S' -q
