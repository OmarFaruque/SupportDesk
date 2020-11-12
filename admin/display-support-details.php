<?php 
    $process = false;
    if(isset($_REQUEST['replay'])){
        // Validateion
        $msg = '';
        if(isset($_POST['msg']) && empty($_POST['msg'])){
            $msg .= __('Message are required.', 'support-desk');
        }
        if(empty($msg)){
            $process = $this->processAdminReplay($_POST);
        }
        
    }


    $table_name = $this->option_tbl;
    $results = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE `id`=%d", $_GET['id'] ), OBJECT ); 

    // echo '<pre>';
    // print_r($results);
    // echo '</pre>';


    $table_name = $this->replay_tbl;
    $replay_tbl = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE `support_id`=%d", $_GET['id'] ), OBJECT ); 

    // echo '<pre>';
    // print_r($replay_tbl);
    // echo '</pre>';
                             
?>

<style>
.mt-2{
    margin-top: 2rem;
}
.mb-2{
    margin-bottom: 2rem;
}
.mt-1{margin-top: 1rem;}
.mb-1{margin-bottom: 1rem;}
.successMsg {
    color: green;
}

</style>
<div id="wrap">
    <div id="welcome-panel" class="welcome-panel max-width-75 flot-left">
        <div class="welcome-panel-content">
            <h2>
                <?php 
                    echo sprintf('Details about "%s"', $results->subject );
                ?>
            </h2>
            <p class="about-description text-right">
                <?php if(isset($results->email) && !empty($results->email)): ?>
                    <span><small><i><?php echo sprintf('E-mail: %s,', $results->email); ?></i></small></span>
                    &nbsp;
                <?php endif; ?>
                
                <?php if(isset($results->name) && !empty($results->name)): ?>
                <span><small><i><?php echo sprintf('Name: %s,', $results->name); ?></i></small></span>
                &nbsp;
                <?php endif; ?>


                <!-- Phone Number -->
                <?php if(isset($results->phone_number) && !empty($results->phone_number)): ?>
                <span><small><i><?php echo sprintf('Phone: %s,', $results->phone_number); ?></i></small></span>
                &nbsp;
                <?php endif; ?>

                <span><small><i><?php echo sprintf('Date: %s', date('F Y d', strtotime($results->ticket_date))); ?></i></small></span>
            </p>
            <br><br>
            <div id="history" class="pt-3">
                <?php
                foreach ($replay_tbl as $single_reply){
                    $className = ( $single_reply->message_author == 'admin' ) ? 'mine' : 'theirs';
                    $time = strtotime( $single_reply->r_date );
                    ?>
                    <div class="<?php echo $className; ?>">
                        <p class="author_name mb-0"><i><?php echo $single_reply->message_author == 'admin' ? __('Support Agent', 'support-desk') : $results->name; ?></i></p>
                        <p class="msg-time"><?php echo date("M d, Y H:i A", $time ); ?></p>
                        <p class="owner-msg"><?php echo $single_reply->message; ?></p>
                    </div>
                <?php } ?>
            </div>
            <div class="welcome-panel-column-container mt-2 mb-2">
                <div id="post-body-content">
               <form id="replayForm" method="POST" action="">
                    <?php wp_nonce_field( 'support_form', 'support_form_nonce' ); ?>
                    <input type="hidden" name="nonce" value="<?php echo $results->nonce; ?>">
                    <input type="hidden" name="email" value="<?php echo $results->email; ?>">
                    <input type="hidden" name="name" value="<?php echo $results->name; ?>">
                    <input type="hidden" name="number" value="<?php echo $results->phone_number; ?>">
                    <input type="hidden" name="subject" value="<?php echo $results->subject; ?>">
                    <div class="form-group">
                        <label for="replay">
                        <?php _e('Replay', 'support-desk'); ?></label>
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
                            <?php if(isset($msg) && !empty($msg)): ?>
                            <div class="mt-1 alert alert-danger" role="alert">
                                <?php echo $msg; ?>
                            </div>
                            <?php endif; ?>
                    </div>
                    <div class="form-group mb-1 mt-1">
                        <label for="status"><?php _e('Status', 'support-desk'); ?></label>
                        <select name="status" id="status">
                            <?php foreach($this->support as $k => $single_s): 
                                $selected = ( $results->status == $k ) ? 'selected' : '';
                            ?>
                                <option <?php echo $selected; ?> value="<?php echo $k; ?>"><?php echo $single_s; ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="successMsg">
                                <?php  
                                    if($process){
                                        _e('Replay Successfully.', 'support-desk');
                                    }
                                ?>
                    </div>
                    <input type="submit" id="replaySubmitButton" name="replay" <?php echo ($results->status == 'close') ? 'disabled="disabled"' : ''; ?> class="button button-primary" value="<?php _e('Submit', 'support-desk'); ?>">
               </form>
               </div>
            </div>
        </div>
    </div>

    <!-- Note Section Start -->
        <div id="notes" class="max-width-22 flot-right">
            <div class="p-1">
                <h4 class="note-title"><?php echo sprintf('Notes'); ?></h4>
                <?php 
                
                $allNotes = $this->getAllNotes(); 
                if($allNotes): ?>

                 <ul class="mt-0 all-notes">
                 <?php foreach($allNotes as $note): ?>
                     <li><?php echo $note->note; ?></li>
                     
                 <?php endforeach; ?>
                 </ul>
                <?php endif; ?>
            </div>

            <!-- NOte Form -->
            <form action="" method="post">
                <textarea class="w-100" required name="note" id="note" cols="30" rows="5"></textarea>
                <input type="submit" class="button button-primary" name="note_submit" value="<?php _e('Submit', 'support-desk'); ?>">
            </form>
        </div>
    <!-- Note Section End  -->
</div>