$(function() {
    fillIfBlank();
});

function prettyPrint() {
    let $field = $('#redcap-entity-prop-custom_daily_schedule');
    let ugly = $field.val();
    let pretty = JSON.stringify(JSON.parse(ugly), undefined, 2);
    $field.val(pretty);
}

/*
function prettyPrint() {
    // jQuery version doesn't work
    let ugly = document.getElementById('redcap-entity-prop-custom_daily_schedule').value;
    let pretty = JSON.stringify(JSON.parse(ugly), undefined, 2);
    document.getElementById('redcap-entity-prop-custom_daily_schedule').value = pretty;
}
*/

function fillIfBlank() {
    let $field = $("#redcap-entity-prop-custom_daily_schedule");
    if ( !$field.val() ) {
        fillDefault();
    }
}

function fillDefault() {
    let $field = $("#redcap-entity-prop-custom_daily_schedule");
    $field.val(JSON.stringify(default_days, undefined, 2));
}

default_days = {
    "Monday": {
        "open": "",
        "close": ""
    },
    "Tuesday": {
        "open": "",
        "close": ""
    },
    "Wednesday": {
        "open": "",
        "close": ""
    },
    "Thursday": {
        "open": "",
        "close": ""
    },
    "Friday": {
        "open": "",
        "close": ""
    },
    "Saturday": {
        "open": "",
        "close": ""
    },
    "Sunday": {
        "open": "",
        "close": ""
    }
}
