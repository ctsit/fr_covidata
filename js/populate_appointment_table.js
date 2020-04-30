$(document).ready(function() {
    populateTable();
});

function populateTable() {
    $.getJSON(FRCOVID.data_endpoint, function(data) {
        let tbl_body = "";
        let i = 0;
        tbl_body += "<thead><tr>";

        // dynamically set table header columns based on JSON keys
        let header_titles = data.data[0];
        $.each(header_titles, function(k, v) {
            tbl_body += `<th class='text-center'>${mapHeaderNames(k)}</th>`;
        });
        tbl_body += "</tr></thead>";
        tbl_body += "<tbody>";

        // dynamically populate table rows
        $.each(data.data, function() {
            let tbl_row = "";
            let row_color = "";
            $.each(this, function(k , v) {
                if (k == 'available' && v == 0) {
                    row_color = "style='color: orange'";
                }
                v = mapNames(k, v);
                tbl_row += "<td>"+v+"</td>";
            });
            tbl_body += `<tr ${row_color}>${tbl_row}</tr>`;
        });

        tbl_body += "</tbody>";
        $("#appointment_table").html(tbl_body);
    });
}

function mapHeaderNames(k) {
    return (k in header_names_map ? header_names_map[k] : k);
}

header_names_map = {
    'location': 'Site',
    'testing_type': 'Test',
    'hours': 'Hours',
    'available': 'Avail'
};

function mapNames(k, v) {
    switch(k) {
        case "testing_type":
            v = (v == 'swabandserum' ? 'Swab and Blood' :
                    (v == 'swab' ? 'Swab' : v)
                );
            break;
        case "location":
            let site_short_name = v.match(/\(.*\)/);
            v = ( site_short_name in location_names_map ? location_names_map[site_short_name] : v);
            break;
    }
    return v;
}

location_names_map = {
    '(KED)':  'UF Health - Kanapaha ER (KED)',
    '(SHED)':  'UF Health - Spring Hill ER (SHED)',
    '(UFEDSERUM)' : 'UF Health - Shands ER (UFEDSERUM)',
    '(UFED)' : 'UF Health - Shands ER (UFED)'
};
