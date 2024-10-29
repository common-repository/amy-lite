<?php
function amylite_campaigns() {
    global $wpdb, $user_ID;
    
    $is_admin = current_user_can('manage_options');
    $userid_sql = $is_admin ? '' : $wpdb->prepare(" AND user_id=%d ", $user_ID);
    
    if(!empty($_REQUEST['add'])) {
			// find user
			if($is_admin and !empty($_POST['userlogin'])) {
				$advertiser = get_user_by('login', $_POST['userlogin']);
				if(empty($advertiser->ID)) wp_die(__('The user login does not exist. Each advertiser must be a registered user of your blog.','amylite'));
			}
			else $advertiser = get_user_by("id", $user_ID);	
    	        
        $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_CAMPAIGNS." (name, date, user_id) VALUES (%s,%s, %d)",
            $_POST['name'], date("Y-m-d"), $advertiser->ID));
    }
    
    if(!empty($_REQUEST['save'])) {
    	// find user
			if($is_admin and !empty($_POST['userlogin'])) {
				$advertiser = get_user_by('login', $_POST['userlogin']);
				if(empty($advertiser->ID)) wp_die(__('The user login does not exist. Each advertiser must be a registered user of your blog.','amylite'));
			}
			else $advertiser = get_user_by("id", $user_ID);	
			
        $wpdb->query($wpdb->prepare("UPDATE ".AMYLITE_CAMPAIGNS." SET name=%s, user_id=%d WHERE id=%d", 
            $_POST['name'], $advertiser->ID, $_POST['id']));
    }
    
    if(!empty($_REQUEST['del'])) {
			// select campaign		
			$campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".AMYLITE_CAMPAIGNS." 
			WHERE id=%d $userid_sql", $_REQUEST['id']));    	
    	
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_CAMPAIGNS." 
        	WHERE id=%d $userid_sql", $campaign->id));
        
        // delete ads assigned to the campaign
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_ADS." 
            WHERE id IN (SELECT ad_id FROM ".AMYLITE_ADS_CAMPAIGNS." 
            WHERE campaign_id=%d)", $campaign->id));
    }
    
    // select campaigns
    $campaigns=$wpdb->get_results("SELECT tC.*, tU.user_login as username 
	    FROM ".AMYLITE_CAMPAIGNS." tC JOIN {$wpdb->users} tU ON tC.user_id=tU.ID
	    $userid_sql ORDER BY name");
    ?>
    
    <div class="wrap">
    <h2><?php _e('Manage Advertising Campaigns', 'amylite')?></h2>
    
    <div class="widefat">
        <form method="post">
        <input type="hidden" name="add" value="1">        
        <label><?php _e('Campaign Name:', 'amylite')?> </label> <input type="text" name="name"> 
        <?php if($is_admin): _e('Advertiser (username):', 'amylite')?> 
        <input type="text" name="userlogin">
        <?php endif; ?>
        <input type="submit" name="add" value="<?php _e('Add Campaign', 'amylite')?>"> 
        </form>
        </div>
    
    <?php foreach($campaigns as $campaign):?>
        <div class="widefat">
        <form method="post">
        <input type="hidden" name="id" value="<?php echo $campaign->id?>">
        <input type="hidden" name="del" value="0">
        <label><?php _e('Campaign Name:', 'amylite')?> </label> <input type="text" name="name" value="<?php echo stripslashes($campaign->name)?>"> 
        
        <?php if($is_admin): _e('Advertiser (username):', 'amylite')?> 
        <input type="text" name="userlogin" value="<?php echo $campaign->username?>">
        <?php endif;?>
        [<a href="admin.php?page=<?php echo $is_admin ? 'amylite_packages' : 'amylite_my_packages'?>&id=<?php echo $campaign->id?>"><?php _e('Activate/Manage Packages')?></a>]
        <input type="submit" name="save" value="<?php _e('Save', 'amylite')?>"> <input type="button" value="<?php _e('Delete', 'amylite')?>" onclick="if(confirm('<?php _e('Are you sure?', 'amylite')?>')){ this.form.del.value=1; this.form.submit();}">
        </form>
        </div>
    <?php endforeach;?>
    <?php if($is_admin):?>
	    <p><?php _e("Campaigns are simply collections of ads. You can use them for example to differentiate the ads of different advertisers or different ad networks (Adsense, Chitika etc). If you don't need this, you can simply have one campaign for all ads.", 'amylite')?></p>
	    
	    <p><?php _e('Note that advertisers should be registered users in your site', 'amylite')?></p>
    <?php endif;?>
    </div>
    <?php
}
?>