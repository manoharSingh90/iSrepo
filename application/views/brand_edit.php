<div class="pageArea">
  <div class="pageHeader clearfix">
    <!-- <h2 class="float-left pageTitle"><a href="<?php //echo base_url('brands');?>" class="text-white mr-2">Back</a>  -->
    <h2 class="float-left pageTitle"><a   href="<?php echo base_url('brand-detail-campaign/'.$brandDtl->id);?>" class="text-white mr-2" onClick="history.back();" value="Back" style="cursor: pointer">Back</a>| <span class="ml-2">Edit Brand</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
    </div>
    <?php 
    //print_r($brandDtl);
    $attributes = array('id' => 'brand-form');
    echo form_open('admin/brands/editBrands', $attributes);?>
    <input type="hidden" name="id" value="<?php echo $brandDtl->id;?>">
    <div id="err_msg" style="display: none">   
      <h2 style="color: red"><div id="msg"> </div></h2>
    </div> 
    <div class="createInfo mt-1">
      <div class="pt-2 pb-5">
        <div class="row ">
          <div class="col-12 col-md-3">
            <h2>Logo</h2>
            <small>Image size: 1 MB Max | Image Resolution: 50 X 50</small>
            <ul class="logoImages">
              <?php /*if($brandDtl->brand_logo_url=="" || !file_exists('assets/brand/logo/'.$brandDtl->brand_logo_url)) { ?>
               <li>
                <div class="boxImg"><img src="" alt="#" style="display:none;"/>
                  <p class="text-center text-uppercase font-weight-bold text-link uploadProfile" style="display:block;">+ Upload file</p>
                </div>
                <input type="file" class="uploadPic" id="logo" data-width="50" data-height="50" data-img-size="1024" accept="image/png, image/jpeg, image/jpg"  required/>
              <input type="text" class="hidden" value="" name="logo"/>
                <a href="#" class="removePic"><b>X</b> File: </a> </li>
              <?php } else {*/ ?>
              <li>
                <div class="boxImg"><?php if($brandDtl->brand_logo_url!="" && file_exists('assets/brand/logo/'.$brandDtl->brand_logo_url)) { ?><img src="<?php  echo base_url('assets/brand/logo/'.$brandDtl->brand_logo_url);?>" alt="<?php echo $brandDtl->brand_name ;?>"><?php } ?></div>
                <input type="file" class="uploadPic" id="logo" data-width="50" data-height="50" data-img-size="1024" accept="image/png, image/jpeg, image/jpg" <?php if($brandDtl->brand_logo_url=="" || !file_exists('assets/brand/logo/'.$brandDtl->brand_logo_url)) echo "required";?>/>
                <input type="text" class="hidden" value="<?php //echo $brandDtl->brand_logo_url;?>" name="logo"/>
                <a href="#" class="removePic" style="display: inline;"><b>X</b> File: <?php  echo $brandDtl->brand_logo_url;?></a> </li>
                <input type="hidden" name="old_logo" value="<?php echo $brandDtl->brand_logo_url;?>"/>
              <?php //} ?>
              </ul>
            </div>
            <div class="col-12 col-md-4">
              <h2>Basic Details</h2>
              <label class="col-form-label-sm">Brand Name</label>
              <input type="text" name="brand_name" value="<?php echo $brandDtl->brand_name ;?>"  class="form-control" required/>
            </div>
            <div class="col-12 col-md-4">
              <label class="col-form-label-sm pt-4">Brand Description (upto 250 characters)</label>
              <textarea class="form-control" name="brand_desc" required><?php echo $brandDtl->brand_desc ;?></textarea>
            </div>
          </div>
        </div>
        <h2>Media</h2>
        <small>Image size: 1 MB Max | Min Image Resolution: 460 X 460 | Video Size: 5 MB Max an MP4 Format Only</small>
        <div class="pb-1">
          <ul class="postImages squareImg mt-1">
            <?php 
            $counter=1; 
            if($media) {
              foreach ($media as $key => $value) {  ?> 
                <li>
                  <?php if($value->asset_type=='1') { if(file_exists('assets/brand/assets/'.$value->asset_url)) {?>
                  <div class="boxImg yesCrop"><img src="<?php if(file_exists('assets/brand/assets/'.$value->asset_url)) echo base_url('assets/brand/assets/'.$value->asset_url); ?>" alt="<?php echo $brandDtl->brand_name ;?>">
                  </div>
                <?php } }else if($value->asset_type=='2') { if(file_exists('assets/brand/assets/'.$value->asset_url)) {?>
                  <div class="boxImg noCrop"><video src="<?php if(file_exists('assets/brand/assets/'.$value->asset_url)) echo base_url('assets/brand/assets/'.$value->asset_url);?>"></video>
                  </div>
                <?php } }?>
                  <input type="file" class="uploadFile" id="uploadDoc-<?php echo $counter;?>" data-width="460" data-height="460" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4" />
                  <input type="text" value="<?php //echo $value->asset_url;?>" name="imgupload[]" id="imgupload-<?php echo $counter;?>" class="hidden" />
                  <input type="text" name="asset_url[]" value="<?php echo $value->asset_url;?>" class="hidden">
                  <input type="text" name="brand_asset_id[]" value="<?php echo $value->id;?>" class="hidden">
                  <input type="text" name="asset_type[]" value="<?php echo $value->asset_type;?>" class="hidden">
                  <?php if(file_exists('assets/brand/assets/'.$value->asset_url)) { ?>
                  <div class="removeImg" style="display: block;"><b>X</b> File: <?php echo $value->asset_url;?></div>
                <?php } ?>
                </li>
              <?php $counter++;} } ?>
              <li>
                <div class="boxImg"><img src="" alt="#" style="display: none;" >
                  <p class="text-center text-uppercase font-weight-bold text-link uploadLink">+ Upload file</p>
                </div>
                <input type="file" class="uploadFile" id="uploadDoc-<?php echo ($counter+1);?>" data-width="460" data-height="460" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4" />
                <input type="text" value="" name="imgupload[]" id="imgupload-<?php echo ($counter+1);?>" class="hidden" />
                <input type="text" name="asset_url[]" value="" class="hidden">
                <input type="text" name="brand_asset_id[]" value="" class="hidden">
                <input type="text" name="asset_type[]" value="" class="hidden">
                <div class="removeImg"><b>X</b> File: Camp1.jpg</div>
              </li>
            </ul>
          </div>
          <div class="pt-2 pb-2 text-right">
            <button class="btn btn-link text-dark mr-3 cancel">Cancel</button>
            <button class="btn btn-style" id="create">Update</button>
          </div>
        </div>
        <?php echo form_close();?>
      </div>
      <footer></footer>

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
                  <div class="img-container"> <img id="imageCrop" src="" alt="Picture"> </div>
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
      <script type="text/javascript">
        $(document).ready(function(){
        });
        (function($) {
          'use strict';
        // FORM SUBMIT
        $("#brand-form").validate({
          errorElement: 'small',
          submitHandler: function() {
            $("#create").attr("disabled", true);
            $('#startConfetti').trigger('click');
            var user = $('#brand-form').serialize();
            $.ajax({  
              type: "POST",  
              url:  "<?php echo base_url(); ?>" + "admin/brands/editBrands",  
              data: user,  
              cache: false,  
              success: function(result){  
               if(result=="success"){ 
                window.location.replace("<?php echo base_url('brands');?>");  
              }  
              else  {
                $("#create").attr("disabled", false);
                /*jQuery("div#err_msg").show();  
                jQuery("div#msg").html(result); */
              }
            }  
          });  
          }
        });


 // UPLOAD FILE
 $(document).on('click', '.uploadLink', function() {
   var $fileUpload = $(this).closest('li').find('.uploadFile');
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
  var uploadData = '<li> <div class="boxImg"><p class="text-center text-uppercase font-weight-bold text-link uploadLink">+ Upload file</p> </div> <input type="file" class="uploadFile" id="uploadDoc-'+counter+'" data-width="460" data-height="460" data-img-size="1024" data-video-size="5120" accept="image/png, image/jpeg, image/jpg, video/mp4"><input type="text" class="hidden" value="" name="imgupload[]" id="imgupload-'+counter+'" /> <input type="text" name="asset_url[]" value="" class="hidden"><input type="text" name="brand_asset_id[]" value="" class="hidden"> <input type="text" name="asset_type[]" value="" class="hidden"> <div class="removeImg"><b>X</b> File: </div> </li>'

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
  var uploadData = '<li> <div class="boxImg"><p class="text-center text-uppercase font-weight-bold text-link uploadProfile">+ Upload file</p> </div> <input type="file" class="uploadPic" id="logo" data-width="50" data-height="50" data-img-size="1024"  accept="image/png, image/jpeg, image/jpg"><input type="text" class="hidden" value="" name="logo" /> <div class="removePic"><b>X</b> File: </div> </li>'
  $itemWrap.append(uploadData);
  $itemUpload.remove();
});

$(document).on('change', '.uploadPic', function(e) {
  picURL(this);
});


function picURL(input) {
  var $this = $(input);
  var $thisID = $this.attr('id');
  var $width = $this.attr('data-width');
  var $height = $this.attr('data-height');
  var $vidSize = $this.attr('data-video-size')

  var $imgSize = $this.attr('data-img-size')
  var $box = $this.closest('li');
  var $nameText = $box.find('.removePic');
  var $callRadio = $box.find('.custom-control');
  var $uploadLink = $box.find('.uploadLink');
  var $fileinfo = $box.find('.boxImg');
  var $imgInput = $fileinfo.find('img');
  var dNum = new Date();
  var count = dNum.getFullYear() + '' + (dNum.getMonth() + 1) + '' + dNum.getDate() + '' + dNum.getHours() + '' + dNum.getMinutes() + '' + dNum.getSeconds();
  var counter = count;

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    var $fileName = input.files[0].name
    var $fileSize = input.files[0].size
    var $fileMB = Math.round($fileSize / 1024);
    var fileExtension = ['jpeg', 'jpg', 'png'];

    if ($.inArray($this.val().split('.').pop().toLowerCase(), fileExtension) == -1) {
      $('#exnErrorModal').modal();
      console.log('FILE TYPE ERROR: ' + input.files[0].type)
      return false;
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
            console.log('IMAGE OK')
          }
        }
        img.src = reader.result;
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
}
})(jQuery);
</script>
</body>
</html>