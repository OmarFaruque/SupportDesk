<?php 
/*
* Settings page
*/


if(isset($_REQUEST['replay'])){
    $mail_body = esc_attr($_POST['mail_body']);
    $support_desk_user_reply_page = esc_attr($_POST['support_desk_user_reply_page']);
    $notification_email = esc_attr($_POST['notification_email']);
    update_option( 'support_desk_mail_body', $mail_body );
    update_option( 'support_desk_user_reply_page', $support_desk_user_reply_page );
    update_option( 'notification_email', $notification_email );
    update_option( 'add_these_fields', $_POST['add_these_fields'] );
}

// echo 'post<pre>';
// print_r($_POST);
// echo '</pre>';

// $add_these_fields = get_option( 'add_these_fields');
// echo 'get<pre>';
// print_r($add_these_fields);
// echo '</pre>';
?>

<div id="wrap">
    <div class="welcome-panel max-width-75 flot-left">
        <div class="settingsForm">
            <h2><?php _e('Settings', 'support-desk'); ?></h2>
            <form action="" method="post">
                
                <div class="select-list-page-cover">
                    <label for="wssn-ks-cb-div pb-1"><?php _e('Select For User List Page', 'support-desk'); ?></label>
                    <div class="wssn-ks-cb-div">
                        <ul class="ks-cboxtags">
                            <li>
                                <input type="checkbox" class="wssn-hidden" name="add_these_fields[]" id="checkbox_auto_approving" value="checkbox" checked/>
                            </li>
                            <?php

                            $all_fields = array( 'no', 'id', 'name', 'email', 'first_name', 'last_name', 'phone_number', 'user_date', 'user_time', 'user_number', 'subject', 'message', 'status', 'nonce', 'ticket_date' );
                            $add_these_fields = array();
                            if ( get_option( 'add_these_fields' ) !== false ) {
                                $add_these_fields = get_option( 'add_these_fields');
                            }
                            foreach($all_fields as $single) {

                                $checked = ( in_array( $single ,$add_these_fields ) ) ? 'checked' : '';
                            ?>
                                <li>
                                    <input type="checkbox" name="add_these_fields[]" id="<?php echo $single ?>" value="<?php echo $single ?>" <?php echo $checked; ?>/>
                                    <label for="<?php echo $single ?>"><?php echo str_replace('_', ' ', $single); ?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>

                <div class="support_desk_user_page">
                    <?php $allpages = get_all_page_ids(); ?>
                    <label for="support_desk_user_reply_page pb-1"><?php _e('User Reply Page', 'support-desk'); ?></label>
                    <select name="support_desk_user_reply_page" class="support_desk_user_reply_page">
                        <?php   foreach( $allpages as $sp):
                            $selected = ( get_option('support_desk_user_reply_page') == $sp ) ? 'selected' : '';
                            ?>
                            <option <?php echo $selected; ?> value="<?php echo $sp; ?>"><?php echo get_the_title($sp); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Mail for admin notification -->
                <div class="form-group">
                  <label for="notification_email"><?php _e('Admin Email', 'support-desk'); ?></label>
                  <input type="email" value="<?php echo get_option( 'notification_email', get_option('admin_email') ); ?>" name="notification_email" id="notification_email" class="form-control" placeholder="" aria-describedby="helpId">
                  <small id="helpId" class="text-muted"><?php _e('Set email for admin notification', 'support-desk'); ?></small>
                </div>

                <!-- Mail Body -->
                <div class="form-group mt-2">
                    <label for="subject"><?php _e('Mail Body for replay email', 'support-desk'); ?></label>
                    <?php                         
                    if ( get_option( 'support_desk_mail_body' ) !== false ) {
                        $mail_body = get_option( 'support_desk_mail_body');
                    }
                    $editor_id = 'mail_body';
                    $content = $mail_body;
                    $args = array(
                        'media_buttons' => false,
                        'textarea_rows' => 8,
                        'tabindex' => 4,
                        'tinymce'       => array(
                            'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                            'toolbar2'      => '',
                            'toolbar3'      => '',
                        ),
                    );
                    wp_editor( $content, $editor_id, $args );
                ?>
                <small id="helpId" class="text-muted"><?php _e('Set Mail body. Add following parameter for dynamic content {replay_url}, {customer_name}, {customer_email}, {admin_answer}, {subject}, {phone_number}', 'support-desk'); ?></small>
                </div>
                <input type="submit" name="replay" class="button button-primary" value="<?php _e('Submit', 'support-desk'); ?>">
            </form>
        </div>
    </div>
    <!-- Note Section Start -->
    <div id="notes" class="max-width-22 flot-right">
        <div class="p-1">
            <h4 class="note-title"><?php echo sprintf('Notes'); ?></h4>
            
            <div class='note-for-setings'>
                <p><?php echo sprintf('1. <a target="_blank" href="%s">Contact Form 7</a> are required for Support Desk.', 'https://wordpress.org/plugins/contact-form-7/') ?></p>
                <p><?php echo '2. Contact Forms name must be "sDeskForm".'; ?></p>
                <p><?php echo '3. Contact Forms form parameter like:
                </br>
                </br>i. User Name for "your-name", 
                </br>ii. User Email for "your-email", 
                </br>iii. User First Name for "your-first-name", 
                </br>iv. User Last name for "your-last-name", 
                </br>v. User Phone Number for "your-phone-number", 
                </br>vi. User subject for "your-subject", 
                </br>vii. User message for "your-message", 
                </br>viii. User Date for "your-date", 
                </br>ix. User Time for "your-time", 
                </br>x. User Number for "your-number",';?></p>
                <!-- <ul>
                    <li></li>
                    <li>'your-email' for user email</li>
                    <li>'your-email' for user email</li>
                    <li>'your-email' for user email</li>
                </ul> -->

            </div>
           
        </div>

    </div>
    <!-- Note Section End  -->

</div>
