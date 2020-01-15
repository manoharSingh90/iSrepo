<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>iSamplez: Brand Details</title>
<link rel="shortcut icon" type="image/png" href="assets/img/favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,400i,500,500i,600" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/isamplez_global.css">
<style>
.form-control { border-radius: 0; }
</style>
</head>

<div class="container-fluid pl-0 pr-0 bg-light">
  <div class="row justify-content-center no-gutters">
  
     <!-- <div class="col-12 pt-2 text-right">
	  <a href="javascript:;" onclick="history.go(-1);" class="btn-link text-primary btn-sm btn">Back</a>
</div>-->
    <div class="col-12">
      <h2 class=" pl-3 pr-3 pt-3 pb-0 m-0 bg-light text-uppercase small text-dark">About Us</h2>
      <div class="pl-3 pr-3 pt-3 pb-4 mt-2 mb-2 bg-white font-normal ">
        <p><?php echo $campDtl[0]->brand_desc;?></p>
      </div>
    </div>
    <div class="col-12">
      <h2 class="pt-3 pl-3 pr-3 pb-0 m-0 bg-light text-uppercase small text-dark">Our Products</h2>
      <div class="mt-2 mb-2 bg-white pl-1 pr-1 font-normal ">
        <div class="brandDetail-gallery pt-2 pb-2 pl-4 pr-4">
          <ul id="gallerySilder" class="owl-carousel owl-theme ownCarousel parent-container ">
            <?php if($media) {
            foreach ($media as $value) { ?>
              <?php if($value->asset_type=='1' && file_exists('assets/brand/assets/'.$value->asset_url) ){ ?> <li class="item img-link d-inline-block" data-width="640" data-height="360" data-src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>"><img src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>" alt="thumbnail"></li>
			  
            <?php } elseif ($value->asset_type=='2' && file_exists('assets/brand/assets/'.$value->asset_url)) { ?>
			 <li class="item img-link" data-width="640" data-height="360" data-src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>">
              <video>
                <source src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>" type="video/mp4">
              </video>
            </li>
             
              <?php } ?>
            <?php } } else echo "No record exists.";   ?>
           
           
            
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- SCRIPT --> 
<!-- SCRIPT --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script> 

<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script> 
<script type="text/javascript" src="<?php echo base_url();?>assets/dependencies/Small-jQuery-Video-Image-Lightbox-Plugin-MediaBox/js/jquery.media.box.js"></script> 
<script type="text/javascript">
(function($) {
    'use strict';

    $(".deactiveCheck").each(function() {
        $(this).on("change", function() {
            if ($(this).is(":checked")) {
                $(this).closest("tr").addClass("disabled");
            } else {
                $(this).closest("tr").removeClass("disabled");
            }
        });
    });

    $('#gallerySilder').owlCarousel({
        margin: 10,
        nav: true,
        autoWidth: true,
        dots: false,
        responsive: {
            0: {
                items: 3,
                nav: true,
            },
            768: {
                items: 6,
            }

        }
    });

      $('.img-link').mediaBox({
        closeImage: 'media/close.png',
        openSpeed: 1000,
        closeSpeed: 800
      });

})(jQuery);
</script>
</body></html>
