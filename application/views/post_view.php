<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a class="text-white mr-2" href="<?php echo base_url('brand-detail-post/'.$brand_id);?>" onClick="history.back();" value="Back" style="cursor: pointer">Back</a> | <span class="ml-2">View Post</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>
    <div class="createInfo mt-3">
      <?php 
      $attributes = array('id' => 'posts-form');
      echo form_open('admin/posts/editPosts', $attributes);
      $readonly="readonly";
     // print_r($postDtl);
      if($postDtl[0]->is_publish=='0')
        $readonly="";?>
      <h2>Campaign Name</h2>
      <div class="pb-2 pt-2">
        <div class="row">
          <div class="col-12 col-md-4">
            <input type="text" class="form-control" value="<?php echo $postDtl[0]->campaign_name ;?>" name="campaign_name" id="campaign_name" readonly/>
            <input type="text" class="hidden" value="<?php echo $postDtl[0]->campaign_id ;?>" name="campaign_id" id="campaign_id"/>
            <input type="text" class="hidden" value="<?php echo $postDtl[0]->id ;?>" name="post_id" id="post_id" readonly/>
            <input type="text" name="button_type" id="button_type" class="hidden"/>
          </div>
        </div>
      </div>
      <hr>
      <div class="pb-2 pt-2">
        <div class="row">
          <div class="col-12 col-md-4">
            <label class="col-form-label-sm">Post Description</label>
            <textarea class="form-control" placeholder="Description" name="post_desc" <?php echo $readonly;?>><?php echo $postDtl[0]->post_desc ;?></textarea>
          </div>

          <?php if($postDtl[0]->is_publish=='1') { ?>
          <div class="col-12 col-md-4">
            <div class="countStatus">
              <label class="col-form-label-sm">Statistics</label>
              <div class="sampleCount xtraWide borderStyle">
                <ul>
                  <li><span><?php echo $postDtl[0]->total_likes;?></span><small>Likes</small></li>
                  <li><span><?php echo $postDtl[0]->total_comments;?></span><small>Comments</small></li>
                  <li><span><?php echo $postDtl[0]->total_coupons;?></span><small>Coupons Generated</small></li>
                  <li><span><?php echo $postDtl[0]->total_coupon_used;?></span><small>Coupons Used</small></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-3">
            <div>
              <label class="col-form-label-sm">Publish Date</label>
            <input type="text" class="form-control" value="<?php if($postDtl[0]->publish_date!="0000-00-00") echo date("d M Y",strtotime($postDtl[0]->publish_date));?>" readonly />
            </div>
            <?php if($postDtl[0]->buy_now_status=="1"){ ?>
            <div class="pt-3">
              <label class="col-form-label-sm ">Buy Now Url</label>
              <input type="text" name="buy_now_url"  value="<?php echo $postDtl[0]->buy_now_url;?>" class="form-control" required="" autocomplete="off" readonly>
            </div>
            <?php } ?>
          </div>
        <?php } ?>
        </div>
        <?php if($postDtl[0]->has_promo=="1"){ ?>
          <p class="text-dark font-weight-bold pt-3 pb-0 m-0"><b class="badge badge-pill badge-warning text-white">!</b> This is a promo post</p>
        <?php } ?>
        
      </div>
      <hr>
      <h2>Media</h2>
      <div class="pb-1">
        <ul class="postImages">
          <li>
            <div class="boxImg"><?php if($postDtl[0]->banner_type=="1") { ;?>
              <div class="boxImg yesCrop"><img src="<?php if(file_exists('assets/post/banner/'.$postDtl[0]->post_banner_url)) echo base_url('assets/post/banner/'.$postDtl[0]->post_banner_url); ?>" alt="">
                </div>
              <?php } else if($postDtl[0]->banner_type=='2') { ?>
                <div class="boxImg noCrop"><video src="<?php if(file_exists('assets/post/banner/'.$postDtl[0]->post_banner_url)) echo base_url('assets/post/banner/'.$postDtl[0]->post_banner_url);?>"></video>
                </div>
              <?php } ?>
            </li>
          </ul>
        </div>
        <?php /*if($postDtl[0]->is_publish=='0'){ ?>
        <div class="pt-2 pb-2 text-right">
          <button class="btn btn-style mr-2" id="create">Save</button>
          <button class="btn btn-style" id="publish">Publish</button>
        </div>
      <?php } */?>
         <?php echo form_close();?>
      </div>
    </div>
    <script type="text/javascript">
      (function($) {
        'use strict';

           // FORM SUBMIT
    $(document).on('click', '#create', function() {
     submitform("create");
   })
    $(document).on('click', '#publish', function() {
     submitform("publish");
   })
  function submitform(type){
    $("#button_type").val(type);
     $("#posts-form").validate({
      errorElement: 'small',
      submitHandler: function() {
       // $("#publish").attr("disabled", true);
        $('#startConfetti').trigger('click');
        var user = $('#posts-form').serialize();
        $.ajax({  
          type: "POST",  
          url:  "<?php echo base_url(); ?>" + "admin/posts/editPosts",  
          data: user,  
          cache: false,  
          success: function(result){  
            if(result=="success"){  
                  window.location.replace("<?php echo base_url('brand-detail-post/'.$brand_id);?>");  
            }  
            else {
             // 
              /*jQuery("div#err_msg").show();  
              jQuery("div#msg").html(result); */
            }
            //$("#publish").attr("disabled", false);
          }   

            });  
      }
    });
   }

      })(jQuery);
    </script>
  </body>
  </html>
