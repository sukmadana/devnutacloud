<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
    
    let dropdown = jQuery('#provinsi');
    dropdown.select2(); 
    dropdown.attr('disabled','disabled');
    dropdown.append(jQuery('<option></option>').attr({'value': '',"provinsi_id": '0'}).text('Pilih Provinsi'));
    dropdown.val('').trigger("change");
    let cityDropdown = jQuery('#kota');
    cityDropdown.attr('disabled','disabled');
    cityDropdown.append('<option value="">Silahkan Pilih Provinsi Dahulu</option>');


    const url = '<?=base_url('perusahaan/get_province_ajax/')?>';

    // Populate dropdown with list of provinces
    jQuery.getJSON(url, function (data) {
        jQuery.each(data, function (key, entry) {
            dropdown.append(jQuery('<option></option>').attr({"value": entry.name,"provinsi_id": entry.id}).text(entry.name));
        })
        dropdown.removeAttr('disabled');
        dropdown.attr('onchange','myFunction(this)');
    }); 

});

jQuery.noConflict();

function myFunction(obj)
{
    
    let province_id = jQuery('#provinsi').find(":selected").attr("provinsi_id");
    let cityDropdown = jQuery('#kota');
    cityDropdown.empty();
    cityDropdown.select2('data', null);
    cityDropdown.select2('data', {id: 0, text: 'Mohon Tunggu....'});

    cityDropdown.attr('disabled','disabled');
    cityDropdown.append(jQuery('<option></option>').attr('value', 'dad').text('Mohon Tunggu....'));
    const url = '<?=base_url('perusahaan/get_city_ajax/')?>' + '/' + province_id;
    if (province_id > 0) {
        jQuery.getJSON(url, function (data) {
            cityDropdown.empty();
            jQuery.each(data, function (key, entry) {
                cityDropdown.append(jQuery('<option></option>').attr('value', entry.name).text(entry.name));
            })
            cityDropdown.removeAttr('disabled');
            jQuery("#kota option[value='']").remove();
            cityDropdown.select2();
        });
    } else {
        cityDropdown.empty();
        cityDropdown.append(jQuery('<option></option>').attr('value', '').text('Silahkan Pilih Provinsi Dahulu'));
        cityDropdown.val('').trigger("change");
    }
}

</script>
