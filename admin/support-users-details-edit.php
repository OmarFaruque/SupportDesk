<?php 
/*
* Address book edit 
*/

$details = $this->singleUserDetails();
?>

<div class="support-users-details-edit">
    <div class="edit-inner">
        <a class="button button-primary align-items-center mb-3" href="<?php echo admin_url( 'admin.php?page=support_users_details' ); ?>">
            <span><<</span>
            <?php _e('Back', 'support-desk'); ?>
        </a>
        <h3><?php _e('Edit Contact', 'support-desk'); ?></h3>
        <div class="card-group">
            <div class="card w-100" style="max-width: unset;">
                <div class="card-body pl-0 pr-0">
                    <form class="form-inline" method="POST">
                        <div class="form-group w-100 mb-3">
                            <label class="col-md-2 col-xs-12 text-left justify-content-start"
                                for="name"><?php _e('Name', 'support-desk'); ?></label>
                            <input type="text" name="name" value="<?php echo $details->name; ?>" id="name"
                                class="form-control col-md-10 col-sm-10 col-xs-12"
                                placeholder="<?php _e('Name', 'support-desk'); ?>" aria-describedby="namehelp">
                            <small id="namehelp"
                                class="text-muted d-block offset-md-2"><?php _e('Full name', 'support-desk'); ?></small>
                        </div>

                        <!-- First Name -->
                        <div class="form-group w-100 mb-3">
                            <label class="col-md-2 col-xs-12 text-left justify-content-start"
                                for="first_name"><?php _e('First Name', 'support-desk'); ?></label>
                            <input type="text" name="first_name" id="first Name"
                                value="<?php echo $details->first_name; ?>"
                                class="form-control col-md-10 col-sm-10 col-xs-12"
                                placeholder="<?php _e('First Name', 'support-desk'); ?>" aria-describedby="fname">
                            <small id="fname"
                                class="text-muted d-block offset-md-2"><?php _e('First Name', 'support-desk'); ?></small>
                        </div>

                        <!-- Last Name  -->
                        <div class="form-group w-100 mb-3">
                            <label class="col-md-2 col-xs-12 text-left justify-content-start"
                                for="last_name"><?php _e('Last Name', 'support-desk'); ?></label>
                            <input type="text" name="last_name" id="last_name"
                                value="<?php echo $details->last_name; ?>"
                                class="form-control col-md-10 col-sm-10 col-xs-12"
                                placeholder="<?php _e('Last Name', 'support-desk'); ?>" aria-describedby="lname">
                            <small id="lname"
                                class="text-muted d-block offset-md-2"><?php _e('Last Name', 'support-desk'); ?></small>
                        </div>

                        <!-- Email Address  -->
                        <div class="form-group w-100 mb-3">
                            <label class="col-md-2 col-xs-12 text-left justify-content-start"
                                for="email"><?php _e('Email Address', 'support-desk'); ?></label>
                            <input type="email" name="email" value="<?php echo $details->email; ?>" id="email"
                                class="form-control col-md-10 col-sm-10 col-xs-12"
                                placeholder="<?php _e('yourmail@example.com', 'support-desk'); ?>"
                                aria-describedby="emailF">
                            <small id="emailF"
                                class="text-muted d-block offset-md-2"><?php _e('Email Address', 'support-desk'); ?></small>
                        </div>

                        <!-- Telephone  -->
                        <div class="form-group w-100">
                            <label class="col-md-2 col-xs-12 text-left justify-content-start"
                                for="phone"><?php _e('Phone', 'support-desk'); ?></label>
                            <input type="tel" name="phone" id="phone" value="<?php echo $details->phone_number; ?>"
                                class="form-control col-md-10 col-sm-10 col-xs-12"
                                placeholder="<?php _e('123456789', 'support-desk'); ?>" aria-describedby="phoneF">
                            <small id="phoneF"
                                class="text-muted d-block offset-md-2"><?php _e('Phone', 'support-desk'); ?></small>
                        </div>

                        <input type="hidden" name="exist_mail" value="<?php echo $details->email; ?>">
                        <div class="form-group">
                            <div class="col-md-12 text-right">
                                <input type="submit" name="user_details_update"
                                    value="<?php _e('Update', 'support-desk'); ?>" class="button button-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>