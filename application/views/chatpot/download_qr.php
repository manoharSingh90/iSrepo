<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link type="image/x-icon" href="images/favicon.png" rel="shortcut icon">
    <title>iSamplez</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800&display=swap" rel="stylesheet">

    <!-- STYLE -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/css/bootstrap-slider.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/css/star-rating.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/themes/krajee-uni/theme.css" crossorigin="anonymous">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: url(<?php echo base_url('assets/img/ChatPot/bg_ribbion.png'); ?>) no-repeat center top;
            background-size: contain;
        }
        
        .color-primary {
            color: #b21f79;
        }
        
        .btn {
            border-radius: 30px;
            letter-spacing: .2px;
            box-shadow: none;
        }
        
        .btn-primary {
            background: #b21f79;
            border-color: #b21f79;
        }
        
        .btn-primary:not(:disabled):not(.disabled).active,
        .btn-primary:not(:disabled):not(.disabled):active,
        .show>.btn-primary.dropdown-toggle,
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background: #750449;
            border-color: #750449;
            box-shadow: none;
        }
        
        .btn.btn-icon {
            text-align: left;
            position: relative;
            padding-left: 60px;
            padding-right: 45px;
            font-size: 18px;
            line-height: 18px;
        }
        
        .btn.btn-icon .icon {
            width: 30px;
            height: 30px;
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .btn small {
            text-transform: capitalize;
            font-size: 11px;
            font-weight: 600;
            display: block;
            line-height: 12px;
        }
        
        textarea {
            min-height: 140px;
        }
        
        .rating-md {
            font-size: 16px;
        }
        
        .rating-container .star {
            font-size: 20px;
            -webkit-text-stroke: .1px #e6a622;
            text-shadow: none;
            color: #e6a622;
        }
        
        .slick-dots li {
            margin: 0 2px;
        }
        
        .slick-dots li button:before {
            font-size: 10px;
        }
        
        .slick-next,
        .slick-prev {
            background: none;
            z-index: 1;
            width: 30px;
            height: 30px;
        }
        
        .slick-next:before,
        .slick-prev:before {
            font-size: 30px;
            opacity: 1;
            color: #999;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: none;
        }
        
        .slick-prev {
            left: -10px;
        }
        
        .slick-next {
            right: -10px;
        }
        
        .ratingSliderWrap {
            position: relative;
            width: 100%;
        }
        
        .ratingSliderWrap b {
            font-weight: normal;
            color: #999;
            position: absolute;
            top: 18px;
        }
        
        .ratingSliderWrap b:first-child {
            left: 0;
        }
        
        .ratingSliderWrap b:last-child {
            right: 0;
        }
        
        .slider.slider-horizontal {
            width: 100%;
        }
        
        .slider.slider-horizontal .slider-track {
            height: 4px;
            margin-top: -2px;
        }
        
        .slider-track {
            box-shadow: none;
            background: #ddd;
        }
        
        .tooltip.in {
            z-index: 1;
            opacity: 1;
        }
        
        .tooltip-arrow {
            position: absolute;
            width: 0;
            height: 0;
            border-color: transparent;
            border-style: solid;
        }
        
        .tooltip.bottom .tooltip-arrow {
            top: 0;
            left: 50%;
            margin-left: -5px;
            border-width: 0 5px 5px;
            border-top-color: #fff;
        }
        
        .slider-selection {
            background: #b21f79;
            box-shadow: none;
        }
        
        .tooltip-inner {
            color: #666;
            background: #fff;
        }
        
        .mainWrap {
            padding: 0;
        }
        
        .cmyLogo {
            padding: 20px 15px;
            margin: 0 auto;
            max-width: 200px;
            text-align: center;
        }
        
        .cmyLogo img {
            display: block;
            margin: 0 auto;
        }
        
        .activateTitle {
            padding: 10px 15px;
        }
        
        .activateTitle h1 {
            position: relative;
            padding: 0 2px;
            font-size: 26px;
            display: inline-block;
        }
        
        .activateTitle h1:before {
            content: "";
            background-image: url(https://isamplez.com/images/gift-box.svg);
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            width: 1.75rem;
            height: 1.75rem;
            left: 0;
            top: 0;
            padding: 0 1.2rem;
        }
        
        .activateTitle h1:after {
            content: "";
            position: absolute;
            left: 0;
            top: 40%;
            width: 100%;
            height: 80%;
            background: rgba(178, 31, 121, 0.1);
        }
        
        .activateTitle p {
            font-size: 18px;
            color: #495057;
            font-weight: 300;
            padding: 10px 0;
            margin-bottom: 0;
        }
        
        .checkWrap {
            max-width: 410px;
            margin: 0 auto;
            padding: 25px 15px;
        }
        
        .qrCode {
            max-width: 380px;
            margin: 0 auto;
            padding: 0px 15px;
        }
        
        .qrCode img {
            max-width: 100%;
            display: block;
        }
        
        .availableWrap {
            text-align: center;
            padding: 20px 5px;
            background: url(<?php echo base_url('assets/img/ChatPot/bg_bubble.jpg'); ?>) no-repeat center bottom;
        }
        
        .availableWrap h2 {
            font-size: 22px;
            font-weight: bold;
            padding: 15px 0;
            margin-bottom: 0;
        }
        
        .availableWrap ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 0;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .availableWrap ul li {
            width: 33.3%;
            padding: 15px;
            list-style: none;
            max-width: 160px;
        }
        
        .storeLogo {
            font-weight: 500;
            font-size: 16px;
            line-height: 16px;
            color: #212529;
        }
        
        .storeLogo .storeLogo-img {
            border: 1px solid #777;
            border-radius: 10px;
            background: #fff;
            margin-bottom: 10px;
            padding: 5px;
        }
        
        .storeLogo .storeLogo-img img {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .festivalWrap {
            text-align: center;
            padding: 20px 20px;
            border-top: 1px solid #ccc;
        }
        
        .festivalWrap h2 {
            font-size: 22px;
            font-weight: bold;
            padding: 20px 0;
            padding-top: 5px;
            margin-bottom: 0;
        }
        
        .festivalWrap p {
            color: #212529;
            font-size: 15px;
            margin-bottom: 0;
        }
        
        .imageSlider {
            padding: 20px 0;
        }
        
        .imageSlider img {
            display: block;
            margin: 0 auto;
        }
        
        .reviewWrap {
            text-align: center;
            padding: 20px 20px;
            background: #f8ebf4 url(<?php echo base_url('assets/img/ChatPot/bg_wave.jpg'); ?>) no-repeat center bottom;
        }
		
		.reviewWrap .reviewCrousel {
		margin:0 auto;
		max-width:620px;
		padding: 0 15px;
		}
        
        .reviewWrap h2 {
            font-size: 22px;
            font-weight: bold;
            padding: 20px 0;
            padding-top: 5px;
            margin-bottom: 0;
        }
        
        .reviewWrap p {
            color: #666;
            font-size: 15px;
        }
        
        .reviewWrap img {
            display: block;
            margin: 0 auto;
        }
        
        .feedbackWrap {
            padding: 20px;
            max-width: 540px;
            margin: 0 auto;
        }
        
        .rangeInput {
            display: block;
            width: 100%;
        }
        
        .slider-handle {
            background: #b21f79;
        }
        
        .socialWrap {
            padding: 25px 15px;
        }
        
        .reviewItem {
            padding: 30px;
            background: #fff;
            text-align: left;
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            margin: 10px 20px;
        }
        
        .reviewItem p {
            margin: 0;
        }
        
        .userBox {
            display: flex;
            align-items: center;
            padding-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .userBox .userBox-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }
        
        .userBox .userBox-img img {
            display: block;
            max-width: 100%;
        }
        
        .userBox p {
            font-weight: 700;
            color: #333;
            font-size: 18px;
            margin-bottom: 0;
            width: 100%;
        }
		.socialWrap-link .btn-primary{ min-width:222px;}
    </style>
</head>

<body>
    <div class="mainWrap">
        <div class="cmyLogo"><img src="https://isamplez.com/images/logo-isamplez.svg" alt="#" /> </div>
        <div class="text-center activateTitle">
            <h1 class="color-primary">Need your consent to activate <?php echo !empty($campaignData) ? $campaignData[0]["campaign_name"] : ""; ?> QR Code</h1>
            <p>Valid from <?php echo date("d M y", strtotime($dates->start_date)); ?>  -  <?php echo date("d M y", strtotime($dates->end_date)); ?> or till stocks last.</p>
        </div>

        <div class="checkWrap">
            <div class="custom-control custom-checkbox pb-4">
                <input type="checkbox" class="custom-control-input chkbox is_agree" id="receivenews" name="recieve" value="2">
                <label class="custom-control-label font-weight-bold" for="receivenews">I agree to receive news and promotions from <?php echo $brnd['brand_name']; ?> and its parent company.</label>
            </div>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input chkbox is_chk" id="confirmage" name="confirm" value="1">
                <label class="custom-control-label font-weight-bold" for="confirmage">I confirm I am over 18 years old and I agree to the T&C and Privacy Policy of <a href="https://isamplez.com/" target="_blank">iSamplez</a></label>
            </div>
        </div>

        <div class="qrCode overlay">
            <img src="https://isamplez.com/images/qr-code.png" alt="#" />
        </div>
		
		<?php
		if(!empty($campaignSamplesData)) { ?>
        <div class="availableWrap">
            <h2 class="color-primary">Available for purchase at:</h2>
            <ul>
				<!--<?php
				foreach($campaignSamplesData as $key => $value) { ?>
                <li>
                    <div class="storeLogo">
                        <div class="storeLogo-img"><img src="<?php echo $value["location_image"]; ?>" alt="#" /></div><?php// echo $value["location_name"]; ?>
					</div>
                </li>
				<?php } ?>-->
				<li>
                    <div class="storeLogo">
                        <div class="storeLogo-img"><a target="_blank" href="https://www.watsons.com.sg/apricot-invigorating-srubs-170g/p/BP_25819?text=st%20ives&page=0"><img src="https://isamplez.com/images/watson.png" alt="#" /></div></a><?php //echo @$value["location_name"]; ?>
					</div>
                </li>
				<li>
                    <div class="storeLogo">
                        <div class="storeLogo-img"><a target="_blank" href="https://www.guardian.com.sg/st-ives-fresh-skin-apricot-invigorating-scrub-/p/523873"><img src="https://isamplez.com/images/gradian.png" alt="#" style="width:72px;" /></div></a><?php //echo @$value["location_name"]; ?>
					</div>
                </li>
            </ul>
			
			
			
				<a href="https://c.lazada.sg/t/c.beh2?url=https%3A%2F%2Fwww.lazada.sg%2Fproducts%2Fst-ives-fresh-skin-apricot-scrub-170g-i282711089-s449266664.html&sub_aff_id=isamplez&sub_id1=webpage&sub_id2=app  " class="btn btn-primary btn-lg text-uppercase font-weight-bold pl-5 pr-5 mt-4 mb-3" target="_blank">Buy Now</a>
			
			
        </div>
		<?php } ?>

        <div class="festivalWrap">
            <h2 class="color-primary"><?php echo !empty($campaignData) ? $campaignData[0]["campaign_name"] : ""; ?></h2>
            <p><?php echo !empty($campaignData) ? $campaignData[0]["campaign_desc"] : ""; ?></p>
        </div>

        <div class="imageSlider">
            <div class="imgCrousel text-center">
			<div><img class="img-fluid" src="https://isamplez.com/images/St Ives Square-banner-4.jpg" alt="#" /></div>
			<div><img class="img-fluid" src="https://isamplez.com/images/St Ives Square-banner-3.jpg" alt="#" /></div>
			<div><img class="img-fluid" src="https://isamplez.com/images/St Ives Square-banner-2.jpg" alt="#" /></div>
			<div><img class="img-fluid" src="https://isamplez.com/images/Fb page2.jpg" alt="#" /></div>
				<?php
				/*if(!empty($campaignData))
				{
					foreach($campaignData as $key => $value)
					{ ?>
						<div><img class="img-fluid" src="<?php echo base_url('assets/campaign/banner/'.$value["banner_url"]); ?>" alt="#" /></div>
					<?php }
				}*/ ?>
            </div>
        </div>

        <div class="reviewWrap">
            <h2 class="color-primary">Ratings & Reviews</h2>
            <p>Avg. Rating <?php echo !empty($campaignData) ? $campaignData[0]["avg_rating"] : ""; ?></p>
            <div class="reviewCrousel text-center">
				<?php
				if(!empty($userReviewData))
				{
					foreach($userReviewData as $key => $value)
					{ ?>
						<div>
							<div class="reviewItem">
								<div class="userBox">
									<div class="userBox-img"><img class="img-fluid" src="<?php echo !empty($value["image"]) ? $value["image"] : base_url('assets/img/ChatPot/users/users.png'); ?>" alt="#" /></div>
									<div>
										<p><?php echo !empty($value["name"]) ? $value["name"] : ""; ?></p>
										<div class="d-block">
											<input name="ratingInput" class="ratingInput rating-loading" value="<?php echo $value["rating"]; ?>" data-min="0" data-max="5" data-step="0.5">
										</div>
									</div>
								</div>
								<p><?php echo !empty($value["review_text"]) ? $value["review_text"] : ""; ?></p>
							</div>
						</div>
					<?php }
				} ?>
            </div>
        </div>

        <div class="feedbackWrap users_reviews"  <?php echo $is_review==1?"style='display:none'":''?>>
            <form id="submit_review">
				<input type="hidden" name="compain_id" value="<?php echo $dates->campaign_id; ?>">
				<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <div class="form-group mb-5">
                    <label class="font-weight-bold text-dark mb-0">Rate the Product</label>
                    <div class="rangeInput">
                        <div id="ratingSliderWrap" class="ratingSliderWrap">
                            <b>1</b>
                            <input id="ratingVal" name="range" type="range" data-slider-id='ratingSliderWrap' type="text" data-slider-min="1" data-slider-max="10" data-slider-step="1" data-slider-value="1" data-slider-tooltip="show" />
                            <b>10</b>
                        </div>
                    </div>
                </div>
				
				<?php
				foreach($qsnansr as $k => $qsnans)
				{ ?>
                <div class="form-group mb-4">
					<input type="hidden" name="qsn_id[]" class="custom-control-input" value="<?php echo $qsnans['qsn']->id; ?>">
                    <label class="font-weight-bold text-dark"><?php echo $qsnans['qsn']->ques_text; ?></label>
					
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
					
					foreach($qsnans['ans'] as $ans)
					{ ?>
						<div class="custom-control custom-<?php echo $type; ?> pb-4">
							<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" class="custom-control-input" id="custom-Check-<?php echo $ans->id; ?>" value="<?php echo $ans->id; ?>">
							<label class="custom-control-label" for="custom-Check-<?php echo $ans->id; ?>"><?php echo $ans->answer_text; ?></label>
						</div>
					<?php } ?>
                </div>
				<?php } ?>

                <div class="form-group">
                    <label class="font-weight-bold text-dark">Further Comments</label>
                    <textarea class="form-control" name="comment" placeholder="Write your comments here."></textarea>
                </div>
				
				<div class="col-12 text-center bg-white">
					<small class="text-danger error"> </small>
				</div>
				
				<div class="col-12">
					<div class="pl-3 pr-3 pt-1 pb-1 mt-2 mb-2 bg-white text-center">
						
						<?php
						if($is_review['is_review']==0) { ?>
							<div class="pt-3 text-center">
								<button class="btn btn-primary text-uppercase font-weight-bold pl-4 pr-4 submit" type="button">Submit</button>
							</div>
						<?php }
						else { ?>
							<div class="alert alert-info" role="alert">You have already given the Review on this sample.</div>
						<?php } ?>
					</div>
				</div>
            </form>
        </div>
		<div class="alert alert-success review_submit text-center" role="alert" style="display:none"></div>
        <div class="socialWrap text-center">
            <p>To view more vending machine locations and all upcoming campaigns, Download the iSamplez App</p>
            <div class="socialWrap-link">
                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.isamplez.app" title="Download ON Google Play" class="btn btn-lg btn-primary btn-icon mr-2 ml-2 mb-3"> <span class="icon">
        <object type="image/svg+xml" data="https://isamplez.com/images/icons/play-store.svg?color=white"></object>
        </span> <small>Download ON</small> Google Play </a>
                <a target="_blank" href="https://apps.apple.com/app/id1480673072" title="Download ON App Store" class="btn btn-lg btn-primary btn-dark btn-icon mr-2 ml-2 mb-3 "><span class="icon">
                <object type="image/svg+xml" data="https://isamplez.com/images/icons/apple-logo.svg?color=white"></object>
                </span><small>Download ON </small>
                App Store</a>
            </div>
        </div>



    </div>

<!-- SCRIPT -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/bootstrap-slider.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/js/star-rating.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/themes/krajee-uni/theme.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
	$('.imgCrousel').slick({
		autoplay: true,
		arrows: false,
		dots: true
	});

	$('.reviewCrousel').slick({
		autoplay: true,
		arrows: true,
		dots: false,
		prevArrow: '<button type="button" class="slick-prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 35"><path class="arrow" stroke="#aaaaaa" stroke-width="2px" fill="none" fill-rule="evenodd" d="M20,31.8L5.8,17.9L20.6,2.7"/></svg></button>',
		nextArrow: '<button type="button" class="slick-next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 35"><path class="arrow" stroke="#aaaaaa" stroke-width="2px" fill="none" fill-rule="evenodd" d="M5.5,2.7l14.2,13.9L4.8,31.8"/></svg></button>',

	});

	$('#ratingVal').slider({
		formatter: function(value) {
			return value;
		},
		tooltip_position: 'bottom'
	});

	$('.ratingInput').rating({
		displayOnly: true,
		showCaption: false,
		theme: 'krajee-uni',
		filledStar: '&#x2605;',
		emptyStar: '&#x2606;'
	});
	
	var imgurl='<?php echo $url; ?>'
	var imgurlduo='https://isamplez.com/images/qr-code.png';
	$('input[type="checkbox"]').click(function()
	{
		var length= $('.is_chk:checked').length;
		var lengthagr= $('.is_agree:checked').length;
		if(lengthagr==1)
		{
			$.ajax({
				method:'post',
				url: "<?php echo base_url().'ChatPot/update_consent'?>",
				cache: false,
				data:{user_id:'<?php echo $user_id;?>',sample_id:'<?php echo $sample_id;?>'},
				success: function(html)
				{
				}
			});
		}
		if(length==1)
		$('.overlay').children('img').attr('src',imgurl);
		else
		$('.overlay').children('img').attr('src',imgurlduo);
	});
	
	$(document).on('click', '.submit', function()
	{
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
			success: function()
			{
				document.getElementById('submit_review').reset();
				$('.users_reviews').hide();
				$('.review_submit').text('Your review submitted successfully.');
				$('.review_submit').show();
			}
		});	
	});
	
	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}

});
</script>
</body>

</html>