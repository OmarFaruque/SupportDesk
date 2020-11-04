<?php 
	$table_name = $this->option_tbl;
	$foler = (isset($_GET['spam']) && $_GET['spam'] == 'all') ? 0 : 1;
	$results = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE `folder`=%d", $foler ), OBJECT ); 

	$inbox = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT count(*) as total FROM {$table_name} WHERE `folder`=%d", 1 ), OBJECT ); 
	$spam = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT count(*) as total FROM {$table_name} WHERE `folder`=%d", 0 ), OBJECT ); 
	

?>	
	
	<div class="title"><h1>Support Desk List</h1></div>
	<ul class="subsubsub">
			<li class="inbox"><a href="<?php echo admin_url( 'admin.php?page=support_desk' ); ?>" class="<?php echo (!isset($_GET['spam'])) ? 'current' : ''; ?>">Inbox <span class="count">(<?php echo $inbox->total; ?>)</span></a> |</li>
			<li class="spam"><a class="<?php echo (isset($_GET['spam'])) ? 'current' : ''; ?>" href="<?php echo admin_url('admin.php?page=support_desk&spam=all') ?>">Spam <span class="count">(<?php echo $spam->total; ?>)</span></a></li>
		</ul>
	<div class="tablenav top">

				<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
<option value="-1">Bulk actions</option>
	<option value="delete">Delete</option>
</select>
<input type="submit" id="doaction" class="button action" value="Apply">

		</div>
			<div class="alignleft actions">
			<input type="submit" id="post-query-submit" class="button" value="Filter"><input type="submit" name="export" id="export" class="button" value="Export"></div>
			<div class="tablenav-pages one-page"><span class="displaying-num">2 items</span>
			<span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
			<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
			<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">1</span></span></span>
			<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
			<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
			<br class="clear">
 		</div>

<table class="wp-list-table widefat fixed striped table-view-list posts">
	<thead>
		<tr>
			<td id="cb" class="manage-column column-cb check-column">
				<input id="cb-select-all-1" type="checkbox">
				</td>
				<th scope="col" id="title" class="manage-column column-title column-primary sortable asc">
					<a href="#">
						<span>Subject</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" id="shortcode" class="manage-column column-shortcode">Email</th>
				<th scope="col" id="author" class="manage-column column-author sortable desc">
					<span>Name</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" id="date" class="manage-column column-date sortable desc">
				<a href="#">
					<span>Status</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</thead>

	<tbody id="the-list">

		 <?php   
	    foreach($results as $row){     
	    	$id=  $row->id;    
	    	$name=  $row->name;    
	    	$subject=  $row->subject; 
	    	$email=  $row->email;  
			//echo "<th>Email</th>" . "<td>" . $row->email . "</td>";
			   
			$table_name = $this->option_tbl;
			$all_data = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$table_name} WHERE `id`=%d", $id ), OBJECT ); 
		   
 	 	?> 
		<tr>
			<th scope="row" class="check-column"><input type="checkbox" name="" value="6"></th>
			<td class="title column-title column-primary">
				<strong>
				<a class="row-title" href="<?php echo admin_url('admin.php?page=support_desk&id='.$id) ?>"><?php echo $subject; ?></a></strong>
				<div class="row-actions">
					<span class="edit"><a href="<?php echo admin_url('admin.php?page=support_desk&id='.$id) ?>">Details</a> | </span>
					<span class="<?php echo (!isset($_GET['spam'])) ? 'spam' : ''; ?>"><a href="<?php 
						$action = (!isset($_GET['spam'])) ? 'spam' : 'not-spam';
						$action_close = ( $all_data->status == 'close' ) ? 'open' : 'close';
						echo admin_url('admin.php?page=support_desk&action='.$action.'&sid='.$id) ?>" aria-label="Move to spam folder"><?php echo (!isset($_GET['spam'])) ? __('Spam', 'support-desk') : __('Not Spam', 'support-desk'); ?></a> | </span>
					<span class="close-support  css<?php echo $action_close; ?>"><a href="<?php echo admin_url('admin.php?page=support_desk&action='.$action_close.'&sid='.$id) ?>" aria-label="Move to spam folder"><?php echo ucfirst($action_close); ?></a> </span>

				</div>

			</td>
			<td class="email_show" data-colname="Shortcode"><span class="shortcode"><?php echo $email; ?></span></td>
			<td class="author column-author" data-colname="Author"><?php echo $name; ?></td>
			<?php $status = ( $all_data->status == '' ) ? 'Not Yeat' : $all_data->status; ?>
			<td class="date column-date css<?php echo $status; ?>" data-colname="Date"><?php echo ucfirst($status); ?></td>
		</tr>	
	</tbody>


		<?php } ?>

	<tfoot>
	<tr>
		<td class="manage-column column-cb check-column">
			<label class="screen-reader-text" for="cb-select-all-2">Select All</label>
			<input id="cb-select-all-2" type="checkbox">
			</td>
			<th scope="col" class="manage-column column-title column-primary sortable asc">
				<a href="#">
					<span>Subject</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column column-shortcode">Email</th>
			<th scope="col" class="manage-column column-author sortable desc">
				<a href="#">
					<span>Name</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
			<th scope="col" class="manage-column column-date sortable desc">
				<a href="#">
					<span>Status</span>
					<span class="sorting-indicator"></span>
				</a>
			</th>
		</tr>
	</tfoot>

</table>