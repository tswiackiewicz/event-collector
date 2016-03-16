## CHANGELOG

# 0.2.0 - 2016-03-16

* Collect events with registered collector, syslog / null collectors supported
* Watch events (and perform action) with registered watcher, single / fields based aggregator supported
* Fix: only JSON payload accepted

# 0.1.0 - 2016-03-04

* Register events, actions and collectors via REST API interface
* Load configuration from YAML file (on daemon start)
* Dump configuration periodically
* Daemon configuration via .ENV file