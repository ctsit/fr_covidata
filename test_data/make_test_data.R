# Generate datasets for FR Covid-19

library(tidyverse)
library(lubridate)

source("./R/functions.R")

# control limits on the test data
n <- 100
max_age <- 95

# generate a vector of values for each column with a uniform distribution
record_id <- tibble(record_id = seq(from = 1, to = n))

# Get same names and fake demographic data
email_addresses <- c("pbc@ufl.edu", "tls@ufl.edu")
demographic_data <- read_csv("us-500.csv") %>%
  mutate(email = sample(email_addresses, nrow(demographic_data), replace = TRUE)) %>%
  sample_n(n)

 # make ICF
icf <- make_icf_data(record_id, n, demographic_data, "baseline_arm_1")
write_csv(icf, "output/villages_informed_consent_form_2.csv", na = "")

#str(demographic_data)

# questionnaire
questionnaire <- make_questionnaire(record_id, n, demographic_data, "baseline_arm_1", icf$ce_orgconsentdate)
write_csv(questionnaire, "output/questionnaire.csv", na = "")

# mini questionnaire
mini_n = n
record_id_n = record_id
mini_questionnaire_0 <- make_mini_questionnaire(record_id, n, "baseline_arm_1")
write_csv(mini_questionnaire_0, "output/mini_questionnaire_0.csv", na = "")

mini_n = round(0.7*n)
record_id_n = sample_n(record_id_n, mini_n)
mini_questionnaire_n <- make_mini_questionnaire(record_id_n, mini_n, "retest_1_arm_1")
write_csv(mini_questionnaire_n, "output/mini_questionnaire_1.csv", na = "")

mini_n = round(0.7*mini_n)
record_id_n = sample_n(record_id_n, mini_n)
mini_questionnaire_n <- make_mini_questionnaire(record_id_n, mini_n, "retest_2_arm_1")
write_csv(mini_questionnaire_n, "output/mini_questionnaire_2.csv", na = "")

mini_n = round(0.7*mini_n)
record_id_n = sample_n(record_id_n, mini_n)
mini_questionnaire_n <- make_mini_questionnaire(record_id_n, mini_n, "retest_3_arm_1")
write_csv(mini_questionnaire_n, "output/mini_questionnaire_3.csv", na = "")
