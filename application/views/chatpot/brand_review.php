<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>iSamplez: Brand Reviews</title>
<link rel="shortcut icon" type="image/png" href="assets/img/favicon.png" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,400i,500,500i,600" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/isamplez_global.css">
<style>
.form-control { border-radius: 0; }
.font-normal { font-size:14px;}
</style>
</head>

<div class="container-fluid pl-0 pr-0 bg-light">
  <div class="row justify-content-center no-gutters ">
 <!-- <div class="col-12 pt-2 text-right">
	  <a href="javascript:;" onclick="history.go(-1);" class="btn-link text-primary btn-sm btn">Back</a>
</div>-->
    <div class="col-12">
      <h2 class="pt-3 pl-3 pr-3 pb-0 m-0 bg-light text-uppercase small text-dark"><?php echo $campDtl[0]->campaign_name;?> Reviews</h2>
      <div class="mt-2 mb-2 bg-white pl-2 pr-2 font-normal ">
        <div class="p-2">
          <ul>
			<?php 
			if(!empty($reviewList)){
			foreach($reviewList as $rev){ ?>
            <li class="border-bottom pt-2 pb-2">
              <div class="reviewBox">
                <p class="m-0 pb-1 font-weight-bold "><?php echo $rev->name;?> (<?php echo $rev->rating;?>)</p>
                <p class="pb-2 m-0"><?php echo $rev->review_text;?></p>
                <small class="d-block pb-2"><?php echo date("d M Y", strtotime($rev->created_dttm));?></small> </div>
            </li>
            <?php } } else {?> <li class="border-bottom pt-2 pb-2">No reviews available</li><?php }?>
          </ul>
          
        </div>
      </div>
    </div>
	 <div class="col-8 text-center mb-5">
	 <?php if($is_review['is_review']==0 && $is_review['is_qrcode_used']==1){ ?>
	 <a class="btn btn-style mt-3" href="<?php echo base_url().'ChatPot/review_sample/'.$fb_id.'/'.$cmn_id?>">Review samples</a><?php } ?>
   <a class="btn btn-style mt-3" href="https://isamplez.com">Download app for more samples </a>
   </div>
  </div>
</div>

</body></html>
