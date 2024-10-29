<?php
class AmyLiteShortcodes {
	static function show_zone($atts) {
		$zone_id = intval(@$atts[0]);
		if(empty($zone_id)) $zone_id = 0;
		return amylite_show_zone($zone_id);
	}
}