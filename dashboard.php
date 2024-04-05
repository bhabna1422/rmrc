<?php
include("includes/connection.php");
// error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
// ini_set('display_errors', TRUE); // Error/Exception display, use FALSE only in production
// ini_set('log_errors', TRUE); // Error/Exception file logging engine.
// ini_set('error_log', 'errors.log');
if (!isset($_SESSION['id'])):
    header("location:index.php");
endif;

$page = "dashboard";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<?php include("css.php"); ?>
	
	<style>
		.page-wrapper > .content {  max-width: 100% !important;}
	</style>
</head>
<body>
<div class="main-wrapper">
	
<?php include("admin_header.php"); ?>
<?php include("left.php"); ?>
	


	<div class="page-wrapper">
		<div class="content container-fluid w-100">
			<div class="page-header">
				<div class="row">
					<div class="col">
						<h4 class="card-title">Tables</h4>
					</div>
					<div class="col-auto">
						<div class="bookingrange btn btn-white btn-sm" style="line-height: 18px; color: #131523; margin-left: 15px;">
						<div class="cal-ico">
							<i class="feather-calendar mr-1"></i>
							<span>Select Date</span>
						</div>
						<div class="ico">
							<i class="fas fa-chevron-left" style="padding: 10px 8px;"></i>
							<i class="fas fa-chevron-right" style="padding: 10px 8px;"></i>
						</div>
					</div>
					</div>
				</div>
			</div>
			<div class="row">
				
			</div>
			<div class="row">
				<div class="col">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon bg-primary">
									<i class="feather-user-plus"></i>
								</span>
								<div class="dash-count">
									<h5 class="dash-title">Cluster</h5>
									<div class="dash-counts">
									<p>4505</p>
									</div>
								</div>
							</div>
							<p class="trade-level mb-0"><span class="text-danger me-1"><i class="fas fa-caret-down me-1"></i>1.15%</span> last week</p>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon bg-primary">
									<i class="feather-user-plus"></i>
								</span>
								<div class="dash-count">
									<h5 class="dash-title">Household</h5>
									<div class="dash-counts">
									<p>4505</p>
									</div>
								</div>
							</div>
							<p class="trade-level mb-0"><span class="text-danger me-1"><i class="fas fa-caret-down me-1"></i>1.15%</span> last week</p>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon bg-primary">
									<i class="feather-user-plus"></i>
								</span>
								<div class="dash-count">
									<h5 class="dash-title">Individual</h5>
									<div class="dash-counts">
									<p>4505</p>
									</div>
								</div>
							</div>
							<p class="trade-level mb-0"><span class="text-danger me-1"><i class="fas fa-caret-down me-1"></i>1.15%</span> last week</p>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon bg-primary">
								<i class="feather-user-plus"></i>
								</span>
								<div class="dash-count">
									<h5 class="dash-title">Biometrics</h5>
									<div class="dash-counts">
									<p>4505</p>
									</div>
								</div>
							</div>
							<p class="trade-level mb-0"><span class="text-danger me-1"><i class="fas fa-caret-down me-1"></i>1.15%</span> last week</p>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card">
						<div class="card-body">
							<div class="dash-widget-header">
								<span class="dash-widget-icon bg-primary">
									<i class="feather-user-plus"></i>
								</span>
								<div class="dash-count">
									<h5 class="dash-title">Tribe</h5>
									<div class="dash-counts">
									<p>4505</p>
									</div>
								</div>
							</div>
							<p class="trade-level mb-0"><span class="text-danger me-1"><i class="fas fa-caret-down me-1"></i>1.15%</span> last week</p>
						</div>
					</div>
				</div>
				
			</div>			
		</div>
	</div>	
</div>	
	
	

	
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/datatables.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/js/moment.min.js"></script>
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="assets/js/script.js"></script>
<script>
	$(document).ready(function() {
		$('.select_one').select2();
	});
	
</script>
</body>
</html>