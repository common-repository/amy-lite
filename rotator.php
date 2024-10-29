<?php
function amylite_show_zone($id) {
    global $wpdb;
    
    // find zone
    $zone=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".AMYLITE_ZONES." WHERE id=%d", $id));
    
    if(empty($zone->id)) return "Ad zone does not exist.";
    
    // find random ad in the zone
    $ad=$wpdb->get_row($wpdb->prepare("SELECT tA.* FROM ".AMYLITE_ADS." tA,
        ".AMYLITE_ADS_CAMPAIGNS." tC, ".AMYLITE_PACKAGES." tP
        WHERE tA.id=tC.ad_id AND tC.campaign_id=tP.campaign_id AND tP.zone_id=%d
        AND tP.date_end>CURDATE() AND tP.is_pending=0
        GROUP BY tA.id ORDER BY RAND()", $zone->id));
    if(empty($ad->id)) return "";    
        
    return stripslashes($ad->code);    
}