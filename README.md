# Asynchronous Test
```bash
$> ab -n 100 -c 10 http://localhost:4000/
This is ApacheBench, Version 2.3 <$Revision: 1843412 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking localhost (be patient).....done


Server Software:
Server Hostname:        localhost
Server Port:            4000

Document Path:          /
Document Length:        334 bytes

Concurrency Level:      10
Time taken for tests:   1.199 seconds
Complete requests:      100
Failed requests:        0
Total transferred:      46400 bytes
HTML transferred:       33400 bytes
Requests per second:    83.38 [#/sec] (mean)
Time per request:       119.935 [ms] (mean)
Time per request:       11.993 [ms] (mean, across all concurrent requests)
Transfer rate:          37.78 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.2      0       1
Processing:     4   18   3.4     18      25
Waiting:        2   18   3.4     18      25
Total:          4   18   3.3     18      25

Percentage of the requests served within a certain time (ms)
  50%     18
  66%     18
  75%     19
  80%     20
  90%     22
  95%     22
  98%     25
  99%     25
 100%     25 (longest request)
```

# Synchronous Test
```bash
> ab -n 100 -c 10 http://localhost:4002/test_db_sync.php
This is ApacheBench, Version 2.3 <$Revision: 1843412 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking localhost (be patient).....done


Server Software:        nginx/1.14.2
Server Hostname:        localhost
Server Port:            4002

Document Path:          /test_db_sync.php
Document Length:        335 bytes

Concurrency Level:      10
Time taken for tests:   2.823 seconds
Complete requests:      100
Failed requests:        0
Total transferred:      53200 bytes
HTML transferred:       33500 bytes
Requests per second:    35.43 [#/sec] (mean)
Time per request:       282.256 [ms] (mean)
Time per request:       28.226 [ms] (mean, across all concurrent requests)
Transfer rate:          18.41 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.2      0       1
Processing:    27  170  34.0    176     238
Waiting:       26  170  34.1    176     238
Total:         27  171  34.0    176     238

Percentage of the requests served within a certain time (ms)
  50%    176
  66%    182
  75%    186
  80%    188
  90%    197
  95%    218
  98%    237
  99%    238
 100%    238 (longest request)
```