# Change Log
All notable changes to the First Responder COVID-19 Testing project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).


## [0.5.0] - 2020-04-09
### Added
- Add 'Clone a project into Production' to README (Philip Chase)
- Add start_date to test site that decides when appointments will begin (Kyle Chesney)
- Add AUTHORS.md file to recognize those responsible for this software (Kevin Hanson)

### Changed
- Stop deleting all relevant Entity tables when this module is disabled... (Kyle Chesney)
- Update project XML with export of production project (Philip Chase)
- Fix bad open_time values in site data (Philip Chase)
- Correct enclosing on logic for dynamic SQL field to keep project's tables separate (Kyle Chesney)


## [0.4.1] - 2020-04-07
### Changed
- Correct issue in form test in redcap_save_record (Kyle Chesney)
- Fix site longnames, short names, close and open (Philip Chase)
- prevent hiding of any previously scheduled appointment for a record to prevent data loss due to null entries (Kyle Chesney)


## [0.4.0] - 2020-04-07
### Added
- Add location id to research_encounter_id (Kyle Chesney, Philip Chase)
- Add make_test_data.R with icf, q, and mini_q_0 -- mini_q_3 (Philip Chase)

### Changed
- Fix open and close times on KED (Philip Chase)


## [0.3.0] - 2020-04-04
### Changed
- Remove "FRC-" prefix from research_encounter_id (Philip Chase)
- Update test_site_data (Philip Chase)
- Add swabandserum and AEDS to appointment form of project XML (Philip Chase)
- support repeat instances and individual events, add testing_type to site, rename db columns again (Kyle Chesney)
- Add custom event label to events to project XML (Philip Chase)
- update names of entity columns, alter code accordingly improve appointment block generation (Kyle Chesney)


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
