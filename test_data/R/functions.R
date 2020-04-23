
make_icf_data <- function(record_id, n, demographic_data, event_name) {
  ce_orgconsentdate <- tibble(ce_orgconsentdate = Sys.Date() - sample(1:6, n, replace = T))
  redcap_event_name <- tibble(redcap_event_name = rep(event_name,n))
  form_complete <- tibble(villages_informed_consent_form_2_complete =rep(2,n))

  # make villages_informed_consent_form_2
  demo <- demographic_data %>%
    rename(ce_firstname = first_name,
           ce_lastname = last_name,
           ce_email = email) %>%
    select(starts_with("ce_"))

  form <- bind_cols(record_id,
                    redcap_event_name,
                    demo,
                    ce_orgconsentdate,
                    form_complete
  )

  return(form)
}

make_questionnaire <- function(record_id, n, demographic_data, event_name, q_date) {
  # make questionaire

  redcap_event_name <- tibble(redcap_event_name = rep(event_name,n))
  form_complete <- tibble(coronavirus_covid19_questionnaire_complete =rep(2,n))

  # make required text fields
  q_date <- tibble(q_date = q_date)
  demo <- demographic_data %>%
    rename(
      streetaddress = address,
      zipcode = zip,
      phone = phone1
    ) %>%
    select(streetaddress, city, state, zipcode, phone)
  patient_dob <- tibble(patient_dob = today()
                        - dyears(sample(20:65, n, replace = T))
                        - ddays(sample(1:365, n, replace = T)))

  # make radio fields
  first_responder_role <- tibble(first_responder_role = sample(1:6, n, replace = T))
  gender <- tibble(gender = sample(1:2, n, replace = T))
  race <- tibble(race = sample(c(1:6,-9), n, replace = T))
  ethnicity <- tibble(ethnicity = sample(c(1:2,-9), n, replace = T))
  q_educ_level <- tibble(q_educ_level = sample(1:6, n, replace = T))

  # make yesno fields
  q_smoke <- tibble(q_smoke = sample(0:1, n, replace=T))
  q_vape <- tibble(q_vape = sample(0:1, n, replace=T))
  q_taking_meds <- tibble(q_taking_meds = sample(0:1, n, replace=T))
  q_travelhome <- tibble(q_travelhome = sample(0:1, n, replace=T))
  q_travelusa <- tibble(q_travelusa = sample(0:1, n, replace=T))
  q_travelchina <- tibble(q_travelchina = sample(0:1, n, replace=T))
  q_work_hosp <- tibble(q_work_hosp = sample(0:1, n, replace=T))
  q_student <- tibble(q_student = sample(0:1, n, replace=T))
  q_apartment <- tibble(q_apartment = sample(0:1, n, replace=T))

  form <- bind_cols(record_id,
                    redcap_event_name,
                    demo,
                    q_date,
                    patient_dob,
                    # radio fields
                    first_responder_role,
                    gender,
                    race,
                    ethnicity,
                    q_educ_level,
                    # yesno fields
                    q_smoke,
                    q_vape,
                    q_taking_meds,
                    q_travelhome,
                    q_travelusa,
                    q_travelchina,
                    q_work_hosp,
                    q_student,
                    q_apartment,
                    form_complete
                      )

  return(form)
}

make_mini_questionnaire <- function(record_id, n, event_name) {
  # make mini_questionaire

  redcap_event_name <- tibble(redcap_event_name = rep(event_name,n))
  form_complete <- tibble(coronavirus_covid19_mini_questionnaire_complete =rep(2,n))

  # make required text fields
  q_hours_contact_patient <- tibble(q_hours_contact_patient = sample(10:50, n, replace=T))
  q_hours_contact_public <- tibble(q_hours_contact_public = sample(10:50, n, replace=T))

  # make yesno fields
  q_recent_exposure <- tibble(q_recent_exposure = sample(0:1, n, replace=T))
  ppe_availability <- tibble(ppe_availability = sample(0:1, n, replace=T))

  form <- bind_cols(record_id,
                    redcap_event_name,
                    q_recent_exposure,
                    ppe_availability,
                    q_hours_contact_patient,
                    q_hours_contact_public,
                    form_complete
  )

  return(form)
}

make_mini_fraction <- function(record_id, fraction, arm, output_file) {
  mini_n = round(fraction*nrow(record_id))
  record_id_n = sample_n(record_id, mini_n)
  mini_questionnaire_n <- make_mini_questionnaire(record_id_n, mini_n, arm)
  write_csv(mini_questionnaire_n, output_file, na = "")
  return(record_id_n)
}


make_icf_data_pky <- function(record_id, n, demographic_data, event_name) {
  ce_orgconsentdate <- tibble(ce_orgconsentdate = Sys.Date() - sample(1:6, n, replace = T))
  redcap_event_name <- tibble(redcap_event_name = rep(event_name,n))
  icf_grade <- tibble(icf_grade = sample(c(0,1,2,3,4,5,6,7,8,9,10,11,12), n, replace = T))

  icf_fingerstick <- tibble(icf_fingerstick = sample(c('BBC', 'CBS', 'IBM', 'YMM', 'IPF', 'DK', 'LMAO'), n, replace = TRUE))
  icf_age <- tibble(icf_age = icf_grade$icf_grade + 6)
  patient_dob <- tibble(patient_dob = today()
                        - dyears(icf_age$icf_age)
                        - ddays(sample(1:350, n, replace = T)))
  icf_lar_name <- tibble(icf_lar_name = case_when(
    icf_age < 18 ~ sample(c('Eunice Bigelow', 'Pris Chase', 'Joyce Zeitler'), n, replace = TRUE),
    TRUE ~ as.character(NA)
  ))
  icf_lar_relationship <- tibble(icf_lar_relationship = case_when(
    icf_age < 18 ~ sample(c('Mother', 'God Mother'), n, replace = TRUE),
    TRUE ~ as.character(NA)
  ))
  icf_child_date <- tibble(icf_child_date = ce_orgconsentdate$ce_orgconsentdate)
  form_complete <- tibble(informed_consent_complete =rep(2,n))


  # make informed_consent_complete
  demo <- demographic_data %>%
    rename(ce_firstname = first_name,
           ce_lastname = last_name,
           icf_email = email) %>%
    select(starts_with("ce_"), icf_email)

  form <- bind_cols(record_id,
                    redcap_event_name,
                    demo,
                    icf_grade,
                    icf_fingerstick,
                    icf_age,
                    patient_dob,
                    icf_lar_name,
                    icf_lar_relationship,
                    icf_child_date,
                    ce_orgconsentdate,
                    form_complete
  )

  return(form)
}


make_questionnaire_pky <- function(record_id, n, demographic_data, event_name, icf) {
  # make questionaire
  redcap_event_name <- tibble(redcap_event_name = rep(event_name,n))
  form_complete <- tibble(pky_coronavirus_covid19_questionnaire_complete =rep(2,n))

  # make required text fields
  qpk_date <- tibble(qpk_date = icf$ce_orgconsentdate)
  demo <- demographic_data %>%
    rename(
      qpk_street_address = address,
      qpk_city = city,
      qpk_state = state,
      qpk_zipcode = zip,
      qpk_email_address = email,
      qpk_phone = phone1
    ) %>%
    mutate(
      qpk_contact_name = paste(first_name, last_name),
      qpk_name_pt = qpk_contact_name
    ) %>%
    select(contains("qpk"))

# make radio fields
  qpk_gender <- tibble(qpk_gender = sample(1:2, n, replace = T))
  qpk_school <- tibble(qpk_school = rep(18,n))
  qpk_patient_age <- tibble(qpk_patient_age = icf$icf_age)

  form <- bind_cols(record_id,
                    redcap_event_name,
                    demo,
                    qpk_date,
                    qpk_school,
                    qpk_gender,
                    qpk_patient_age,
                    form_complete
  ) %>%
  mutate(qpk_contact_name = case_when(
         qpk_patient_age >= 18 ~ qpk_contact_name,
         TRUE ~ icf$icf_lar_name
    )
  )

  return(form)
}
