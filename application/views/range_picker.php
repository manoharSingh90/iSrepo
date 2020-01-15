<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title;?></title>
<link rel="shortcut icon" type="image/png" href="<?php //echo base_url('assets/img/favicon.png');?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,400i,500,500i,600" rel="stylesheet">
<!-- <link rel="stylesheet" href="<?php echo base_url();?>assets/dependencies/DonutWidget/dist/jquery.DonutWidget.min.css"> -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/isamplez_global.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.1.0/jquery-migrate.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/popper/popper.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/bootstrap-4.3.1/dist/js/bootstrap.min.js"></script> 
<!--<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/jquery-validation-master/dist/jquery.validate.min.js"></script> -->
<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/jquery.confetti.js-master/jquery.confetti.js"></script>  -->
<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/Small-jQuery-Video-Image-Lightbox-Plugin-MediaBox/js/jquery.media.box.js"></script> -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js"></script>  
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/jquery-date-range-picker-master/dist/jquery.daterangepicker.min.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/cropper-master/dist/cropper.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/jquery.tableScroll-master/jquery.tablescroll.js"></script> --> 
<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/floatThead/dist/jquery.floatThead.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/DonutWidget/dist/jquery.DonutWidget.min.js"></script>
 -->

</head>

<body>
<header>
  <h1><img src="<?php echo base_url();?>assets/img/isamplez_logo.png" alt="#"/></h1>
  <ul>
    <li> <a href="<?php echo base_url('brands') ;?>" class="<?php if($this->session->userdata('menu')=='Brands') echo 'active';  ?>" ><img src="<?php echo base_url();?>assets/img/icons/brand_icon.png" alt="#"/><span>Brands</span></a> </li>
    <li> <a href="<?php echo base_url('users-list') ;?>" class="<?php if($this->session->userdata('menu')=='Users') echo 'active';  ?>"><img src="<?php echo base_url();?>assets/img/icons/user_icon.png" alt="#"/><span>Users</span></a> </li>
    <li> <a href="<?php echo base_url('report') ;?>" class="<?php if($this->session->userdata('menu')=='Report') echo 'active';  ?>"><img src="<?php echo base_url();?>assets/img/icons/report_icon.png" alt="#"/><span>Reports</span></a> </li>
  </ul>
</header>
<!-- <div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle">All <?php echo $title;?></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
  </div> -->
<div class="pageArea">
	<div class="pageHeader clearfix">
		<h2 class="float-left pageTitle"><span class="ml-2"><?= $title?></span></h2>
		<div class="float-right pageAction">
			<h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
			| <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#" /></a> </div>
		</div>
		<div class="pageTabs mt-1 extrawWide">
			<ul>
				<li><a href="<?php echo base_url('report') ;?>" class="active">Users</a></li>
				<li><a href="<?php echo base_url('campaign-report');?>">Campaigns</a></li>
				<li><a href="<?php echo base_url('vending-machine-report');?>">Vending Machines</a></li>
			</ul>
		</div>
		<div class="createInfo">
			<div class="row pb-2 align-items-center">
				<div class="col-12 col-md-3 col-lg-3">
					<label class="col-form-label-sm">Start Date</label>
					<input id="startDateInput" type="text" value=" " name="startValDate" autocomplete="off" class="form-control dateIcon sddsasdasdasd" />

				</div>
				<div class="col-12 col-md-3 col-lg-3">
					<label class="col-form-label-sm ">End Date</label>
					<input id="endDateInput" type="text" value=" " name="endValDate" autocomplete="off"  class="form-control dateIcon" />
				</div>
	
			
			</div>
		</div>




<script type="text/javascript">
 $(document).ready(function() {
    'use strict';
   
    // DATE PICKER
    var todayDate = moment().format('DD-MM-YYYY');
    
    $('#endDateInput').attr('data-date', todayDate);

    
    $('#startDateInput').dateRangePicker({
        format: 'DD-MM-YYYY',
        autoClose: true,
        singleDate: true,
        showTopbar: false,
        singleMonth: true,
        selectBackward:true ,
      	stickyMonths: true,
       selectForward: false,
        endDate: todayDate,

        setValue: function(s) {
            if (!$(this).attr('readonly') && !$(this).is(':disabled') && s != $(this).val()) {
                $(this).val(s);
                $('#endDateInput').attr('data-date', s).val('');
            }
        },
    });

    $(document).on('focus', '#endDateInput', function(e) {
        //var defaultDate = $(this).val();
        var startDate = $(this).attr('data-date');
        if (startDate == '') {
            var startDate = $(this).attr('data-date', todayDate);
        } else {
            var startDate = $(this).attr('data-date');
        }
        $(this).dateRangePicker({
            format: 'DD-MM-YYYY',
            autoClose: true,
            singleDate: true,
            showTopbar: false,
            singleMonth: true,
            changeMonth: true,
            selectBackward: false,
            selectForward: true,
            startDate: startDate,
            endDate: todayDate
        });
    });


});
</script> 
