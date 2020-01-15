<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a class="text-white mr-2" href="<?php echo base_url('brand-detail-campaign/'.$brand_id);?>"  onClick="history.back();"  value="Back" style="cursor: pointer">Back</a>  | <span class="ml-2">Edit Campaign</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>| <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a>
    </div>
  </div>
  <div class="pageTabs mt-1 extrawWide">
   <ul>
    <li><a  id="camp" href="<?php echo base_url('create-campaigns/'.$brand_id.'/'.$campaign_id);?>" class="<?php if($this->uri->segment(1)=='create-campaigns') echo "active";?>" >Campaign</a></li>
    <li><a href="<?php echo base_url('create-samples/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-samples') echo "active";?>">Samples</a></li>
      <li><a href="<?php if($campaign_id=='') 
      echo  "javascript: void(0)" ;
      else echo base_url('create-targetAudience/'.$brand_id.'/'.$campaign_id);?>" class="<?php if($this->uri->segment(1)=='create-targetAudience') echo "active";?>">Target Audience</a></li>
      <li><a href="<?php echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='create-questionnaire') echo "active";?>">Questionnaire</a></li>
      <li class="disabled" ><a href="<?php echo base_url('create-campaign-brand/'.$brand_id.'/'.$campaign_id);?>"  class="<?php if($this->uri->segment(1)=='edit-campaign-brand') echo "active";?>" >Brand Details</a></li>
    </ul>
  </div>
  <div class="createInfo">
   <?php 
   $attributes = array('id' => 'editCampBrands-form');
   echo form_open('admin/brands/editCampBrands', $attributes);?>
   <div id="err_msg" style="display: none">   
    <h2 style="color: red"><div id="msg"> </div></h2>
  </div> 
  <input type="text" name="campaign_id" value="<?php echo $campaign_id;?>" class="hidden">
  <input type="text" name="brand_id" value="<?php echo $brand_id;?>" class="hidden">
  <h2>Brand Media</h2>
  <small class="mt-3 mb-1 d-block">Select images/videos from gallery:</small>
  <div class="pb-5">
    <ul class="galleryImages mt-0 flex-wrap">
      <?php if($media) { 
        $count=0;
        $disable='';
        $selectedAssetIds=array();
        if($campMedia){
          foreach ($campMedia as $campMedias) {
           $selectedAssetIds[]=$campMedias->asset_id;
         }
       }
       else
        $campMedia=array();
      foreach ($media as $key => $value) {  ?> 
        <li>
          <?php if($value->asset_type=='1') { if(file_exists('assets/brand/assets/'.$value->asset_url)){?>
            <div class="boxImg yesCrop"><img src="<?php if(file_exists('assets/brand/assets/'.$value->asset_url)) echo base_url('assets/brand/assets/'.$value->asset_url);?>" >
            </div>
          <?php } }else if($value->asset_type=='2') { if(file_exists('assets/brand/assets/'.$value->asset_url)){?>
            <div class="boxImg noCrop"><video src="<?php if(file_exists('assets/brand/assets/'.$value->asset_url)) echo base_url('assets/brand/assets/'.$value->asset_url);?>"></video>
            </div>
          <?php } }?>
          <div class="custom-control custom-checkbox">
            <!-- name="customCheck[]" value="<?php //if(!empty($selectedAssetIds)) { //if(in_array($value->id, $selectedAssetIds)) echo '1' ;else echo '0';} ?>"  -->
            <?php if(file_exists('assets/brand/assets/'.$value->asset_url)){ ?>
            <input type="checkbox" class="custom-control-input checkbox" id="customCheck-<?php echo $count;?>" <?php if(!empty($selectedAssetIds)) { if(in_array($value->id, $selectedAssetIds)) echo "checked"; }  ?> >
            <label class="custom-control-label font-weight-bold" for="customCheck-<?php echo $count;?>">Select</label>
          <?php }?>
          </div>
          <input type="text" name="asset_url[]" value="<?php echo $value->asset_url;?>" class="hidden">
          <input type="text" name="brand_asset_id[]" value="<?php echo $value->id;?>" class="hidden">
          <input type="text" name="asset_type[]" value="<?php echo $value->asset_type;?>" class="hidden">
          <input type="text" name="camp_brand_asset_id[]" value="<?php if(array_key_exists($key,$campMedia)) echo $campMedia[$key]->id; else echo '0';?> " class="hidden">
        </li>
        <?php $count++;} }
        else echo "No media available in DB."; ?>
      </ul>
    </div>

    <input type="text" name="button_type" id="button_type" class="hidden"/>
    <small>Image size: 1 MB Max | Image Resolution: 460 X 460 | Video Size: 5 MB Max an MP4 Format Only</small>
    <div class="pb-1">
      <ul class="postImages mt-1">
        <li>
          <div class="boxImg"><img src="" alt="#" style="display:none;">
            <p class="text-center text-uppercase font-weight-bold text-link uploadLink">+ Upload file</p>
          </div>
          <input type="file" class="uploadFile" id="uploadDoc-1" data-width="460" data-height="460" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4" />
          <input type="text" value="" name="imgupload[]" id="imgupload-1" class="hidden" />
          <div class="removeImg"><b>X</b> File: Camp1.jpg</div>
        </li>
      </ul>
    </div>
    <div class="pt-2 pb-2 text-right"> <a class="btn btn-link mr-2"  href="<?php echo base_url('create-questionnaire/'.$brand_id.'/'.$campaign_id);?>" onClick="history.back();"  value="Back" style="cursor: pointer">Back</a>
      <button class="btn btn-style mr-2" id="create">Save</button>
      <button class="btn btn-style" id="publish">Publish</button>
    </div>
    <?php echo form_close();?>
  </div>
</div>
<!-- CROP MODAL -->
<div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">CROP</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body pt-0">
        <div class="row">
          <div class="col-12">
            <div class="img-container"> <img id="imageCrop" src="<?php echo base_url();?>assets/img/isamplez_logo.png" alt="Picture"> </div>
          </div>
          <div class="col-12 docs-buttons">
            <div class="btn-group hidden">
              <button type="button" class="btn btn-primary" data-method="crop" title="Crop">Crop</button>
              <button type="button" class="btn btn-primary" data-method="clear" title="Clear">Clear</button>
              <button type="button" class="btn btn-primary" data-method="disable" title="Disable">Disable</button>
              <button type="button" class="btn btn-primary" data-method="enable" title="Enable">Enable</button>
              <button type="button" class="btn btn-primary" data-method="reset" title="Reset">Reset</button>
              <button id="destroyCrop" type="button" class="btn btn-primary" data-method="destroy" title="Destroy">Destroy</button>
              <button id="getCrop" type="button" class="btn btn-success" data-method="getCroppedCanvas" data-option="{ &quot;maxWidth&quot;: 460, &quot;maxHeight&quot;: 460 }"> Get Cropped Canvas </button>
              <div id="afterCrop"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-link" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-style" type="button" data-dismiss="modal" id="cropBtn">Crop</button>
      </div>
    </div>
  </div>
</div>

<!-- ERROR MESSAGE MODAL -->
<div class="modal fade" id="reqErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">Error</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <p>Please upload required size image or video</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-style" type="button" data-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>

<!-- EXN. ERROR MESSAGE MODAL -->
<div class="modal fade" id="exnErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">Error</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <p>Invalid Extension!</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-style" type="button" data-dismiss="modal">Done</button>
      </div>
    </div>
  </div>
</div>
<!-- ERROR MESSAGE MODAL -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content ">
      <div class="modal-header">
       <!--  <h5 class="modal-title pt-1 text-success">Campain details added successfully.</h5> -->
       <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
     </div>
     <div class="modal-body text-center">
      <p>Campain details added successfully.</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-style" type="button" data-dismiss="modal">OK</button>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="ErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">Error</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <p>Please select atleast one Brand media.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-style" type="button" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
<footer></footer>
<script type="text/javascript">
  (function($) {
    'use strict';

    $(document).on('click', '#create', function() {
     submitform("create");
   })
    $(document).on('click', '#publish', function() {
     submitform("publish");
   })
    function submitform(type){
    $("#button_type").val(type);
     $("#editCampBrands-form").validate({
      errorElement: 'small',
      submitHandler: function() {
        var numberOfCheckbox = $('input:checkbox').length;
        var numberOfChecked = $('input:checkbox:checked').length;
        if(numberOfCheckbox>1 && numberOfChecked<1 ){
          $('#ErrorModal').modal();
          return false;
        }
        $("#"+type).attr("disabled", true).addClass("loading");
        $('#startConfetti').trigger('click');
        var user = $('#editCampBrands-form').serialize();
        $('input[type=checkbox]').each(function() {     
          if (!this.checked) {
            user += '&customCheck[]=0';
          }
          if (this.checked) {
            user += '&customCheck[]=1';
          }
        });
        console.log(user);
        $.ajax({  
          type: "POST",  
          url:  "<?php echo base_url(); ?>" + "admin/brands/editCampBrands",  
          data: user, 
          cache: false,  
          success: function(campaign_id){
            $("#"+type).attr("disabled", false).removeClass("loading");
            if(campaign_id>0){  
              //$('#successModal').modal();
               //window.location.replace("<?php //echo base_url('create-campaign-brand/'.$brand_id.'/');?>"+campaign_id);  
               window.location.replace("<?php echo base_url('brand-detail-campaign/'.$brand_id);?>"); 
               
             }  
            /*else  {
              $("#create").attr("disabled", false);
                jQuery("div#err_msg").show();  
                jQuery("div#msg").html("error"); 
              }*/
            }  
          });  
      }
    });
   } 
   // UPLOAD FILE
   $(document).on('click', '.uploadLink', function() {
     var $fileUpload = $(this).closest('li').find('.uploadFile')
     $fileUpload.trigger('click');
   });

   $(document).on('click', '.removeImg', function() {
    var $itemUpload = $(this).closest('li');
    $itemUpload.remove();
  });

   function readURL(input,counter) {
    var $this = $(input);
    var $thisID = $this.attr('id');
    var $width = $this.attr('data-width');
    var $height = $this.attr('data-height');
    var $vidSize = $this.attr('data-video-size')

    var $imgSize = $this.attr('data-img-size')
    var $box = $this.closest('li');
    var $nameText = $box.find('.removeImg');
    var $callRadio = $box.find('.custom-control');
    var $uploadLink = $box.find('.uploadLink');
    var $fileinfo = $box.find('.boxImg');
    var $imgInput = $fileinfo.find('img');
    var dNum = new Date();
// var count = dNum.getFullYear() + '' + (dNum.getMonth() + 1) + '' + dNum.getDate() + '' + dNum.getHours() + '' + dNum.getMinutes() + '' + dNum.getSeconds();
// var counter = count;
if (input.files && input.files[0]) {
  var reader = new FileReader();
  var $fileName = input.files[0].name;
  var $fileSize = input.files[0].size;
  var $fileMB = Math.round($fileSize / 1024);
  var fileExtension = ['jpeg', 'jpg', 'png', 'mp4'];
  var uploadData = '<li> <div class="boxImg"><p class="text-center text-uppercase font-weight-bold text-link uploadLink">+ Upload file</p> </div> <input type="file" class="uploadFile" id="uploadDoc-'+counter+'" data-width="460" data-height="460" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4"><input type="text" class="hidden" value="" name="imgupload[]" id="imgupload-'+counter+'" /> <div class="removeImg"><b>X</b> File: </div> </li>'

  if ($.inArray($this.val().split('.').pop().toLowerCase(), fileExtension) == -1) {
    $('#exnErrorModal').modal();
    console.log('FILE TYPE ERROR: ' + input.files[0].type)
    return false;
  } else {

    if (input.files[0].type === 'video/mp4') {
      reader.onload = function(e) {
        if ($fileMB > $vidSize) {
          $('#reqErrorModal').modal();
          console.log('FILE AND SIZE ERROR');
        } else {
          $nameText.html('<b>X</b> File: ' + $fileName).show();
          $this.next('input').val(e.target.result)
           // $("#imgupload-" +(counter-1)).val($fileName);
           $fileinfo.addClass('noCrop').html('').append('<video src="' + e.target.result + '" ></video>')
           $callRadio.show();
           $box.after(uploadData);
           console.log('VIDEO OK')
         }
       }
     } else {

      reader.onload = function(e) {
        var img = new Image;
        img.onload = function() {
          console.log('------- FILE ----------')
          console.log('SIZE: ' + $fileMB)
          console.log('Width: ' + img.width)
          console.log('Height: ' + img.width)
          console.log('Type: ' + input.files[0].type)
          console.log('-----------------');

          if (img.width < $width || img.height < $height || $fileMB > $imgSize) {
            $('#reqErrorModal').modal();
            console.log('FILE AND SIZE ERROR');
          } else {
            $nameText.html('<b>X</b> File: ' + $fileName).show();
            $this.next('input').val(e.target.result)
            $fileinfo.addClass('yesCrop').html('').append('<img src="' + e.target.result + '" alt="#" />')
            $callRadio.show();
            $box.after(uploadData);
            $box.find('.yesCrop').trigger('click');
            console.log('IMAGE OK')
          }
        }
        img.src = reader.result;
      }


    }

  };
  reader.readAsDataURL(input.files[0]);
}
}
$(document).on('change', '.uploadFile', function(e) {
  var idArr= this.id.split("-");
  var counter=(Number(idArr[1])+1);
  readURL(this,counter);
});


$(document).on('click', '.yesCrop', function(e) {
  var $imageID = $(this).next('input').attr('id');
  var $image = $(this).find('img').attr('src');
  $('#imageCrop').attr('src',$image);
  $('#afterCrop').attr('data-rel',$imageID);
  $('#cropModal').modal();
});

$('#cropModal').on('show.bs.modal', function () {
  $('#destroyCrop').trigger('click');
  $('#afterCrop').html('');
  setTimeout( function(){
    var $image = $('#imageCrop');
    $image.cropper({
      aspectRatio: 460/460,
      zoomable: true,
      rotatable: false,
      background: false,
      zoomOnTouch: false,
      zoomOnWheel: true,
      restore: true,
      strict: false,
      responsive: true,
      dragMode:'move',
      dragCrop: true,
      checkCrossOrigin:false,
      cropBoxMovable: true,
      cropBoxResizable: false,
                      //    minCropBoxWidth:1125,
                      minCropBoxHeight:280
                    });   
  }, 400);
});


$(document).on('click', '#cropBtn', function(e) {
  $('#getCrop').trigger('click');
  var $imgID = $('#afterCrop').attr('data-rel');
  var $imgData = $('#afterCrop').html();
  $('#'+$imgID ).next('input').addClass('ddddddd').val($imgData);
  $('#'+$imgID).prev('div').find('img').attr('src', $imgData);
});


// UPLOAD PROFILE PIC
$(document).on('click', '.uploadProfile', function() {
 var $fileUpload = $(this).closest('li').find('.uploadPic')
 $fileUpload.trigger('click');
});

$(document).on('click', '.removePic', function() {
  var $itemWrap = $(this).closest('ul');
  var $itemUpload = $(this).closest('li');
  var uploadData = '<li> <div class="boxImg"><p class="text-center text-uppercase font-weight-bold text-link uploadProfile">+ Upload file</p> </div> <input type="file" class="uploadPic" id="logo" data-width="460" data-height="460" data-img-size="1024"  accept="image/png, image/jpeg, image/jpg"><input type="text" class="hidden" value="" name="logo" /> <div class="removePic"><b>X</b> File: </div> </li>'
  $itemWrap.append(uploadData);
  $itemUpload.remove();
});
$(document).on('click', '.checkbox', function() {
  $('#'+this.id).is(":checked") ? 
  $("#"+this.id).val("1") :
  $("#"+this.id).val("0") ;

})

})(jQuery);

</script>
</body>
</html>
