# REDCap First Responder COVID-19

## Prerequisites
- REDCap >= 9.3.5
- [REDCap Entity](https://github.com/ctsit/redcap_entity) >= 2.3.0

## Manual Installation
- Clone this repo into to `<redcap-root>/modules/fr_covidata_v0.0.0`.
- Clone [redcap_entity](https://github.com/ctsit/redcap_entity) repo into `<redcap-root>/modules/fr_covidata_v0.0.0`.
- Go to **Control Center > External Modules**, enable REDCap Entity, and then this module. REDCap Entity will be enabled globally, but this module has to be enabled on a per-project basis after *Global Configuration* is completed.
