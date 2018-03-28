<?php
	header('Content-Type: application/json');
	require_once "Dbconnect.php";
	//To store array of response;
	$response = array();
	

	if(isset($_GET['apicall'])){
		switch($_GET['apicall']){
			case 'signup':
				$response = get_signup($conn);
				$conn = NULL;
				break;
				
			case 'signin':
				$response = get_signin($conn);
				break;
				
			case 'getmarks':
				$response = get_marks($conn);
				break;
			case 'addmarks':
				$response = add_marks($conn);	
				break;
			default:
				$response['error'] = true;
				$response['message'] = 'wrong request.';
		}
	}else{
		$response['error'] = true;
		$response['message'] = "Invalid api call";
	}
    echo json_encode($response);  
	

	/**
		Check all parameter are set or not
	**/

	function is_all_param_set($params){
		if(count($params)==0)
			return false;
		foreach($params as $param){
			if(!isset($_POST[$param])){
				echo $param;
				return false;	
			}
		}
		return true;
	}


	/*
		signup function perform on *signup* request	
	*/
	
	function get_signup($conn){
		$cparam = array('username','userrollno','usersrno','userbranch','useryear','userpass');
		if(is_all_param_set($cparam)){
			$un = $_POST['username'];
			$usr = $_POST['usersrno'];
			$urn = $_POST['userrollno'];
			$ubr = $_POST['userbranch'];
			$uy = $_POST['useryear'];
			$up = $_POST['userpass'];
			$data = array("userrollno"=>$urn);
			if(!check_exist_user($data,$conn)){
				$sql = "insert into user_info values(?,?,?,?,?,?)";
				if($stmt = $conn->prepare($sql)){
					$stmt->bind_param("ssssss",$un,$urn,$usr,$ubr,$uy,$up);
					$stmt->execute();
					$response['error'] = false;
 					$response['message'] = $un." signup successful.";
				}else{
					$response['error'] = true;
 					$response['message'] = $conn->error;
				}
			}else{
				$response['error'] = true;
 				$response['message'] = 'User already registered.';
			}
		}else{
			$response['error'] = true;
			$response['message'] = "Required Parameter is not exist."; 
		}
		return $response;
	}

	/*
		function for sigin		
	*/

	function get_signin($conn){
		$cparam = array('userrollno','userpass');
		if(is_all_param_set($cparam)){
			$urn = $_POST['userrollno'];
			$up = $_POST['userpass'];
			$data = array("userrollno"=>$urn,"userpass"=>$up);
			if(check_exist_user($data,$conn)){
				$sql = "select username,userrollno,usersrno,userbranch,useryear from user_info where userrollno = 
				'".$urn."'";
				$result = $conn->query($sql);
				if($result->num_rows > 1){
					$response['error'] = true;
 					$response['message'] = "You have too many responses [ write to us about it ].";
				}else{
					$row = $result->fetch_assoc();					
					$response['error'] = false;
					$user = array(
						'username'=>$row['username'],
						'userrollno'=>$row['userrollno'],
						'usersrno'=>$row['usersrno'],
						'userbranch'=>$row['userbranch'],
						'useryear'=>$row['useryear']
						);
 					$response['message'] = "Successfully signedin.";
					$response['user'] = $user;
				}
			}else{
				$response['error'] = true;
 				$response['message'] = 'User Not registered.';
			}
		}else{
			$response['error'] = true;
			$response['message'] = "Required Parameter is not exist."; 
		}
		return $response;
	}

	/*
		check exist user in database or not
	*/
	function check_exist_user($data,$conn){
		$sql = "SELECT username FROM user_info WHERE ";
		$i = 0;
		foreach($data as $k=>$v){
			$sql = $sql."$k = '".$v."'";
			if($i<count($data)-1){
				$sql =$sql." AND "; 
				$i++;
			}
		}
		$result = $conn->query($sql);
		if($result->num_rows > 0){
 			return true;
 		}else{
			return false;
		}
	}
		
	function add_marks($conn){
		$response = array();
		if(count($_POST)==4){
			$repeated = 0;
			$inserted = 0;
			$subcodes = explode(",", $_POST['subcode']);
			$subexammarks = explode(",",$_POST['examination']);
			$subsessmarks = explode(",", $_POST['sessional']);
			echo count($subcodes);
			for($i=0;$i<count($subcodes);$i++){
				$sql = "select * from marks where sturollno='".$_POST['sturollno']."' and subcode='".$subcodes[$i]."'";
				$result = $conn->query($sql);
				if($result->num_rows == 0){
					$stmt = $conn->prepare('insert into marks values(?,?,?,?)');
					$stmt->bind_param("ssss",$_POST['sturollno'],$subcodes[$i],$subexammarks[$i],$subsessmarks[$i]);
					$stmt->execute();
					$inserted++;
				}else{
					continue;
					$repeated++;
				}
			}
			$response['error'] = false;
			$message = "";
			if($repeated!=0){
				$message = $repeated." marks are already added.";
			}
			$message = $message." ".$inserted." new marks are added.";
			$response['message'] = $message;
		}else{
			$response['error'] = true;
			$response['message'] = "Parameter error.";
		}
		return $response;
	}	
	function get_marks($conn){
		$response = array();
		if(isset($_POST["sturollno"])){
			$rollno = $_POST['sturollno'];
			$studentinfo = get_student_info($conn,$rollno);
			if(!$studentinfo["error"]){
				$response["error"] = false;
				$response["student"] = $studentinfo["studentinfo"];
				$sql = "select subject_code,subject_name,bryear,brsem,examination,sessional from subjectcode join marks on subjectcode.subject_code = marks.subcode WHERE marks.sturollno='".$rollno."'";
				if(isset($_POST['bryear']) && $_POST['bryear']!="all" && $_POST['bryear'] != "null"){
					$sql = $sql." and bryear = '".$_POST['bryear']."'";
				}
				if(isset($_POST['brsem']) && $_POST['brsem']!='all' && $_POST['brsem'] != "null"){
					$sql = $sql." and brsem = '".$_POST['brsem']."'";
				}
				$sqldata = array();
				$years = array();
				$result = $conn->query($sql);
                if($result->num_rows>0){
					while($row = $result->fetch_assoc()){
                        $marksobj = new stdClass($row["subject_name"],$row["subject_code"],$row["examination"],$row["sessional"]);
                        $marksobj->subject_name = $row["subject_name"];
                        $marksobj->subject_code = $row["subject_code"];
                        $marksobj->examination = $row["examination"];
                        $marksobj->sessional = $row["sessional"];
                        $marksobj->total = $row["examination"]+$row["sessional"];
                        $marksobj->backlog = checkBacklog(array("exam"=>$row["examination"],"sess"=>$row["sessional"],"sub"=>$row["subject_name"]));
                        $yearobj = new stdClass();
                        $yearobj->year = $row["bryear"];
                        $yearobj->yeartotal = 0;                        
                        $yearobj->semesters = array();
                        $semobj = new stdClass();
                        $semobj->semester = $row["brsem"];
                        $semobj->totalresults = 0;
                        $semobj->totalmarks = 0;
                        $semobj->carryoverpaper = 0;
                        $semobj->subjectmarks = array();
                        if(count($years)==0){                      
                            array_push($years,$yearobj);
                        }else{
                            $found = false;
                            for($i=0; $i<count($years);$i++){
                                $y = $years[$i];
                                if($y->year == $row["bryear"]){
                                    $yearobj = $y;
                                    $found = true;
                                    break;
                                }
                            }
                            if(!$found){
                                array_push($years,$yearobj);
                            }
                        }
                        $semesters = $yearobj->semesters;
                        $foundsem = false;
                        if(!empty($semesters)){
                            for($i=0;$i<count($semesters);$i++){
                                $s = $semesters[$i];
                                if($s->semester==$row["brsem"]){
                                    $semobj = $s;
                                    $foundsem = true;
                                    break;
                                }
                            }
                        }
                        if(!$foundsem){
                            array_push($yearobj->semesters,$semobj);
                        }
                        $semobj->totalresults++;
                        $semobj->carryoverpaper += ($marksobj->backlog)?1:0;
                        $semobj->totalmarks += $marksobj->total;
                        $yearobj->yeartotal += $marksobj->total;
                        array_push($semobj->subjectmarks,$marksobj);
                    }                  
                } 
                $response["marks"]=$years;
			}
		}else{
			$response['error']=true;
			$response['message']="There is incompleted data provided.";	
		}
		return $response;
	}

	function add_post_data($sql, $data){
		$i=0;
		foreach($data as $k=>$v){
			if($k != "sturollno" && $i<count($data)){
				$sql= $sql."$k='".$v."' ";
			}
			if($i<count($data)-1)
				$sql = $sql."AND ";
			$i++;
		}
		return trim($sql);
	}

	function checkBacklog($data){
		$obj = (object)$data;
		if(($obj->exam < 30 || $obj->sess <30) &&  ($obj->exam+$obj->sess)<60 && $obj->sub != "GENERAL PROFICIENCY"){
			return true;
		}
		return false;
	}
	function get_branch($conn,$rollno){
		$sql = "select userbranch from user_info where userrollno = '".$rollno."'";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			return $row['userbranch'];	
		}else{
			$response['error'] = true;
			$response['message'] = "Roll no is not found."; 
		}
	}


	function get_student_info($conn,$rollno){
		$sql = "select username,usersrno,userbranch,useryear from user_info where userrollno='".$rollno."'";
		$res = $conn->query($sql);
		$response = array();
		if($res->num_rows == 0){
			$response['error'] = true;
			$response['message'] = "Roll no not found.";
		}else if($res->num_rows == 1){
			$user = array();
			$response['error'] = false;
			$row = $res->fetch_assoc();
			$user["Name"] = $row["username"];
			$user["Branch"] = $row["userbranch"];
			$user["Srno"] = $row["usersrno"];
			$user["Admission Year"] = $row["useryear"];
			$response["studentinfo"]=$user;
		}else{
			$response["error"] = true;
			$response["message"] = "Something went wrong...";
		}
		return $response;
	}

?>