<?php
function amylite_zones()
{
    global $wpdb;
     if(!empty($_REQUEST['add']))
    {        
        $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_ZONES." (name, description, width, height) 
        VALUES (%s,%s, %d, %d)",
            $_REQUEST['name'], $_REQUEST['description'], $_REQUEST['width'], $_REQUEST['height']));
            
        $id=$wpdb->insert_id;
        
        if(!empty($_POST['plan_ids']))
        {
            foreach($_POST['plan_ids'] as $plan)
            {
                $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_PRICING_ZONES." (pricing_id, zone_id)
                 VALUES (%d, %d)", $plan, $id));
            }   
        }        
    }
    
    if(!empty($_REQUEST['save']))
    {
        $wpdb->query($wpdb->prepare("UPDATE ".AMYLITE_ZONES." 
        SET name=%s, description=%s, width=%d, height=%d 
        WHERE id=%d", 
            $_REQUEST['name'], $_REQUEST['description'],
            $_REQUEST['width'], $_REQUEST['height'], $_REQUEST['id']));
            
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_PRICING_ZONES." 
            WHERE zone_id=%d", $_REQUEST['id']));    
            
        if(!empty($_POST['plan_ids']))
        {
            foreach($_POST['plan_ids'] as $plan)
            {
                $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_PRICING_ZONES." (pricing_id, zone_id)
                 VALUES (%d, %d)", $plan, $_REQUEST['id']));
            }   
        }        
    }
    
    if(!empty($_REQUEST['del']))
    {
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_ZONES." WHERE id=%d", $_REQUEST['id']));
              
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_PRICING_ZONES." 
            WHERE zone_id=%d", $_REQUEST['id']));
    }
    
    // select zones
    $zones=$wpdb->get_results("SELECT * FROM ".AMYLITE_ZONES." ORDER BY name");    
    
    // select pricing plans assigned to each zone
    foreach($zones as $cnt=>$zone)
    {
        $plans=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".AMYLITE_PRICING_ZONES." 
            WHERE zone_id=%d ORDER BY id", $zone->id));
        $plan_ids=array();       
        foreach($plans as $plan) $plan_ids[]=$plan->pricing_id;         
        $zones[$cnt]->plans=$plan_ids;
    }    
    // slect all pricing plans
    $plans=$wpdb->get_results("SELECT * FROM ".AMYLITE_PRICINGS." ORDER BY name");
    
    ?>
    <div class="wrap">
        <h2><?php _e('Manage Advertising Zones', 'amylite')?></h2>
        
        <p><?php _e('Note the <b>shortcode</b> in each ad zone. You need to insert it in the place where you want the ads from the zone to appear. In case you are inserting it in the theme file, you have to use the do_shortcode() call.', 'amylite')?></p>
        
        <form method="post" onsubmit="return validateZone(this);">
        <table class="widefat">
        <tr><th><?php _e('Zone Name', 'amylite')?></th><th><?php _e('Description (optional)', 'amylite')?></th><th><?php _e('Width', 'amylite')?></th>
        <th><?php _e('Height', 'amylite')?></th><th><?php _e('Applicable Plans', 'amylite')?></th><th><?php _e('Create', 'amylite')?></th></tr>   
        <tr><td><input type="text" name="name"></td><td>
        <textarea name="description" rows="3" cols="30"></textarea></td>
        <td><input type="text" name="width" size="4"> px</td>
        <td><input type="text" name="height" size="4"> px</td>
        <td><?php foreach($plans as $plan):?>
        <input type="checkbox" name="plan_ids[]" value="<?php echo $plan->id?>"> <?php echo $plan->name;?><br />
        <?php endforeach;?></td>
        <td><input type="submit" value="<?php _e('Create Ad Zone', 'amylite')?>"></td></tr>
        </table>
        <input type="hidden" name="add" value="1">
        </form>
        
        <?php foreach($zones as $zone):?>
           <form method="post" onsubmit="return validateZone(this);">
        <table class="widefat">
        <tr><th><?php _e('Zone Name', 'amylite')?></th><th><?php _e('Description (optional)', 'amylite')?></th><th><?php _e('Width', 'amylite')?></th>
        <th><?php _e('Height', 'amylite')?></th><th><?php _e('Applicable Plans', 'amylite')?></th><th><?php _e('Save', 'amylite')?></th></tr>   
        <tr><td><input type="text" name="name" value="<?php echo stripslashes($zone->name)?>"></td><td>
        <textarea name="description" rows="3" cols="30"><?php echo stripslashes($zone->description)?></textarea></td>
        <td><input type="text" name="width" size="4" value="<?php echo $zone->width;?>"> px </td>
        <td><input type="text" name="height" size="4" value="<?php echo $zone->height;?>"> px</td>
        <td><?php foreach($plans as $plan):?>
        <input type="checkbox" name="plan_ids[]" value="<?php echo $plan->id?>" <?php if(in_array($plan->id, $zone->plans)) echo "checked='true'"?>> <?php echo $plan->name;?><br />
        <?php endforeach;?></td>
        <td>
            <input type="submit" value="Save">
            <input type="button" value="Delete" onclick="if(confirm('Are you sure?')){ this.form.del.value=1; this.form.submit();}"></td></tr>
            <tr><td colspan="5"><?php _e('Shortcode:', 'amylite')?> <input type="text" value="[amylite-zone <?php echo $zone->id?>]" onclick="this.select();" size="20" readonly="true"></b></td></tr>
            </table>
            <input type="hidden" name="save" value="1">
            <input type="hidden" name="del" value="0">
            <input type="hidden" name="id" value="<?php echo $zone->id?>">       
        </form>
        <?php endforeach;?>
    </div>
    
    <script type="text/javascript">
    function validateZone(frm)
    {
        if(frm.name.value=="")
        {
            alert("Please enter plan name");
            frm.name.focus();
            return false;
        }
        
        if(frm.width.value=="" || isNaN(frm.width.value))
        {
            alert("Please enter zone width (numbers only)");
            frm.width.focus();
            return false;
        }
        
        if(frm.height.value=="" || isNaN(frm.height.value))
        {
            alert("Please enter zone height, numbers only");
            frm.height.focus();
            return false;
        }
        
        return true;
    }
    </script>
    <?php
}
?>