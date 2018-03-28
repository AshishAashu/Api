<?php
?>
<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
        $("document").ready(function(){
            $("#bryear").change(function(){
                x= $(this).val();
                if(x=='all'){
                    $("#brsemdiv").css("display","none");
                }else if(x=="null"){
                    $("#brsem").html('<option value="null">--Select Semester(Optional)--</option>');
                    $("#brsemdiv").css("display","");
                }
                else{
                    x=2*x-1;
                    con = '<option value="all">--All Semester--</option>';
                    for(i=0;i<2;i++){
                        con += "<option value='"+(x+i)+"'>Semester "+(x+i)+"</option>";
                    }
                    $("#brsem").html(con);
                    $("#brsemdiv").css("display","");
                }
            })
            $("#resultform").submit(function(e){
            	$("#contentdiv").html('<img src="wait.gif"/>');
            	$("#showresultbtn").attr("disabled",true);
                $("#contentdiv").css("display","");
                function userInfoData(data){
                    con = '<div class="panel-header table-responsive"><table class="table table-striped">';
                    $.each(data,function(key,val){
                        con += '<tr><td>'+key+'</td><td>'+val+'</td></tr>';
                    });
                    con +='<table><div>';
                    return con; 
                }
                function setSemester(data){
                    con ='<div class="table-responsive"><hr><h5 class="text-info">Semester:'+data.semester+'</h5><table class="table table-striped"><thead>'+
                        '</thead><tr><th>Subject Name</th><th>Subject Code</th><th>Examination</th><th>Sessional</th><th>Total Marks</th></thead><tbody>';
                    for(var i = 0;i<data.subjectmarks.length;i++){
                        sub = data.subjectmarks[i];
                        con+='<tr>';
                        $.each(sub,function(key,val){
                            if(key!="backlog")
                            con += '<td>'+val+'</td>';
                        });
                        con += '</tr>';
                    }   
                    con +='</tbody></table></div>';
                    con +='<div><hr><h4 class="text-success">Total Semester Marks:'+data.totalmarks+'</h4></div>'
                    return con;
                }
                function setMarks(data){
                    var con = "";
                    for(var i=0;i<data.length;i++){
                        yeardata = data[i];
                        con += '<div class="panel-body"><span>Marksheet Year :</span><span class="badge">'+yeardata.year+'</span>';
                        for(var j=0;j<yeardata.semesters.length;j++){

                            con += setSemester(yeardata.semesters[j]);
                        }  
                        //console.log(data.length);                      
                        con += '<h4 class="text-danger">Total Year Marks:'+yeardata.yeartotal+'</h4></div>';
                        //alert(yeardata.year);
                        //con += JSON.stringify(yeardata);
                    }
                    return con;                 
                }
                $.ajax({
                    url: "http://localhost/Api/api.php?apicall=getmarks",
                    type: "post",
                    data:  $("#resultform").serialize(),
                    success: function(res){
                        if(res.error){
                            $("#contentdiv").html("<h3>Oops...Something went wrong.</h3>");
                        }else{
                            content = "";
                            content += userInfoData(res.student);
                            content += setMarks(res.marks);
                            $("#contentdiv").html(content);                            
            				$("#showresultbtn").attr("disabled",false);
                        }
                    }
                });          
                e.preventDefault();
            })
        })
    </script>
</head>

<body>
    <header class="container-fluid" style="margin-bottom: 10px;">
        <div class="row">
            <div class="col-md-6">
                <img src="hbtu-logo.png" class="img-responsive" />
            </div>
            <div class="col-md-6">
                <h3 style="font-weight: bold;font-family: vardana;">MCA Result </h3>
            </div>
        </div>
    </header>
    <hr style="height: 1px;
color: red;
background-color: red;
border: none;">
    <div class="container-fluid"  style="margin : 0px 5px;">
        <div class="row">
            <div class="col-md-3 panel panel-default">
                <div class="panel-body">
                    <form id="resultform" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-12 text-info" for="rollno">Roll No.</label>
                            <div class="col-md-12">
                                <input type="text" name="sturollno" placeholder="Enter Your RollNo." maxlength="10" class=" form-control" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12 text-info" for="year">Year</label>
                            <div class="col-md-12">
                                <select name="bryear" id="bryear" class="form-control">
								<option value="null">--Select Year (Optional)--</option>
								<option value="all">All years</option>
								<option value="1">1st year</option>
								<option value="2">2nd year</option>
								<option value="3">3rd year</option>
				            </select>
                            </div>
                        </div>
                        <div class="form-group" id="brsemdiv">
                            <label class="col-md-12 text-info" for="year">Semester</label>
                            <div class="col-md-12">
                                <select name="brsem" id="brsem" class="form-control">
                                    <option value="null">--Select Semester(Optional)--</option>
                                    
                                </select>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-success btn-disabled" id="showresultbtn">Show Result</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-9 panel panel-success" id="contentdiv" style="display: none;">
                <!-- 
                    Result Shown Here
                -->
            </div>
        </div>
    </div>
</body>

</html>
