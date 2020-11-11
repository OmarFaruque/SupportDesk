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

    });
});