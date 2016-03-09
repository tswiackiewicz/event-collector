# Events collector

[![Build Status](https://travis-ci.org/tswiackiewicz/events-collector.png?branch=feature/collector_configuration)](https://travis-ci.org/tswiackiewicz/events-collector)

Events collection and alerting asynchronous daemon

## CHANGELOG

# 0.2.0 - 2016-03-??

* Collect events with registered collector, only syslog collectors supported

# 0.1.0 - 2016-03-04

* Register events, actions and collectors via REST API interface
* Load configuration from YAML file (on daemon start)
* Dump configuration periodically
* Daemon configuration via .ENV file

## TODO

* alerting support -> 0.2.0
* server activities log - Monolog -> 0.2.0
* postman test case scenarios -> 0.2.0
* documentation -> 0.2.0
* configuration refactoring -> 0.2.0
* controller naming refactoring -> 0.2.0
* not JSON payloads error support -> 0.2.0
* fix: JsonException only in controller & dispatcher -> 0.2.0

* nice2have: clear / get alerting counters -> 0.3.0
* nice2have: configuration validation via symfony/config -> 0.3.0
* validate configuration object using defined regex expression -> 0.3.0
* different alerting aggregation policy -> 0.3.0

* multi-node architecture: zookeeper vs consul -> 0.4.0
* benchmarks -> 0.4.0
