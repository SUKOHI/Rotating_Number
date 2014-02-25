<?php

/*  Dependency: pdo_manager.php  */

class Rotating_Number {
	
	const MODE_DB = 1;
	const MODE_FILE = 2;
	
	public function __construct($params=array()) {
		
		if(!empty($params)) {
			
			$this->setParams($params);
			
		}
		
	}
	
	public function getNumber($params) {
		
		if(is_array($params[self::MODE_DB])) {
			
			$mode_params = $params[self::MODE_DB];
			$pdo = $mode_params['pdo_manager'];
			$db_table = $mode_params['db'][0];
			$db_field = $mode_params['db'][1];
			$db_where = $mode_params['db'][2];
			$db_params = $mode_params['db'][3];
			$max_number = intval($mode_params['max_number']);
			
			$current_number = intval($pdo->selectOne($db_table, $db_field, $db_where, $db_params));
			$next_number = $this->getNextNumber($current_number, $max_number);
			
			$db_params = array_merge(array($next_number), $db_params);
			$pdo->updateDb($db_table, $db_field .'=?', $db_where, $db_params);
			
		} else if(is_array($params[self::MODE_FILE])) {
			
			$mode_params = $params[self::MODE_FILE];
			$file_path = $mode_params['file_path'];
			$max_number = intval($mode_params['max_number']);
			
			$current_number = intval(file_get_contents($file_path));
			$next_number = $this->getNextNumber($current_number, $max_number);
			
			file_put_contents($file_path, $next_number);
			
		}
		
		return $next_number;
		
	}
	
	private function getNextNumber($current_number, $max_number) {
		
		$current_number++;
		
		if($current_number > $max_number) {
			
			$current_number = 0;
			
		}
		
		return $current_number;
		
	}
	
}

/*** Sample

	echo $rotating_number = $rn->getNumber(array(
			
		Rotating_Number::MODE_DB => array(
		
			'pdo_manager' => $pdo_instance, 
			'db' => array('site_info', 'info_value', 'WHERE info_key = ?', array('rotating_number')), 
			'max_number' => 10
		
		)
			
	));
	
	// or

	echo $rotating_number = $rn->getNumber(array(
				
			Rotating_Number::MODE_FILE => array(
						
					'file_path' => '/cache/rotating_number.dat',
					'max_number' => 10
						
			)
				
	));

***/
