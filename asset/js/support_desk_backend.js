jQuery(document).ready(function($){
    if(jQuery('.jquerydatatable').length){
        jQuery('.jquerydatatable').DataTable();
    }

    jQuery(document).on('click', 'input.support_bulk', function(e){
        let ids = [];
        let action = jQuery('select#bulk-action-selector-top').val();
        
        jQuery('input[type="checkbox"][name="bulk_action[]"]:checked').each(function(k, v){
            ids.push(jQuery(v).val());
        });

        if(action == 'delete'){
            // Delete Support
            jQuery.get(object.base_url + 'supportdesk/status/' + action + '/' + ids.join('-'), function(data){
                location.reload();
            })
        }

    }); // End on click input.support_bulk



    jQuery(document).on('change', 'select#status', function(){
        var thisvalue = jQuery(this).val();
        if(thisvalue != 'close'){
            document.getElementById('replaySubmitButton').removeAttribute('disabled');
        }else{
            document.getElementById('replaySubmitButton').setAttribute('disabled', 'disabled');
        }
    }); // Emd on Change event 



    jQuery(document).on('click', 'div#history a', function(e){
        e.preventDefault();
        window.open( jQuery(this).attr('href'), "_blank"); 
    });

});