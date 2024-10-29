<?php
function amylite_plans()
{
    global $wpdb;
    
    if(!empty($_REQUEST['add']))
    {        
        $wpdb->query($wpdb->prepare("INSERT INTO ".AMYLITE_PRICINGS." (name, description, code, price) 
        VALUES (%s,%s, %d, %d)",
            $_REQUEST['name'], $_REQUEST['description'], $_REQUEST['code'], $_REQUEST['price']));
    }
    
    if(!empty($_REQUEST['save']))
    {
        $wpdb->query($wpdb->prepare("UPDATE ".AMYLITE_PRICINGS." 
        SET name=%s, description=%s, code=%d, price=%d 
        WHERE id=%d", 
            $_REQUEST['name'], $_REQUEST['description'],
            $_REQUEST['code'], $_REQUEST['price'], $_REQUEST['id']));
    }
    
    if(!empty($_REQUEST['del']))
    {
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_PRICINGS." WHERE id=%d", $_REQUEST['id']));
        
        // delete ads assigned to the campaign
        $wpdb->query($wpdb->prepare("DELETE FROM ".AMYLITE_PRICING_ZONES." 
            WHERE pricing_id=%d", $_REQUEST['id']));
    }
    
    // select pricing plans
    $plans=$wpdb->get_results("SELECT * FROM ".AMYLITE_PRICINGS." ORDER BY name");
    
    ?>
    <div class="wrap">
        <h2><?php _e('Manage Pricing Plans', 'amylite')?></h2>
        
        <p><?php _e('The ad duration is important because it defines how long a campaign will be active once a package is activated for it.', 'watupro')?></p>
        <p><?php _e('For the moment you are expected to manually charge the advertisers. Stay tuned, we will be adding Paypal integration pretty soon.','amylite')?></p>
        
        <form method="post" onsubmit="return validatePlan(this);">
        <table class="widefat">
        <tr><th><?php _e('Plan Name', 'amylite')?></th><th><?php _e('Description (optional)', 'amylite')?></th><th><?php _e('Duration', 'amylite')?></th>
        <th>Cost</th><th>Create</th></tr>   
        <tr><td><input type="text" name="name"></td><td>
        <textarea name="description" rows="3" cols="30"></textarea></td>
        <td><input type="text" name="code" size="4"> days</td>
        <td>$<input type="text" name="price" size="4"></td>
        <td><input type="submit" value="Create New Plan"></td></tr>
        </table>
        <input type="hidden" name="add" value="1">
        </form>
        
        <?php foreach($plans as $plan):?>
            <form method="post" onsubmit="return validatePlan(this);">
            <table class="widefat">
            <tr><th>Plan Name</th><th>Description (optional)</th><th>Duration</th>
            <th>Cost</th><th>Save</th></tr>   
            <tr><td><input type="text" name="name" value="<?php echo stripslashes($plan->name);?>"></td><td>
            <textarea name="description" rows="3" cols="30"><?php echo stripslashes($plan->description);?></textarea></td>
            <td><input type="text" name="code" size="4" value="<?php echo $plan->code;?>"> days</td>
            <td>$<input type="text" name="price" size="4" value="<?php echo $plan->price;?>"></td>
            <td>
            <input type="submit" value="Save">
            <input type="button" value="Delete" onclick="if(confirm('Are you sure?')){ this.form.del.value=1; this.form.submit();}"></td></tr>
            </table>
            <input type="hidden" name="save" value="1">
            <input type="hidden" name="del" value="0">
            <input type="hidden" name="id" value="<?php echo $plan->id?>">
            </form>
        <?php endforeach;?>
    </div>
    
    <script type="text/javascript">
    function validatePlan(frm)
    {
        if(frm.name.value=="")
        {
            alert("Please enter plan name");
            frm.name.focus();
            return false;
        }
        
        if(frm.code.value=="" || isNaN(frm.code.value))
        {
            alert("Please enter plan duration in days (numbers only)");
            frm.code.focus();
            return false;
        }
        
        if(frm.price.value=="" || isNaN(frm.price.value))
        {
            alert("Please enter plan price, numbers only");
            frm.price.focus();
            return false;
        }
        
        return true;
    }
    </script>
    <?php   
}
?>