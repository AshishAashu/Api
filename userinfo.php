<?php
	class userinfo{

		private $username,$usersrno,$userrollno,$useryear;
		function __construct($name,$sr,$roll,$year){
			$this->username = $name;
			$this->usersrno = $sr;
			$this->userrollno = $roll;
			$this->useryear = $year;
		}

		function get_data(){
			$user = array();
			$user["name"] = $this->username;
			$user["srno"] = $this->usersrno;
			$user["rollno"] = $this->userrollno;
			$user["year"] = $this->useryear;
			return $user;
		}
	}
?>