<?php 
    $table_name = $this->option_tbl;
    $results = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} GROUP BY `email`" ), OBJECT ); 

    $add_these_fields = array();
    if ( get_option( 'add_these_fields' ) !== false ) {
        $add_these_fields = get_option( 'add_these_fields');
    }

?>
<div class="support-users-details">
    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top"
                class="screen-reader-text"><?php _e('Select bulk action', 'support-desk'); ?></label><select
                name="action" id="bulk-action-selector-top">
                <option value="-1"><?php _e('Bulk actions', 'support-desk'); ?></option>
                <option value="delete"><?php _e('Delete', 'support-desk'); ?></option>
            </select>
            <input type="submit" id="doaction_forUserList" class="button action support_bulkUserList" value="<?php _e('Apply', 'support-desk'); ?>">

        </div>

        <div class="tablenav-pages one-page"><span class="displaying-num">2 items</span>
            <span class="pagination-links"><span class="tablenav-pages-navspan button disabled"
                    aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current
                        Page</label><input class="current-page" id="current-page-selector" type="text" name="paged"
                        value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span
                            class="total-pages">1</span></span></span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span>
        </div>
        <br class="clear">
    </div>

    <table class="table table-support-users-details jquerydatatable">
        <thead>
            <tr class="support-users-wrapper">
                <td id="cb" class="manage-column column-cb check-column">
                    <input id="cb-select-all-1" type="checkbox">
                </td>
                <?php if( in_array( 'user_number' ,$add_these_fields ) ) {?>
                <th><?php _e('User Number', 'support-desk'); ?></th>
                <?php } if( in_array( 'first_name' ,$add_these_fields ) ) {?>
                <th><?php _e('First Name', 'support-desk'); ?></th>
                <?php } if( in_array( 'last_name' ,$add_these_fields ) ) {?>
                <th><?php _e('Last Name', 'support-desk'); ?></th>
                <?php } if( in_array( 'name' ,$add_these_fields ) ) {?>
                <th><?php _e('Name', 'support-desk'); ?></th>
                <?php } if( in_array( 'email' ,$add_these_fields ) ) {?>
                <th><?php _e('Email', 'support-desk'); ?></th>
                <?php } if( in_array( 'phone_number' ,$add_these_fields ) ) {?>
                <th><?php _e('Phone Number', 'support-desk'); ?></th>
                <?php } if( in_array( 'user_date' ,$add_these_fields ) ) {?>
                <th><?php _e('User Date', 'support-desk'); ?></th>
                <?php } if( in_array( 'user_time' ,$add_these_fields ) ) {?>
                <th><?php _e('User Time', 'support-desk'); ?></th>
                <?php } if( in_array( 'subject' ,$add_these_fields ) ) {?>
                <th><?php _e('Subject', 'support-desk'); ?></th>
                <?php } if( in_array( 'status' ,$add_these_fields ) ) {?>
                <th><?php _e('Status', 'support-desk'); ?></th>
                <?php } if( in_array( 'nonce' ,$add_these_fields ) ) {?>
                <th><?php _e('Nonce', 'support-desk'); ?></th>
                <?php } if( in_array( 'ticket_date' ,$add_these_fields ) ) {?>
                <th><?php _e('Date', 'support-desk'); ?></th>
                <?php }?>
            </tr>
        </thead>
        <tbody>
            <?php
            
            $i = 1;
            foreach($results as $single_user){
                if(empty($single_user->name)) $single_user->name = $single_user->first_name . ' ' . $single_user->last_name;
            ?>
            <tr>
                <th scope="row" class="check-column"><input type="checkbox" name="bulk_action[]" value="<?php echo $single_user->id; ?>"></th>
                <?php if( in_array( 'user_number' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->user_number; ?></td>
                <?php } if( in_array( 'first_name' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->first_name; ?></td>
                <?php } if( in_array( 'last_name' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->last_name; ?></td>
                <?php } if( in_array( 'name' ,$add_these_fields ) ) {?>
                <td>
                    <?php echo $single_user->name; ?>
                    <div class="row-actions">
                        <span class="edit">
                            <a
                                href="<?php echo admin_url( 'admin.php?page=support_users_details&eid=' . $single_user->id ); ?>"><?php _e('Edit', 'support-desk'); ?></a>
                            |
                        </span>
                        <span class="edit">
                            <a class="text-danger"
                                href="<?php echo admin_url( 'admin.php?page=support_users_details&did=' . $single_user->id ); ?>"><?php _e('Delete', 'support-desk'); ?></a>
                        </span>
                    </div>
                </td>
                <?php } if( in_array( 'email' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->email; ?></td>
                <?php } if( in_array( 'phone_number' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->phone_number; ?></td>
                <?php } if( in_array( 'user_date' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->user_date; ?></td>
                <?php } if( in_array( 'user_time' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->user_time; ?></td>
                <?php } if( in_array( 'subject' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->subject; ?></td>
                <?php } if( in_array( 'status' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->status; ?></td>
                <?php } if( in_array( 'nonce' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->nonce; ?></td>
                <?php } if( in_array( 'ticket_date' ,$add_these_fields ) ) {?>
                <td><?php echo $single_user->ticket_date; ?></td>
                <?php }?>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>