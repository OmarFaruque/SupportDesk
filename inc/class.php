<?php

if (!class_exists('supportDeskClass')) {
    class supportDeskClass{
        public $wpdb;
        private $support;
        private $replay_tbl;
        private $option_tbl;
        private $plugin_url;
        private $noteTable;
        
        /**Plugin init action**/ 
        public function __construct() {
            global $wpdb;
            $this->option_tbl   = 'sDesk';
            $this->wpdb         = $wpdb;
            $this->replay_tbl   = $this->wpdb->prefix . 'support_replay_tbl';
            $this->noteTable    = $this->wpdb->prefix . 'support_note_tbl';
            $this->support      = array(
                'open'          => __('Open', 'support-desk'),
                'close'         => __('Close', 'support-desk')
            );
            $this->plugin_url   = supportDeskURL;
            $this->init();
        }

        public function init(){
            register_activation_hook( SD_PATH, array($this,'create_contact_table'));
            add_action('admin_menu', array($this, 'site_menu'));
            add_action( 'wpcf7_mail_sent', array($this, 'your_wpcf7_mail_sent_function'));
            //add_action('init','ticket_type');
            //add_action( 'init', array($this,'custom_ticket_post'));
            // add_action( 'wp_head', array($this, 'testFunction') );
            add_action( 'admin_enqueue_scripts', array($this, 'supportAdminEnqueScripts') );
            add_action( 'wp_enqueue_scripts', array($this, 'supportFrontendEnqueScripts') );

            //Shortcode
            add_shortcode( 'support-desk-user-reply', array($this, 'supportDeskUserReplyCallback') );            // Filter the content
            add_filter('the_content', array($this, 'addShortcodeToTheContent'));

            // route
            add_action( 'init', array($this, 'wpse26388_rewrites_init') );
            add_filter( 'query_vars', array($this, 'wpse26388_query_vars') );
            add_action( 'rest_api_init', array($this, 'registerSupportDeskRestAPI'));
            add_filter( 'wp_mail_content_type', array($this, 'wpse27856_set_content_type') );
            
        }

        public function wpse27856_set_content_type(){
            return "text/html";
        }

        public function testF(){
            echo 'omar Faruque';
            $ids = array(1,2,3);
            $update = $this->wpdb->query( $this->wpdb->prepare( "UPDATE ".$this->option_tbl." SET `status`=%s WHERE `id` IN (%s)", 'close', $ids ) );

            echo 'UPdate: ' . $update . '<br/>';
            
        }

        public function registerSupportDeskRestAPI(){
           /*
            * Rest API
            */
            register_rest_route( 'supportdesk', '/status/(?P<action>[a-zA-Z0-9-]+)/(?P<ids>[a-zA-Z0-9-]+)', array(
                // Supported methods for this endpoint. WP_REST_Server::READABLE translates to GET.
                'methods' => 'GET',
                // Register the callback for the endpoint.
                'callback' => array($this, 'deleteSupportDesk'),
            ) );   
        }


        public function deleteSupportDesk($data){
            $ids = explode('-', $data['ids']);
            $action = $data['action'];
            $action = $action == 'delete' ? 'close' : 'open';
            foreach($ids as $id){
                $update = $this->wpdb->update(
                    $this->option_tbl,
                    array(
                        'status' => $action
                    ),
                    array(
                        'id' => $id
                    ),
                    array('%s'), 
                    array('%d')
                );    
            }

            return array(
                'msg' => 'success'
            );
            
        }

        function wpse26388_rewrites_init(){

            $pageurl = 1;
            if ( get_option( 'support_desk_user_reply_page' ) !== false ) {
                $support_desk_user_reply_page = get_option( 'support_desk_user_reply_page');
            }
            $pageurl = get_permalink( $support_desk_user_reply_page );
            $pageurl_array = explode("/",$pageurl);
            $filter_pageurl = array_filter($pageurl_array); 
            $page_name = end($filter_pageurl);

            add_rewrite_rule(
                $page_name . '/([A-Za-z0-9\-\_]+)/?$',
                'index.php?pagename=' . $page_name . '&user_support_id=$matches[1]',
                'top' );

        }
        
        function wpse26388_query_vars( $query_vars ){
            $query_vars[] = 'user_support_id';
            return $query_vars;
        }

        private function processUserReplay($posts){

            global $wpdb; 
                $insert_replay = $wpdb->insert(
                    $this->replay_tbl, 
                    array(
                        'support_id' => $posts['nonce_author_id'],
                        'message' => $posts['msg'],
                        'message_author' => 'user'
                   ), 
                   array('%d', '%s', '%s')
               );

               $name = $posts['nonce_author_name'];
               $lastid = $posts['nonce_author_id'];

               if($insert_replay){
                   // Send mail to admin
                   $to = get_option( 'notification_email', get_option('admin_email') );
                   $subject = sprintf('A New Reply Support Desk ticket from %s', $name);
                   $body = sprintf('
                       Hi, <br/>
                       <p>A new reply support desk tecket submit by %s</P>
                       <p>Please click <a href="%s">here</a> for get details.</p>
                   ', $name, admin_url( 'admin.php?page=support_desk&id=' . $lastid ));
                   $headers = array('Content-Type: text/html; charset=UTF-8'); 
                   wp_mail( $to, $subject, $body, $headers );
               }
        }


        function supportDeskUserReplyCallback(){
            ob_start();

            if(isset($_REQUEST['replay'])){
                $error = '';
                if(isset($_POST['msg']) && empty($_POST['msg'])){
                    $error .= __('Message are required', 'support-desk');
                }else{
                    $process = $this->processUserReplay($_POST);
                }
            }
            $user_support_id = get_query_var( 'user_support_id', 1 );

            $table_name = $this->option_tbl;
            $nonce_author_id_array = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE `nonce`=%s", $user_support_id ), OBJECT );
            
            $nonce_author_id = $nonce_author_id_array->id;

            $table_name = $this->replay_tbl;
            $replay_tbl = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE `support_id`=%d", $nonce_author_id ), OBJECT ); 
            ?>
                <div class="welcome-user-reply-content">
                    <h2>
                        <?php 
                            echo sprintf('Details about %s', $nonce_author_id_array->subject );
                        ?>
                    </h2>
                    <p class="about-description">
                        <span><small><i><?php echo sprintf('E-mail: %s,', $nonce_author_id_array->email); ?></i></small></span>
                        &nbsp;
                        <span><small><i><?php echo sprintf('Name: %s,', $nonce_author_id_array->name); ?></i></small></span>
                        &nbsp;
                        <span><small><i><?php echo sprintf('Date: %s', date('M d, Y H:i A', strtotime($nonce_author_id_array->ticket_date))); ?></i></small></span>
                    </p>
                    <br><br>
                    <div id="history">
                        <?php
                        foreach ($replay_tbl as $single_reply){
                            $className = ( $single_reply->message_author == 'user' ) ? 'mine' : 'theirs';
                            $time = strtotime( $single_reply->r_date );
                            ?>
                            <div class="<?php echo $className; ?>">
                                <p class="author_name mb-0 mt-0"><i><?php echo $single_reply->message_author != 'user' ? __('Support Agent', 'support-desk') : $nonce_author_id_array->name; ?></i></p>
                                <p class="msg-time mt-0 mb-0"><?php echo date("M d, Y H:i A", $time ); ?></p>
                                <p class="owner-msg mt-0"><?php echo $single_reply->message; ?></p>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="welcome-panel-column-container mt-2 mb-2">
                        <div id="post-body-content">
                            <form id="replayForm" method="POST" action="">
                                <input type="hidden" name="nonce_author_id" value="<?php echo $nonce_author_id; ?>">
                                <input type="hidden" name="nonce_author_name" value="<?php echo $nonce_author_id_array->name; ?>">
                                <input type="hidden" name="email" value="<?php echo $nonce_author_id_array->email; ?>">
                                <input type="hidden" name="subject" value="<?php echo $nonce_author_id_array->subject; ?>">
                                <div class="form-group">
                                    <label for="msg"><?php _e('Replay', 'support-desk'); ?></label>
                                    <?php      
                                        $editor_id = 'msg';
                                        $content = '';
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

                                    <?php if(isset($error) && !empty($error)): ?>
                                        <div id="errorMsg">
                                            <div class="errorinner text-danger"><?php echo $error; ?></div>
                                        </div>
                                    <?php endif; ?>
                                    
                                </div>
                                <input type="submit" name="replay" class="button button-primary" value="<?php _e('Submit', 'support-desk'); ?>">
                            </form>
                        </div>
                    </div>
                </div>

            <?php
            $output = ob_get_clean();
            return $output;
        }




        /*
        * Filter The content
        */
        public function addShortcodeToTheContent( $content ){
            global $post;
            if( $post->ID == get_option('support_desk_user_reply_page') ){
                $content = '[support-desk-user-reply]';
            }
            
            return $content;
        }



        public function supportAdminEnqueScripts(){
            wp_register_style( 'supportAdminStyle', $this->plugin_url . 'asset/css/support_desk_backend.css', array(), time(), 'all' );
            wp_enqueue_style( 'supportAdminStyle' );
            wp_enqueue_style( 'fontawesomeCSS', 'https://use.fontawesome.com/releases/v5.4.1/css/all.css', array(), true, 'all' );
            wp_enqueue_script('supportAdminStyleJS', $this->plugin_url . 'asset/js/support_desk_backend.js', array('jquery'), time(), true);
            
            wp_enqueue_script( 'dataTableJS', 'https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js', array(), time(), true);
            wp_enqueue_style( 'dataTableCSS', 'https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.css', array(), true, 'all' );

            wp_localize_script( 'supportAdminStyleJS', 'object', array(
                'base_url' => get_rest_url( null, '' )
            ) );
        }
        
        public function supportFrontendEnqueScripts(){
            wp_enqueue_style( 'supportfrontendStyleCSS', $this->plugin_url . 'asset/css/support_desk_frontend.css', array(), true, 'all' );
        }


        public function testFunction(){
            $to = 'ronymaha@gmail.com';
            $subject = 'The subject';
            $body = 'The email body content for test';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            $mail = wp_mail( $to, $subject, $body, $headers );

            if(!$mail){
                echo 'mail send failed';
            }


            $pageurl = 1;
            if ( get_option( 'support_desk_user_reply_page' ) !== false ) {
                $support_desk_user_reply_page = get_option( 'support_desk_user_reply_page');
            }
            $pageurl = get_permalink( $support_desk_user_reply_page );
            $pageurl_array = explode("/",$pageurl);
            $filter_pageurl = array_filter($pageurl_array); 
            echo $page_name = end($filter_pageurl);
        }


        // Sub Menu

        public function site_menu(){
            add_menu_page(
                'SupportDesk',
                'Support Desk',
                'manage_options',
                'support_desk',
                array($this, 'sub_option'),
                'dashicons-buddicons-buddypress-logo',
                null
            );

            add_submenu_page(
                'support_desk',
                __('Support Users List', 'support-desk'),
                __('Support Users List', 'support-desk'),
                'manage_options',
                'support_users_details',
                array($this, 'support_users_detailsCallback')
            );

            add_submenu_page(
                'support_desk',
                __('Settings', 'support-desk'),
                __('Settings', 'support-desk'),
                'manage_options',
                'settings',
                array($this, 'settingsPageCallback')
            );
        }


        public function settingsPageCallback(){
            include_once(supportDeskDIR . 'admin/display-support-setings.php');
        }

        public function support_users_detailsCallback(){
            include_once(supportDeskDIR . 'admin/support-users-details.php');
        }



        /*
        * Toggle close / open
        */
        public function marktoClose($id, $status){
            $update = $this->wpdb->update(
                $this->option_tbl,
                array(
                    'status' => $status
                ),
                array(
                    'id' => $id
                ),
                array('%s'), 
                array('%d')
            );

            if($update){
                if(isset($_GET['spam']) && $_GET['spam'] == 'all'){
                    wp_safe_redirect( admin_url( 'admin.php?page=support_desk&spam=all' ) );
                    exit;
                }else{
                    wp_safe_redirect( admin_url( 'admin.php?page=support_desk' ) );
                    exit;
                }
            }
        }


        /*
        * Mark support as spam
        */
        public function markAsSpam($id, $spam){
                $update = $this->wpdb->update(
                    $this->option_tbl,
                    array(
                        'folder' => $spam
                    ),
                    array(
                        'id' => $id
                    ),
                    array('%d'), 
                    array('%d')
                );

                if($update){
                    if($spam == 0){
                        wp_safe_redirect( admin_url( 'admin.php?page=support_desk' ) );
                        exit;
                    }else{
                        wp_safe_redirect( admin_url( 'admin.php?page=support_desk&spam=all' ) );
                        exit;
                    }
                    
                }

        }


        public function getAllNotes(){
            $allNOtes = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT `note` FROM {$this->noteTable} WHERE `support_id`=%d", $_GET['id']), OBJECT );
            return $allNOtes;
        }

        public function setNote($note){
            $insert_note = $this->wpdb->insert(
                $this->noteTable, 
                array(
                    'note' => $note, 
                    'support_id' => $_GET['id']
                ), 
                array(
                    '%s', '%d'
                )
            );
        }

        public function sub_option() {
            if(isset($_GET['id']) && !empty($_GET['id'])){
                if(isset($_POST['note_submit'])){
                    $this->setNote($_POST['note']);
                }
                include_once(supportDeskDIR . 'admin/display-support-details.php');
            }
            elseif(isset($_GET['sid']) && isset($_GET['action']) && $_GET['action'] == 'spam') {
                $this->markAsSpam($_GET['sid'], 0);
            }
            elseif(isset($_GET['sid']) && isset($_GET['action']) && $_GET['action'] == 'not-spam') {
                $this->markAsSpam($_GET['sid'], 1);
            }
            elseif(isset($_GET['sid']) && isset($_GET['action']) && $_GET['action'] == 'close') {
                $this->marktoClose($_GET['sid'], 'close');
            }
            elseif(isset($_GET['sid']) && isset($_GET['action']) && $_GET['action'] == 'open') {
                $this->marktoClose($_GET['sid'], 'open');
            }

            else{
                include_once(supportDeskDIR . 'admin/display_data_files.php');
            }


          
        }


        private function sendMailToUser($posts){

            /*
            #@ Source: https://developer.wordpress.org/reference/functions/wp_mail/
            * Mail Send to user while replay
            */

            $email = $posts['email'];
            $name = $posts['name'];
            $subject = $posts['subject'];
            $message = $posts['msg'];
            $nonce = $posts['nonce'];
            $number = $posts['number'];

            $pageurl = 1;
            if ( get_option( 'support_desk_user_reply_page' ) !== false ) {
                $support_desk_user_reply_page = get_option( 'support_desk_user_reply_page');
            }
            $pageurl = get_permalink( $support_desk_user_reply_page );

            $custom_url = $pageurl . $nonce;

            $mail_body = '';
            if ( get_option( 'support_desk_mail_body' ) !== false ) {
                $mail_body = get_option( 'support_desk_mail_body');
            }

            if(strpos($mail_body, '{customer_name}') !== false){
                $mail_body = str_replace( '{customer_name}', $name, $mail_body );
            }
            if(strpos($mail_body, '{admin_answer}') !== false){
                $mail_body = str_replace( '{admin_answer}', $message, $mail_body );
            }
            if(strpos($mail_body, '{replay_url}') !== false){
                $mail_body = str_replace( '{replay_url}', $custom_url, $mail_body );
            }
            if(strpos($mail_body, '{customer_email}') !== false){
                $mail_body = str_replace( '{customer_email}', $email, $mail_body );
            }
            if(strpos($mail_body, '{subject}') !== false){
                $mail_body = str_replace( '{subject}', $subject, $mail_body );
            }
            if(strpos($mail_body, '{phone_number}') !== false){
                $mail_body = str_replace( '{phone_number}', $number, $mail_body );
            }

            $to = $email;
            $subject = $subject;
            $body = $mail_body;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            wp_mail( $to, $subject, $body, $headers );
        }

        private function processAdminReplay($posts){
            $insert = false;
            if(wp_verify_nonce( $posts['support_form_nonce'], 'support_form' )){
                
                $insert = $this->wpdb->insert(
                    $this->replay_tbl, 
                    array(
                        'support_id' => $_GET['id'],
                        'status' => $posts['status'],
                        'message' => $posts['msg'],
                        'message_author' => 'admin'
                    ), 
                    array('%d', '%s', '%s', '%s')
                );
                
                if($insert){

                    $this->sendMailToUser($posts);                
                }

                $this->wpdb->update( 
                    $this->option_tbl,
                array(
                        'status' => $posts['status']
                    ),
                array(
                    'id'=> $_GET['id']
                ),
                array('%s'),
                array('%d')
                );

                
            }
            return $insert;
        }

        public function create_contact_table(){
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $this->option_tbl;

            // $this->wpdb->query("DROP TABLE $table_name");
            // Support Table
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                email varchar(150) NOT NULL,
                first_name varchar(100) NOT NULL,
                last_name varchar(100) NOT NULL,
                phone_number int(50) NOT NULL,
                user_date varchar(100) NOT NULL,
                user_time varchar(100) NOT NULL,
                user_number int(50) NOT NULL,
                subject text NOT NULL,
                message text NOT NULL,
                status varchar(100) NOT NULL,
                nonce text NOT NULL,
                folder int(9) NOT NULL DEFAULT 1,
                ticket_date TIMESTAMP NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );


            // $this->wpdb->query("DROP TABLE $replay_tbl");
            // Replay Table
            $rep_sql = "CREATE TABLE $this->replay_tbl (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                support_id int(15) NOT NULL,
                message_author varchar(150) NOT NULL,
                status varchar(150) NOT NULL,
                message text NOT NULL,
                r_date TIMESTAMP NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $rep_sql );

            // $this->wpdb->query("DROP TABLE $noteTable");
            // Note Table
            $note_sql = "CREATE TABLE $this->noteTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                support_id int(15) NOT NULL,
                note text NOT NULL,
                c_date TIMESTAMP NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $note_sql );

        }


        function your_wpcf7_mail_sent_function( $contact_form ) {
            $title = $contact_form->title;
            $submission = WPCF7_Submission::get_instance();  
            if ( $submission ) {
                $posted_data = $submission->get_posted_data();
            }       
           if ( 'sDeskForm' == $title ) {
                $name = strtolower($posted_data['your-name']);
                //$name = strtolower(str_replace(' ', '_',  $name));
                $email = strtolower($posted_data['your-email']);
                $subject = strtolower($posted_data['your-subject']);
                $message = strtolower($posted_data['your-message']);

                $nonce = wp_create_nonce( $email . $subject . time() );
                //$phone = strtolower($posted_data['phone']);
                
                $first_name = ( strtolower($posted_data['your-first-name']) == '' ) ? $name : strtolower($posted_data['your-first-name']);
                $last_name = ( strtolower($posted_data['your-last-name']) == '' ) ? $name : strtolower($posted_data['your-last-name']);
                $phone_number = ( strtolower($posted_data['your-phone-number']) == '' ) ? '123456789' : strtolower($posted_data['your-phone-number']);
                $user_date = ( strtolower($posted_data['your-date']) == '' ) ? date("Y-m-d") : strtolower($posted_data['your-date']);
                $user_time = ( strtolower($posted_data['your-time']) == '' ) ? date("h:i:sa") : strtolower($posted_data['your-time']);
                $user_number = ( strtolower($posted_data['your-number']) == '' ) ? '1' : strtolower($posted_data['your-number']);

                global $wpdb; 
                $insert = $wpdb->insert($this->option_tbl, 
                    array(
                        'name' => $name, 
                        'email' => $email,
                        'subject' => $subject,
                        'message' => $message,
                        'status' => 'open',
                        'nonce' => $nonce,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'phone_number' => $phone_number,
                        'user_date' => $user_date,
                        'user_time' => $user_time,
                        'user_number' => $user_number,
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%d',

                    ) 
                );

                if($insert){
                    $lastid = $this->wpdb->insert_id;
                }

                $insert_replay = $wpdb->insert(
                     $this->replay_tbl, 
                     array(
                         'support_id' => $lastid,
                         'message' => $message,
                         'message_author' => 'user'
                    ), 
                    array('%d', '%s', '%s')
                );

                if($insert_replay){
                    // Send mail to admin
                    $to = get_option( 'notification_email', get_option('admin_email') );
                    $subject = sprintf('A New Support Desk ticket from %s', $name);
                    $body = sprintf('
                        Hi, <br/>
                        <p>A new support desk tecket submit by %s</P>
                        <p>Please click <a href="%s">here</a> for get details.</p>
                    ', $name, admin_url( 'admin.php?page=support_desk&id=' . $lastid ));
                    $headers = array('Content-Type: text/html; charset=UTF-8'); 
                    wp_mail( $to, $subject, $body, $headers );
                }
         }
        }


    } // End Class
} // End Class check if exist / not




