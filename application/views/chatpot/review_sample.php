<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>iSamplez: Products Review</title>
<link rel="shortcut icon" type="image/png" href="assets/img/favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,400i,500,500i,600" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/isamplez_global.css">
<style>
.form-control { border-radius: 0; }
.rangeDiv { position: relative; }
.currentValue { padding: 0; text-align: center; color: #333; position: absolute; top: 100%; left: 50%; margin-left: -12px; width: 24px; display: none; }
</style>
</head>

<div class="container-fluid pl-0 pr-0 bg-light">
  <form class="row justify-content-center no-gutters" id="submit_review">
	<input type="hidden" name="compain_id" value="<?php echo $campaign_id?>">
	<input type="hidden" name="fb_id" value="<?php echo $fb_id?>">
		<div class="col-12">
		  <h2 class=" pl-3 pr-3 pt-3 pb-0 m-0 bg-light text-uppercase small text-dark">Rate the Product</h2>
		  <div class="pl-3 pr-3 pt-3 pb-4 mt-2 mb-2 bg-white text-center">
			<div class="d-flex pb-1 ">
			  <label class="small text-dark m-0 pr-2">0</label>
			  <div class="rangeDiv w-100">
				<input id="rateRange" name="range" type="range" value="0" class="w-100" min="0" step=".5" max="10" />
				<span id="slider_value" class="currentValue small text-dark">NA</span> </div>
			  <label class="small text-dark m-0 pl-2">10</label>
			</div>
		  </div>
		</div>
		<?php foreach($qsnansr as $k=>$qsnans){  ?>
		<div class="col-12">
		  <h2 class="pt-3 pl-3 pr-3 pb-0 m-0 bg-light text-uppercase small text-dark"><?php echo $qsnans['qsn']->ques_text; ?></h2>
		   <input type="hidden" name="qsn_id[]" class="custom-control-input" value="<?php echo $qsnans['qsn']->id; ?>">
		  <div class=" mt-2 mb-2 bg-white">
			<ul>
			<?php 
			$name='ansver_'.$qsnans['qsn']->id.'[]';
			if($qsnans['qsn']->ques_type==1)
			{
				$type="radio";
			}
			else
			{
				$type="checkbox";
			}
			foreach($qsnans['ans'] as $ans){
			?>
			  <li class="pt-2 pb-2 pl-3 pr-3 border-bottom">
				<div class="custom-control custom-<?php echo $type;?>">
				  <input type="<?php echo $type;?>" name="<?php echo $name?>" class="custom-control-input" id="custom-Check-<?php echo $ans->id;?>" value="<?php echo $ans->id;?>">
				  <label class="custom-control-label small text-dark" for="custom-Check-<?php echo $ans->id;?>"><?php echo $ans->answer_text; ?></label>
				</div>
			  </li>
			<?php } ?>
			</ul>
		  </div>
		</div>
		<?php } if($is_email==0){ ?>
		<div class="col-12">
		  <h2 class=" pl-3 pr-3 pt-3 pb-0 m-0 bg-light text-uppercase small text-dark">Email Id</h2>
		  <div class="pl-3 pr-3 pt-1 pb-1 mt-2 mb-2 bg-white text-center">
		  <input type="text"  name="emai_id" class="form-control" id="emai_id">
			
		  </div>
		</div>
		<?php } ?>
		<div class="col-12">
		  <h2 class=" pl-3 pr-3 pt-3 pb-0 m-0 bg-light text-uppercase small text-dark">Further Comments</h2>
		  <div class="pl-3 pr-3 pt-1 pb-1 mt-2 mb-2 bg-white text-center">
			<textarea name="comment" class="form-control border-0 bg-white pl-1 pr-1" placeholder="Write your comments here.."></textarea>
		  </div>
		</div>		
		<div class="col-12 text-center bg-white">
			<small class="text-danger error"> </small>
		</div>
		<div class="col-12">
		  
		  <div class="pl-3 pr-3 pt-1 pb-1 mt-2 mb-2 bg-white text-center">
			<div class="alert alert-success review_submit" role="alert" style="display:none">
			 
			</div>
			 <?php if($is_review['is_review']==0){ ?>
			<button class="btn btn-style mt-3 submit" type="button">Submit</button>
			 <?php } else{ ?> <div class="alert alert-info" role="alert">
  You have already given the Review on this sample.
</div><?php } ?>
		  </div>
		</div>
	</form>
</div>
<!-- SCRIPT --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script> 
<script type="text/javascript">
(function($) {
    'use strict';
	
	$(document).on('change', '#rateRange', function() {
		var currentVal = $(this).val();
		var currentWidth = ($(this).val() * 100)/10
		$('#slider_value').html($(this).val()).show().css('left',currentWidth+'%');
	});
	$(document).on('click', '.submit', function() {
		$('.error').text('');
		var totalRadio=$('input[type="radio"]:checked').length; 
		var total=$('input[type="checkbox"]:checked').length;
		if(total==0 && totalRadio==0)
		{
			$('.error').text('Please select atleast one answer');
			return false;
		}
		else if('<?php echo $is_email?>'==0)
		{
			if( $('#emai_id').val()=='')
			{
				$('.error').text('Please enter your email id');
				return false;
			}
			else if( isEmail($('#emai_id').val())==false) 
			{
				$('.error').text('Please enter valid email id');
				return false;
			}
		}
		$('.submit').text('Please wait...');
		$.ajax({
				url : "<?php echo base_url(); ?>"+'ChatPot/review_sample',
				type :'POST',
				data : $('#submit_review').serialize(),
				success: function(){ 
					$('.submit').hide();
					$('.review_submit').text('Your review submitted successfully.');
					$('.review_submit').show();
				}
		});	
	});
	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
})(jQuery);
</script>
</body></html>
