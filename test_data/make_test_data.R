# Generate datasets for FR Covid-19

library(tidyverse)
library(lubridate)

source("./R/functions.R")

# control limits on the test data
n <- 200

# generate a vector of values for each column with a uniform distribution
record_id <- tibble(record_id = seq(from = 1, to = n))

# Get some names and fake demographic data
demographic_data <- read_csv("us-500.csv")
demographic_data <- demographic_data %>%
  sample_n(n)

# Replace contact info with the team's contact info
replacement_demographics_file <- "replacement_demographics.csv"
if (file.exists(replacement_demographics_file)) {
  replacement_demographics <- read_csv(replacement_demographics_file) %>%
    select (-last_name)
  replacement_sample <- sample_n(replacement_demographics, n, replace = T)
  demographic_data <- demographic_data %>%
    select (-first_name, -email, -phone1) %>%
    bind_cols(replacement_sample)
}

# make ICF
icf <- make_icf_data(record_id, n, demographic_data, "baseline_arm_1")
write_csv(icf, "output/villages_informed_consent_form_2.csv", na = "")

#str(demographic_data)

# questionnaire
questionnaire <- make_questionnaire(record_id, n, demographic_data, "baseline_arm_1", icf$ce_orgconsentdate)
write_csv(questionnaire, "output/questionnaire.csv", na = "")

# mini questionnaire
record_id_n <- make_mini_fraction(record_id,   1.0, "baseline_arm_1", "output/mini_questionnaire_0.csv")
record_id_n <- make_mini_fraction(record_id_n, 0.7, "retest_1_arm_1", "output/mini_questionnaire_1.csv")
record_id_1 <- record_id_n
record_id_n <- make_mini_fraction(record_id_n, 0.7, "retest_2_arm_1", "output/mini_questionnaire_2.csv")
record_id_2 <- record_id_n
record_id_n <- make_mini_fraction(record_id_n, 0.7, "retest_3_arm_1", "output/mini_questionnaire_3.csv")
record_id_3 <- record_id_n

# Make concatenated dataset
mini_questionnaire_0 <- read_csv("output/mini_questionnaire_0.csv")
all_baseline_data <- bind_cols(icf, questionnaire, mini_questionnaire_0) %>%
  select(-ends_with("1"), -ends_with("2"))
write_csv(all_baseline_data, "output/all_baseline_data.csv")
