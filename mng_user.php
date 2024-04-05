<?php
include("includes/connection.php");

if (!isset($_SESSION['id'])):
    header("location:index.php");
endif;

$page = "mng_user";
$stage = $connect->real_escape_string($_POST['stage']);

$user_type 	= $connect->real_escape_string($_POST['user_type']);
$fullname 	= $connect->real_escape_string($_POST['fullname']);
$email 	= $connect->real_escape_string($_POST['email']);
$phone 		= $connect->real_escape_string($_POST['phone']);
$username 	= $connect->real_escape_string($_POST['username']);
$password 	= $connect->real_escape_string($_POST['password']);
$status		= $connect->real_escape_string($_POST['status']);
$password 	= encryptIt($password);

if ($_POST['stage']){

        if($stage == 2){
            db_query("INSERT INTO `mng_user` set fullname= '".$fullname."',email= '".$email."', phone= '".$phone."',username= '".$username."', password= '".$password."', user_type= '".$user_type."', status='".$status."'");
			
            $msg = "Record Added Successfully";
            setcookie("msg", $msg, time() + 3);
            header("location:mng_user.php?success");
    		exit();
        }
        else{
            $editid		= decryptIt($_POST['editid']);
            // echo "UPDATE `mng_user` set fullname= '".$fullname."', phone= '".$phone."',password= '".$password."', user_type= '".$user_type."', status='".$status."' WHERE id='".$editid."'";
            // die();
            db_query("UPDATE `mng_user` set fullname= '".$fullname."',email= '".$email."', phone= '".$phone."',password= '".$password."', user_type= '".$user_type."', status='".$status."' WHERE id='".$editid."'");
            
            $msg = "Record Updated Successfully.";
            setcookie("msg", $msg, time() + 3);
            header("Location: mng_user.php?success");
            exit();
        }
        
}

$delid = $connect->real_escape_string($_GET['delid']);
if ($delid != ""){
    $delid = decryptIt($_GET['delid']); 
    db_query("DELETE FROM `mng_user` WHERE id='".$delid."'");
    
    $msg = "Record Deleted Successfully.";
    setcookie("msg", $msg, time() + 3);
    header("Location: mng_user.php");
    exit();
}


if(isset($_GET['get_id'])){
	$getid	= decryptIt($_GET['get_id']);
	
	$sql 	= db_query("SELECT * FROM `mng_user` where id='".$getid."'");
	$row	= mysqli_fetch_array($sql);
	
	$get_user_type 	    = $row['user_type'];
	$get_fullname 	    = $row['fullname'];
    $get_email 	    = $row['email'];
	$get_phone 		    = $row['phone'];
	$get_username 	    = $row['username'];
	$get_password 	    = decryptIt($row['password']);
	$get_status 	    = $row['status'];
	
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include("css.php"); ?>
</head>
<body>
<div class="main-wrapper">
<?php include("admin_header.php"); ?>
<?php include("left.php"); ?>

	<div class="page-wrapper">
		<div class="content container-fluid">
			<div class="page-header">
				<div class="row">
					<div class="col">
						<h4 class="card-title">Manage User</h4>
					</div>
				</div>
			</div>			
			<div class="row">
				<div class="col-sm-12">					
					<div class="card">
						
						<div class="card-body">
						    <form action="" method="POST" onsubmit="return validate()">
                                <?php
                                if (isset($_GET['get_id'])) {
                                    ?>
                                    <input type="hidden" name="stage" value="3">
                                    <input type="hidden" name="editid" value="<?php echo $_GET['get_id']; ?>">
                                <?php } else { ?>
                                    <input type="hidden" name="stage" value="2">
                                <?php } ?>
                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Select User Type</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="form-select" name="user_type" id="user_type" required>
                                            <option value="">Select Usertype</option>
                                            <option value="2" <?php if(isset($get_user_type) && $get_user_type == "2"){ echo "selected";}?>>Field Investigator</option>
                                            <option value="3" <?php if(isset($get_user_type) && $get_user_type == "3"){ echo "selected";}?>>Technical</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Enter Full Name</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="fullname" id="fullname" autocomplete="off" value="<?php if(isset($get_fullname)){ echo $get_fullname; } ?>" required />
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Enter Email</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" name="email" id="email" autocomplete="off" value="<?php if(isset($get_email)){ echo $get_email; } ?>" required />
                                    </div>
                                </div>
                                
                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Enter Phone Number</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="phone" id="phone" autocomplete="off" value="<?php if(isset($get_phone)){ echo $get_phone; } ?>" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" maxlength="15" />
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Enter User Name</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="username" id="username" autocomplete="off" value="<?php if(isset($get_username)){ echo $get_username; } ?>" <?php if(isset($get_username)){ echo "readonly"; } else { echo "required";} ?> />
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Enter Password</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="password" id="password" autocomplete="off" value="<?php if(isset($get_password)){ echo $get_password; } ?>" required />
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Enter Confirm Password</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="cpassword" id="cpassword" autocomplete="off" value="<?php if(isset($get_password)){ echo $get_password; } ?>" required />
                                    </div>
                                </div>

								<div class="row form-group">
                                    <div class="col-sm-4">
                                        <label>Select User Status</label>
                                    </div>
                                    <div class="col-sm-8">
										<select class="form-select" name="status" id="status" required>
                                            <option value="1" <?php if(isset($get_status) && $get_status == "1"){ echo "selected";}?>>Active</option>
											<option value="0" <?php if(isset($get_status) && $get_status == "0"){ echo "selected";}?>>Inactive</option>
										</select>
                                    </div>
                                </div>
                                    
                                <div class="row form-group">
                                    <div class="col-sm-4"> </div>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-secondary">Submit</button>
                                    </div>
                                </div>
                            </form>
						</div>
					</div>
				</div>
			</div>	
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <div id="data-tables_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6"></div>
                                    <div class="col-sm-12 col-md-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="datatable table table-borderless hover-table dataTable no-footer" id="data-tables" role="grid">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
        											<th>Username</th>
                                                    <th>Name</th>
                                                    <th>User Type</th>
                                                    <th>Phone</th>
        											<th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
        										$c = 0;
        										$sql  = db_query("SELECT * FROM `mng_user` order by username;");
        
        										while($row = mysqli_fetch_array($sql)){
        										$c++;
        										if($row['user_type'] == "2"){
        										    $user_type = "Field Investigator";
        										}
        										else{
        											$user_type =  "Technical";
        										}
        										if($row['status'] == "1"){
        										    $status = "Active";
        										}
        										else{
        											$status =  "Inactive";
        										}
        										?>
                                                <tr>
                                                    <td><?php echo $c; ?></td>
        											<td><?php echo $row['username'];?></td>
        											<td><?php echo $row['fullname'];?></td>
                                                    <td><?php echo $user_type; ?></td>
                                                    <td><?php echo $row['phone'];?></td>
        											<td><?php echo $status;?></td>
                                                    <td>
                                                        <a href="?get_id=<?php echo encryptIt($row['id']); ?>"><i class="far fa-edit"></i></a> |
                                                        <a href="?delid=<?php echo encryptIt($row['id']); ?>" onclick="return confirm('Are you sure to delete?');" ><i class="far fa-trash-alt" id="delbtn"></i></a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-sm-12 mt-4"><div id="tablepagination" class="dataTables_wrapper"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>		
		</div>	
	</div>	
</div>	
	
	

	
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/typeahead.bundle.js"></script>
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/datatables.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="assets/plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
	$(document).ready(function() {
		$('.select_one').select2();
	});
	
</script>
<script>
    function validate() {
        var password = $("#password").val();
        var cpassword = $("#cpassword").val();
        if (password != cpassword) {
            alert("Confirm password mismatched!");
            $("#cpassword").val("");
            return false;
        }
    }
        
    $(document).ready(function() {
        $('.select_one').select2();
    });
    $(document).ready(function(){
        $('input[type="radio"]').click(function(){
            var inputValue = $(this).attr("value");
            var targetHiddenfield = $("." + inputValue);
            $(".hiddenfield").not(targetHiddenfield).hide();
            $(targetHiddenfield).show();
        });
    });

</script>
<script>
$(document).ready(function(){
    // Sonstructs the suggestion engine
    var countries = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        // The url points to a json file that contains an array of country names
        prefetch: 'assets/countries.json'
    });
    
    // Initializing the typeahead with remote dataset
    $('.myAutocomplete').typeahead(null, {
        name: 'countries',
        source: countries,
        limit: 5 /* Specify maximum number of suggestions to be displayed */
    });
});  
</script>
<script type="text/javascript">
	$(function () {
		$('#datetimepicker1').datetimepicker({
			 format: 'DD-MM-YYYY',
			 //maxDate: moment().startOf('day')
		  })/*.on( "dp.change", function() {
					// Fired when the date is changed.
					var dt = $("#datetimepicker1").val();
					var given = moment(dt, "DD/MM/YYYY");
					var current = moment().startOf('day');

					//Difference in number of days
					var diff= moment.duration(current.diff(given)).asDays();
					//alert(diff);
					
					var start = moment();
					var end = moment().add(diff, 'days');

					var years = end.diff(start, 'year');
					start.add(years, 'years');

					var months = end.diff(start, 'months');
					start.add(months, 'months');

					var days = end.diff(start, 'days');
                    
                    $("#years").val(years);
                    $("#months").val(months);
                    $("#days").val(days);
					console.log(years + ' years ' + months + ' months ' + days + ' days');
					
				});    	*/
	});	
	
</script>
</body>
</html>