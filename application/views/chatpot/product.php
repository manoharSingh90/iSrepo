<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>iSamplez: Products</title>
<link rel="shortcut icon" type="image/png" href="assets/img/favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,400i,500,500i,600" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/isamplez_global.css">
</head>

<body>
<div class="container-fluid pl-0 pr-0 bg-light">
  <div class="row justify-content-center no-gutters">
   <?php if($media) {
            foreach ($media as $value) {
      if($value->banner_type=='1' && file_exists('assets/campaign/banner/'.$value->banner_url) ){ ?>			
    <div class="col-12 text-center">
      <div class="p-3 mt-2 mb-2 bg-white"><img class="img-fluid" src="<?php echo base_url('assets/campaign/banner/'.$value->banner_url);?>" alt="#"/></div>
    </div>
   <?php } elseif ($value->banner_type=='2' && file_exists('assets/campaign/banner/'.$value->banner_url)) { ?>
	<div class="col-12 text-center">
  
  
	
	 <video controls style="max-width:100%;">
  <source src="<?php echo base_url('assets/campaign/banner/'.$value->banner_url);?>" type="video/mp4">
  
</video> 
  </div>

   <?php } } }?>
   
   <div class="col-8 text-center mb-5">
   <a class="btn btn-style mt-3" href="<?php echo base_url().'ChatPot/brand_review/'.$brand_id.'/'.$cmn_id?>">View Review</a>
   <a class="btn btn-style mt-3" href="<?php echo base_url().'ChatPot/brand_details/'.$brand_id.'/'.$cmn_id?>">View Brand Details</a>
   <a class="btn btn-style mt-3" href="<?php echo base_url().'ChatPot/review_sample/'.$fb_id.'/'.$cmn_id?>">Review samples</a>
   </div>
  </div>
</div>

</body>
</html>
