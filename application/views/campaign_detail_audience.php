<div class="pageBody mt-0">
  <div class="audienceView">
      <?php if($audienceData->age){ 
      if(!empty($audienceData->gender)){
      $gender=explode(',', $audienceData->gender); ?>
      <div class="row pb-2 pl-1 pr-1">
        <div class="col-5">
          <h3>Gender</h3>
          <ul class="listStyle">
            <?php foreach ($gender as $value) { ?> 
            <li><?php if($value=='1') echo "Male";if($value=='2') echo "Female";?></li>
            <?php } ?>
          </ul>
        </div>
      <?php } } ?>
      <?php if(!empty($audienceData->age_bracket)){ 
      $age_bracket=explode(',', $audienceData->age_bracket); ?>
      <div class="col-6">
        <h3>Age</h3>
        <ul class="listStyle">
          <?php foreach ($age_bracket as $age) { ?> 
          <li><?php echo $age;?></li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <hr>
    <?php } ?>
    <?php if(!empty($inetrests)) { ?>
    <div class="row pt-2 pl-1 pr-1">
     <?php foreach ($inetrests as $key => $value) { 
      if(strpos($value ,',') !== false) {
      $inerestVal=explode(',',$value); ?>
      <div class="col-5">
        <h3><?php echo $key;?></h3>
        <ul class="listStyle">
          <?php foreach ($inerestVal as $val) { ?>
          <li><?php echo $val;?></li>
        <?php } ?>
        </ul>
      </div>
    <?php } } ?>
    </div>
    <hr>
    <?php
    } 
    if($campBehaviour){ 
      $cnt=1;
      foreach ($campBehaviour as  $camp) { ?>
      <div class="pb-2 pt-2 pl-1 pr-1">
        <h3><?php echo $camp->campaign_name;?></h3>
        <ul class="listStyle">
          <?php if(in_array('1', explode(',', $camp->camp_behaviour))){ ?>
          <li>Added A Review</li>
        <?php } ?>
          <?php if(in_array('2', explode(',', $camp->camp_behaviour))){ ?>
          <li>Did not Add A Review</li>
        <?php } ?>
          <?php if(in_array('3', explode(',', $camp->camp_behaviour))){ ?>
          <li>Did Not Scan QR Code At Vending Machine</li>
        <?php } ?>
          <?php if(in_array('4', explode(',', $camp->camp_behaviour))){ ?>
          <li>Obtained Sample QR Code</li>
        <?php } ?>
          <?php if(in_array('5', explode(',', $camp->camp_behaviour))){ ?>
          <li>Did Not Obtain Sample QR Code</li>
        <?php } ?>
          <?php if(in_array('6', explode(',', $camp->camp_behaviour))){ ?>
          <li>Scanned QR Code At Vending Machine</li>
        <?php } ?>
        </ul>
      </div>
    <hr>
  <?php } } ?>
  <?php
  if($postBehaviour){ 
    $cnt=1;
    foreach ($postBehaviour as  $post) { ?>
      <div class="pb-2 pt-2 pl-1 pr-1">
        <h3>Post <?php echo $cnt;?></h3>
        <div class="postView mb-3 w-50">
          <div class="postView-img"><?php if($post->post_banner_url!='' && file_exists('assets/post/banner/'.$post->post_banner_url)) { ?><img src="<?php echo base_url('assets/post/banner/'.$post->post_banner_url);?>" alt="<?php echo $post->post_banner_url;?>"/><?php } ?></div>
          <p class="postView-text"><?php echo $post->post_desc;?></p>
        </div>
        <ul class="listStyle">
          <?php if(in_array('1', explode(',', $post->post_behaviour))){ ?>
          <li>Liked A Post</li>
        <?php } ?>
          <?php if(in_array('2', explode(',', $post->post_behaviour))){ ?>
          <li>Did not Like A Post</li>
        <?php } ?>
          <?php if(in_array('3', explode(',', $post->post_behaviour))){ ?>
          <li>Obtained Promo Code</li>
        <?php } ?>
          <?php if(in_array('4', explode(',', $post->post_behaviour))){ ?>
          <li>Commented On A Post</li>
        <?php } ?>
          <?php if(in_array('5', explode(',', $post->post_behaviour))){ ?>
          <li>Did Not Comment On A Post</li>
        <?php } ?>
          <?php if(in_array('6', explode(',', $post->post_behaviour))){ ?>
          <li>Did Not Obtain Promo Code</li>
        <?php } ?>
        </ul>
      </div>

  <?php $cnt++;} } ?>
  </div>
</div>
</div>
<footer></footer>

<!-- MESSAGE MODAL -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-sm">
      <form id="forgot-form">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Add Sample</h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body pt-0 text-center">
          <label class="col-form-label-sm d-block">No. of Samples</label>
          <input type="text" class="form-control text-center w-75 d-inline-block" />
        </div>
        <div class="modal-footer">
          <button class="btn btn-link" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-style" type="button" data-dismiss="modal">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SCRIPT --> 
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/popper/popper.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/bootstrap-4.3.1/dist/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>  -->
<script type="text/javascript">
  (function($) {
    'use strict';

    $('.item').each(function() {
     $(this).on('click', function() {
       $(this).addClass('active');
       $(this).closest('.owl-item').siblings().find('.item').removeClass('active');
     });
   });

    $('#gallerySilder').owlCarousel({
      margin: 10,
      nav: false,
      autoWidth: false,
      dots: false,
      responsive: {
        0: {
          items: 1,
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
</body>
</html>
