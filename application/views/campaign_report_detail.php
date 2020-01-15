<style>
.activityWrap { width: 300px; }
.activityBox { font-size: 12px; font-weight: 500; color: #777; position: relative; padding-right: 45px; margin-bottom: 5px; }
.activityBox .activityBar { width: 0%; height: 14px; background: #000; position: relative; }
.activityBox .activityBar span { position: absolute; top: 50%; transform: translateY(-50%); right: -45px; text-align: right; }
.yellow-bg { background: #ffb400 !important; }
.pink-bg { background: #bf1f81 !important; }
</style>
<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a href="<?= base_url('campaign-report')?>" class="text-white mr-2">Back</a> | <span class="ml-2">Campaign Reports</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
  </div>
  <div class="brandDetail align-items-center pb-4">
    <div class="brandDetail-info w-auto">
      <div class="brand-link mb-0"><span><img src="<?php echo base_url();?>assets/campaign/banner/<?= $summery['banner_url']?>" alt="#"/></span>
        <h1><?= $summery['campaign_name']?></h1>
        <small class="d-block">Created on <?= date('M d, Y',strtotime($summery['created_dttm']))?></small></div>
    </div>
    <div class="brandDetail-gallery w-75">
      <div class="countStatus mb-0 w-auto">
        <div class="sampleCount w-auto borderStyle">
          <ul>
            <li><span><?= $summery['total_campaign_samples']?></span><small>Total Samples</small></li>
            <li><span><?= $summery['total_campaign_samples_used']?></span><small>Sample Redeemed</small></li>
            <li><span><?= $summery['promo']?></span><small>Promotions Redeemed</small></li>
            <li><span><?= $summery['wallpost']?></span><small>Total Post</small></li>
            <li><span><?= NPS($summery['id'])?></span><small>NPS</small></li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="text-right w-25 pr-3"><a href="#" rel="<?= $summery['id'];?>" class="btn btn-primary detail_data_export">EXPORT DATA (.exl)</a></div>
  </div>
  <div class="pageBody mt-0">
    <div class="row">
      <div class="col-7">
        <h2 class="text-uppercase text-dark h6 font-weight-bold mb-0">Promo Code Activity</h2>
      </div>
      <div class="col-5">
        <ul class="chartLegends text-right p-0">
          <li class="d-inline-block">
            <div class="legendBox"><b style="background:#bf1f81;"></b>Save</div>
          </li>
          <li class="d-inline-block ml-3">
            <div class="legendBox"><b style="background:#ffb400;"></b>Redeemed</div>
          </li>
        </ul>
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th>Post Description</th>
          <th>Post Type</th>
          <th class="text-center">Publish Date</th>
          <th>Activity</th>
          <th>Total NPS</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($promeActivity)) {
          foreach ($promeActivity as $key => $promoValue) { 
                  $total = $promoValue['Saved'] + $promoValue['Redeemed'];
                    if($total !=0 && $total !=''){
                      $savedBar = ($promoValue['Saved']*100)/$total;
                      $redeemBar = ($promoValue['Redeemed']*100)/$total;
                    }else{
                      $savedBar = 0;
                      $redeemBar = 0;
                    }
            ?>
            <tr>
              <td><a href="#" class="user-link font-weight-bold" style="word-break: break-all!important; width:250px; "><?= $promoValue['post_desc'];?></a></td>
              <td> Text + <?= $promoValue['banner_type']==1?'Image':'Video'?></td>
              <td class="text-center"><?= date('d M Y',strtotime($promoValue['publish_date']))?></td>
              <td><div class="activityWrap">
                  <div class="activityBox">
                    <div class="activityBar pink-bg" style="width:<?= $savedBar?>%;"><span><?= $promoValue['Saved']?></span></div>
                  </div>
                  <div class="activityBox">
                    <div class="activityBar yellow-bg" style="width:<?= $redeemBar?>%;"><span><?= $promoValue['Redeemed']?></span></div>
                  </div>
                </div></td>
              <td class="text-primary font-weight-bold"><?= NPS($promoValue['id'])?></td>
            </tr>
        <?php  }
        }else{echo"<td colspan='5' class='text-center'> No record found </td>";} ?>
       
      </tbody>
    </table>
    <!-- <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> -->
  </div>
<div class="pageBody mt-4">
    <h2 class="text-uppercase text-dark h6 font-weight-bold mb-0">Promo Post likes</h2>
    <table>
      <thead>
        <tr>
          <th rowspan="2">Post Description</th>
          <th rowspan="2">Post Type</th>
          <th rowspan="2" class="text-center">Total Likes</th>
          <th colspan="2" class="text-center">Male</th>
          <th colspan="2" class="text-center">Female</th>
        </tr>
        <tr>
          <th class="text-center">in no.</th>
          <th class="text-center">in %</th>
          <th class="text-center">in no.</th>
          <th class="text-center">in %</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($promeLike)) {
          foreach ($promeLike as $key => $likeValue) { ?>
            <tr>
              <td><a href="#" class="user-link font-weight-bold" style="word-break: break-all!important; width:250px; "><?= $likeValue['post_desc'];?></a></td>
              <td>Text + <?= $likeValue['banner_type']==1?'Image':'Video'?></td>
              <td class="text-center"><?= $likeValue['no_of_likes']?></td>
              <td class="text-center"><?= $likeValue['MALE']?></td>
              <td class="text-center"><?= $mPerc = $likeValue['M_percent']==''?'0.00':sprintf('%0.2f', $likeValue['M_percent']); ?>%</td>
              <td class="text-center"><?= $likeValue['Female']?></td>
              <td class="text-center"><?= $mPerc = $likeValue['F_percent']==''?'0.00':sprintf('%0.2f', $likeValue['F_percent']); ?>%</td>
            </tr>
        <?php  }
        }else{echo"<td colspan='6' class='text-center'> No record found </td>";} ?>
        
      </tbody>
    </table>
    <!-- <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> -->
  </div>
  <!-- <div class="pageBody mt-4">
    <h2 class="text-uppercase text-dark h6 font-weight-bold mb-0">Promo Post Shares</h2>
    <table>
      <thead>
        <tr>
          <th rowspan="2">Post Description</th>
          <th rowspan="2">Post Type</th>
          <th rowspan="2" class="text-center">Total Shares</th>
          <th colspan="2" class="text-center">Male</th>
          <th colspan="2" class="text-center">Female</th>
        </tr>
        <tr>
          <th class="text-center">in no.</th>
          <th class="text-center">in %</th>
          <th class="text-center">in no.</th>
          <th class="text-center">in %</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="6" class='text-center'>No record found</td>
          
        </tr>
      </tbody>
    </table>
     <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> 
  </div> -->
    <div class="pageBody mt-4">
    <h2 class="text-uppercase text-dark h6 font-weight-bold mb-0">Promo Post Comments</h2>
    <table>
      <thead>
        <tr>
          <th rowspan="2">Post Description</th>
          <th rowspan="2">Post Type</th>
          <th rowspan="2" class="text-center">Total Comments</th>
          <th colspan="2" class="text-center">Male</th>
          <th colspan="2" class="text-center">Female</th>
        </tr>
        <tr>
          <th class="text-center">in no.</th>
          <th class="text-center">in %</th>
          <th class="text-center">in no.</th>
          <th class="text-center">in %</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($promeComments)) {
          foreach ($promeComments as $key => $commentValue) { ?>
            <tr>
              <td><a href="#" class="user-link font-weight-bold" style="word-break: break-all!important; width:250px; "><?= $commentValue['post_desc'];?></a></td>
              <td>Text + <?= $commentValue['banner_type']==1?'Image':'Video'?></td>
              <td class="text-center"><?= $commentValue['no_of_comments']?></td>
              <td class="text-center"><?= $commentValue['MALE']?></td>
              <td class="text-center"><?= $mPerc = $commentValue['M_percent']==''?'0.00':sprintf('%0.2f', $commentValue['M_percent']); ?>%</td>
              <td class="text-center"><?= $likeValue['Female']?></td>
              <td class="text-center"><?= $mPerc = $commentValue['F_percent']==''?'0.00':sprintf('%0.2f', $commentValue['F_percent']); ?>%</td>
            </tr>
        <?php  }
        }else{echo"<td colspan='6' class='text-center'> No record found </td>";}?>
        
      </tbody>
    </table>
    <!-- <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> -->
  </div>
    <div class="pageBody mt-4">
    <h2 class="text-uppercase text-dark h6 font-weight-bold mb-0">Regular Post - All Activity</h2>
    <div class="tableWrap">
      <table class="scrollTbl">
        <thead>
          <tr>
            <th rowspan="2">Post Description</th>
            <th rowspan="2" class="text-center">Likes</th>
            <th colspan="2" class="text-center">Male</th>
            <th colspan="2" class="text-center">Female</th>
            <th rowspan="2" class="text-center border-left">Comments</th>
            <th colspan="2" class="text-center">Male</th>
            <th colspan="2" class="text-center">Female</th>
          </tr>
          <tr>
            <th class="text-center">in no.</th>
            <th class="text-center">in %</th>
            <th class="text-center">in no.</th>
            <th class="text-center">in %</th>
            <th class="text-center">in no.</th>
            <th class="text-center">in %</th>
            <th class="text-center">in no.</th>
            <th class="text-center">in %</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($regularPost)){
            foreach ($regularPost as $key => $postValue) { ?>
              <tr><?php //$postText = substr($postValue['post_desc'],0,20) ?>
                <td class="text-dark font-weight-bold"><a href="#" class="user-link text-dark font-weight-bold" style="word-break: break-all!important; width:250px; " ><?= $postValue['post_desc']?></a><span class="d-block text-black-50"> Text + <?= $postValue['banner_type']==1?'Image':'Video'?></span></td>
                <td class="text-center font-weight-bold text-primary"><?= $postValue['no_of_likes']?></td>
                <td class="text-center"><?= $postValue['Likes'][0]['MALE']==''?'0':$postValue['Likes'][0]['MALE']?></td>
                <td class="text-center"><?= $mPerc = $postValue['Likes'][0]['M_percent']==''?'0.00':sprintf('%0.2f', $postValue['Likes'][0]['M_percent']); ?>%</td>
                <td class="text-center"><?= $postValue['Likes'][0]['Female']==''?'0':$postValue['Likes'][0]['Female']?></td>
                <td class="text-center"><?= $mPerc = $postValue['Likes'][0]['F_percent']==''?'0.00':sprintf('%0.2f', $postValue['Likes'][0]['F_percent']); ?>%</td>
                <td class="text-center font-weight-bold text-primary border-left"><?= $postValue['no_of_comments']?></td>
                <td class="text-center"><?= $postValue['Comments'][0]['MALE']==''?'0':$postValue['Comments'][0]['MALE']?></td>
                <td class="text-center"><?= $mPerc = $postValue['Comments'][0]['M_percent']==''?'0.00':sprintf('%0.2f', $postValue['Comments'][0]['M_percent']); ?>%</td>
                <td class="text-center"><?= $postValue['Comments'][0]['Female']==''?'0':$postValue['Comments'][0]['Female']?></td>
                <td class="text-center"><?= $mPerc = $postValue['Comments'][0]['F_percent']==''?'0.00':sprintf('%0.2f', $postValue['Comments'][0]['F_percent']); ?>%</td>
              </tr>
          <?php  }

          }else{echo"<td colspan='10' class='text-center'> No record found </td>";}?>
          
          <!-- <tr>
            <td class="text-dark font-weight-bold"><a href="#" class="user-link text-dark font-weight-bold">Lorem Ipsum is simply dummy text ...</a><span class="d-block text-black-50">Text</span></td>
            <td class="text-center font-weight-bold text-primary">1500</td>
            <td class="text-center">500</td>
            <td class="text-center">50%</td>
            <td class="text-center">500</td>
            <td class="text-center">50%</td>
            <td class="text-center font-weight-bold text-primary border-left">1500</td>
            <td class="text-center">500</td>
            <td class="text-center">50%</td>
            <td class="text-center">500</td>
            <td class="text-center">50%</td>
          </tr> -->
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- SCRIPT --> 

<script type="text/javascript">
(function($) {
    'use strict';

    /*$(".deactiveCheck").each(function() {
        $(this).on("change", function() {
            if ($(this).is(":checked")) {
                $(this).closest("tr").addClass("disabled");
            } else {
                $(this).closest("tr").removeClass("disabled");
            }
        });
    });*/

    /*$('#gallerySilder').owlCarousel({
        margin: 10,
        nav: true,
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
    });*/

    /*  $('.img-link').mediaBox({
        closeImage: 'media/close.png',
        openSpeed: 1000,
        closeSpeed: 800
      });*/

    $(document).on('click','.detail_data_export',function(){
            //alert('obob');
            //var st = $('#startDateInput').val();
            //var ed = $('#endDateInput').val();
            var campaign_id = $(this).attr('rel');
                        
            $.ajax({
              url:"<?php echo base_url('campaign-detail-data-export');?>",
              type:"POST",
              data:{campaign_id:campaign_id},
              dataType:"json",
              success: function(rs){
                console.log(rs);
                if(rs.msg=='success'){                  
                  location.href="<?= base_url('assets/files/');?>"+rs.file;
                  
                }
              }
            });            
          });

})(jQuery);
</script>