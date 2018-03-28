<?php
	class SQLdata{

		private $subname,$subcode,$exam,$sess,$total,$yr,$sem;

		function __construct($sub_name,$sub_code,$exam,$sess,$y,$sem){
			//$this->year = $y;
			//$this->sem = $s;
			$this->subname = $sub_name;
			$this->subcode = $sub_code;
			$this->exam = $exam;
			$this->sess = $sess;
			$this->total = $exam+$sess;
			$this->yr = $y;
			$this->sem =$sem;
		}

		function get_all_data(){
			$marks = array();
			$marks["sub_name"] = $this->subname;
			$marks["sub_code"] = $this->subcode;
			$marks["exam"] = $this->exam;
			$marks["sess"] = $this->sess;
			$marks["total"] = $this->total;
			$marks["bryear"] = $this->yr;
			$marks["brsem"] = $this->sem;
			return $marks;
		}
		
	}

	
?>