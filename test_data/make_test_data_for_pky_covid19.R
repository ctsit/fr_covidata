# Generate datasets for FR Covid-19

library(tidyverse)
library(lubridate)

source("./R/functions.R")

# control limits on the test data
record_start <- 10
n <- 200

# generate a vector of values for each column with a uniform distribution
record_id <- tibble(record_id = seq(from = record_start, to = n + record_start-1))

# Get some names and fake demographic data
demographic_data <- read_csv("us-500.csv")
demographic_data <- demographic_data %>%
  sample_n(n)

# Replace contact info with the team's contact info
replacement_demographics_file <- "replacement_demographics_for_pky_covid19.csv"
if (file.exists(replacement_demographics_file)) {
  replacement_demographics <- read_csv(replacement_demographics_file) %>%
    select (-last_name)
  replacement_sample <- sample_n(replacement_demographics, n, replace = T)
  demographic_data <- demographic_data %>%
    select (-first_name, -email, -phone1) %>%
    bind_cols(replacement_sample)
}

# make ICF
icf <- make_icf_data_pky(record_id, n, demographic_data, "baseline_arm_1")
write_csv(icf, "output/informed_consent_form_pky.csv", na = "")

str(demographic_data)

# questionnaire
questionnaire <- make_questionnaire_pky(record_id, n, demographic_data, "baseline_arm_1", icf)
write_csv(questionnaire, "output/questionnaire_pky.csv", na = "")

# Make concatenated dataset
all_baseline_data <- bind_cols(icf, questionnaire) %>%
  select(-ends_with("1"), -ends_with("2"))
write_csv(all_baseline_data, "output/all_baseline_data_pky.csv")
