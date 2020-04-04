# Change Log
All notable changes to the First Responder COVID-19 Testing project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [0.2.0] - 2020-04-03
### Changed
- Update project XML (Philip Chase)
- allow rescheduling by changing appointment in form split scheduling out into function update dynamic SQL to not hide selected appointment (Kyle Chesney)
- do not allow next-day appointments after 4pm in dynamic appointment SQL (Kyle Chesney)
- force ordering by datetime for appointment selection (Kyle Chesney)
- encode info into research_encounter_id with Luhn checksum (Kyle Chesney)


## [0.1.0] - 2020-04-03
### Summary
 - First release of fr_covidata
 - Supports site data, appointment blocks, and revising of appointment details
