<?php
include('lcheck.php');
//FOR INCLUDE THE COMMON FILE

$sqlismainaccessusers=$con->prepare("SELECT * FROM pairmainaccess WHERE userid=? AND grouptype='Reports' ORDER BY id ASC");
$sqlismainaccessusers->bind_param("s", $userid);
$sqlismainaccessusers->execute();
$sqlismainaccessuser = $sqlismainaccessusers->get_result();
$infomainaccessuser=$sqlismainaccessuser->fetch_array();
$sqlismainaccessuser->close();
$sqlismainaccessusers->close();
//FOR REPORT MODULE INFORMATIONS AND PREFERENCES

$sqlismainaccessuserinvs=$con->prepare("SELECT * FROM pairmainaccess WHERE franchiseid=? AND moduletype='Invoices' ORDER BY id ASC");
$sqlismainaccessuserinvs->bind_param("s", $_SESSION['franchisesession']);
$sqlismainaccessuserinvs->execute();
$sqlismainaccessuserinvoice = $sqlismainaccessuserinvs->get_result();
$infomainaccessuserinvoice=$sqlismainaccessuserinvoice->fetch_array();
$sqlismainaccessuserinvoice->close();
$sqlismainaccessuserinvs->close();
//FOR INVOICE MODULE INFORMATIONS AND PREFERENCES

$sqlismainaccessuserbills=$con->prepare("SELECT * FROM pairmainaccess WHERE franchiseid=? AND moduletype='Bills' ORDER BY id ASC");
$sqlismainaccessuserbills->bind_param("s", $_SESSION['franchisesession']);
$sqlismainaccessuserbills->execute();
$sqlismainaccessuserbill = $sqlismainaccessuserbills->get_result();
$infomainaccessuserbill=$sqlismainaccessuserbill->fetch_array();
$sqlismainaccessuserbill->close();
$sqlismainaccessuserbills->close();
//FOR BILL MODULE INFORMATIONS AND PREFERENCES

if((($franchisesrole==''))||((($infomainaccessuser['createdid']=='0')&&($infomainaccessuser['groupaccess']=='0'))||((($infomainaccessuser['createdid']!='0'))&&(($infomainaccessuser['groupaccess']=='0')||($infomainaccessuser['useraccessview']==0))))) {
  header('Location:dashboard.php');
}
//FOR CHECK THE THIS FILES ACCESSES ARE ALLOW OR NOT

$sqlbranchs=$con->prepare("SELECT * FROM pairfranchises WHERE id=?");
$sqlbranchs->bind_param("i", $_SESSION['franchisesession']);
$sqlbranchs->execute();
$sqlbranch = $sqlbranchs->get_result();
$branch=$sqlbranch->fetch_array();
$sqlbranch->close();
$sqlbranchs->close();
//FOR INVOICE MODULE INFORMATIONS AND PREFERENCES

if (isset($_POST['submitcustomizes'])) {
	$dateformatforurlredirection = 'Y-m-d';
	$from = htmlspecialchars($_POST['from'], ENT_QUOTES, 'UTF-8');
	$to = htmlspecialchars($_POST['to'], ENT_QUOTES, 'UTF-8');
	$reportperiod = htmlspecialchars($_POST['reportperiod'], ENT_QUOTES, 'UTF-8');
	$printtype = htmlspecialchars($_POST['printtype'], ENT_QUOTES, 'UTF-8');

	$sqlreportmoduless=$con->prepare("SELECT * FROM pairreportmodules WHERE types='stockandsales' ORDER BY id ASC");
	$sqlreportmoduless->execute();
	$sqlreportmodules = $sqlreportmoduless->get_result();
	while($inforeportmodules=$sqlreportmodules->fetch_array()){
		$ansmodules = $inforeportmodules[2];
		$newmodules = explode(',',$ansmodules);
	}
	$sqlreportmodules->close();
	$sqlreportmoduless->close();
	//FOR REPORT INFORMATIONS LOOPING UPDATIONS AND INSERTIONS

	$modcolumncolchanges='';
	foreach ($newmodules as $newmoduleskey) {
		$coltypemod = preg_replace('/\s+/', '',$newmoduleskey);
		$modcolumncol=$coltypemod."stockandsales";
		$modcolumncol=mysqli_real_escape_string($con, (isset($_POST[$modcolumncol]))?$newmoduleskey:' ');
		if($modcolumncolchanges!=''){
			$modcolumncolchanges.=','.$modcolumncol;
		}
		else{
			$modcolumncolchanges.=$modcolumncol;
		}
	}

	$sqlupreport = $con->prepare("UPDATE pairreports SET rowcolumns=? , printtype=? WHERE franchiseid=? AND createdid=? AND types='stockandsales'");
	$sqlupreport->bind_param("ssss", $modcolumncolchanges, $printtype, $_SESSION['franchisesession'], $companymainid);
	$sqlupreport->execute();
	$sqlupreport->close();

	$filtername = htmlspecialchars(((isset($_POST['filtername']))?'1':'0'), ENT_QUOTES, 'UTF-8');
	$companyname = htmlspecialchars(((isset($_POST['companyname']))?'1':'0'), ENT_QUOTES, 'UTF-8');
	$dateprepare = htmlspecialchars(((isset($_POST['dateprepare']))?'1':'0'), ENT_QUOTES, 'UTF-8');
	$timeprepare = htmlspecialchars(((isset($_POST['timeprepare']))?'1':'0'), ENT_QUOTES, 'UTF-8');
	$gstrule = htmlspecialchars(((isset($_POST['gstrule']))?'1':'0'), ENT_QUOTES, 'UTF-8');
	$commision = htmlspecialchars($_POST['commision'], ENT_QUOTES, 'UTF-8');
	$category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
	$categoryname = '';

	if($category!='all'){
		$sqlicatnames=$con->prepare("SELECT category FROM paircategory WHERE id=? ORDER BY category ASC");
		$sqlicatnames->bind_param("i", $category);
		$sqlicatnames->execute();
		$sqlicatname = $sqlicatnames->get_result();
		$infocatname=$sqlicatname->fetch_array();
		$sqlicatname->close();
		$categoryname = $infocatname['category'];
	}

	$prefix = "CONCAT(SUBSTRING_INDEX(reporturl, '&period=', 1),'&period=')";
	$newReportUrl = $prefix . $reportperiod;

	$sqlreportfav = $con->prepare("UPDATE pairreportfavourites SET reporturl=? WHERE franchisesession=? AND createdid=? AND reportnames='stockandsales'");
	$sqlreportfav->bind_param("sss", $newReportUrl, $_SESSION['franchisesession'], $companymainid);
	$sqlreportfav->execute();
	$sqlreportfav->close();

	$sqlreport = $con->prepare("UPDATE pairreports SET commision=?,category=?,categoryname=?,reportperiod=?,filtername=?,companyname=?,dateprepare=?,timeprepare=?,gstrule=? WHERE franchiseid=? AND createdid=? AND types='stockandsales'");
	$sqlreport->bind_param("issssssssss", $commision, $category, $categoryname, $reportperiod, $filtername, $companyname, $dateprepare, $timeprepare, $gstrule, $_SESSION['franchisesession'], $companymainid);
	$sqlreport->execute();
	$sqlreport->close();

	header("Location:reportstockandsales.php?datefrom=".date($dateformatforurlredirection,strtotime($from))."&dateto=".date($dateformatforurlredirection,strtotime($to))."&commision=".$commision."&category=".$category."");
}

$sqlreportviews=$con->prepare("SELECT * FROM pairreports WHERE franchiseid=? AND createdid=? AND types='stockandsales'");
$sqlreportviews->bind_param("ss", $_SESSION['franchisesession'], $companymainid);
$sqlreportviews->execute();
$sqlreportview = $sqlreportviews->get_result();
$sqlviewreport=$sqlreportview->fetch_array();
$sqlreportview->close();
$sqlreportviews->close();
//FOR THIS REPORT ROWS AND COLUMNS ON/OFF PREFERENCES

$anscheck = $sqlviewreport['rowcolumns'];
$newanscheck = explode(',',$anscheck);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="shortcut icon" href="favicon.ico">
	<!-- FontAwesome JS-->
	<script defer src="assets/plugins/fontawesome/js/all.min.js"></script>
	<!-- App CSS -->  
	<link id="theme-style" rel="stylesheet" href="assets/css/portal.css">
	<script type="text/javascript" src="https://unpkg.com/react@18/umd/react.development.js"></script>
	<script type="text/javascript" src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
	<script type="text/javascript" src="https://unpkg.com/dayjs@1/dayjs.min.js"></script>
	<script type="text/javascript" src="https://unpkg.com/antd@5.11.0/dist/antd-with-locales.js"></script>
	<script type="text/javascript" src="https://unpkg.com/@ant-design/icons/dist/index.umd.js"></script>
	<script type="text/javascript" src="https://unpkg.com/react-router-dom/dist/umd/react-router-dom.production.min.js"></script>
	<script type="text/javascript" src="https://unpkg.com/react-router/dist/umd/react-router.production.min.js"></script>
<?php
	include('externals.php');
?>
	<link rel="stylesheet" href="pdf/examples/libs/pure-min.css">
	<link rel="stylesheet" href="pdf/examples/libs/grids-responsive-min.css">
	<title>
		<?= $infomainaccessuser['groupname']; ?> View
	</title>
	<style>
	.modal-header{
		background: #F1F2F6 !important;
		font-size: 13px !important;
		font-weight: normal !important;
	}
	h5{
		font-weight: normal !important;
	}
	input:focus{
		outline: none !important;
		box-shadow: none !important;
		border: none !important;
	}
	@media screen and (max-width: 666px){
		.mobreswords{
			display: none !important;
		}
	}
	</style>
	<link href="CSS/Master.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body class="g-sidenav-show" style="background-color:#F1F2F6">
	<?php
		// sidebar
		include('sidebar.php');
	?>
	<main class="main-content position-relative max-height-vh-100 h-100 mt-0 " style="overflow-y: scroll !important;">
	<?php 
		// navbar
		include('navhead.php');
	?>
		<div class="container-fluid py-4 bg-body">
		<?php
			// notifications
			if(isset($_GET['remarks'])){
		?>
			<div class="alert alert-dismissible" style="position: relative;top: 50px;z-index: 1999;height: 10px;background-color: #53b05a !important;margin-top: -50px;border-radius: 0px !important;">
				<button type="button" class="btn-close" data-bs-dismiss="alert" style="z-index: 900000;color: white;top: -11px;background-image: white !important;"></button>
				<p style="position: relative;top: -10px;color: white !important;background-color: #53b05a !important;">
					<i class="fa fa-check"></i> &nbsp;<?=$_GET['remarks']?>
				</p>
			</div>
		<?php
			}
			if(isset($_GET['error'])){
		?>
			<div class="alert alert-dismissible" style="position: relative;top: 50px;z-index: 1999;height: 10px;background-color: #d64830 !important;margin-top: -50px;border-radius: 0px !important;">
				<button type="button" class="btn-close" data-bs-dismiss="alert" style="z-index: 900000;color: white;top: -11px;background-image: white !important;"></button>
				<p style="position: relative;top: -10px;color: white !important;background-color: #d64830 !important;">
					<i class="fa fa-times"></i> &nbsp;<?=$_GET['error']?>
				</p>
			</div>
		<?php
			}
			$sqlismainaccessusers=$con->prepare("SELECT * FROM pairmainaccess WHERE userid=? AND grouptype='Reports' ORDER BY id ASC");
			$sqlismainaccessusers->bind_param("s", $userid);
			$sqlismainaccessusers->execute();
			$sqlismainaccessuser = $sqlismainaccessusers->get_result();
			$infomainaccessuser=$sqlismainaccessuser->fetch_array();
			$sqlismainaccessuser->close();
			$sqlismainaccessusers->close();
			//FOR REPORT MODULE INFORMATIONS AND PREFERENCES
		?>
			<div style="max-width: 1650px;">
				<div class="row min-height-480">
					<div class="col-12">
						<div class="card mb-4 mt-5">
							<div class="card-body p-3" style="color:black;font-family:'Myriad Set Pro','Helvetica Neue',Helvetica,Arial,sans-serif;">
								<form action="" method="post" enctype="multipart/form-data" class="form-horizontal mt-0" role="form">
									<div class="row">
										<div class="col-lg-6">
											<p class="mb-3" style="color:black;font-size: 20px;margin-top: -8px;">
												<a href="reports.php" style="color: #1878F1">
													<i class="fa fa-pie-chart" aria-hidden="true"></i>
													<?= $infomainaccessuser['groupname']; ?>
												</a> &gt;
												Stock And Sales Statement
											</p>
										</div>
										<div class="col-lg-6">
											<span style="float:right">
												<a style="margin:4.5px 4.5px !important;" id="calendars" class="btn btn-primary btn-sm btn-custom-grey" onclick="calendars()">
													<i class="fa-solid fa-calendar-days"></i>
													<span class="mobreswords "> Custom</span>
												</a>
												<a style="margin:4.5px 4.5px !important;" id="customizes" data-bs-toggle="modal" data-bs-target="#Customizesmodal" class="btn btn-primary btn-sm btn-custom-grey">
													<i class="fa fa-sliders" aria-hidden="true"></i>
													<span class="mobreswords "> Customize Report</span>
												</a>
												<!-- Customization modal start -->
												<div class="modal fade" id="Customizesmodal" tabindex="-1" role="dialog">
													<div class="modal-dialog modal-lg" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="exampleModalLabel" style="font-weight: normal;">
																	Customize Report
																</h5>
																<span type="button" onclick="funescustomizes()" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true" id="procloseicon">&times;</span>
																</span>
															</div>
															<form action="" method="post" enctype="multipart/form-data" class="form-horizontal mt-0" role="form">
																<div class="modal-body mbsub">
																	<div class="accordion" id="accordionRental">
																		<div class="accordion-item mb-1">
																			<h5 class="accordion-header" id="customizegeneral">
																				<button class="accordion-button font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#customizegenerals" aria-expanded="true" aria-controls="customizegenerals">
																					<div class="customcont-header ml-0 mb-1">
																						<a class="customcont-heading">General</a>
																					</div>
																				</button>
																			</h5>
																			<div id="customizegenerals" class="accordion-collapse collapse show" aria-labelledby="customizegeneral">
																				<div class="accordion-body text-sm">
																					<div class="row justify-content-center">
																						<div class="col-lg-8">
																							<p style="font-size:16px !important;margin-bottom: 6px !important;">
																								Report Period
																							</p>
																							<div class="row">
																								<div class="col-lg-5 mb-1 automodalchecking">
																									<select class="form-control form-control-sm select4" id="reportperiod" name="reportperiod" onchange="reportperiodfun(this.value)">
																										<option <?= ($sqlviewreport['reportperiod']=='lastmonth')?'selected':''?> value="lastmonth">
																											Last Month
																										</option>
																									</select>
																								</div>
																								<div class="col-lg-3 mb-1">
																									<input type="text" class="form-control form-control-sm" readonly style="background-color: #e9ecef;" id="from" name="from">
																								</div>
																								<div class="col-lg-1 m-0 p-0" style="text-align:center;">
																									To
																								</div>
																								<div class="col-lg-3 mb-1">
																									<input type="text" class="form-control form-control-sm" readonly style="background-color: #e9ecef;" id="to" name="to">
																								</div>
																							</div>
																							<div class="row mb-3" style="padding-top: 5px;padding-bottom: 0px;margin-bottom: 0px;">
																								<div class="col-lg-5">
																									<label class="custom-label mt-2">
																										Print Type
																									</label>
																								</div>
																								<div class="col-lg-7">
																									<div class="row">
																										<div class="col-sm-6">
																											<div class="custom-control custom-radio mr-sm-2">
																												<input type="radio" class="custom-control-input" name="printtype" id="print" value="1" <?= ($sqlviewreport['printtype']=='1')?'checked':'' ?>>
																												<label class="custom-control-label custom-label" for="print">
																													Print
																												</label>
																											</div>
																										</div>
																										<div class="col-sm-6">
																											<div class="custom-control custom-radio mr-sm-2">
																												<input type="radio" class="custom-control-input" name="printtype" id="windowprint" value="0" <?= ($sqlviewreport['printtype']=='0')?'checked':'' ?>>
																												<label class="custom-control-label custom-label" for="windowprint">
																													Window Print
																												</label>
																											</div>
																										</div>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="accordion" id="accordionRental">
																		<div class="accordion-item mb-1">
																			<h5 class="accordion-header" id="customizerowcolumn">
																				<button class="accordion-button font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#customizerowcolumns" aria-expanded="true" aria-controls="customizerowcolumns">
																					<div class="customcont-header ml-0 mb-1">
																						<a class="customcont-heading">Rows / Columns</a>
																					</div>
																				</button>
																			</h5>
																			<div id="customizerowcolumns" class="accordion-collapse collapse show" aria-labelledby="customizerowcolumn">
																				<div class="accordion-body text-sm">
																					<div class="row justify-content-center">
																						<div class="col-lg-8">
																							<p style="font-size:16px !important;margin-bottom: -3px !important;">
																								Select and Reorder Columns
																							</p>
																						<?php
																							$newans=array();
																							$sqlreportaccesss=$con->prepare("SELECT * FROM pairreports WHERE franchiseid=? AND createdid=? AND types='stockandsales'");
																							$sqlreportaccesss->bind_param("ss", $_SESSION['franchisesession'], $companymainid);
																							$sqlreportaccesss->execute();
																							$sqlreportaccess = $sqlreportaccesss->get_result();
																							while($inforeportaccess=$sqlreportaccess->fetch_array()){
																								$ans = $inforeportaccess['rowcolumns'];
																								$newans = explode(',',$ans);
																							}
																							$sqlreportaccess->close();
																							$sqlreportaccesss->close();
																							$newmodules=array();
																							$sqlreportmoduless=$con->prepare("SELECT * FROM pairreportmodules WHERE types='stockandsales' ORDER BY id ASC");
																							$sqlreportmoduless->execute();
																							$sqlreportmodules = $sqlreportmoduless->get_result();
																							while($inforeportmodules=$sqlreportmodules->fetch_array()){
																								$ansmodules = $inforeportmodules[2];
																								$newmodules = explode(',',$ansmodules);
																							}
																							$sqlreportmodules->close();
																							$sqlreportmoduless->close();
																							foreach ($newmodules as $newmoduleskey) {
																								$coltypemod = preg_replace('/\s+/', '',$newmoduleskey);
																						?>
																							<div class="row" style="padding-left: 0px !important;">
																								<div class="col-lg-6 my-1" style="padding-left: 0px !important;">
																									<div class="custom-control custom-checkbox mr-sm-2" style="padding: 0px 12px !important;">
																										<div class="input-group input-group-sm" id="flaghead">
																											<div class="input-group-prepend">
																												<span class="input-group-text" style="height:28px !important;border: 0px !important;padding-left: 0px !important;padding-right: 33px !important;">
																													<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" viewBox="0 0 512 512" xml:space="preserve" class="icon icon-drag align-text-bottom">
																														<circle cx="153.6" cy="451" r="61"></circle><circle cx="153.6" cy="256" r="61"></circle><circle cx="153.6" cy="61" r="61"></circle><circle cx="358.4" cy="256" r="61"></circle><circle cx="358.4" cy="61" r="61"></circle><circle cx="358.4" cy="451" r="61"></circle>
																													</svg>
																												</span>
																											</div>
																											<input type="checkbox" class="custom-control-input" name="<?= $coltypemod; ?>stockandsales" id="<?= $coltypemod; ?>stockandsales" checked disabled>
																											<label class="custom-control-label custom-label" for="<?= $coltypemod; ?>stockandsales">
																												<?= (($newmoduleskey=='Sno')?'(#) Row Number':(str_replace(" or ", " / ",$newmoduleskey))) ?>
																											</label>
																										</div>
																									</div>                  
																								</div>
																							</div>
																						<?php
																							}
																						?>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="accordion" id="accordionRental">
																		<div class="accordion-item mb-1">
																			<h5 class="accordion-header" id="filter">
																				<button class="accordion-button font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#filters" aria-expanded="true" aria-controls="filters">
																					<div class="customcont-header ml-0 mb-1">
																						<a class="customcont-heading">
																							Filter
																						</a>
																					</div>
																				</button>
																			</h5>
																			<div id="filters" class="accordion-collapse collapse show" aria-labelledby="filter">
																				<div class="accordion-body text-sm">
																					<div class="row justify-content-center">
																						<div class="col-lg-8">
																							<div class="row mb-3">
																								<div class="col-lg-6 my-1">
																									<div class="custom-control custom-checkbox mr-sm-2">
																										<input type="checkbox" class="custom-control-input" name="filtecategory" id="filtecategory" checked disabled>
																										<label class="custom-control-label custom-label" for="filtecategory">
																											<?=$access['txtnamecategory']?>
																										</label>
																									</div>
																								</div>
																								<div class="col-lg-6 automodalchecking">
																									<select class="form-control form-control-sm select4 reportcatsearch" id="categorys" name="category">
																									<?php
																										if ($sqlviewreport['category']=='all') {
																									?>
																										<option value="all" data-foo="" data-receivable="" selected>
																											All
																										</option>
																									<?php
																										}
																										else{
																											$sqlicat = mysqli_query($con, "SELECT id,category From paircategory WHERE id='".$sqlviewreport['category']."' order by category asc");
																											while ($infocat = mysqli_fetch_array($sqlicat)){
																									?>
																										<option value="<?=$infocat['id']?>" <?=($sqlviewreport['category']==$infocat['id'])?'selected':''?>>
																											<?=$infocat['category']?>
																										</option>
																									<?php
																											}
																										}
																									?>
																									</select>
																								</div>
																							</div>
																							<div class="row mb-3">
																								<div class="col-lg-6 my-1">
																									<div class="custom-control custom-checkbox mr-sm-2">
																										<input type="checkbox" class="custom-control-input" name="filterselect" id="filterselect" checked disabled>
																										<label class="custom-control-label custom-label" for="filterselect">
																											Select
																										</label>
																									</div>
																								</div>
																								<div class="col-lg-6 automodalchecking">
																									<select class="form-control form-control-sm select4" id="commision" name="commision">
																										<option value="0" <?=($sqlviewreport['commision']=='0')?'selected':''?>>
																											Actual
																										</option>
																										<option value="5" <?=($sqlviewreport['commision']=='5')?'selected':''?>>
																											5%
																										</option>
																										<option value="10" <?=($sqlviewreport['commision']=='10')?'selected':''?>>
																											10%
																										</option>
																										<option value="15" <?=($sqlviewreport['commision']=='15')?'selected':''?>>
																											15%
																										</option>
																										<option value="20" <?=($sqlviewreport['commision']=='20')?'selected':''?>>
																											20%
																										</option>
																										<option value="25" <?=($sqlviewreport['commision']=='25')?'selected':''?>>
																											25%
																										</option>
																										<option value="30" <?=($sqlviewreport['commision']=='30')?'selected':''?>>
																											30%
																										</option>
																									</select>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="accordion" id="accordionRental">
																		<div class="accordion-item mb-1">
																			<h5 class="accordion-header" id="headerorfooter">
																				<button class="accordion-button font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#headerorfooters" aria-expanded="true" aria-controls="headerorfooters">
																					<div class="customcont-header ml-0 mb-1">
																						<a class="customcont-heading">
																							Header / Footer
																						</a>
																					</div>
																				</button>
																			</h5>
																			<div id="headerorfooters" class="accordion-collapse collapse show" aria-labelledby="headerorfooter">
																				<div class="accordion-body text-sm">
																					<div class="row justify-content-center">
																						<div class="col-lg-8">
																							<p style="font-size:16px !important;margin-bottom: -7px !important;">
																								Header
																							</p>
																							<div class="row mb-1">
																								<div class="col-lg-6 my-1">
																									<div class="custom-control custom-checkbox mr-sm-2">
																										<input type="checkbox" class="custom-control-input" name="companyname" id="companyname" <?= ($sqlviewreport['companyname']=='1')?'checked':''?>>
																										<label class="custom-control-label custom-label" for="companyname">
																											Company Name
																										</label>
																									</div>
																								</div>
																								<div class="col-lg-6">
																									<input type="text" value="<?= $branch['franchisename'] ?>" class="form-control form-control-sm" readonly style="background-color: #e9ecef;">
																								</div>
																							</div>
																							<p style="font-size:16px !important;margin-bottom: -3px !important;">
																								Footer
																							</p>
																							<div class="row">
																								<div class="col-lg-6 my-1">
																									<div class="custom-control custom-checkbox mr-sm-2">
																										<input type="checkbox" class="custom-control-input" name="dateprepare" id="dateprepare" <?= ($sqlviewreport['dateprepare']=='1')?'checked':''?>>
																										<label class="custom-control-label custom-label" for="dateprepare">
																											Date Prepared
																										</label>
																									</div>                  
																								</div>
																							</div>
																							<div class="row">
																								<div class="col-lg-6 my-1">
																									<div class="custom-control custom-checkbox mr-sm-2">
																										<input type="checkbox" class="custom-control-input" name="timeprepare" id="timeprepare" <?= ($sqlviewreport['timeprepare']=='1')?'checked':''?>>
																										<label class="custom-control-label custom-label" for="timeprepare">
																											Time Prepared
																										</label>
																									</div>                  
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="accordion" id="accordionRental">
																		<div class="accordion-item mb-1">
																			<h5 class="accordion-header" id="advancedrules">
																				<button class="accordion-button font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#advancedruless" aria-expanded="true" aria-controls="advancedruless">
																					<div class="customcont-header ml-0 mb-1">
																						<a class="customcont-heading">
																							Advanced
																						</a>
																					</div>
																				</button>
																			</h5>
																			<div id="advancedruless" class="accordion-collapse collapse show" aria-labelledby="advancedrules">
																				<div class="accordion-body text-sm">
																					<div class="row justify-content-center">
																						<div class="col-lg-8">
																							<p style="font-size:16px !important;margin-bottom: -7px !important;">
																								Validation Rule
																							</p>
																							<div class="row mb-1">
																								<div class="col-lg-6 my-1">
																									<div class="custom-control custom-checkbox mr-sm-2">
																										<input type="checkbox" class="custom-control-input" name="gstrule" id="gstrule" <?= ($sqlviewreport['gstrule']=='1')?'checked':''?>>
																										<label class="custom-control-label custom-label" for="gstrule">
																											If the bill does not contain a GSTIN, it will be forwarded to the consumer for display and calculation. Conversely, if the bill contains a GSTIN, it will be directed to the registered persons for display and calculation.
																										</label>
																									</div>                  
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="modal-footer mfsub" style="margin: 0px 9px -16px 9px !important;border-top: 1px solid #b6bcc5 !important;">
																	<div class="col">
																		<button onclick="funaddcustomizes()" class="btn btn-primary btn-sm btn-custom arlina-button expand-left" type="submit"  name="submitcustomizes" id="submitcustomizes" value="Submit">
																			<span class="label">
																				Run Report
																			</span>
																			<span class="spinner"></span>
																		</button>
																		<button type="button" class="btn btn-primary btn-sm btn-custom-grey" onclick="funescustomizes()">
																			Cancel
																		</button>
																	</div>
																</div>
															</form>
														</div>
													</div>
												</div>
												<!-- Customization modal end -->
												<a style="margin:4.5px 4.5px !important;<?= ($sqlviewreport['printtype']=='0')?'':'display: none;'?>" class="btn btn-primary btn-sm btn-custom-grey" onclick="window.open('reportstockandsalesprint.php?datefrom=<?=$_GET['datefrom']?>&dateto=<?=$_GET['dateto']?>&commision=<?=$_GET['commision']?>&category=<?=$_GET['category']?>','_blank')">
													<i class="fa fa-print" aria-hidden="true"></i>
													<span class="mobreswords "> Window Print</span>
												</a>
												<a style="margin:4.5px 4.5px !important;<?= ($sqlviewreport['printtype']=='0')?'display: none;':''?>" class="btn btn-primary btn-sm btn-custom-grey" id="showoptionstimer">
													<i class="fa fa-timer" aria-hidden="true"></i>
													<span class="timeroptionstext"> Loading...</span>
												</a>
												<a style="margin:4.5px 4.5px !important;<?= ($sqlviewreport['printtype']=='0')?'display: none;':'display: none;'?>" class="btn btn-primary btn-sm btn-custom-grey hidtillajax" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="converHTMLFileToPDF()">
													<i class="fa fa-print" aria-hidden="true"></i>
													<span class="mobreswords "> Print</span>
												</a>
												<!-- Print Report Modal Preview Start -->
												<div class="modal fade" id="LoadingExampleModal" tabindex="-1" role="dialog" aria-labelledby="LoadingExampleModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-dialog-centered" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="LoadingExampleModalLabel" style="font-weight: normal;">
																	Please Wait
																</h5>
																<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div class="modal-body">
																<img src="loading.gif" alt="Loading..." id="loadimgobj" style="width:100%;height: 100%;">
															</div>
															<div class="modal-footer" style="margin-top: 33px !important;">
																<a data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-custom add" style="font-size: 13px;height: 24px;margin-bottom:1rem;margin-top: 0px;margin-right:0px;padding-right: 5px;">
																	<p style="width: max-content;margin-top:-5px;margin-left: -6px;padding: 0px;">
																		<i class="fa fa-times" style="font-size:13px;padding: 0px;width: max-content;"></i>
																		&nbsp;
																		<span style="margin-left: -5px;width: max-content;">
																			Close
																		</span>
																	</p>
																</a>    
															</div>
														</div>
													</div>
												</div>
												<!-- Print Report Modal Preview End -->
												<!-- Print Report Modal Preview Start -->
												<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-dialog-centered" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="exampleModalLabel" style="font-weight: normal;">
																	Preview
																</h5>
																<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div class="modal-body" id="pdfObj">
																<img src="loading.gif" alt="Loading..." id="loadimgobj">
															</div>
															<div class="modal-footer" style="margin-top: 33px !important;">
																<a data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-custom add" style="font-size: 13px;height: 24px;margin-bottom:1rem;margin-top: 0px;margin-right:0px;padding-right: 5px;">
																	<p style="width: max-content;margin-top:-5px;margin-left: -6px;padding: 0px;">
																		<i class="fa fa-times" style="font-size:13px;padding: 0px;width: max-content;"></i>
																		&nbsp;
																		<span style="margin-left: -5px;width: max-content;">
																			Close
																		</span>
																	</p>
																</a>    
															</div>
														</div>
													</div>
												</div>
												<!-- Print Report Modal Preview End -->
												<a style="margin:4.5px 4.5px !important;<?= ($sqlviewreport['printtype']=='0')?'display: none;':'display: none;'?>" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-primary btn-sm btn-custom-grey hidtillajax" onclick="downloadpdf()">
													<i class="fa fa-download" aria-hidden="true"></i>
													<span class="mobreswords ">
														Download
														&nbsp;
														<i class="fa fa-caret-down" style="font-size:10px !important;"></i>
													</span>
												</a>
												<!-- Print Report Modal Download Start -->
												<div class="modal fade" id="exampleModaldownload" tabindex="-1" role="dialog" aria-labelledby="downloadexampleModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-dialog-centered" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<h5 class="modal-title" id="downloadexampleModalLabel" style="font-weight: normal;">
																	Download
																</h5>
																<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<div class="modal-body" id="pdfObjdownload">
																<img src="loading.gif" alt="Loading..." id="loadimgobj" style="width:100px">
															</div>
															<div class="modal-footer" style="margin-top: 33px !important;">
																<a data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-custom add" style="font-size: 13px;height: 24px;margin-bottom:1rem;margin-top: 0px;margin-right:0px;padding-right: 5px;">
																	<p style="width: max-content;margin-top:-5px;margin-left: -6px;padding: 0px;">
																		<i class="fa fa-times" style="font-size:13px;padding: 0px;width: max-content;"></i>
																		&nbsp;
																		<span style="margin-left: -5px;width: max-content;">
																			Close
																		</span>
																	</p>
																</a>    
															</div>
														</div>
													</div>
												</div>
												<!-- Print Report Modal Download End -->
												<div class="dropdown-menu  dropdown-menu-end customdropdown  me-sm-2" aria-labelledby="dropdownMenuButton1">
													<i class="fa fa-caret-down" id="reparup" style="color: #3c3c46 !important;position: relative;top: -13px;left: 240px;"></i>
													<div style="background-color: #3c3c46;margin-top: 10px !important;">
														<a class="nav-link" href="#" style="color: #fff;margin-top: -30px;" onclick="downloadaspdf()">
															<i class="fa-solid fa-file-pdf"></i>
															<span class="nav-link-text ms-2">
																PDF (Portable Document Format)
															</span>
														</a>
														<a class="nav-link" href="#" style="color: #fff;" onclick="downloadascsv()" id="csvdownopt">
															<i class="fa-sharp fa-solid fa-file-csv"></i>
															<span class="nav-link-text ms-2">
																CSV (Comma Seperated Values)
															</span>
														</a>
													</div>
												</div>
											</div>
										</div>
										<nav>
											<div class="nav nav-tabs" id="nav-tab" role="tablist">
												<button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
													<div class="customcont-header ml-0">
														<a class="customcont-heading">
															Overview
														</a>  
													</div>
												</button>
												<button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
													<div class="customcont-header ml-0">
														<a class="customcont-heading">
															History
														</a>   
													</div>
												</button>
											</div>
										</nav>
										<div class="tab-content" id="nav-tabContent">
											<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
												<div class="table-responsive m-3" id="histables">
													<table class="table table-bordered">
														<thead>
															<tr>
																<th style="border:1px solid #ddd !important;color: grey !important;font-weight: 600 !important;">
																	DATE
																</th>
																<th style="border:1px solid #ddd !important;color: grey !important;font-weight: 600 !important;">
																	DETAILS
																</th>
															</tr>
														</thead>
														<tbody>
														<?php
															$sqluse=$con->prepare("SELECT * FROM pairusehistory WHERE usetype = 'REPstockandsales' AND useid = ? ORDER BY createdon DESC");
															$sqluse->bind_param("s", $companymainid);
															$sqluse->execute();
															$resultprefer = $sqluse->get_result();
															while($infouse=$resultprefer->fetch_array()){
														?>
															<tr>
																<td data-label="DATE" id="datehis">
																	<?=date('d/m/Y h:i:s a', strtotime($infouse['createdon']))?>
																</td>
																<td data-label="DETAILS">
																	<?=str_replace('?', $resmaincurrencyans[0], $infouse['useremarks'])?>
																	<br>
																	<span>Generated By</span>
																	<span  id="chhis"> <?=$infouse['createdby']?></span>
																</td>
															</tr>
														<?php
															}
														?>
														</tbody>
													</table>
												</div>
											</div>
											<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
												<div class="container mt-1 mb-3" style="padding:0px;margin:0px;">
													<div class="row d-flex justify-content-center mt-5">
														<div class="col-lg-8 col-md-12">
															<div class="card" id="zoomforprint"  style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); padding:10px; width:max-content;height: max-content;padding-bottom: 0px !important;" align="center">
																<div class="table-responsive" style="width: max-content !important;height: max-content !important;max-width:max-content !important; max-height:max-content !important;min-width:max-content !important; min-height:max-content !important;">
																	<table id="printarea" style="border:1px solid #cccccc;margin-bottom: -13px !important;width: 29cm !important;height: 21cm !important;max-width:29cm !important; max-height:21cm !important;min-width:29cm !important; min-height:21cm !important;">
																		<tr>
																			<td width="100%" style="height:10px !important;">
																				<table width="100%" style="text-align: center;font-weight: bold;">
																					<tr>
																						<td style="<?= ($sqlviewreport['companyname']=='1')?'':'display: none;'?>">
																							<?= $branch['franchisename'] ?>
																						</td>
																					</tr>
																					<tr>
																						<td>
																							Stock And Sales Statement
																						</td>
																					</tr>
																					<tr>
																						<td>
																							<?=(($sqlviewreport['category']=='all')?'All':$sqlviewreport['categoryname'])?>
																						</td>
																					</tr>
																					<tr>
																						<td id="datefromto"></td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																	<?php
																		$datefrom = mysqli_real_escape_string($con, $_GET['datefrom']);
																		$dateto = mysqli_real_escape_string($con, $_GET['dateto']);
																	?>
																		<input type="hidden" name="datefrom" id="datefrom" value="<?= $datefrom ?>">
																		<input type="hidden" name="dateto" id="dateto" value="<?= $dateto ?>">
																		<tr style="height:1px;">
																			<td width="100%" style="padding:10px;">
																				<table id="print-are1" style="border: 1px solid #eee;" width="100%">
																					<thead>
																						<tr>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									#
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Pro ID
																								</span>
																							</td>
																							<td class="text-uppercase" style="max-width:3%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Product
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Pack
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Opening Stock
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Purchase Qty
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Sales Return Qty
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Total Stock
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Purchase Return Qty
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Last Month Sales Qty
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Sales Qty
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Free Qty
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Adjust Qty
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Closing Stock
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Selling Price
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Closing Value at Selling Price
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									GST
																								</span>
																							</td>
																							<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">
																								<span style="font-size:13px;color:black;">
																									Total Value
																								</span>
																							</td>
																						</tr>
																					</thead>
																					<tbody id="myTable">
																					
																					</tbody>
																				</table>
																			</td>
																		</tr>
																		<tr style="height:40px !important;">
																			<td style="padding:0px !important;border-bottom: none;">
																				<table width="100%">
																					<tr>
																						<td width="30%" style="padding: 0px !important;border-right: 1px solid #cccccc;font-weight: bold;">
																							<table width="100%">
																								<tr>
																								<?php
																									$date = 'd/m/Y h:i:s A';
																									if (($sqlviewreport['dateprepare']=='1')&&($sqlviewreport['timeprepare']=='1')) {
																										$date = 'd/m/Y h:i:s A';
																										$dates = date('d-m-Y h:i:s A');
																									}
																									elseif ($sqlviewreport['dateprepare']!='1') {
																										$date = 'h:i:s A';
																										$dates = date('h:i:s A');
																									}
																									elseif ($sqlviewreport['timeprepare']!='1') {
																										$date = 'd/m/Y';
																										$dates = date('d-m-Y');
																									}
																								?>
																									<td style="vertical-align:middle !important;text-align: center !important;padding-top: 7px !important;">
																										<div style="text-align:center;margin-top: -10px !important;font-size: 12px !important;<?=(($sqlviewreport['timeprepare']=='1'||$sqlviewreport['dateprepare']=='1')?'':'display: none;')?>">
																											<span>
																												Printed On : <?php echo date($date,strtotime($dates))?>
																											</span>
																										</div>
																										<div style="text-align:center;line-height: 7px !important;font-size: 12px !important;">
																											<b>
																												(Page
																												<span class="pagesforcurrent" style="padding: 0px 3px;">
																													1
																												</span>
																												/1)
																											</b>
																										</div>
																									</td>
																								</tr>
																							</table>
																						</td>
																					</tr>
																				</table>
																			</td>
																		</tr>
																		<tr>
																			<td></td>
																		</tr>
																	</table>
																	<span>
																		<span style="background-color:#fff !important;font-size: 8px !important;color: #ccc !important;">
																			PAIRSCRIPT
																		</span>
																	</span>
																</div>
															</div>
															<p align="right" class="mt-3" style="margin-right:-25px; cursor:pointer" id="templatetext">
																Template: 'Standard A4 Landscape'
																<a data-bs-toggle="modal" data-bs-target="#changeModal" class="text-blue">
																	Change
																</a>
															</p>
															<input type="hidden" id="totalrowscount" value="<?=0?>">
															<input type="hidden" value="10" id="limitforpagenum">
															<input type="hidden" value="0" id="totalpagenums">
															<div id="pagenumcontainer" style="padding: 24px;text-align: center;display: none;"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<table id="print-are3" style="border: 1px solid #eee;display: none;" width="100%">
											<thead>
												<tr>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">#	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Pro ID	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Product	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Pack	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Opening Stock	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Purchase Qty	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Sales Return Qty	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Total Stock	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Purchase Return Qty	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Last Month Sales Qty	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Sales Qty	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Free Qty	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Adjust Qty	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Closing Stock	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Selling Price	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Closing Value at Selling Price	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">GST	</span></td>
													<td class="text-uppercase" style="width:5%;border:1px solid #eee;padding-left: 3px;">	<span style="font-size:13px;color:black;">Total Value	</span></td>
												</tr>
											</thead>
											<tbody id="myTables">
											</tbody>
										</table>
										<!-- Modal -->
										<div class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="changeModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-dialog-centered" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="changeModalLabel">
															Choose Template
														</h5>
														<button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<!-------------->
														<table width="100%">
															<tr>
																<td width="30%" style="text-align:center">
																	<div class="imgcontainer" id="standardcontainer">
																		<img src="a4.png" id="standardimg" alt="Snow" onclick="standandclick()" style="width:90%;box-shadow: 0 0 5px #aaaaaa;border: 1px solid #1BBC9B;opacity: 0.5;">
																		<div class="centered" id="standardtext" style="display:block;">
																			<i class="fa fa-check-circle"></i>
																		</div>
																	</div>
																	<p class="text-blue mt-2 mb-0 pb-0">
																		Standard A4 Landscape
																	</p>
																</td>
															</tr>
														</table>
														<!-------------->
													</div>
													<div class="modal-footer" style="margin-top: 33px !important;">
														<a data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-sm btn-custom add" style="font-size: 13px;height: 24px;margin-bottom:1rem;margin-top: 0px;margin-right:0px;padding-right: 5px;">
															<p style="width: max-content;margin-top:-5px;margin-left: -6px;padding: 0px;">
																<i class="fa fa-times" style="font-size:13px;padding: 0px;width: max-content;"></i>
																&nbsp;
																<span style="margin-left: -5px;width: max-content;">
																	Close
																</span>
															</p>
														</a>       
													</div>
												</div>
											</div>
										</div>
										<!-- modal -->
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
			include('footer.php');
		?>
		</div>
	</main>
<?php
	include('fexternals.php');
?>
<script type="text/javascript" src="vendor/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="vendor/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var ajaxRequestview;
const mountNode = document.getElementById('pagenumcontainer');
"use strict";
const { createRoot } = ReactDOM;
const { Pagination } = antd;
// Total ${range[0]} - ${range[1]} of ${total} (Datas)
const App = () => (
	React.createElement(
		Pagination,
		{
			total: <?=ceil(0)?>,
			showSizeChanger: false,
			showQuickJumper: true,
			showTotal: (total, range) => `Total ${range[0]} - ${range[1]} of ${total} items`,
			onChange: pagechanges,
			defaultPageSize: 22
		}
	)
);
const ComponentDemo = App;
createRoot(mountNode).render(React.createElement(ComponentDemo, null));
$(document).ready(function () {
	$("#LoadingExampleModal").modal("show");
	ajaxRequestview = $.ajax({
		type: "GET",
		url: 'reportviewsearch.php?term=term&limitings='+($('#limitforpagenum').val())+'&datefrom=<?=$datefrom?>&dateto=<?=$dateto?>&dif=stockandsales&commision=<?=$_GET['commision']?>&category=<?=$_GET['category']?>&lastornone=yes',
		success: function (result) {
			$('#myTable').html(result);
			console.log('finish');
			$("#LoadingExampleModal").modal("hide");
		},
		error: function (error) {
			console.log(error);
		}
	});
});
// const pagechanges = (page, pageSize) => {
// 	// alert('Page changed to'+ page+'Items per page:'+ pageSize);
// 	$('#limitforpagenum').val(parseInt(pageSize));
// 	var totalpagesnumber = <?=ceil(0/22)?>;
// 	if ((page=='')||(page==0)) {
// 		var isthisval = 1;
// 	}
// 	else if(page>totalpagesnumber){
// 		var isthisval = totalpagesnumber;
// 	}
// 	else{
// 		var isthisval = page;
// 	}
// 	var perpages = ''+((parseInt(isthisval)-1)*22);
// 	// $(".pagesforcurrent").html(page);
// 	var lastornone = 'no';
// 	if (totalpagesnumber==page) {
// 		lastornone = 'yes';
// 	}
// 	// ajax for get
// 	ajaxRequestview = $.ajax({
// 		type: "GET",
// 		url: 'reportviewsearch.php?term='+perpages+'&limitings='+($('#limitforpagenum').val())+'&datefrom=<?=$datefrom?>&dateto=<?=$dateto?>&dif=stockandsales&commision=<?=$_GET['commision']?>&category=<?=$_GET['category']?>&lastornone='+lastornone+'&curpage='+page+'',
// 		success: function (result) {
// 			$("#myTable").html(result);
// 			if (totalpagesnumber!=page) {
// 				document.getElementById("lastpagetotal").style.display = 'none';
// 			}
// 		},
// 		error: function (error) {
// 			alert(error);
// 		}
// 	});
// 	// it is done
// };
//FOR PAGE NUMBER SETUP USING REACT ANTDESIGN FRAMEWORK

function downloadpdf(){
	document.getElementById('reparup').style.animation = "repone 2s 3000000";
}
//FOR DOWNLOAD PDF

$(document).ready(function () {
	$('.automodalchecking').one("click",function () {
		setTimeout(function() {
			$('#dummymodalshowing').modal('show');
		},100);
		setTimeout(function() {
			$('#dummymodalshowing').modal('hide');
		},1000);
	});
});
//FOR DUMMY MODAL TO ACTIVATE SELECT SEARCH TO WRITEABLE

function funescustomizes() {
	$("#Customizesmodal").modal("hide");
}
//FOR CUSTOMIZE MODAL HIDDEN OPTION

$(function() {
	const fromDateString = $("#datefrom").val();
	const [fromyear, frommonth, fromday] = fromDateString.split('-').map(Number);
	var start = new Date(fromyear, frommonth - 1, fromday);
	const toDateString = $("#dateto").val();
	const [toyear, tomonth, today] = toDateString.split('-').map(Number);
	var end = new Date(toyear, tomonth - 1, today);
	var quarter = moment().quarter();
	$('#calendars').daterangepicker({
		locale: {
			format: 'DD/MM/YYYY'
		},
		"showDropdowns": true,
		"opens": "center",
		startDate: start,
		endDate: end,
		ranges: {
		'Last Month': [moment().subtract(1, 'month').startOf('month').startOf('day'), moment().subtract(1, 'month').endOf('month').endOf('day')]
	},
	"linkedCalendars": false,
	"alwaysShowCalendars": true,        
	"applyClass": "btn-custom",
	"cancelClass": "btn-custom-grey"
	},
	function(start, end, label) {
		console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
		let startspace = start.format('DD MM YYYY');
		let startslice = startspace.split(" ");
		let startfinalday = startslice[0];
		const startmonth = new Date(startslice[1]);
		const startmonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		let startfinalmonths = startmonths[startmonth.getMonth()];
		let startfinalmonth = startfinalmonths;
		let startfinalyear = startslice[2];
		// alert(startfinalday + " " + startfinalmonth + " " + startfinalyear);

		let endspace = end.format('DD MM YYYY');
		let endslice = endspace.split(" ");
		let endfinalday = endslice[0];
		const endmonth = new Date(endslice[1]);
		const endmonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		let endfinalmonths = endmonths[endmonth.getMonth()];
		let endfinalmonth = endfinalmonths;
		let endfinalyear = endslice[2];
		// alert(endfinalday + " " + endfinalmonth + " " + endfinalyear);

		$("#datefromto").html("From " + startfinalday + " " + startfinalmonth + " " + startfinalyear + ' To ' + endfinalday + " " + endfinalmonth + " " + endfinalyear);
		$("#datefrom").val(start.format('YYYY-MM-DD'));
		$("#dateto").val(end.format('YYYY-MM-DD'));
		let datefrom = $("#datefrom").val();
		let dateto = $("#dateto").val();
		window.open("reportstockandsales.php?datefrom="+datefrom+"&dateto="+dateto+"&commision=<?=$_GET['commision']?>&category=<?=$_GET['category']?>","_self");
	});
});

$(document).ready(function() {
	let datefrom = $("#datefrom").val();
	let dateto = $("#dateto").val();
	const startmonth = new Date(datefrom);
	const startmonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	let startfinalmonths = startmonths[startmonth.getMonth()];
	let startday = startmonth.getDate();
	let startyear = startmonth.getFullYear();
	const endmonth = new Date(dateto);
	const endmonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	let endfinalmonths = endmonths[endmonth.getMonth()];
	let endday = endmonth.getDate();
	let endyear = endmonth.getFullYear();
	// alert(day+ " " + startfinalmonths + " " +year);

	$("#datefromto").html("From " + startday + " " + startfinalmonths + " " + startyear + ' To ' + endday + " " + endfinalmonths + " " + endyear);
});
</script>

<script src="pdf/examples/libs/jspdf.umd.js"></script>
<script src="pdf/examples/mitubachi-normal.js"></script>
<script src="pdf/examples/libs/faker.min.js"></script>
<script src="pdf/dist/jspdf.plugin.autotable.js"></script>
<script src="pdf/examples/examples.js"></script>

<script type="text/javascript">
function converHTMLFileToPDF() {
	var fromtopdf = document.getElementById("datefromto").innerHTML;
	$("#loadimgobj").css("display","block");
	$('#pdfObj').html('');
	$('#pdfObj').html('<p style="text-align:center;">Please wait <img src="assets/img/timer35sec.gif?' + new Date().getTime() + '" style="width:13px;height:13px;position:relative;top:-1.5px;"> seconds</p><br><img src="loading.gif" width="100%">');
	sIndex = 0;
	dt = 10000;
	$("body").on("click",function() {
		if ($("#exampleModal").hasClass("show")) {
			console.log("yes");
		}
		else{
			clearInterval(intervaling);
			clearInterval(intervalings);
			clearTimeout(finaltimer);
		}
	});
	var finaltimer = setTimeout(function(){
		var doc = new jspdf.jsPDF('l', 'pt','a4');
		var totalPagesExp = $("#totalpagenums").val();
		doc.setFontSize(9);
		doc.setFont(undefined, 'bold');
		var header = function(data) {
			var headtext = "<?= ($sqlviewreport['companyname']=='1')?$branch['franchisename']:''?>";
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2;
			doc.text(headtext,x,35);
	 
			var headtext1 = "Stock And Sales Statement";
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext1)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2; 
			doc.text(headtext1, x, 50);

			var headtext2 = "<?=(($sqlviewreport['category']=='all')?'All':$sqlviewreport['categoryname'])?>";
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext2)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2; 
			doc.text(headtext2, x, 65);

			var headtext3 = fromtopdf;
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext3)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2; 
			doc.text(headtext3, x, 80);

			// Footer
			var str = 'Page ' + doc.internal.getNumberOfPages()
			if (typeof doc.putTotalPages === 'function') {
				str = str + " of " + totalPagesExp;
			}
			var pageSize = doc.internal.pageSize
			var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(str)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2;
			doc.text(str, x, pageHeight - 15);

			const date = new Date();
			let currentPeriod = date.getHours() >= 12 ? 'PM' : 'AM';
			let currentHour= String((date.getHours()>12)?date.getHours()-12:date.getHours()).padStart(2, '0');
			let currentMinute= String(date.getMinutes()).padStart(2, '0');
			let currentSecond= String(date.getSeconds()).padStart(2, '0');
			let currentDay= String(date.getDate()).padStart(2, '0');
			let currentMonth = String(date.getMonth()+1).padStart(2,"0");
			let currentYear = date.getFullYear();
			let dateprepare = '';
			let timeprepare = '';
			let printedword = '';
		<?php 
			if ($sqlviewreport['dateprepare']=='1') {
		?>
			dateprepare = `${currentDay}/${currentMonth}/${currentYear}`;
		<?php 
			}
			if ($sqlviewreport['timeprepare']=='1') {
		?>
			timeprepare = `${currentHour}:${currentMinute}:${currentSecond} ${currentPeriod}`;
		<?php 
			}
			if ($sqlviewreport['timeprepare']=='1'||$sqlviewreport['dateprepare']=='1') {
		?>
			printedword = 'Printed On : ';
		<?php 
			}
		?>
			let currentDate = `${printedword} ${dateprepare} ${timeprepare}`;
			var printedon = currentDate;
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(printedon)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2;
			doc.text(printedon, x, pageHeight - 30);
		};
		var botforprint = 45;
		doc.autoTable({
			html: '#print-are3',
			theme: 'plain',
			headStyles :{
				fillColor : [245, 242, 242]
			},
			margin: {
				top: 95,
				bottom: botforprint
			},
			styles: {
				fontSize: 8,
				overflow:'hidden',
				fontStyle: 'normal',
				halign : 'center',
				fillColor: [255,255,255],
				lineColor: [204, 204, 204],
				lineWidth: 0.1
			},
			didDrawPage: header, 
			columnStyles: { 
				0: { halign: 'right', cellWidth: 29},
				1: { halign: 'right', cellWidth: 29},
				2: { halign: 'right', cellWidth: 135},
				3: { halign: 'left', cellWidth: 35},
				4: { halign: 'right', cellWidth: 35},
				5: { halign: 'right', cellWidth: 35},
				6: { halign: 'right', cellWidth: 35},
				7: { halign: 'right', cellWidth: 35},
				8: { halign: 'right', cellWidth: 35},
				9: { halign: 'right', cellWidth: 35},
				10: { halign: 'right', cellWidth: 35},
				11: { halign: 'right', cellWidth: 35},
				12: { halign: 'right', cellWidth: 35},
				13: { halign: 'right', cellWidth: 35},
				14: { halign: 'right', cellWidth: 35},
				15: { halign: 'right', cellWidth: 35},
				16: { halign: 'right', cellWidth: 35},
				17: { halign: 'right', cellWidth: 35},
				18: { halign: 'right', cellWidth: 35}
			}
		});
		obj = '<object id="pdfObjObj" data="'+doc.output('bloburi')+'" type="application/pdf" width="90%" height="550"></object>';
		$.ajax({
			type: "GET",
			url: 'reporthistory.php?types=stockandsales&titles=Stock And Sales Statement&printdownpdfcsv=Printed PDF&fromto='+fromtopdf+'',
			success: function (result) {
				console.log(result);
			},
			error: function (error) {
				console.log(error);
			}
		});
		$('#pdfObj').html(obj);
	}, 1500);
}
//FOR PDF PREVIEW

function downloadaspdf() {
	$("#exampleModaldownload").modal("show");
	var fromtopdf = document.getElementById("datefromto").innerHTML;
	$("#loadimgobj").css("display","block");
	$('#pdfObjdownload').html(' ');
	$('#pdfObjdownload').html('<p style="text-align:center;">Please wait <img src="assets/img/timer35sec.gif?' + new Date().getTime() + '" style="width:13px;height:13px;position:relative;top:-1.5px;"> seconds</p><br><img src="loading.gif" width="100%">');
	sIndex = 0;
	dt = 10000;
	$("body").on("click",function() {
		if ($("#exampleModaldownload").hasClass("show")) {
			console.log("yes");
		}
		else{
			clearInterval(intervaling);
			clearInterval(intervalings);
			clearTimeout(finaltimer);
		}
	});
	var finaltimer = setTimeout(function(){
		var doc = new jspdf.jsPDF('l', 'pt','a4');
		var totalPagesExp = $("#totalpagenums").val();
		doc.setFontSize(9);
		doc.setFont(undefined, 'bold');
		var header = function(data) {
			var headtext = "<?= ($sqlviewreport['companyname']=='1')?$branch['franchisename']:''?>";
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2;
			doc.text(headtext,x,35);
	 
			var headtext1 = "Stock And Sales Statement";
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext1)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2; 
			doc.text(headtext1, x, 50);

			var headtext2 = "<?=(($sqlviewreport['category']=='all')?'All':$sqlviewreport['categoryname'])?>";
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext2)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2; 
			doc.text(headtext2, x, 65);

	 
			var headtext3 = fromtopdf;
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(headtext3)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2; 
			doc.text(headtext3, x, 80);

			// Footer
			var str = 'Page ' + doc.internal.getNumberOfPages()
			if (typeof doc.putTotalPages === 'function') {
				str = str + " of " + totalPagesExp;
			}
			var pageSize = doc.internal.pageSize
			var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(str)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2;
			doc.text(str, x, pageHeight - 15);

			const date = new Date();
			let currentPeriod = date.getHours() >= 12 ? 'PM' : 'AM';
			let currentHour= String((date.getHours()>12)?date.getHours()-12:date.getHours()).padStart(2, '0');
			let currentMinute= String(date.getMinutes()).padStart(2, '0');
			let currentSecond= String(date.getSeconds()).padStart(2, '0');
			let currentDay= String(date.getDate()).padStart(2, '0');
			let currentMonth = String(date.getMonth()+1).padStart(2,"0");
			let currentYear = date.getFullYear();
			let dateprepare = '';
			let timeprepare = '';
			let printedword = '';
		<?php 
			if ($sqlviewreport['dateprepare']=='1') {
		?>
			dateprepare = `${currentDay}/${currentMonth}/${currentYear}`;
		<?php 
			}
			if ($sqlviewreport['timeprepare']=='1') {
		?>
			timeprepare = `${currentHour}:${currentMinute}:${currentSecond} ${currentPeriod}`;
		<?php 
			}
			if ($sqlviewreport['timeprepare']=='1'||$sqlviewreport['dateprepare']=='1') {
		?>
			printedword = 'Printed On : ';
		<?php 
			}
		?>
			let currentDate = `${printedword} ${dateprepare} ${timeprepare}`;
			var printedon = currentDate;
			var fontSize = doc.internal.getFontSize();
			var pageWidth = doc.internal.pageSize.width;
			txtWidth = doc.getStringUnitWidth(printedon)*fontSize/doc.internal.scaleFactor;
			x = ( pageWidth - txtWidth ) / 2;
			doc.text(printedon, x, pageHeight - 30);
		};
		var botforprint = 45;
		doc.autoTable({
			html: '#print-are3',
			theme: 'plain',
			headStyles :{
				fillColor : [245, 242, 242]
			},
			margin: {
				top: 95,bottom: botforprint
			},
			styles: {
				fontSize: 8,
				overflow:'hidden',
				fontStyle: 'normal',
				halign : 'center',
				fillColor: [255,255,255],
				lineColor: [204, 204, 204],
				lineWidth: 0.1
			},
			didDrawPage: header,
			columnStyles: { 
				0: { halign: 'right', cellWidth: 29},
				1: { halign: 'right', cellWidth: 29},
				2: { halign: 'right', cellWidth: 135},
				3: { halign: 'left', cellWidth: 35},
				4: { halign: 'right', cellWidth: 35},
				5: { halign: 'right', cellWidth: 35},
				6: { halign: 'right', cellWidth: 35},
				7: { halign: 'right', cellWidth: 35},
				8: { halign: 'right', cellWidth: 35},
				9: { halign: 'right', cellWidth: 35},
				10: { halign: 'right', cellWidth: 35},
				11: { halign: 'right', cellWidth: 35},
				12: { halign: 'right', cellWidth: 35},
				13: { halign: 'right', cellWidth: 35},
				14: { halign: 'right', cellWidth: 35},
				15: { halign: 'right', cellWidth: 35},
				16: { halign: 'right', cellWidth: 35},
				17: { halign: 'right', cellWidth: 35},
				18: { halign: 'right', cellWidth: 35}
			}
		});
		$.ajax({
			type: "GET",
			url: 'reporthistory.php?types=stockandsales&titles=Stock And Sales Statement&printdownpdfcsv=Downloaded PDF&fromto='+fromtopdf+'',
			success: function (result) {
				console.log(result);
			},
			error: function (error) {
				console.log(error);
			}
		});
		doc.save('Stock-And-Sales-Statement-<?=$datefrom?>-To-<?=$dateto?>');
		$("#exampleModaldownload").modal("hide");
		alert("Downloaded Successfully");
	}, 1500);
}
//FOR PDF DOWNLOAD

function downloadascsv() {
	$("#exampleModaldownload").modal("show");
	var fromtopdf = document.getElementById("datefromto").innerHTML;
	$("#loadimgobj").css("display","block");
	$('#pdfObjdownload').html(' ');
	$('#pdfObjdownload').html('<p style="text-align:center;">Please wait <img src="assets/img/timer35sec.gif?' + new Date().getTime() + '" style="width:13px;height:13px;position:relative;top:-1.5px;"> seconds</p><br><img src="loading.gif" width="100%">');
	sIndex = 0;
	dt = 10000;
	$("body").on("click",function() {
		if ($("#exampleModaldownload").hasClass("show")) {
			console.log("yes");
		}
		else{
			clearInterval(intervaling);
			clearInterval(intervalings);
			clearTimeout(finaltimer);
		}
	});
	var finaltimer = setTimeout(function(){
		var table = document.getElementById("print-are3");
		var rows = table.querySelector("tbody").getElementsByTagName("tr");
		var headers = table.querySelector("thead").getElementsByTagName("td");
		var data = [];
		data.push(["<?= ($sqlviewreport['companyname']=='1')?$branch['franchisename']:''?>"]);
		data.push(["Stock And Sales Statement"]);
		data.push(["<?=(($sqlviewreport['category']=='all')?'All':$sqlviewreport['categoryname'])?>"]);
		data.push([fromtopdf]);
		var visibleHeaders = Array.from(headers).filter(function(th) {
			return getComputedStyle(th).display !== 'none';
		});
		// Push visible headers
		var headerRow = [];
		for (var h = 0; h < visibleHeaders.length; h++) {
			headerRow.push(visibleHeaders[h].innerText);
		}
		data.push(headerRow);
		for (var i = 0; i < rows.length; i++) {
			var row = [];
			var cells = rows[i].getElementsByTagName("td");
			if (cells.length > 0) {
				for (var j = 0; j < cells.length; j++) {
					var cell = cells[j];
					var computedStyle = getComputedStyle(cell);
					if (computedStyle.display !== 'none' && computedStyle.visibility !== 'hidden') {
						var colspan = parseInt(cell.getAttribute('colspan')) || 1;
						// Handle colspan
						if (colspan > 1) {
							row.push(' ' + cell.innerText + ' '); // Push the value once
							for (var k = 1; k < colspan; k++) {
								row.push(''); // Push empty strings for remaining colspan - 1 columns
							}
						}
						else {
							row.push(' ' + cell.innerText + ' ');
						}
					}
				}
				data.push(row);
			}
		}
		var csvContent = "data:text/csv;charset=utf-8,";
		data.forEach(function(rowArray) {
			var row = rowArray.map(function(cell) {
				return '"' + cell.replace(/"/g, '""') + '"';
			}).join(",");
			csvContent += row + "\r\n";
		});
		var encodedUri = encodeURI(csvContent);
		var link = document.createElement("a");
		link.setAttribute("href", encodedUri);
		link.setAttribute("download", "Stock-And-Sales-Statement-<?=$datefrom?>-To-<?=$dateto?>");
		document.body.appendChild(link);
		link.click();
		$.ajax({
			type: "GET",
			url: 'reporthistory.php?types=stockandsales&titles=Stock And Sales Statement&printdownpdfcsv=Downloaded CSV&fromto='+fromtopdf+'',
			success: function (result) {
				console.log(result);
			},
			error: function (error) {
				console.log(error);
			}
		});
		$("#exampleModaldownload").modal("hide");
		alert("Downloaded Successfully");
	}, 1500);
}
//FOR CSV DOWNLOAD

document.addEventListener('DOMContentLoaded', function() {
	var mytabprev = '';
	var ajaxRequest;
	var intervalingopt;
	var counteringforoptions = <?=((ceil(0/100)>0)?ceil(0/100):1)?>;
	intervalingopt = setInterval(function() {
		if ((counteringforoptions <= 0)&&($('#myTables').html()!='')) {
			if(mytabprev==''){
				$('.timeroptionstext').text('Nothing To Download.');
			}
			else{
				clearInterval(intervalingopt);
				$('.timeroptionstext').text('Please wait just a few seconds.');
			}
		}
		else{
			counteringforoptions--;
			$('.timeroptionstext').text('Please wait '+counteringforoptions+' Seconds');
		}
	}, 1000);
<?php
	if($sqlviewreport['printtype']=='1'){
?>
	function makeAjaxRequest() {
		ajaxRequest = $.ajax({
			type: "GET",
			url: 'check.php?term=stockandsales&datefrom=<?=$datefrom?>&dateto=<?=$dateto?>&limitfrom=0&limitto=10000&commision=<?=$_GET['commision']?>&category=<?=$_GET['category']?>',
			success: function (result) {
				if (result != mytabprev) {
					$('#myTables').append(result);
					mytabprev = result;
					$('.hidtillajax').css('display', 'inline-block');
					$('#showoptionstimer').css('display', 'none');
					console.log('finish');
				}
			},
			error: function (error) {
				console.log(error);
			}
		});
	}
	makeAjaxRequest();
<?php
	}
?>
	$(window).on('beforeunload', function(){
		if (typeof intervalingopt !== 'undefined') {
			clearInterval(intervalingopt);
		}
		if (typeof ajaxRequest !== 'undefined' && ajaxRequest) {
			ajaxRequest.abort();
		}
		if (typeof ajaxRequestview !== 'undefined' && ajaxRequestview) {
			ajaxRequestview.abort();
		}
	});
});
var buttons = document.querySelectorAll('.arlina-button');
Array.prototype.slice.call(buttons).forEach(function(button) {
	var resetTimeout;
	button.addEventListener('click', function() {
		if (typeof button.getAttribute('data-loading') === 'string') {
			button.removeAttribute('data-loading');
		}
		else {
			button.setAttribute('data-loading', '');
		}
		clearTimeout(resetTimeout);
		resetTimeout = setTimeout(function() {
			button.removeAttribute('data-loading');
		}, 1000);
	}, false);
});
//FOR BUTTON SPINNER

$(document).ready(function() {
	reportperiodfun("<?=$sqlviewreport['reportperiod']?>");
});
function reportperiodfun(val) {
	var selectedValue = val;
	var endDate = new Date();
	var startDate = new Date();
	switch (selectedValue) {
		case "all":
			startDate.setDate(1);
			startDate.setMonth(0);
			startDate.setYear(2000);
			// endDate.setDate(endDate.getDate() - 1);
			break;
		case "thisweek":
			startDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() - startDate.getDay());
			endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 6);
			break;
		case "thismonth":
			startDate.setDate(1);
			endDate.setMonth(endDate.getMonth() + 1, 0);
			break;
		case "thisquarter":
			startDate = new Date(startDate.getFullYear(), Math.floor(startDate.getMonth() / 3) * 3, 1);
			endDate = new Date(startDate.getFullYear(), Math.floor(startDate.getMonth() / 3) * 3 + 3, 0);
			break;
		case "thisyear":
			startDate = new Date(startDate.getFullYear(), 0, 1);
			endDate = new Date(startDate.getFullYear(), 11, 31);
			break;
		case "yesterday":
			startDate.setDate(startDate.getDate() - 1);
			endDate.setDate(endDate.getDate() - 1);
			break;
		case "lastweek":
			startDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() - startDate.getDay() - 7);
			endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() - startDate.getDay() + 6);
			break;
		case "lastmonth":
			startDate = new Date(startDate.getFullYear(), startDate.getMonth() - 1, 1);
			endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
			break;
		case "lastquarter":
			startDate = new Date(startDate.getFullYear(), Math.floor(startDate.getMonth() / 3) * 3 - 3, 1);
			endDate = new Date(startDate.getFullYear(), Math.floor(startDate.getMonth() / 3) * 3 + 3, 0);
			break;
		case "lastyear":
			startDate = new Date(startDate.getFullYear() - 1, 0, 1);
			endDate = new Date(startDate.getFullYear() , 11, 31);
			break;
		default:
			// Default to "Today"
			break;
	}
	$("#from").val(formatDateSwitch(startDate));
	$("#to").val(formatDateSwitch(endDate));
}
function formatDateSwitch(date) {
	var day = date.getDate();
	var month = date.getMonth() + 1;
	var year = date.getFullYear();
	return  (day < 10 ? '0' : '') + day + '-' + (month < 10 ? '0' : '') + month + '-' + year ;
}
//FOR CUSTOMIZE DATE
</script>
<style type="text/css">
.hidtillajax{
	display: none;
}
/**************************************	BUTTON BASE		**************************************/

.arlina-button {
	position: relative;
	border: 0;
	cursor: pointer;
	outline: 0;
	-webkit-appearance: none;
	-webkit-font-smoothing: antialiased;
	-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}

.arlina-button[data-loading] {
	cursor: default;
}

/* Blue button */
.arlina-button.blue {
	background: #53b5e6;
	color: #fff;
	border-radius: 2px;
	border: 1px solid transparent;
}

.arlina-button.blue:hover {
	border-color: rgba(0, 0, 0, 0.07);
	background-color: #58c2f8;
}

.arlina-button.blue[data-loading] {
	border-color: rgba(0, 0, 0, 0.07);
	background-color: #999;
}

/* Orange button */
.arlina-button.orange {
	background: #ea8557;
	color: #fff;
	border-radius: 2px;
	border: 1px solid transparent;
}

.arlina-button.orange:hover {
	border-color: rgba(0, 0, 0, 0.07);
	background-color: #ffa96c;
}

.arlina-button.orange[data-loading] {
	border-color: rgba(0, 0, 0, 0.07);
	background-color: #999;
}

/* Spinner animation */
.arlina-button .spinner {
	position: absolute;
	width: 20px;
	height: 20px;
	top: 50%;
	margin-top: -10px;
	opacity: 0;
	background-image: url("./assets/img/spin.gif");
	background-repeat: no-repeat;
	/* background-image: url(http://2.bp.blogspot.com/-GPSLDnKmX3s/VSvPkXsCHvI/AAAAAAAACOg/Xmm2kIDu-CU/s1600/spin.gif); */
}

/**************************************	EASING	**************************************/

.arlina-button,
.arlina-button .spinner,
.arlina-button .label {
	-webkit-transition: 0.3s cubic-bezier(0.175, 0.885, 0.320, 1.275) all;
	-moz-transition: 0.3s cubic-bezier(0.175, 0.885, 0.320, 1.275) all;
	-ms-transition: 0.3s cubic-bezier(0.175, 0.885, 0.320, 1.275) all;
	transition: 0.3s cubic-bezier(0.175, 0.885, 0.320, 1.275) all;
}

.arlina-button.zoom-in,
.arlina-button.zoom-in .spinner,
.arlina-button.zoom-in .label,
.arlina-button.zoom-out,
.arlina-button.zoom-out .spinner,
.arlina-button.zoom-out .label {
	-webkit-transition: 0.3s ease all;
	-moz-transition: 0.3s ease all;
	-ms-transition: 0.3s ease all;
	transition: 0.3s ease all;
}

/**************************************	EXPAND RIGHT	**************************************/

.arlina-button.expand-left .spinner {
	left: 0.8em;
}

.arlina-button.expand-left[data-loading] {
	padding-left: 40px;
}

.arlina-button.expand-left[data-loading] .spinner {
	opacity: 1;
}

.macsyl{
	animation-name: repone;
	animation-duration: 1.5s;
	animation-iteration-count: infinite;
	animation-timing-function: linear;
}
@keyframes repone {
	from {
		transform:rotate(0deg);
	}
	to {
		transform:rotate(180deg);
	}
}
@media screen and (max-width: 1199px){
	#reparup{
		margin-left: -8.9px !important;
	}
}
/*FOR DROPDOWN STYLING*/

.insidecard{
	 width: max-content !important;
	 height: max-content !important;
}
            @-moz-document url-prefix() {
@media screen and (min-device-width: 100px) and (max-device-width: 350px) {
#templatetext{
margin-top: -650px !important;
position:relative;
left:-30px;
}
}
@media screen and (min-device-width: 351px) and (max-device-width: 430px) {
#templatetext{
margin-top: -560px !important;
position:relative;
left:-30px;
}
}
@media screen and (min-device-width: 431px) and (max-device-width: 500px) {
#templatetext{
margin-top: -670px !important;
position:relative;
left:-30px;
}
}
@media screen and (min-device-width: 501px) and (max-device-width: 580px) {
#templatetext{
margin-top: -560px !important;
position:relative;
left:-30px;
}
}
@media screen and (min-device-width: 581px) and (max-device-width: 600px) {
#templatetext{
margin-top: -500px !important;
position:relative;
left:-70px;
}
}
@media screen and (min-device-width: 601px) and (max-device-width: 767px) {
#templatetext{
margin-top: -350px !important;
position:relative;
left:-70px;
}
}
@media screen and (min-device-width: 768px) and (max-device-width: 1300px) {
#templatetext{
margin-top: -262px !important;
position:relative;
left:-100px;
}
}
@media screen and (min-device-width: 1301px) and (max-device-width: 1400px) {
#templatetext{
margin-top: -160px !important;
}
}
@media screen and (min-device-width: 1401px) and (max-device-width: 1500px) {
#templatetext{
margin-top: 100px !important;
}
}
@media screen and (min-device-width: 1501px) and (max-device-width: 3000px) {
#templatetext{
margin-top: 60px !important;
}
}
            @media screen and (min-device-width: 100px) and (max-device-width: 768px) {
                .totalleftside{
                    font-size: 10px !important;
                }
            }
            @media screen and (min-device-width: 100px) and (max-device-width: 259px) {
                #zoomforprint{
                    transform: scale(3%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 260px) and (max-device-width: 270px) {
                #zoomforprint{
                    transform: scale(3%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 271px) and (max-device-width: 280px) {
                #zoomforprint{
                    transform: scale(5%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 281px) and (max-device-width: 290px) {
                #zoomforprint{
                    transform: scale(6%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 291px) and (max-device-width: 320px) {
                #zoomforprint{
                    transform: scale(10%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 321px) and (max-device-width: 330px) {
                #zoomforprint{
                    transform: scale(20%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 331px) and (max-device-width: 350px) {
                #zoomforprint{
                    transform: scale(21%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 351px) and (max-device-width: 380px) {
                #zoomforprint{
                    transform: scale(22%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 381px) and (max-device-width: 400px) {
                #zoomforprint{
                    transform: scale(23%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 401px) and (max-device-width: 430px) {
                #zoomforprint{
                    transform: scale(24%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 431px) and (max-device-width: 460px) {
                #zoomforprint{
                    transform: scale(25%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 461px) and (max-device-width: 490px) {
                #zoomforprint{
                    transform: scale(26%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 491px) and (max-device-width: 500px) {
                #zoomforprint{
                    transform: scale(27%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 501px) and (max-device-width: 520px) {
                #zoomforprint{
                    transform: scale(33%);
                    transform-origin: 0 0;
                }
            }
           @media screen and (min-device-width: 521px) and (max-device-width: 550px) {
                #zoomforprint{
                    transform: scale(35%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 551px) and (max-device-width: 580px) {
                #zoomforprint{
                    transform: scale(38%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 581px) and (max-device-width: 600px) {
                #zoomforprint{
                    transform: scale(40%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 601px) and (max-device-width: 767px) {
                #zoomforprint{
                    transform: scale(45%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 768px) and (max-device-width: 800px) {
                #zoomforprint{
                    transform: scale(57%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 801px) and (max-device-width: 1199px) {
                #zoomforprint{
                    transform: scale(63%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 1200px) and (max-device-width: 1300px) {
                #zoomforprint{
                    transform: scale(57%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 1301px) and (max-device-width: 1400px) {
                #zoomforprint{
                    transform: scale(65%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 1401px) and (max-device-width: 1500px) {
                #zoomforprint{
                    transform: scale(75%);
                    transform-origin: 0 0;
                }
            }
            @media screen and (min-device-width: 1501px) and (max-device-width: 1700px) {
                #zoomforprint{
                    transform: scale(80%);
                    transform-origin: 0 0;
                }
            }
            }
@supports (not (-moz-appearance:button)) and (contain:paint) and (-webkit-appearance:none) { 
	 @media screen and (min-device-width: 1px) and (max-device-width: 199px) {
		  #zoomforprint{
				zoom: 5% !important;
				margin-left: -10px !important;
		  }
		  #templatetext{
				margin-right: -15px !important;
		  }
	 }
	 @media screen and (min-device-width: 200px) and (max-device-width: 249px) {
		  #zoomforprint{
				zoom: 10% !important;
		  }
		  #templatetext{
				margin-right: -6px !important;
		  }
	 }
	 @media screen and (min-device-width: 250px) and (max-device-width: 299px) {
		  #zoomforprint{
				zoom: 15% !important;
		  }
		  #templatetext{
				margin-right: 0px !important;
		  }
	 }
	 @media screen and (min-device-width: 300px) and (max-device-width: 349px) {
		  #zoomforprint{
				zoom: 20% !important;
		  }
		  #templatetext{
				margin-right: -3px !important;
		  }
	 }
	 @media screen and (min-device-width: 350px) and (max-device-width: 399px) {
		  #zoomforprint{
				zoom: 25% !important;
		  }
		  #templatetext{
				margin-right: -6px !important;
		  }
	 }
	 @media screen and (min-device-width: 400px) and (max-device-width: 459px) {
		  #zoomforprint{
				zoom: 30% !important;
		  }
		  #templatetext{
				margin-right: -6px !important;
		  }
	 }
	 @media screen and (min-device-width: 460px) and (max-device-width: 529px) {
		  #zoomforprint{
				zoom: 35% !important;
		  }
		  #templatetext{
				margin-right: 9px !important;
		  }
	 }
	 @media screen and (min-device-width: 530px) and (max-device-width: 589px) {
		  #zoomforprint{
				zoom: 40% !important;
		  }
		  #templatetext{
				margin-right: 9px !important;
		  }
	 }
	 @media screen and (min-device-width: 590px) and (max-device-width: 639px) {
		  #zoomforprint{
				zoom: 45% !important;
		  }
		  #templatetext{
				margin-right: 9px !important;
		  }
	 }
	 @media screen and (min-device-width: 640px) and (max-device-width: 699px) {
		  #zoomforprint{
				zoom: 50% !important;
		  }
		  #templatetext{
				margin-right: 0px !important;
		  }
	 }
	 @media screen and (min-device-width: 700px) and (max-device-width: 760px) {
		  #zoomforprint{
				margin-left: 25px !important;
				zoom: 55% !important;
		  }
		  #templatetext{
				margin-right: 0px !important;
				zoom: 55% !important;
		  }
	 }
	 @media screen and (min-device-width: 761px) and (max-device-width: 829px) {
		  #zoomforprint{
				margin-left: 25px !important;
				zoom: 60% !important;
		  }
		  #templatetext{
				margin-right: 0px !important;
				zoom: 60% !important;
		  }
	 }
	 @media screen and (min-device-width: 830px) and (max-device-width: 890px) {
		  #zoomforprint{
				margin-left: 25px !important;
				zoom: 65% !important;
		  }
	 }
	 @media screen and (min-device-width: 891px) and (max-device-width: 991.7px) {
	 		#zoomforprint{
				margin-left: 35px !important;
				zoom: 70% !important;
		  }
		  #templatetext{
				margin-right: 25px !important;
				zoom: 70% !important;
		  }
	 }
	 @media screen and (min-device-width: 992px) and (max-device-width: 1020px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -123px !important;
		  }
		  #templatetext{
				margin-right: -90px !important;
		  }
	 }
	 @media screen and (min-device-width: 1021px) and (max-device-width: 1199px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -111px !important;
		  }
		  #templatetext{
				margin-right: -90px !important;
		  }
	 }
	 @media screen and (min-device-width: 1200px) and (max-device-width: 1220px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -111px !important;
		  }
		  #templatetext{
				margin-right: -111px !important;
		  }
	 }
	 @media screen and (min-device-width: 1221px) and (max-device-width: 1250px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -100px !important;
		  }
		  #templatetext{
				margin-right: -100px !important;
		  }
	 }
	 @media screen and (min-device-width: 1251px) and (max-device-width: 1290px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -90px !important;
		  }
		  #templatetext{
				margin-right: -90px !important;
		  }
	 }
	 @media screen and (min-device-width: 1291px) and (max-device-width: 1330px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -80px !important;
		  }
		  #templatetext{
				margin-right: -80px !important;
		  }
	 }
	 @media screen and (min-device-width: 1331px) and (max-device-width: 1360px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -70px !important;
		  }
		  #templatetext{
				margin-right: -70px !important;
		  }
	 }
	 @media screen and (min-device-width: 1361px) and (max-device-width: 1400px) {
		  #zoomforprint{
				zoom: 70% !important;
				margin-left: -60px !important;
		  }
		  #templatetext{
				margin-right: -60px !important;
		  }
	 }
	 @media screen and (min-device-width: 1401px) and (max-device-width: 1500px) {
		  #zoomforprint{
				zoom: 70% !important;
		  }
		  #templatetext{
				margin-right: -54px !important;
		  }
	 }
	 @media screen and (min-device-width: 1501px) and (max-device-width: 1549px) {
		  #zoomforprint{
				zoom: 70% !important;
		  }
		  #templatetext{
				margin-right: -45px !important;
		  }
	 }
	 @media screen and (min-device-width: 1550px) and (max-device-width: 1599px) {
		  #zoomforprint{
				zoom: 80% !important;
	 }
		  #templatetext{
				margin-right: 27px !important;
		  }
	 }
	 @media screen and (min-device-width: 1600px) and (max-device-width: 1799px) {
		  #zoomforprint{
				zoom: 90% !important;
	 }
		  #templatetext{
				margin-right: 27px !important;
		  }
	 }
	 @media screen and (min-device-width: 1800px) and (max-device-width: 3000px) {
		  #zoomforprint{
				zoom: 100% !important;
	 }
		  #templatetext{
				margin-right: 27px !important;
		  }
	 }
}
/*FOR VIEW RESPONSIVE*/
</style>
<script type="text/javascript">
<?php
	if (0==0) {
?>
	$("#csvdownopt").hide();
<?php
	}
?>
</script>
</body>
</html>