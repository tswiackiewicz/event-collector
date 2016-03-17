# Events collector

[![Build Status](https://travis-ci.org/tswiackiewicz/events-collector.png?branch=feature/collector_configuration)](https://travis-ci.org/tswiackiewicz/events-collector)

Simple, asynchronous daemon for concurrently processing multiple HTTP / REST requests, based on React PHP library.

*Events collector* was made to collect given events (with registered appender) and perform watcher actions when threshold is exceeded.

It provides REST API that you can use to interact with daemon:
 
* register, unregister and manage supported event types
* register, unregister event's collectors (with appenders)
* register, unregister event's watchers (with aggregation policy and action)
* collect event with registered collectors 

## Basic concepts:

* event type - name of event type to be collected and watched, e.g. *user_logged_in*
* collector - registered for particular event type, collects event with defined appender (e.g. *syslog appender*)
* watcher - registered for event type, watches event counters defined by aggregation policy; when specified threshold is exceeded, registered action is performed (e.g. *notify via email*)

## Example:

Request (collect event `user_logged_in`): 

```bash
curl -XPOST 'http://127.0.0.1:1234/event/user_logged_in/' -d '
{
    "user_id": 1,
    "ip": "192.168.0.1", 
    "login": "testuser",
    "date": "2016-03-17 12:00:00", 
    "host": "collector-node-1"     
}'
```

and response:

```json
{
  "_id": "87501f8a-8446-48ae-a26e-15a3fd7cdb1b"
}
```

## License

MIT


