<!DOCTYPE HTML>
<html>
<head>
<title>Download QR Code</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-size:14px;font-family: sans-serif;}
.qrWrapCode{
	    text-align: center;
		margin-top:50;
		padding:15px;
}

.overlay{width:100%;}
.overlay img{margin: 0 auto; max-width:100%;}


</style>
</head>

<body>
	<div class="qrWrapCode">
	<div class=""><h4>Need your consent to activate the QR code </h4>
	</div>
	<div class="">
	<div class="checkbox">
  <label><input type="checkbox" class="chkbox is_agree" name="recieve" value="2">I agree to receive news and promotions from <?php echo $brnd['brand_name']?> and its parent company.</label>
</div>
		<div class="checkbox">
  <label><input type="checkbox" class="chkbox is_chk" name="confirm" value="1"> I confirm I am over 18 years old and I agree to the T&C and privacy policy of <a href="https://isamplez.com/" target="_blank">isamplez </a></label>
</div>

	</div>
	<h5>
	Valid from <?php echo date("d M y", strtotime($dates->start_date)); ?>  -  <?php echo date("d M y", strtotime($dates->end_date)); ?>
					</h5>
	<div class="overlay" ><img src="https://isamplez.com/images/qr-code.png" > </div>
	
	<!--<p class=>By using this code I am giving consent to share my data with the owner of this brand in accordance with the privacy policy of <a href="https://isamplez.com/" target="_blank">iSamplez.com</a>.</p>-->
	
	</div>
	
</body>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script>
  $(document).ready(function(){
	  var imgurl='<?php echo $url; ?>'
	   var imgurlduo='https://isamplez.com/images/qr-code.png'
        $('input[type="checkbox"]').click(function(){
            var length= $('.is_chk:checked').length;
			var lengthagr= $('.is_agree:checked').length;
			if(lengthagr==1)
			{
				$.ajax({
				  method:'post',
				  url: "<?php echo base_url().'ChatPot/update_consent'?>",
				  cache: false,
				  data:{user_id:'<?php echo $user_id;?>',sample_id:'<?php echo $sample_id;?>'},
				  success: function(html){
					
				  }
				});
			}
			if(length==1)
			$('.overlay').children('img').attr('src',imgurl);
			else
			$('.overlay').children('img').attr('src',imgurlduo);
			
        });
    });
</script>
</html>