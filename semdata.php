<?php
 
    class SemData{
    
        private $sem,$totalresult=0,$semtotal=0,$backlog=0,$semmarks=array();
        function __construct($sem){
            $this->sem = $sem;
//            $this->totalresult = $tr;
//            $this->semtotal = $st;
//            $this->backlog = $back;
        }
        
        function getSem(){
            return $this->sem;
        }
        function addSemMarks($semmarks){
            $this->totalresult++;
            $this->semtotal = $this->semtotal+$semmarks->get_total();
            if($semmarks->get_backlog()){
                $this->backlog++;
            }
            array_push($this->semmarks,$semmarks);
        }
        
        function getSemData(){
            $sem = array();
            $sem["semester"] = $this->sem;
            $sem["semester_total"] = $this->semtotal;
            $sem["totalresult"]=$this->totalresult;
            $sem["backlog"]=$this->backlog;
            $sem["semmarks"]=$this->semmarks;
            return $sem;
        }
    }
?>