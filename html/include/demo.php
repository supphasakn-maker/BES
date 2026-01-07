<?php

	class demo{
		private $aName = array(
			'Gold Dream Maker Company',
			'Rojer Company',
			'Nowhere Digital',
			'Maewnam Network Solutions'
		);
		
		private $aUser = array(
			'admin',
			'manager',
			'staff',
			'finance',
			'account',
			'sales',
			'executive'
		);
		
		private $aEmail = array(
			'todsaporn@maewnam.com',
			'apple@banana.com',
			'staff@catwil.com',
			'finance@gmail.com',
			'account@gmail.com',
			'sales@email.com',
			'executive@online.com'
		);
		
		
		
		function randomNumberSequence($requiredLength = 7, $highestDigit = 8) {
			$sequence = '';
			for ($i = 0; $i < $requiredLength; ++$i) {
				$sequence .= mt_rand(0, $highestDigit);
			}
			return $sequence;
		}
		
		function randomPhoneNumber(){
			$numberPrefixes = ['0812', '0813', '0814', '0815', '0816', '0817', '0818', '0819', '0909', '0908'];
			return $numberPrefixes[array_rand($numberPrefixes)].$this->randomNumberSequence();
		}

	
		
		function loop_table($settings,$total){
			for($i=0;$i<$total;$i++){
				echo '<tr>';
				foreach($settings as $item){
					switch($item['type']){
						case "number":
							if(isset($item['from'])){
								$number = rand($item['from'],$item['to']);
							}else{
								$number = $i+1;
							}
							$value = (isset($item['format'])?sprintf($item['format'],$number):$number);
							if(isset($item['number_format']))$value = number_format($value,$item['number_format']);
							
							break;
						case "date":
							$timestamp = time()+rand(-2592000,0);
							$value = (isset($item['format'])?date($item['format'],$timestamp):$timestamp);
							break;
						case "text":
							$value=$item['value'];
							break;
						case "phone":
							$numberPrefixes = ['0812', '0813', '0814', '0815', '0816', '0817', '0818', '0819', '0909', '0908'];
							$value= $numberPrefixes[array_rand($numberPrefixes)].$this->randomNumberSequence();
							break;
						case "phone":
							$numberPrefixes = ['02', '035', '034'];
							$value= $numberPrefixes[array_rand($numberPrefixes)].$this->randomNumberSequence();
							break;
						case "databank":
							switch($item['value']){
								case "name":$aList = $this->aName;break;
								case "user":$aList = $this->aUser;break;
								case "email":$aEmail = $this->aUser;break;
							}
							$rand = rand(0,count($aList)-1);
							$value = $aList[$rand];
							break;
					}
					if(isset($item['prefix']))$value = $item['prefix'].$value;
					if(isset($item['surffix']))$value = $value.$item['surffix'];
					echo '<td class="'.(isset($item['class'])?$item['class']:"").'">'.$value.'</td>';
					
				}
				echo '</tr>';
			}
		}
		
		
		
		/*
		protected $dbc = null;
		protected $auth = null;
		protected $param = null;
		
		function setDBC($dbc){
			$this->dbc = $dbc;
		}
		
		function setAuth($auth){
			$this->auth = $auth;
		}
		
		function setParam($param){
			$this->param = $param;
		}
		
		function Initial($dbc,$auth){
			$this->dbc = $dbc;
			$this->auth = $auth;
		}

		function __construct($dbc,$auth){
			$this->dbc = $dbc;
			$this->auth = $auth;
		}
		*/
		
		
	}
	
?>