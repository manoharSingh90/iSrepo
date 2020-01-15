<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a class="text-white mr-2" href="<?php echo base_url('brand-detail-campaign/'.$brand_id);?>" onClick="history.back();" value="Back" style="cursor: pointer">Back</a> | <span class="ml-2">Campaign Detail</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>| <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a>
    </div>
  </div>
  <?php  //print_r($media); ?>
  <div class="brandDetail">
    <div class="brandDetail-info">
      <div class="brand-link"><span><?php if($coveImage!='' && file_exists('assets/campaign/banner/'.$coveImage)) { ?><img src="<?php echo base_url('assets/campaign/banner/'.$coveImage);?>" alt="#" /> <?php } ?></span>
        <h1><?php echo $campDtl[0]->campaign_name;?></h1>
        <small class="d-block">Created on <?php if($campDtl[0]->created_dttm!="")  echo date('d M Y',strtotime($campDtl[0]->created_dttm)) ;?></small>
      </div>
      <h2>Description</h2>
      <p><?php echo $campDtl[0]->campaign_desc;?></p>
      <div class="brandDetail-button"> <a class="btn btn-light" href="<?php echo base_url('edit-campaigns/'.$brand_id.'/'.$campDtl[0]->id);?>">Edit</a> </div>
    </div>
    <div class="brandDetail-gallery">
      <div class="countStatus">
        <p>Statistics:</p>
        <div class="sampleCount xtraWide borderStyle">
          <ul>
            <li><span><?php echo $campDtl[0]->total_samples_redeemed;?></span><small>Samples Redeemed</small></li>
            <li><span><?php echo $campDtl[0]->total_promo_redeemed;?></span><small>Promotions Redeemed</small></li>
            <li><span><?php echo $campDtl[0]->total_post;?></span><small>Total Posts</small></li>
            <li><span><?php echo $nps=NPS($campDtl[0]->id);;?></span><small>NPS</small></li>
          </ul>
        </div>
      </div>
      <h2>Media</h2>
      <div class="brandDetail-gallery" style="width: 640px;">
        <div id="gallerySilder" class="owl-carousel owl-theme ownCarousel">
          <?php if($media) {
            foreach ($media as $value) { ?>
              <?php if($value->banner_type=='1' && file_exists('assets/campaign/banner/'.$value->banner_url) ){ ?><a class="item img-link" data-width="640" data-height="360" data-src="<?php echo base_url('assets/campaign/banner/'.$value->banner_url);?>"><img style="height: 88px;width: 88px;" src="<?php echo base_url('assets/campaign/banner/'.$value->banner_url);?>" alt="thumbnail"><span><?php if($value->cover_image=='1') echo 'Cover Image';?> </span></a>
            <?php } elseif ($value->banner_type=='2' && file_exists('assets/campaign/banner/'.$value->banner_url)) { ?>
              <a class="item img-link" data-width="640" data-height="360" data-src="<?php echo base_url('assets/campaign/banner/'.$value->banner_url);?>"><video><source src="<?php echo base_url('assets/campaign/banner/'.$value->banner_url);?>" type="video/mp4"></video><?php if($value->cover_image=='1') echo 'Cover Image';?> </span></a>
              <?php } ?>
            <?php } } else echo "No record exists.";   ?>
          </div>
        </div>
      </div>
    </div>
    <div class="pageTabs">
      <ul>
        <li><a href="<?php echo base_url('campaign_detail_samples/'.$brand_id.'/'.$campDtl[0]->id.'');?>" class="<?php if($this->uri->segment(1)=='campaign_detail_samples') echo "active";?> ">Samples</a></li>
        <li><a href="<?php echo base_url('campaign_detail_audience/'.$brand_id.'/'.$campDtl[0]->id.'');?>" class="<?php if($this->uri->segment(1)=='campaign_detail_audience') echo "active";?> ">Target Audience</a></li>
        <li><a href="<?php echo base_url('campaign_detail_reviews/'.$brand_id.'/'.$campDtl[0]->id.'');?>" class="<?php if($this->uri->segment(1)=='campaign_detail_reviews') echo "active";?> ">Reviews</a></li>
      </ul>
    </div>