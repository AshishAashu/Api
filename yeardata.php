<?php

    class YearData{
    
        private $year,$current_back=0,$yeartotal=0,$sems=array();
        function __construct($year){
            $this->year = $year;
//            $this->current_back = $cb;
//            $this->yeartotal = $yt;
        }
        
        function getYear(){
            return $this->year;
        }
        
        
        function getSems(){
            return $this->sems;
        }
        function addSem($sem){
            array_push($this->sems,$sem);
        }
        
        function getYearData(){
            $year = array();
            $year["year"] = $this->year;
            $year["current_back"] = $this->current_back;
            $year["yeartotal"] = $this>yeartotal;
            $year["sems"] = $this->sems;
            return $year;
        }
    }
?>
