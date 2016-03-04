# Events collector

[![Build Status](https://travis-ci.org/tswiackiewicz/events-collector.png?branch=feature/collector_configuration)](https://travis-ci.org/tswiackiewicz/events-collector)

Events collection and alerting asynchronous daemon

## CHANGELOG

# 0.1.0 - 2016-03-04

* Register events, actions and collectors via REST API interface
* Load configuration from YAML file (on daemon start)
* Dump configuration periodically
* Daemon configuration via .ENV file

## TODO

* collect events -> 0.2.0
* alerting support -> 0.2.0
* server activities log - Monolog -> 0.2.0
* postman test case scenarios -> 0.2.0
* documentation -> 0.2.0
* configuration refactoring -> 0.2.0

* nice2have: clear / get alerting counters -> 0.3.0
* nice2have: configuration validation via symfony/config -> 0.3.0

* multi-node architecture: zookeeper vs consul -> 0.4.0
* benchmarks -> 0.4.0
