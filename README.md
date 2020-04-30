# REDCap First Responder COVID-19

[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.3745255.svg)](https://doi.org/10.5281/zenodo.3745255)

A REDCap module to facilitate the scheduling and data management of COVID-19 testing for first responders. This project was created by a multidisciplinary team at the University of Florida to support the testing of first responders in Alachua County, Florida and the surrounding counties.


## Caution
This module was created with numerous abstractions to allow it to be reused in other sites and projects, yet there remain some hard-coded project features. We advise you to use the included Project XML as a starting point to minimize your challenges in running this module. Also, be aware the data entry fields for site data do not carefully test the values keyed in. We recommend you start with the included example sites file, then make modifications.

## Prerequisites
- REDCap >= 9.3.5
- [REDCap Entity](https://github.com/ctsit/redcap_entity) >= 2.3.3

## Manual Installation
- Clone this repo into `<redcap-root>/modules/fr_covidata_v0.0.0`.
- Clone the [redcap_entity](https://github.com/ctsit/redcap_entity) repo into `<redcap-root>/modules/redcap_entity_v0.0.0`.
- Go to **Control Center > External Modules**, enable REDCap Entity, and then this module. REDCap Entity will be enabled globally, but `fr_covidata` has to be enabled on a per-project basis after *Global Configuration* is completed.

## Configuration

To configure and use this module, follow these steps:

1. Create a REDCap project from the file [`First_Responder_COVID19.xml`](example/First_Responder_COVID19.xml)
1. Update the `appointments` field on the `Appointments Form`, changing it to a Dynamic SQL field and configuring it to auto-complete. Paste the appropriate code from [`example/dynamic_sql_*.sql`](example/)
1. Enable the FR Covidata module as described above.
1. Configure the FR Covidata module identify to set `Which location ID is this project for? (0-15)`. This will reduce the risk of errors if one lab is processing specimens from different REDCap projects. Start with 0 and work your way up if you have to deploy multiple production instances of this module.
1. Configure the FR Covidata module identify to set `Which instrument is used for appointments?` In the included REDCap XML files, the form is named "Appointments".
1. Configure the FR Covidata module to indicate which repeat type is used for repeats: _Repeating instances_ or _Individual Events_. In the included REDCap XML files, _events_ are used.
![](images/module_configuration_for_fr_covid_module.png)
1. Use a MySQL client to load sites data from [`redcap_entity_test_site_data.sql`](example/redcap_entity_test_site_data.sql). Alternatively, use the `Define Sites` page of this module to enter the data. Be cautious as some portions of the interface of this module have very few guard rails. I.e., your data entry will not be checked. E.g., items must be entered in a 4-digit, 24-hour clock format such that 7 a.m. is 0700. The leading zero is critical. The development schedule has not allowed the addition of these tests, nor does it allow for much documentation. As such, the authors advise you to _use the example configuration_ as a starting point.
1. Adjust the `project_id`s referenced on those just-loaded sites by editing and running a copy of [`redcap_entity_test_site_update.sql`](example/redcap_entity_test_site_update.sql)
1. Access `Define Sites` to make any needed changes to the site definitions.
1. Generate the initial appointment blocks by accessing `Define Sites` and selecting the sites of interest and clicking `Manually generate new appointments`
![](images/define_sites.png)


## Appointment Scheduling Features

This module adds an appointment scheduling feature to REDCap. This feature is built on top of REDCap Entity and a dynamic SQL field. It allows appointment blocks to be selected from a Dynamic SQL field using the built-in auto-completion feature of Dynamic SQL fields. The SQL query queries a table of appointments to generate a list of available appointment blocks. On form save, REDCap writes the `appointment_id` into the value of the Dynamic SQL field. The module uses the `redcap_save_record` hook to write the `record_id` and `event_id` into the appointment record. The `redcap_save_record` hook will also update appointment-related fields on the record to provide easy lookups of the site details and date and time of the appointment block.

Once assigned to a person, an appointment block is no longer available. This feature is used in a REDCap survey to allow the first responders to select their appointments.

If the research participant needs to cancel or change an appointment, they must call the Study Team. The study team will access the REDCap project, locate the participant's record, and cancel or replace the existing appointment.

REDCap Entity manages the table of appointments. 


## Site Management Features

A _site_ is a COVID-19 testing site. A study coordinator or REDCap admin must define each site before appointment blocks for that site can be created. 

REDCap Entity manages the site data. A study coordinator or REDCap admin can populate the site table by accessing the Define Sites page. It allows for CRUD operations on sites for this project.

Each site allows the configuration of a long name, short name, appointment duration, address, open time, close time, closed days of the week, and the number of appointment days to build out in advance. The open and close times are bounds on the generation of appointments.


## Appointment Block Management Features

_NOTE: All cron management features have been disable as of 0.5.2. This is to allow us (CTS-IT) to more easily manage manual appointment block creation until we can add a per-site, per-weekday, start and stop times. Sorry for the inconvenience. The docs will be over-hauled when that feature is completed._

An _appointment block_ is a fixed block of time at a single site. Appointment blocks must be created for a site before a research participant can schedule an appointment at that site.

This module manages appointment block creation. The script that makes the appointment blocks can be run manually or automatically. The appointment blocks are generated when a study coordinator presses the `Manually generate new appointments` button of the Define sites page or automatically by a cron job that runs each day.

Appointment block creation uses the _appointment horizon_, _site id_, _open_, _close_, _closed\_days_, _start\_date_, and _appointment duration_ attributes for each site to generate records in the appointments table. It will create _appointment horizon_ days of appointment blocks for each site if those blocks do not already exist.

The module will define a cron job that runs daily to assure _appointment\_horizon_ days of appointment blocks exist at all times. Whether you let the cron job make the appointments or use the `Manually generate new appointments` button to make them, the underlying code will create appointment blocks starting on the _next day_. For example, if you configure a site to have three days of appointments, then click the button on Monday, the script will create appointments on Tuesday, Wednesday, and Thursday. If the same site were closed on Wednesday, the script would create appointments only on Tuesday and Thursday.

As of release 0.6.0, the most precise way to make appointment blocks is to set _appointment\_horizon_ days to zero and set _start\_date_ to the precise date for which you want to make appointments. Select the sites that need new appointment blocks and click `Manually generate new appointments`. Repeat this process for each site date that needs new appointment blocks.

## Showing Appointment Availability

A table showing the current number of available appointments can be shown on any survey page after a few steps. Some SQL knowledge is required.  
![Appointment availabilty table example](images/appointment_table.png)
1. Create a query with [REDCap Webservices](https://github.com/ctsit/redcap_webservices) to deliver the data you wish to display.  
    example:
    ```sql
    CONCAT(site_long_name, ' (', site_short_name, ')') AS location,
        ts.testing_type,
        COUNT(IF(fra.record_id IS NULL, 1, NULL)) AS available
            FROM redcap_entity_fr_appointment AS fra
            INNER join redcap_entity_test_site AS ts ON (fra.site = ts.id)
            WHERE ts.project_id = [project-id]
            AND ( fra.appointment_block > UNIX_TIMESTAMP( DATE( NOW() + INTERVAL IF(HOUR(NOW()) >= 16, 2, 1) DAY ) ) )
            GROUP BY site
    ```
1. In the Project Level configuration, set the appropriate URL in for **REDCap Webservices url for appointment data** (remember to fill in variables such as `project_id` in the parameters if needed)
1. Add a descriptive text field to the REDCap survey on any instrument page with the `field_label` containing HTML to create a table with id `appointment_table`:  
    example:  
    ```html
    <table id="appointment_table" class="table table-borderless table-responsive"></table>
    ```

The table will appear in the descriptive field you set. Rows where any value is `0` will be highlighted in orange.

Further styling may be done with the [REDCap CSS Injector](https://github.com/ctsit/redcap_css_injector) module.

## Using test data

The [`test_data`](./test_data/) folder includes RScript to generate test datasets for the ICF, Questionnaire and Mini Questionnaire. Together, these import files can speed the process of testing the custom code used in these project. To make test data. Open the R Project in the [`test_data`](./test_data/) folder and run `make_test_data.R`. If you provide a file called `replacement_demographics.csv` and it has the columns first_name, last_name, email, and phone1 the RScript will replace the first_name, email, and phone on each demographic record. 

To load test data, erase all data in the project, then load these three files from [`./test_data/output/`](./test_data/output/):

```
all_baseline_data.csv
```

There are additional `mini_questionnaire_0*` files, but they might not be useful for typical testing. They fill in the mini-question with diminishing frequency, but they do it out of sync with the Appointment and Result forms. That is probably not helpful, but they are provided here should they prove useful.


## Clone a project into Production

To clone a development project into a production project, follow the steps below. Note: This procedure assumes you are copying a project within a REDCap host.

1. Locate your development project and copy it using `Project Setup`, `Other Functionality`, `Copy the project`.
1. Set the details on the new project 'First Responder COVID-19 Testing - Production'.
1. Also copy the following project attributes:
    - [ ] All records/responses (NNN records total)
    - [x] All users and user rights
    - [x] All users roles
    - [x] All reports
    - [x] All report folders
    - [x] All data quality rules
    - [x] All Project Folders
    - [x] All settings for Survey Queue and Automated Survey Invitations
    - [x] All project bookmarks
    - [x] All custom record status dashboards
    - [x] All settings for External Modules (modules will be disabled by default)
    - [x] All alerts & notifications
1. Enable Project Overlay Banner module.  Set text to
`This is the real, production project. It is under construction and not ready for live data`. Set the CSS to something like this to change the BG color to yellow

    ```
    #project-overlay-banner {
        /* position and z-index cause the banner to appear to float over the page */
        position: fixed;
        z-index: 1000;
    
        opacity: 0.8;
    
        --bg-color: #008888;
        background-color: var(--bg-color);
    
        padding: 1%;
    
        width: 100%;
    
        text-align: center;
    }
    ```
1. Enable the FR Covidata module.
1. Configure the FR Covidata module identify to set `Which location ID is this project for? (0-15)`. Change this to _not_ match the location in the development project.
1. Configure the FR Covidata module identify to set `Which instrument is used for appointments?` In the included REDCap XML files, the form is named "Appointments".
1. Configure the FR Covidata module to indicate which repeat type is used for repeats: _Repeating instances_ or _Individual Events_. In the included REDCap XML files, _events_ are used.
1. Use a MySQL client to load the site data from [`redcap_entity_test_site_data.sql`](example/redcap_entity_test_site_data.sql).
1. Adjust the project_ids referenced on those just-loaded sites by editing and running a copy of [`redcap_entity_test_site_update.sql`](example/redcap_entity_test_site_update.sql)
1. Access `Define Sites` to make any needed changes to the site definitions.
1. Generate the initial appointment blocks by accessing `Define Sites`, selecting the appropriate sites, and clicking `Manually generate new appointments`.
1. Add an API token for the data manager and send the token to be integrated into Rscript-based tools that need it.
1. Assign User Rights privileges to the Study team lead so they can make roles and adjust user rights for their team.
1. Delete reports that are only relevant in the development.
1. Edit Alerts to remove testing email addresses in the To: field, set From addresses to something reasonable, "TEST" banner at the top of the emails, and "TESTING:" from the subject lines.
1. Switch the project to production mode.
1. Disable the Project Overlay Banner module.
1. Add the Public Survey URL to the public-facing landing page.
1. You have now completed the project deployment.

