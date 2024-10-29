<?php
function amylite_packages() {
    global $wpdb, $user_ID;
    
 	$is_admin = current_user_can('manage_options');
 	
 	if($is_admin and !empty($_GET['activate'])) {
 		// activate pending package
 		$wpdb->query($wpdb->prepare("UPDATE ".AMYLITE_PACKAGES." SET is_pending=0
 			WHERE id=%d", $_GET['package_id']));
 		amylite_redirect("admin.php?page=amylite_packages&id=".$_GET['id']);	
 	}
    
    if(!empty($_REQUEST['add'])) {   
        // select pricing
        $pricing=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".AMYLITE_PRICINGS." WHERE id=%d",              
            $_REQUEST['pricing_id']));
            
        $is_pending = $is_admin ? 0 : 1;    
         
        $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_PACKAGES." 
        (campaign_id, zone_id, pricing_id, date_start, date_end, amount, is_pending) 
        VALUES (%d,%d, %d, %s, %s, %d, %d)",
            $_REQUEST['id'], $_REQUEST['zone_id'], $_REQUEST['pricing_id'], 
            date("Y-m-d"), date("Y-m-d",strtotime("+ ".$pricing->code." days")), 
            $pricing->price, $is_pending));        
    }
    
    if(!empty($_REQUEST['del'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_PACKAGES." WHERE id=%d", $_REQUEST['del_id']));        
    }
    
    // select campaign
    $campaign=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".AMYLITE_CAMPAIGNS." WHERE id=%d", 
        $_REQUEST['id']));
        
    // select pricing plans
    $pricings=$wpdb->get_results("SELECT * FROM ".AMYLITE_PRICINGS." ORDER BY name");
    
    // select ad zones
    $zones=$wpdb->get_results("SELECT * FROM ".AMYLITE_ZONES." ORDER BY name");
    
    // select packages    
    $packages=$wpdb->get_results("SELECT tPa.id as id, DATE_FORMAT(tPa.date_start, '%M %e, %Y') as date_start, DATE_FORMAT(tPa.date_end,'%M %e, %Y') as date_end, tP.name as pricing, tZ.name as zone, tPa.is_pending as is_pending 
    FROM ".AMYLITE_PRICINGS." tP, ".AMYLITE_ZONES." tZ, ".AMYLITE_PACKAGES." tPa 
    WHERE tPa.campaign_id='{$campaign->id}' AND tPa.pricing_id=tP.id AND tPa.zone_id=tZ.id
    ORDER BY tPa.date_end DESC, tPa.id DESC");    
    ?>
    
    <div class="wrap">
        <h2><?php _e('Manage Packages for Campaign', 'amylite')?> "<?php echo $campaign->name?>"</h2>
        
        <p><a href="admin.php?page=amylite_campaigns"><?php _e('Back to campaigns', 'amylite')?></a></p>
        
        <form method="post" onsubmit="return validateAd(this);">
        <table class="widefat">
        <tr><th><?php _e('Ad Zone', 'amylite')?></th><th><?php _e('Pricing Plan', 'amylite')?></th><th><?php _e('Activate', 'amylite')?></th></tr>   
        <tr><td><select name="zone_id">
        <?php foreach($zones as $zone):?>
        <option value="<?php echo $zone->id?>"><?php echo $zone->name?></option>
        <?php endforeach;?>
        </select></td>
        <td><select name="pricing_id">
        <?php foreach($pricings as $pricing):?>
        <option value="<?php echo $pricing->id?>"><?php echo $pricing->name?></option>
        <?php endforeach;?>
        </select></td>
        <td><input type="submit" value="<?php echo $is_admin ? _e('Activate Package', 'amylite') : __('Request activation', 'amylite');?>"></td></tr>
        </table>
        <input type="hidden" name="add" value="1">
        </form>
        
        <?php if(sizeof($packages)):?>
	        <h2><?php _e('Existing Packages', 'amylite')?></h2>
	        <?php foreach($packages as $package):?>
	            <form method="post" onsubmit="return validateAd(this);">
	            <table class="widefat">
	            <tr><td><?php echo $package->zone;?></td><td><?php echo $package->pricing;?></td>
	            <td><?php if(strtotime($package->date_end)>time()):?>
	            From <?php echo $package->date_start;?> to <?php echo $package->date_end;?>
	            <?php else:?>
	            <i>Expired (<?php echo $package->date_start;?> to <?php echo $package->date_end;?>)</i>
	            <?php endif;?></td>
	            <td><?php echo $package->is_pending ? __('Pending', 'amylite') : __('Active', 'amylite');
	            if($is_admin and $package->is_pending):?>
	            (<a href="#" onclick="window.location='admin.php?page=amylite_packages&activate=1&id=<?php echo $_GET['id']?>&package_id=<?php echo $package->id?>';return false;"><?php _e('Activate', 'amylite')?></a>)
	            <?php endif;?></td>
	            <td><?php if($is_admin):?><input type="button" value="<?php _e('Delete', 'amylite')?>" onclick="if(confirm('<?php _e('Are you sure?', 'amylite')?>')){ this.form.del.value=1; this.form.submit();}"></td><?php endif;?>	     </tr>       
	            </table>        
	            <input type="hidden" name="del" value="0">
	            <input type="hidden" name="del_id" value="<?php echo $package->id?>">
	            </form>
	        <?php endforeach;
	     endif;?>
    </div>
    <?php
}
?>