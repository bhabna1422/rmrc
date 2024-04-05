<?php
include("includes/connection.php");
error_reporting(1);
// error_reporting(1);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


if (isset($_SESSION['id'])):
    header("location:dashboard.php");
endif;
$act = $connect->real_escape_string($_GET['act']);
// print $_SESSION["vercode"];
if($_GET['act'] == 1) {
		
	$username = $connect->real_escape_string(trim($_POST['uname']));
    $password = $connect->real_escape_string(trim($_POST['pwd']));//base64_encode();
    $sql = db_query("select * from admin_login where uname='$username' && pwd='$password'");
    $res = mysqli_fetch_object($sql); 
	if (mysqli_num_rows($sql)) {
	
        if ($res->uname == $username && $connect->real_escape_string(trim($res->pwd)) == $password) {
			// echo "hi";
                $restime = mysqli_fetch_array(db_query("SELECT * FROM chk_session WHERE ip_address='" . $_SERVER['REMOTE_ADDR'] . "'"));
                $entertime = strtotime($restime['atime']);
                $now = strtotime(date('Y-m-d H:i:s'));
                $remaintime = round(abs($now - $entertime) / 60, 2);
				
                if ($restime['no_of_attempts'] > 4) {
					
                    if (($remaintime > 60)) {
                        db_query("DELETE FROM chk_session WHERE ip_address='" . $_SERVER['REMOTE_ADDR'] . "'");
                        $_SESSION['id'] = $res->uname;
                        if ($res->user_type == "admin") {
                            $_SESSION['admin_type'] = $res->user_type;
                            $_SESSION['admin_id'] = $res->id;
                            $_SESSION['admin_status'] = $res->admin_status;
                        } else {
                            $_SESSION['admin_type'] = $res->user_type;
                            $_SESSION['admin_id'] = $res->id;
                            $_SESSION['admin_status'] = $res->admin_status; 
                        }
                        //header("location:dashboard.php");
                        print "<script>";
                        print "self.location = 'dashboard.php';";
                        print "</script>";
                        exit;
                        exit;
                    } else {
                        $msg = "Please try after 60 mins";
                        print "<script>";
                        print "self.location = 'index.php?strmsg=$msg';";
                        print "</script>";
                    }

                } else {
					
                    db_query("DELETE FROM chk_session WHERE ip_address='" . $_SERVER['REMOTE_ADDR'] . "'");
                    $_SESSION['id'] = $res->uname;
                    if ($res->user_type == "admin") {
                        $_SESSION['admin_type'] = $res->user_type;
                        $_SESSION['admin_id'] = $res->id;
                        $_SESSION['admin_status'] = $res->admin_status;
                    } else {
                        $_SESSION['admin_type'] = $res->user_type;
                        $_SESSION['admin_id'] = $res->id;
                        $_SESSION['admin_status'] = $res->admin_status;                      

                    }
                    //header("location:dashboard.php");
                    print "<script>";
                    print "self.location = 'dashboard.php';";
                    print "</script>";
                    exit;
                }
            
        } else {
            $msg = "Invalid UserName/Password.";
            $extractip = db_query("select * from chk_session where ip_address='" . $_SERVER['REMOTE_ADDR'] . "'") or die("ERROR" . mysqli_error());
            $fetchip = mysqli_fetch_array($extractip);
            if ($fetchip['no_of_attempts'] != 5) {
                if (mysqli_num_rows($extractip) == 0) {
                    $insertchk = db_query("INSERT INTO chk_session SET no_of_attempts=1,ip_address='" . $_SERVER['REMOTE_ADDR'] . "'");
                } else {
                    $no_of_attempts = $fetchip['no_of_attempts'] + 1;
                    $updatechk = db_query("UPDATE chk_session SET atime=now(),no_of_attempts=" . $no_of_attempts . " WHERE ip_address='" . $_SERVER['REMOTE_ADDR'] . "'");
                }
                $msg = "Invalid UserName/Password, " . (5 - $fetchip['no_of_attempts']) . " attempt(s) left";

            } else {
                //$updatechk=db_query("UPDATE chk_session SET status=0 WHERE ip_address='".$_SERVER['REMOTE_ADDR']."'");
                $msg = "Please try after 60 mins";

            }
            print "<script>";
            print "self.location = 'index.php?strmsg=$msg';";
            print "</script>";
        }
		
    } else {
		$extractip = db_query("select * from chk_session where ip_address='" . $_SERVER['REMOTE_ADDR'] . "'") or die("ERROR" . mysqli_error());
		$fetchip = mysqli_fetch_array($extractip);
		if ($fetchip['no_of_attempts'] != 5) {
			if (mysqli_num_rows($extractip) == 0) {
				$insertchk = db_query("INSERT INTO chk_session SET no_of_attempts=1,ip_address='" . $_SERVER['REMOTE_ADDR'] . "'");
			} else {
				$no_of_attempts = $fetchip['no_of_attempts'] + 1;
				$updatechk = db_query("UPDATE chk_session SET atime=now(),no_of_attempts=" . $no_of_attempts . " WHERE ip_address='" . $_SERVER['REMOTE_ADDR'] . "'");
			}
			$msg = "Invalid UserName/Password, " . (4 - $fetchip['no_of_attempts']) . " attempt(s) left";
		} else {
			//$updatechk=db_query("UPDATE chk_session SET status=0 WHERE ip_address='".$_SERVER['REMOTE_ADDR']."'");
			$msg = "Please try after 60 mins";
		}
		//print_r($extractip);exit; 
		print "<script>";
		print "self.location = 'index.php?strmsg=$msg';";
		print "</script>";
	}   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include("css.php"); ?>
	<style>
		.login-wrapper .loginbox {border: 1px solid #1a4496;border-radius: 5px;-webkit-box-shadow: 0 10px 15px #1a4496;box-shadow: 0 10px 15px #1a4496;background: #fff;}
		.form-focus .form-control { background: #f5f6fa; border: 1px solid #1a4496;}
		.form-control:focus { border-color: #1a4496;}
	</style>
</head>
<body>

<div class="main-wrapper">
	<div class="header d-none">
		<ul class="nav nav-tabs user-menu">
			<li class="nav-item">
				<a href="#" id="dark-mode-toggle" class="dark-mode-toggle"><i class="feather-sun light-mode"></i><i class="feather-moon dark-mode"></i></a>
			</li>
		</ul>
	</div>
	<div class="row">
		<div class="col-md-6 login-bg">
			<div class="login-banner"></div>
		</div>

		<div class="col-md-6 login-wrap-bg">
			<div class="login-wrapper">
				<div class="loginbox">
					<div class="img-logo">
						<img src="assets/img/rmrc-logo.png" class="img-fluid" alt="Logo">
					</div>
					<?php if($_GET['strmsg'] == 2){  ?>
								<div class="col-lg-24">				
									<div class="alert alert-success">
										<a href="#" class="close" data-dismiss="alert" onClick="$('.alert').hide('slow');">&times;</a>
										<?php echo $smsg = "Please try after 60 mins. "; ?>
									</div>
								</div>
							<?php } else if(isset($_GET['strmsg'])){ ?>
								<div class="col-lg-24">				
									<div class="alert alert-success">
										<a href="#" class="close" data-dismiss="alert" onClick="$('.alert').hide('slow');">&times;</a>
										<?php echo $_GET['strmsg']; ?>
									</div> 
								</div>
							<?php } ?>
					<!--<h3>Aspire Hospital</h3>-->

					<p class="account-subtitle">login to your account to continue</p>
					<form action="index.php?act=1" method="post" name="loginform">
						<div class="form-group form-focus">
							<input type="text" class="form-control floating" name="uname">
							<label class="focus-label">Enter Username</label>
						</div>
						<div class="form-group form-focus">
							<input type="password" class="form-control floating" name="pwd">
							<label class="focus-label">Enter Password</label>
						</div>
						<!-- <div class="form-group">
							<div class="row">
								<div class="col-6">
									<label class="custom_check mr-2 mb-0 d-inline-flex"> Remember me
										<input type="checkbox" name="radio">
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="col-6 text-end">
									<a class="forgot-link" href="#nullforgot-password.html">Forgot Password ?</a>
								</div>
							</div>
						</div> -->
						<div class="d-grid">
						<button class="btn btn-login"  type="submit" name="submit">Login</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<?php include("js.php"); ?>
</body>
</html>