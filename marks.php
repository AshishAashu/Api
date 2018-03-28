<?php
	class Marks{
		private $subname,$subcode,$exam,$sess,$total,$backlog;

		function __construct($sub_name,$sub_code,$exam,$sess){
			$this->subname = $sub_name;
			$this->subcode = $sub_code;
			$this->exam = $exam;
			$this->sess = $sess;
			$this->total = $exam+$sess;
			$this->backlog = $this->check_backlog();
		}
		function get_total(){
			return $this->total;
		}

		function get_backlog(){
			return $this->backlog;
		}
		function get_marks(){
			$marks = array();
			$marks["sub_name"] = $this->subname;
			$marks["sub_code"] = $this->subcode;
			$marks["exam"] = $this->exam;
			$marks["sess"] = $this->sess;
			$marks["total"] = $this->total;
			$marks["backlog"] = $this->backlog;
			return $marks;
		}
		function check_backlog(){
			if(($this->exam < 30 || $this->sess < 30 || $this->total < 60 )&& $this->subname != "GENERAL PROFICIENCY"){
				return true;
			}
			return false;
		}
	}
?>