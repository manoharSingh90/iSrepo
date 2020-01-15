<div class="pageArea">
  <div class="pageHeader clearfix">
 <!--    <h2 class="float-left pageTitle"><a href="<?php //echo base_url('brands');?>" class="text-white mr-2" onClick="history.back();" value="Back" style="cursor: pointer">Back</a> | <span class="ml-2">View Details</span></h2> -->
    <h2 class="float-left pageTitle"><a class="text-white mr-2" href="<?php echo base_url('brands');?>" onClick="history.back();" value="Back" style="cursor: pointer">Back</a> | <span class="ml-2">View Details</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>
    <div class="brandDetail">
      <div class="brandDetail-info">
       <div class="brand-link"><span><?php if($brandDtl[0]->brand_logo_url!='' && file_exists('assets/brand/logo/'.$brandDtl[0]->brand_logo_url))  { ?><img src="<?php 
       echo base_url('assets/brand/logo/'.$brandDtl[0]->brand_logo_url);
       ?>" alt="<?php echo $brandDtl[0]->brand_name;?>"/><?php } ?></span>
       
       <h1><?php echo $brandDtl[0]->brand_name;?></h1>
       <small class="d-block">Created on <?php echo date('d M, Y',strtotime($brandDtl[0]->created_dttm));?></small></div>
       <h2>Description</h2>
       <p><?php echo $brandDtl[0]->brand_desc;?></p>
       <div class="brandDetail-button"> <a class="btn btn-light" href="<?php echo base_url('edit-brands/'.$brandDtl[0]->id);?>">Edit</a>
         <?php if($brandDtl[0]->is_active=='1'){
          $text="Active";
          $check="";
        }
        else{
          $text="Deactivate";
          $check="checked";
        }?>
        <div class="switchButton">
          <label>
            <input class="deactiveCheck" type="checkbox" value="deactive" id="switch_<?php echo $brandDtl[0]->id;?>" <?php echo $check;?> />
            <b></b><span><?php echo $text;?></span></label>
          </div>
        </div>
      </div>
      <div class="brandDetail-gallery">
        <div class="countStatus">
          <p><span>Campaigns: </span><span>Posts:</span></p>
          <div class="sampleCount">
            <ul>
              <li><span><?php echo $brandDtl[0]->total_campaign;?></span><small>Total</small></li>
              <li><span><?php echo $brandDtl[0]->total_live_campaign;?></span><small>Live</small></li>
              <li><span><?php echo $brandDtl[0]->total_post;?></span><small>Total</small></li>
              <li><span><?php echo $brandDtl[0]->total_live_post;?></span><small>Live</small></li>
            </ul>
          </div>
        </div>
        <h2>Media</h2>
        <div class="brandDetail-gallery">
          <div id="gallerySilder" class="owl-carousel owl-theme ownCarousel">
            <?php if($media) {
              foreach ($media as $value) { ?>
                <?php if($value->asset_type=='1' && file_exists('assets/brand/assets/'.$value->asset_url) ){ ?> <a class="item img-link"  data-width="640" data-height="360" data-src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>"> <img src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>" alt="<?php echo $value->asset_name ;?>"></a>
              <?php } elseif ($value->asset_type=='2' && file_exists('assets/brand/assets/'.$value->asset_url) ) { ?>
               <a class="item img-link" data-width="640" data-height="360" data-src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>"><video><source src="<?php echo base_url('assets/brand/assets/'.$value->asset_url);?>" type="video/mp4"></video></a>
               <?php } ?>
             <?php } }  ?>
           </div>
         </div>
       </div>
     </div>
     <div class="pageTabs">
      <ul>
        <li><a href="<?php echo base_url('brand-detail-campaign/'.$brandDtl[0]->id.'');?>" class="<?php if($this->uri->segment(1)=='brand-detail-campaign') echo "active";?>">Campaigns</a></li>
        <!-- <li><a href="#" class="">Posts</a></li> -->
        <li><a href="<?php echo base_url('brand-detail-post/'.$brandDtl[0]->id.'');?>" class="<?php if($this->uri->segment(1)=='brand-detail-post') echo "active";?>">Posts</a></li>
      </ul>
    </div>