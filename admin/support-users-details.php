<?php 


    $table_name = $this->option_tbl;
    $results = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} GROUP BY `email`" ), OBJECT ); 

    // echo '<pre>';
    // print_r($results);
    // echo '</pre>';

    $add_these_fields = array();
    if ( get_option( 'add_these_fields' ) !== false ) {
        $add_these_fields = get_option( 'add_these_fields');
    }

    // echo '<pre>';
    // print_r($add_these_fields);
    // echo '</pre>';

?>
<div class="support-users-details">
    <table class="table table-support-users-details jquerydatatable">
        <thead>
            <tr class="support-users-wrapper">
                <?php if( in_array( 'no' ,$add_these_fields ) ) {?>
                <th><?php _e('No', 'support-desk'); ?></th>
                <?php } if( in_array( 'id' ,$add_these_fields ) ) {?>
                <th><?php _e('ID', 'support-desk'); ?></th>
                <?php } if( in_array( 'user_number' ,$add_these_fields ) ) {?>
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

            ?>
                <tr>
                <?php if( in_array( 'no' ,$add_these_fields ) ) {?>
                    <td><?php echo $i++; ?></td>
                <?php } if( in_array( 'id' ,$add_these_fields ) ) {?>
                    <td><?php echo $single_user->id; ?></td>
                <?php } if( in_array( 'user_number' ,$add_these_fields ) ) {?>
                    <td><?php echo $single_user->user_number; ?></td>
                <?php } if( in_array( 'first_name' ,$add_these_fields ) ) {?>
                    <td><?php echo $single_user->first_name; ?></td>
                <?php } if( in_array( 'last_name' ,$add_these_fields ) ) {?>
                    <td><?php echo $single_user->last_name; ?></td>
                <?php } if( in_array( 'name' ,$add_these_fields ) ) {?>
                    <td><?php echo $single_user->name; ?></td>
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