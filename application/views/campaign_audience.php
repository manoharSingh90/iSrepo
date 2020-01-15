<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a class="text-white mr-2"  href="<?php echo base_url('brand-detail-campaign/'.$brand_id);?>" onClick="history.back();"  value="Back" style="cursor: pointer">Back</a> Create New Campaign</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>| <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a>
    </div>
  </div>
  <div class="pageTabs mt-1 extrawWide">
    <ul>
      <li><a  id="camp" href="<?php echo base_url('create-campaigns/'.$brand_id.'/'.$campaign_id);?>" class="<?php if($this->uri->segment(1)=='create-campaigns') echo "active";?>" >Campaign</a></li>
      <li><a href="<?php if($campaign_id=='') echo  "javascript: void(0)" ;else echo base_url('create-samples/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='edit-samples') echo "active";?>">Samples</a></li>
      <li><a href="<?php if($sampleData=='') 
      echo  "javascript: void(0)" ;
      else echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-targetAudience') echo "active";?>">Target Audience</a></li>
      <li><a href="<?php if($sampleData=='') echo  "javascript: void(0)" ;else echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-questionnaire') echo "active";?>">Questionnaire</a></li>
      <li class="disabled" ><a href="<?php if(@$qusDataCount==0) echo  "javascript: void(0)" ;else echo base_url('create-campaign-brand/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-campaign-brand') echo "active";?>" >Brand Details</a></li>
    </ul>
  </div>
  <div class="createInfo">
    <?php 
    $attributes = array('id' => 'targetAudience-form');
    echo form_open('admin/targetAudience/add', $attributes);?>
    <input type="hidden" name="brand_id" value="<?php echo $brand_id;?>">
    <input type="hidden" name="campaign_id" value="<?php echo $campaign_id;?>">
    <div class="pt-2 pb-2">
      <h2 class="mb-0">Target Audience</h2>
      <small>Please select audience parameters:</small>
      <div class="p-2 pl-4 mt-2">
        <div class="row">
          <div class="col-12 audienceView">
            <h3 class="mb-2">1. Gender</h3>
            <ul>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="gender[0]" value="1" class="custom-control-input" id="g-01" >
                  <label class="custom-control-label" for="g-01">Male</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="gender[1]" value="2" class="custom-control-input" id="g-02" >
                  <label class="custom-control-label" for="g-02">Female</label>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <hr>
      <div class="p-2 pl-4">
        <div class="row">
          <div class="col-12 audienceView">
            <h3 class="mb-2">2. Age</h3>
            <ul>
              <?php if($ageBracket){
              $count=0;
              foreach ($ageBracket as $value) { ?>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="age[<?php echo  $count;?>]" value="<?php echo $value->id;?>" class="custom-control-input" id="age-<?php echo $count;?>" >
                  <label class="custom-control-label" for="age-<?php echo $count;?>"><?php echo $value->age_bracket_desc;?></label>
                </div>
              </li>
              <?php $count++; } } ?>
            </ul>
          </div>
        </div>
      </div>
      <hr>
      <div class="p-2 pl-4">
        <div class="row">
          <div class="col-12 audienceView">
            <h3 class="mb-3">3. Interests</h3>
            <div class="row">
            <?php if($inetrestMaster) { // print_r($inetrestOption);die;
              $outercnt=0;
              foreach ($inetrestMaster as $key => $value) {  ?>
                <div class="col-6">
                  <h3 class="mb-1"><?php echo $value->interest_title;?></h3>
                  <ul>
                    <?php if($inetrestOption[$key]) { 
                    $cnt=0;
                      foreach ($inetrestOption[$key] as  $values) {  ?>
                        <li>
                          <div class="custom-control custom-checkbox  font-weight-bold">
                            <input type="checkbox" name="interest_option_<?php echo $value->id;?>[<?php echo $cnt;?>]" value="<?php echo $values->id;?>" class="custom-control-input" id="f-<?php echo $outercnt.$cnt;?>" >
                              <input type="hidden" name="interest_ques_id<?php echo $value->id;?>[<?php echo $cnt;?>]" value="<?php echo $value->id;?>">
                            <label class="custom-control-label" for="f-<?php echo $outercnt.$cnt;?>"><?php echo $values->option_text;?></label>
                          </div>
                        </li>
                      <?php $cnt++;} 
                    }  ?>
                  </ul>
                </div>
              <?php $outercnt++;} }  ?>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="p-2">
      <h6 class="mb-2">4. Campaign Behaviour</h6>
      <?php if($campData){ ?>

        <div class="row pt-2 camDiv">
          <div class="col-12 col-md-4 placeVaild">
            <select class="form-control" name="camp_id[1]" required>
              <option value="">Select Campaign</option>
                <?php //if($campData) {
                  foreach ($campData as $value) { ?>
                  <option value="<?php echo $value->id;?>"><?php echo $value->campaign_name;?> </option>
                <?php //} 
              } ?>
            </select>
          </div>
          <div class="col-12 col-md-4 text-left removeCamp"> <a style="cursor:pointer" class="text-danger font-weight-bold  text-uppercase mt-2 d-inline-block">Remove</a> </div>
          <div class="col-12 audienceView pt-3 pb-0 pl-4 placeVaild">
            <!-- <h3 class="mb-2">4. Audience Parameters</h3> -->
            <ul>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="camp_behaviour_1[]" value="1" class="custom-control-input" id="a-10" required>
                  <label class="custom-control-label" for="a-10">Added A Review</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="camp_behaviour_1[]" value="2" class="custom-control-input" id="a-11" >
                  <label class="custom-control-label" for="a-11">Did not Add A Review</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="camp_behaviour_1[]" value="3" class="custom-control-input" id="a-12" >
                  <label class="custom-control-label" for="a-12">Did Not Scan QR Code At Vending Machine</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="camp_behaviour_1[]" value="4" class="custom-control-input" id="a-13" >
                  <label class="custom-control-label" for="a-13">Obtained Sample QR Code</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="camp_behaviour_1[]" value="5" class="custom-control-input" id="a-14" >
                  <label class="custom-control-label" for="a-14">Did Not Obtain Sample QR Code</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="camp_behaviour_1[]" value="6" class="custom-control-input" id="a-15" >
                  <label class="custom-control-label" for="a-15">Scanned QR Code At Vending Machine</label>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <p id="camp_behaviour_1-error" class="text-danger"></p>
        <div class="appendCamplist"></div>
        <div class="row pl-2 pr-2">
          <div class="col-12"><hr></div>
          <div class="col-12"><a href="#" class="text-link  text-uppercase font-weight-bold adCampClick">+ Add Another Campaign</a></div>
        </div>

        <?php } else{ echo "No campaigns available in DB." ; } ?>
      </div>
      <hr>
      <div class="p-2">
      <h6 class="mb-2">5. Post Behaviour</h6>
      <?php if($postData){ ?>
        <div class="row pt-4 postDiv">
          <div class="col-12 col-md-4 placeVaild" id="postDiv1">
            <input type="text" id="selectPostInput_1" readonly placeholder="Select Post" name="post_0[0]" class="form-control selectPostInput" data-toggle="modal" data-target="#selectModal" required/>
          </div>
          <div class="col-12 col-md-4 text-left removePost"> <a style="cursor:pointer" class="text-danger font-weight-bold text-uppercase mt-2 d-inline-block">Remove Post</a></div>
          <!-- <div id="selectedPost0"> -->
            <!-- <div class="col-12 col-md-6">
              <div class="postView">
                <div class="postView-img"><img src="assets/img/post/sample_banner_01.jpg" alt="#"></div>
                <p class="postView-text">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
              </div>
            </div>
            <div class="col-12 col-md-4"> <a href="#" class="text-danger font-weight-bold  text-uppercase mt-2 d-inline-block">Remove</a> </div> -->
        <!--   </div> -->
          <div class="col-12 audienceView pt-3 pb-0 pl-4 placeVaild">
            <!-- <h3 class="mb-2">5. Post Behaviour</h3> -->
            <ul>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="post_behaviour_1[]" value="1" class="custom-control-input" id="p-10" required>
                  <label class="custom-control-label" for="p-10">Liked A Post</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="post_behaviour_1[]" value="2" class="custom-control-input" id="p-11" >
                  <label class="custom-control-label" for="p-11">Did not Like A Post</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="post_behaviour_1[]" value="3" class="custom-control-input" id="p-12" >
                  <label class="custom-control-label" for="p-12">Obtained Promo Code</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="post_behaviour_1[]" value="4" class="custom-control-input" id="p-13" >
                  <label class="custom-control-label" for="p-13">Commented On A Post</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="post_behaviour_1[]" value="5" class="custom-control-input" id="p-14" >
                  <label class="custom-control-label" for="p-14">Did Not Comment On A Post</label>
                </div>
              </li>
              <li>
                <div class="custom-control custom-checkbox  font-weight-bold">
                  <input type="checkbox" name="post_behaviour_1[]" value="6" class="custom-control-input" id="p-15" >
                  <label class="custom-control-label" for="p-15">Did Not Obtain Promo Code</label>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <p id="post_behaviour_1-error" class="text-danger"></p>
        <div class="appendPostlist"></div>
        <div class="row pl-2 pr-2">
          <div class="col-12">
            <hr>
          </div>
          <div class="col-12"><a href="#" class="text-link  text-uppercase font-weight-bold adPostClick">+ Add Another Post</a></div>
        </div>
       <?php } else{ echo "No post available in DB."; } ?>
      </div>
    </div>
    <div class="pt-2 pb-2 text-right"> <a class="btn btn-link mr-2" href="campaign_sample_create.html">Back</a>
      <button class="btn btn-style">Next</button>
    </div>
    <?php echo form_close();?>
  </div>
</div>
<footer></footer>

<!-- POST MODAL -->
<div class="modal fade" id="selectModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <form id="forgot-form">
        <div class="modal-header">
          <h5 class="modal-title pt-1 text-primary">Select Post</h5>
          <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <ul class="postList">
            <?php if($postData){
              $count=1; 
              foreach ($postData as $value) { ?>
                <li>
                  <div class="custom-control custom-radio font-weight-bold">
                    <input type="radio"  name="selectPost" value="<?php echo $value->id;?>" class="custom-control-input" id="sp-<?php echo $count;?>" >
                    <label class="custom-control-label" for="sp-<?php echo $count;?>"><?php echo ($count);?>.</label>
                  </div>
                  <div class="postView">

                    <input type="text"  name="selectedPostId" value="" class="custom-control-input" id="selectedPostId" >
                    <input type="hidden" name="post_id[]" value="<?php echo $value->id;?>" >
                    <div class="postView-img"><?php if($value->banner_type=='1' && $value->post_banner_url!='' && file_exists('assets/post/banner/'.$value->post_banner_url)) { ?><img src="<?php echo base_url('assets/post/banner/'.$value->post_banner_url);?>" alt="<?php echo $value->post_banner_url;?>"/><?php } ?></div>
                    <p class="postView-text"><?php echo $value->post_desc;?></p>
                  </div>
                </li>
                <?php $count++; } } ?>
              </ul>
        </div>
        <div class="modal-footer">
          <button class="btn btn-link" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-style" type="button" data-dismiss="modal" id="addPostBtn">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
(function($) {
'use strict';
$(document).on("click",".selectPostInput",function(e) {
   var newid=this.id.split("_");
   $("#selectedPostId").val(newid[1]);
});

$(document).on("click","#addPostBtn",function(e) {
  var selectedPostId=$("#selectedPostId").val();
  var id = $("input[name='selectPost']:checked").attr('id');
  var html=$("#"+id).closest('li').find('.postView').prop("outerHTML");
  // html+= '<div class="col-12 col-md-4 removeSelectedPost"> <a class="text-danger font-weight-bold  text-uppercase mt-2 d-inline-block" style="cursor:pointer">Remove</a> </div>';
  $("#postDiv"+selectedPostId).html(html);
});
/*$(document).on("click",".removeSelectedPost",function() {
     $(this).closest('.postView').hide();
  });*/

 // FORM SUBMIT
  $("#targetAudience-form").validate({
    errorElement: 'small',
    errorPlacement: function(error, element) {
      error.appendTo(element.closest(".placeVaild"));
    },
    submitHandler: function() {
      $("input").focus(); 
      $("#next").attr("disabled", true);
      $('#startConfetti').trigger('click');
      var user = $('#targetAudience-form').serialize();
       $('.camDiv:visible').each(function(index) {
          var name=$(this).closest(".camDiv").find('[type=checkbox]').attr('name'); 
          var checkboxName= name.split('[')[0];
          var count_checked = $("[name='"+checkboxName+"[]']:checked").length; // count the checked rows
          if(count_checked == 0) 
          {
            var last = checkboxName.substring(checkboxName.lastIndexOf("_") + 1, checkboxName.length);
            $("#camp_behaviour_"+last+"-error").html('Check atleast one checkbox.');
            return false; 
          }
          else
            return true;
        });
        $('.postDiv:visible').each(function(index) {
          var name=$(this).closest(".postDiv").find('[type=checkbox]').attr('name'); 
          var checkboxName= name.split('[')[0];
          var count_checked = $("[name='"+checkboxName+"[]']:checked").length; // count the checked rows
          if(count_checked == 0) 
          {
            var last = checkboxName.substring(checkboxName.lastIndexOf("_") + 1, checkboxName.length);
            $("#post_behaviour_"+last+"-error").html('Check atleast one checkbox.');
            return false; 
          }
          else
            return false;
        });
      $.ajax({  
        type: "POST",  
        url:  "<?php echo base_url(); ?>" + "admin/targetAudience/add",  
        data: user,  
        cache: false,  
        success: function(campaign_id){  
         if(campaign_id>0){  
          console.log(campaign_id);
          /* window.location.replace("<?php //echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>");*/
            window.location.replace("<?php echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>");   
        }  
        else  {
          $("#next").attr("disabled", false);
          jQuery("div#err_msg").show();  
          jQuery("div#msg").html('Something went wrong.'); 
        }
      }  
    });  
    }
  });
var wrapper1         = $(".appendCamplist"); 
var totalCamps="<?php if($campData) echo count($campData); else echo "0";?>";
$(document).on("click",".adCampClick",function(e) {
  e.preventDefault();
  var count = $('.camDiv').length+1;
  var countActiveDiv = $('.camDiv:visible').length+1;
  var newQuestion = '<div class="row pt-2 camDiv"><div class="col-12"><hr></div><div class="col-12 col-md-4 placeVaild"><select class="form-control" id="campaign_id_'+count+'" name="camp_id['+count+']" required><option value="">Select Campaign</option><?php if($campData) { foreach ($campData as $value) { ?><option value="<?php echo $value->id;?>"><?php echo $value->campaign_name;?></option><?php } } ?></select></div><div class="col-12 col-md-4 text-left removeCamp"> <a style="cursor:pointer" class="text-danger font-weight-bold text-uppercase mt-2 d-inline-block">Remove</a> </div><div class="col-12 audienceView pt-3 pb-0 pl-4 placeVaild"><ul><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="camp_behaviour_'+count+'[]" value="1" class="custom-control-input" id="a-'+count+'0" required><label class="custom-control-label" for="a-'+count+'0">Added A Review</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="camp_behaviour_'+count+'[]" value="2" class="custom-control-input" id="a-'+count+'1"><label class="custom-control-label" for="a-'+count+'1">Did not Add A Review</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="camp_behaviour_'+count+'[]" value="3" class="custom-control-input" id="a-'+count+'2"><label class="custom-control-label" for="a-'+count+'2">Did Not Scan QR Code At Vending Machine</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="camp_behaviour_'+count+'[]" value="4" class="custom-control-input" id="a-'+count+'3"><label class="custom-control-label" for="a-'+count+'3">Obtained Sample QR Code</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="camp_behaviour_'+count+'[]" value="5" class="custom-control-input" id="a-'+count+'4"><label class="custom-control-label" for="a-'+count+'4">Did Not Obtain Sample QR Code</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="camp_behaviour_'+count+'[]" value="6" class="custom-control-input" id="a-'+count+'7"><label class="custom-control-label" for="a-'+count+'7">Scanned QR Code At Vending Machine</label></div></li></ul></div></div><p id="camp_behaviour_'+count+'-rror" class="text-danger"></p>';
    if(totalCamps>=countActiveDiv)
  $(wrapper1).append(newQuestion);
});
var wrapper2        = $(".appendPostlist"); 
var totalPosts="<?php if($postData) echo count($postData); else echo "0";?>";
$(document).on("click",".adPostClick",function(e) {
  e.preventDefault();
  var count = $('.postDiv').length+1;
  var countActiveDiv = $('.postDiv:visible').length+1;
  var newQuestion = '<div class="row pt-4 postDiv"><div class="col-12"><hr></div><div class="col-12 col-md-4 placeVaild"  id="postDiv'+count+'"><input type="text" id="selectPostInput_'+count+'" readonly placeholder="Select Post" name="post_'+count+'[0]" class="form-control selectPostInput" data-toggle="modal" data-target="#selectModal" required></div><div class="col-12 col-md-4 text-left removePost"> <a style="cursor:pointer" class="text-danger font-weight-bold text-uppercase mt-2 d-inline-block">Remove Post</a></div><div class="col-12 audienceView pt-3 pb-0 pl-4 placeVaild"><ul><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="post_behaviour_'+count+'[]" value="1" class="custom-control-input" id="p-'+count+'0" required><label class="custom-control-label" for="p-'+count+'0">Liked A Post</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="post_behaviour_'+count+'[]" value="2" class="custom-control-input" id="p-'+count+'1"><label class="custom-control-label" for="p-'+count+'1">Did not Like A Post</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="post_behaviour_'+count+'[]" value="3" class="custom-control-input" id="p-'+count+'2"><label class="custom-control-label" for="p-'+count+'2">Obtained Promo Code</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="post_behaviour_'+count+'[]" value="4" class="custom-control-input" id="p-'+count+'3"><label class="custom-control-label" for="p-'+count+'3">Commented On A Post</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="post_behaviour_'+count+'[]" value="5" class="custom-control-input" id="p-'+count+'4"><label class="custom-control-label" for="p-'+count+'4">Did Not Comment On A Post</label></div></li><li><div class="custom-control custom-checkbox font-weight-bold"><input type="checkbox" name="post_behaviour_'+count+'[]" value="6" class="custom-control-input" id="p-'+count+'5"><label class="custom-control-label" for="p-'+count+'5">Did Not Obtain Promo Code</label></div></li></ul></div></div><p id="post_behaviour_'+count+'-error" class="text-danger"></p>';
   if(totalPosts>=countActiveDiv)
  $(wrapper2).append(newQuestion);
//addOptionMore(count);
});
$(document).on("click",".removeCamp",function() {
  //var campId=$(this).closest('select').find(':selected').attr('id'); //find the value
 // alert(campId);
  //var questionId=$(this).closest(".camDiv").find('input').attr('id'); 
   /* if(campId!=undefined){
      $("#"+campId).prop('required',false);
    }*/
    if($('.camDiv:visible').length>1){
     // $(this).closest(".camDiv").find('input').val(''); 
      $(this).closest('.camDiv').hide();
    }
  });
$(document).on("click",".removePost",function() {
    if($('.postDiv:visible').length>1){
      $(this).closest('.postDiv').hide();
    }
  });
$(document).on('click', '.postView', function(){
 var $this = $(this);
 var $list = $this.closest('li');
 var $radio = $list.find('input');
 $radio.trigger('click');
});

$(".custom-control-input").each(function() {
  $(this).on("change", function() {
    if ($(this).is(":checked")) {
      $(this).closest("li").find('.postView').addClass("active");
      $(this).closest("li").siblings().find('.postView').removeClass("active");
    } 
  });
});

})(jQuery);
</script>
</body>
</html>
