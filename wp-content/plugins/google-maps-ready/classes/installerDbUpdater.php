<?php
class installerDbUpdaterGmp {
	static public function runUpdate() {
		self::update_044();
		self::update_046();
		self::update_05();
	}
	
	public static function update_044(){
		dbGmp::query("Insert into `@__modules` (id, code, active,    
                    type_id, params, has_tab, label, description) VALUES
                  (NULL, 'promo_ready', 1, 1, '', 0, 'Promo Ready', 'Promo Ready')                  
		");
	}
	public static function update_046(){
		$query = "ALTER TABLE `@__icons` ADD column  `title` VARCHAR(100),
			ADD column `description` text;";
		dbGmp::query($query);
	}
	public static function update_05(){
		$query = "ALTER TABLE `@__markers` ADD column `params` text;";
		dbGmp::query($query);		
		
		$query = "insert into `@__options` (`code`,`value`,`label`) VALUES('save_statistic','0','Save Statistic')";
		dbGmp::query($query);

		$query = "insert into `@__options` (`code`,`value`,`label`) VALUES('infowindow_size','".
				utilsGmp::serialize(array('width'=>'100','height'=>'100'))."','Info Window Size')";
		dbGmp::query($query);
	}
        
		
}