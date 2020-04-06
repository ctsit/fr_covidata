library(tidyverse)
library(lubridate)
library(janitor)

metadata <- read_csv("fr-dict.csv") %>% clean_names() %>% rename(text_validation_type = text_validation_type_or_show_slider_number)

str(metadata)

metadata %>%
  filter(!field_type %in% c("descriptive", "file")) %>%
  select(variable_field_name) %>%
  View()

metadata %>%
  filter(!field_type %in% c("descriptive", "file")) %>%
  filter(form_name == "coronavirus_covid19_questionnaire") %>%
  filter(is.na(branching_logic_show_field_only_if)) %>%
  select(variable_field_name, field_type, choices_calculations_or_slider_labels, required_field,  text_validation_type, branching_logic_show_field_only_if) %>%
  filter(!field_type %in% c("radio", "text", "yesno")) %>%
  #filter(field_type %in% c()) %>%
  select(variable_field_name, field_type,choices_calculations_or_slider_labels)
  #select(variable_field_name)


metadata %>%
  filter(!field_type %in% c("descriptive", "file")) %>%
  #distinct(form_name)
  filter(form_name == "coronavirus_covid19_mini_questionnaire") %>%
  filter(is.na(branching_logic_show_field_only_if)) %>%
  select(variable_field_name, field_type, choices_calculations_or_slider_labels, required_field,  text_validation_type, branching_logic_show_field_only_if) %>%
  #filter(!field_type %in% c("radio", "text", "yesno")) %>%
  #filter(field_type %in% c()) %>%
  select(variable_field_name, field_type,choices_calculations_or_slider_labels)
  #select(variable_field_name)

metadata %>%
  filter(!field_type %in% c("descriptive", "file")) %>%
  #distinct(form_name)
  filter(form_name == "appointment") %>%
  filter(is.na(branching_logic_show_field_only_if)) %>%
  select(variable_field_name, field_type, choices_calculations_or_slider_labels, required_field,  text_validation_type, branching_logic_show_field_only_if) %>%
  #filter(!field_type %in% c("radio", "text", "yesno")) %>%
  #filter(field_type %in% c()) %>%
  select(variable_field_name, field_type,choices_calculations_or_slider_labels)
  #select(variable_field_name)


