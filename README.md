# REDCap First Responder COVID-19

This REDCap module facilitates the scheduling and data management of COVID-19 testing for first responders. This project was created by the CTS-IT at the University of Florida to support testing of first responders in Gainesville, Florida and Alachua County.

## Prerequisites
- REDCap >= 9.3.5
- [REDCap Entity](https://github.com/ctsit/redcap_entity) >= 2.3.0

## Manual Installation
- Clone this repo into to `<redcap-root>/modules/fr_covidata_v0.0.0`.
- Clone [redcap_entity](https://github.com/ctsit/redcap_entity) repo into `<redcap-root>/modules/fr_covidata_v0.0.0`.
- Go to **Control Center > External Modules**, enable REDCap Entity, and then this module. REDCap Entity will be enabled globally, but this module has to be enabled on a per-project basis after *Global Configuration* is completed.


## Appointment Scheduling Features

This module adds an appointment scheduling feature to REDCap. This feature is built on top of REDCap Entity and a dynamic SQL field. It allows appointment blocks to be selected from a Dynamic SQL field using the built-in auto-completion feature of Dynamic SQL fields. The SQL query queries a table of appointments to generate a list of available appointment blocks. On form save, REDCap writes the appointment_id into the value of the Dynamic SQL field. The module uses the save_data hook to write the record_id and event_id into the appointment record. The save_data hook will also update appointment-related fields on the record to provide easy lookups of the Site, Date, and Time of the appointment block.

Once assigned to a person, an appointment block is no longer available. This feature is used in a REDCap survey to allow the first responders to select their appointments.

If the research participant needs to cancel or change an appointment, they must call the Study Team. The study team will access the REDCap project, locate the participant's record and cancel or replace the existing appointment. 

The table of appointments is managed by REDCap Entity. 


## Site Management Features

A _site_ is a COVID-19 testing site. A study coordinator or REDCap admin must define each site before appointment blocks for that site can be created. 

REDCap Entity manages the site data. A study coordinator or REDCap admin can populate the site table by accessing the FR Covid Manager Sites page. It allows for CRUD operations on sites for this project.

Each site allows the configuration of a long name, short name, address, open time, close time, and appointment duration. The open and close times are bounds on the generation of appointments. 


## Appointment Block Management Features

An _appointment block_ is a fixed block of time at a single site. Appointment blocks must be created for a site before a research participant can schedule an appointment at that site.

Appointment block creation is managed by scripts in this module. Those scripts can be run manually or automatically. The project-level module configuration allows the setting of the _appointment horizon_ via the field labeled "How many days of appointments do you want to be available to the study respondents at all times? These appointment blocks will be automatically generated nightly."

The scripted process uses the _appointment horizon_, _site id_, _open_, _close_, and appointment duration attributes for each site to generate records in the appointments table. It will generate _appointment horizon_ days of appointment blocks for each site if those blocks do not already exist. 

The module will define a cron job that runs daily to assure _appointment horizon_ days of appointment blocks exist at all times.
