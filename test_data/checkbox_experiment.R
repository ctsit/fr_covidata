
# Define an R version of Perl qw
qw <- function(words){
  unlist(strsplit(words,"[ \n]+"))
}

# symptom Checkboxes - 1 or 2
symptom_checkboxes <- qw("q_fever
    q_cough
    q_fatigue
    q_shortness
    q_sputum
    q_nasal
    q_throat
    q_headache
    q_joints
    q_nausea
    q_diarrhea
    q_lossoftaste
    q_lossofsmell
    q_other")

make_checkbox <- function(checkbox, n, codes) {
  my_tibble <- tibble()
}
