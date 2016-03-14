# Events collector

[![Build Status](https://travis-ci.org/tswiackiewicz/events-collector.png?branch=feature/collector_configuration)](https://travis-ci.org/tswiackiewicz/events-collector)

Events collection and alerting asynchronous daemon

## CHANGELOG

# 0.2.0 - 2016-03-??

* Collect events with registered collector, only syslog collectors supported
* Watch events (and perform action) with registered watcher, only fields based aggregator supported
* Event action renamed to watcher
* Configuration & event refactoring
* ControllerFactory added

# 0.1.0 - 2016-03-04

* Register events, actions and collectors via REST API interface
* Load configuration from YAML file (on daemon start)
* Dump configuration periodically
* Daemon configuration via .ENV file

## TODO

* not JSON payloads error support -> 0.2.0
* fix: JsonException only in controller & dispatcher -> 0.2.0
* counters repository + in memory
* unit tests base tests extended

* documentation -> 0.3.0
* postman test case scenarios -> 0.3.0
* server activities log - Monolog -> 0.3.0
* validate configuration object using defined regex expression -> 0.3.0
* allow to create single / fields aggregator -> 0.3.0
* allow to create null collector appender -> 0.3.0
* allow to create null watcher action -> 0.3.0

* settings (loaded from config/config.yml) validation via symfony/config -> 0.4.0
* clear / get alerting counters API -> 0.4.0
* multi-node architecture: zookeeper vs consul -> 0.4.0
* benchmarks -> 0.4.0
