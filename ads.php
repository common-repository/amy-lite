<?php
function amylite_ads() {
    global $wpdb, $user_ID;
	 $is_admin = current_user_can('manage_options');
    $userid_sql = $is_admin ? '' : $wpdb->prepare(" WHERE user_id=%d ", $user_ID);
    
    if(!empty($_REQUEST['add'])) {  
		 if(!$is_admin) {
		 	// make sure I work with my campaign
		 	$exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".AMYLITE_CAMPAIGNS."
		 		WHERE id=%d AND user_id=%d", $_POST['campaign_id'], $user_ID));
		 	if(empty($exists)) wp_die(__('You can only add ads to your own campaigns', 'amylite'));	
		 }    
          
        $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_ADS." (code, label) VALUES (%s,%s)",
            $_POST['code'], $_POST['label']));
        $id=$wpdb->insert_id;
        
        // insert campaign
        $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_ADS_CAMPAIGNS." (ad_id, campaign_id) 
            VALUES (%d,%d)",$id, $_POST['campaign_id']));            
    }
    
    if(!empty($_REQUEST['save'])) {
		  if(!$is_admin) {
		 	// make sure I work with my campaign
		 	$exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".AMYLITE_CAMPAIGNS."
		 		WHERE id=%d AND user_id=%d", $_POST['campaign_id'], $user_ID));
		 	if(empty($exists)) wp_die(__('You can only edit ads to your own campaigns', 'amylite'));	
		 }  	    	
    	
        $wpdb->query($wpdb->prepare("UPDATE ".AMYLITE_ADS." SET code=%s, label=%s WHERE id=%d", 
            $_POST['code'], $_POST['label'], $_POST['id']));
            
        $wpdb->query($wpdb->prepare("UPDATE ".AMYLITE_ADS_CAMPAIGNS." SET campaign_id=%d 
        WHERE ad_id=%d", 
            $_POST['campaign_id'], $_POST['id']));    
    }
    
    if(!empty($_REQUEST['del'])) {
		  if(!$is_admin) {
		 	// make sure I work with my campaign
		 	$campaign_id = $wpdb->get_var($wpdb->prepare("SELECT campaign_id 
		 		FROM ".AMYLITE_ADS_CAMPAIGNS." WHERE ad_id=%d", $_POST['id']));
		 	$campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".AMYLITE_CAMPAIGNS."
		 		WHERE id=%d", $campaign_id));	
		 	if($campaign->user_id != $user_ID) wp_die(__('You can only delete your own ads', 'amylite'));	
		 }  	    	
    	
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_ADS." WHERE id=%d", $_POST['id']));
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_ADS_CAMPAIGNS." 
            WHERE ad_id=%d", $_POST['id']));
    }
    
    // select campaigns
    $campaigns = $wpdb->get_results("SELECT * FROM ".AMYLITE_CAMPAIGNS." 
    	$userid_sql ORDER BY name");
    $cids = array(0);
    foreach($campaigns as $campaign) $cids[] = $campaign->id;
    $cid_sql = implode(",", $cids);	
    $campaign_id_sql = $is_admin ? '' : " AND tAC.campaign_id IN ($cid_sql) ";
        
    // select ads
    $ads=$wpdb->get_results("SELECT tA.*, tAC.campaign_id as campaign_id 
        FROM ".AMYLITE_ADS." tA, ".AMYLITE_ADS_CAMPAIGNS." tAC 
        WHERE tA.id=tAC.ad_id $campaign_id_sql
        GROUP BY tA.id ORDER BY tA.label");
    ?>
    
    <div class="wrap">
        <h2><?php _e('Manage Ads', 'amylite')?></h2>
        
        <p>You can create any number of ads and assign them to a given campaign. One ad will be displayed at a time.</p>
        
                
        <form method="post" onsubmit="return validateAd(this);">
        <table class="widefat">
        <tr><th>Label</th><th>Ad Code</th><th>Campaign</th><th>Create</th></tr>   
        <tr><td><input type="text" name="label"></td><td>
        <textarea name="code" rows="4" cols="50"></textarea></td><td><select name="campaign_id">
        <?php foreach($campaigns as $campaign):?>
        <option value="<?php echo $campaign->id?>"><?php echo stripcslashes($campaign->name)?></option>
        <?php endforeach;?>
        </select></td>
        <td><input type="submit" value="Create New Ad"></td></tr>
        </table>
        <input type="hidden" name="add" value="1">
        </form>
        
        <?php foreach($ads as $ad):?>
        <form method="post" onsubmit="return validateAd(this);">
        <table class="widefat">
        <tr><th>Label</th><th>Ad Code</th><th>Campaign</th><th>Save</th></tr>   
        <tr><td><input type="text" name="label" value="<?php echo $ad->label?>"></td><td>
        <textarea name="code" rows="4" cols="50"><?php echo stripslashes($ad->code)?></textarea></td><td><select name="campaign_id">
        <?php foreach($campaigns as $campaign):
        if($ad->campaign_id==$campaign->id) $selected=' selected';
        else $selected='';?>
        <option value="<?php echo $campaign->id?>"<?php echo $selected?>><?php echo stripcslashes($campaign->name)?></option>
        <?php endforeach;?>
        </select></td>
        <td>
        <input type="submit" value="Save">
        <input type="button" value="Delete" onclick="if(confirm('Are you sure?')){ this.form.del.value=1; this.form.submit();}"></td></tr>
        </table>
        <input type="hidden" name="save" value="1">
        <input type="hidden" name="del" value="0">
        <input type="hidden" name="id" value="<?php echo $ad->id?>">
        </form>
        <?php endforeach;?>
    </div>
    
    <script type="text/javascript">
    function validateAd(frm)
    {
        if(frm.label.value=="")
        {
            alert("Please enter ad label, it's for your own convenience");
            frm.label.focus();
            return false;
        }
        
        if(frm.code.value=="")
        {
            alert("Please enter ad HTML or Javascript code");
            frm.code.focus();
            return false;
        }
        
        if(frm.campaign_id.value=="")
        {
            alert("Please select ad campaign to assign the advertising to it");
            frm.campaign_id.focus();
            return false;
        }
        
        return true;
    }
    </script>
    <?php
}
?>