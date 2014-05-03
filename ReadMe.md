# Atlassian HipChat Notification & API Services

The »Atlassian HipChat Notification & API Services« provides an API to the HipChat Rest-Webservices (v1).
It should replace the login emails sent by the TYPO3 Install-Tool, the TYPO3 Backend on logins or the
cumulated login error notifications. The public API could be also used to provide HipChat notifications
in your own extension.

## Features
* Basic Login notification
* Compatibility for TYPO3 4.5-LTS, 4.7 and 6.2-LTS

## Planned features & ideas
* Add HipChat calls for »System Status Notifications« (for tx_reports_tasks_SystemStatusUpdateTask)
* Add Install-Tool login notifications and errors
* Add FE User login notification
* Add option to disable email notification
* Add configuration option for rooms and from names
* Different rooms by message severity

## Known issues & to do
* Message (HTML) formatting should be enhanced

## Version History
For full history please see ChangeLog.txt.

### Whats new in this release (Version 1.1.0 - 2014-05-02)
* TER release - add compatibility for TYPO3 6.2