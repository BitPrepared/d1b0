
influx -host dev.d1b0.local -ssl -unsafeSsl -username admininfluxdb -password admininfluxdb

~~~
influx -host dev.d1b0.local -ssl -unsafeSsl -username admininfluxdb -password admininfluxdb -execute 'SHOW DATABASES'
#show series
#-format=json -pretty
~~~

Per vedere i valori presi da collectd

~~~
select * from cpu_value where host=localhost and instance=0 and type=percent
~~~


curl -kin --user admin:SecureAdminPass -X GET https://dev.d1b0.local:4000/api/datasources

[]

curl -kin --user admin:SecureAdminPass -X POST https://dev.d1b0.local:4000/api/datasources -d @createDataSource.json --header "Content-Type: application/json"

{"id":2,"message":"Datasource added"}

curl -kin --user admin:SecureAdminPass -X POST https://dev.d1b0.local:4000/api/dashboards/db -d @createDashboard.json --header "Content-Type: application/json"

{"slug":"production-overview","status":"success","version":0}
