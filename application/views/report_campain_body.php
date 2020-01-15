<div class="col-12">
  <?php if($range =='-'){
    $set_date = $start . '-' . $end;
  }else{
    $set_date = $current_date;
  } ?>
      <p class=" font-weight-bold mt-3 mb-3 tagWrap"> View as <span class="cust_viewDtl"><?= $range?></span><span id="setDate"> (<?= $set_date;?>)</span></p>
      <a class="btn btn-primary ml-3 d-inline-block data_export" href="javascript:void(0);">EXPORT DATA (.xls)</a> </div>
    <div class="col-6">
      <div class="roundBox">
        <div class="row">
          <div class="col-7">
            <h2 class="text-uppercase text-dark h6 font-weight-bold mb-0">Live Campaigns - Codes</h2>
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
        <div class="tableWrap">
          <table class="scrollTbl">
            <thead>
              <tr>
                <th colspan="3">Campaign Name</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($code)){ 
                  foreach ($code as $key => $codeValue) { 
                    $total = $codeValue['saved'] + $codeValue['redeems'];
                    if($total !=0 && $total !=''){
                      $savedBar = ($codeValue['saved']*100)/$total;
                      $redeemBar = ($codeValue['redeems']*100)/$total;
                    }else{
                      $savedBar = 0;
                      $redeemBar = 0;
                    }
                    
                    //print_r($total);die;
                    ?>
              <tr>
                <td><a href="#" class="user-link font-weight-bold"><?= $codeValue['compaign_name'];?></a></td>
                <td><div class="activityWrap">
                    <div class="activityBox">
                      <div class="activityBar pink-bg" style="width:<?=$savedBar?>%;"><span><?= $codeValue['saved'];?></span></div>
                    </div>
                    <div class="activityBox">
                      <div class="activityBar yellow-bg" style="width:<?=$redeemBar?>%;"><span><?= $codeValue['redeems'];?></span></div>
                    </div>
                  </div></td>
                <td class="text-center"><a href="<?= base_url('campaign-report-detali/')?><?= $codeValue['campaignId']?>" target="_blank">View</a></td>
              </tr>
            <?php } }else{echo"<tr><td colspan='3' class='text-center'> No record found";}?>
            
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="roundBox">
        <h2 class="text-uppercase text-dark h6 font-weight-bold mb-3">Live Campaigns - Reviews</h2>
        <div class="tableWrap pt-1">
          <table class="scrollTbl">
            <thead>
              <tr>
                <th>Campaign Name</th>
                <th>Reviews</th>
                <th>Avg. Rating</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($reviews)) { 
                foreach ($reviews as $key => $reviewVal) { 
                  $total = 10;
                    if($total !=0 && $total !=''){
                      $rat = ($reviewVal['avg_rating']*100)/$total;
                    }else{
                      $rat = 0;                      
                    }

                    // REVIEW RATING

                    $tt = $reviewVal['total_review'];
                    $rr = ($reviewVal['avg_review']*100)/$tt;
                  ?>
              <tr>                
                <td><a href="#" class="user-link font-weight-bold mk_campaign"><?= $reviewVal['campaign_name']?></a></td>
                <td><div class="reviewFlex">
                    <div class="donut-widget" data-chart-size="small" data-chart-max="100" data-chart-value="<?= $rr ?>" data-chart-primary="#fe8341"  data-chart-background="#eee"></div>
                    <div class="reviewBox"><strong class="avrev"> <?= $reviewVal['avg_review']?></strong><small class="totalRevw" mkRew="<?= $reviewVal['total_review']?>">Of <?= $reviewVal['total_review']?></small></div>
                  </div></td>
                <td><div class="activityWrap rateBox">
                    <div class="activityBox">
                      <div class="activityBar yellow-bg" style="width:<?= $rat?>%;"><span><?= $reviewVal['avg_rating']?></span></div>
                    </div>
                  </div></td>
                <?php  $rateClass =  $reviewVal['avg_review']==0?'no_data':'rate_review'?>
                <td class="text-center"><input type="hidden" class="getNps" value="<?= NPS($reviewVal['campaign_id'])?>"><a href="javascript:void(0)" class="<?= $rateClass ?>" rel="<?= $reviewVal['campaign_id']?>" data-toggle="modal" >View</a></td>
              </tr>
            <?php } } else{echo"<tr><td colspan='3' class='text-center'> No record found";} ?>              
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="roundBox mt-4">
        <h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">Review Question Distribution</h2>
        <div class="tableWrap">
          <table class="scrollTbl">
            <colgroup>
            <col style="width:20%;">
            <col style="width:15%;">
            <col style="width:15%;">
            <col style="width:10%;">
            <col style="width:15%;">
            <col style="width:15%;">
            <col style="width:10%;">
            </colgroup>
            <thead>
              <tr class="text-uppercase">
                <th rowspan="2" class="font-weight-normal">Campaign Name</th>
                <th colspan="2" class="text-center font-weight-normal">Male</th>
                <th rowspan="2"> </th>
                <th colspan="2" class="text-center font-weight-normal">Female</th>
                <th rowspan="2"> </th>
              </tr>
              <tr>
                <th class="text-center font-weight-normal">in no.</th>
                <th class="text-center font-weight-normal">in %</th>
                <th class="text-center font-weight-normal">in no.</th>
                <th class="text-center font-weight-normal">in %</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($reviews_question)) {
                foreach ($reviews_question as $key => $qusValue) {?>
                    <tr>
                      <td colspan="7" class="pl-0 pr-0 pt-0"><table>
                          <colgroup>
                          <col style="width:20%;">
                          <col style="width:15%;">
                          <col style="width:15%;">
                          <col style="width:10%;">
                          <col style="width:15%;">
                          <col style="width:15%;">
                          <col style="width:10%;">
                          </colgroup>
                          <tr class="border-top-0 border-bottom-0">                            
                            <td class="text-dark font-weight-bold"><div class="fixedWrap"><strong><?= $qusValue['campaign_name'] ?></strong><b class="expandMark1"></b></div></td>
                            <td class="text-center"><strong><?= $qusValue['MALE'] ?></strong></td>
                            <td class="text-center"><strong><?= $mPerc = $qusValue['M_percent']==''?'0.00':sprintf('%0.2f', $qusValue['M_percent']); ?>%</strong></td>
                            <td class="text-center"><a href="#"></a></td>
                            <td class="text-center"><strong><?= $qusValue['Female'] ?></strong></td>
                            <td class="text-center"><strong><?= $mPerc = $qusValue['F_percent']==''?'0.00':sprintf('%0.2f', $qusValue['F_percent']); ?>%</strong></td>
                            <td class="text-center"><a href="#"></a></td>
                          </tr>
                          <?php if(!empty($qusValue['reviewQus'])){
                          for ($i=0;$i<sizeOf($qusValue['reviewQus']);$i++ ) { $expandTab = $i==0?'expandTable':'';?>
                            <tr class="border-top-0 border-bottom-0 expandTable1 ">
                              <td colspan="7" class="pl-0 pr-0"><table class="innerTable">
                                  <colgroup>
                                  <col style="width:20%;">
                                  <col style="width:15%;">
                                  <col style="width:15%;">
                                  <col style="width:10%;">
                                  <col style="width:15%;">
                                  <col style="width:15%;">
                                  <col style="width:10%;">
                                  </colgroup>
                                  <tr>
                                    <td colspan="7" class="qusTxt"><?= $qusValue['reviewQus'][$i]['ques_text'] ?></td>
                                  </tr>
                                  <?php $j=0; $cnt=0; foreach ($qusValue['reviewQus'][$i]['ansOpt'] as $key => $ansTxt) { $j++;

                                    foreach ($reviewAns as $anykey => $anyValue) {
                                      if($ansTxt['question_id']==$anyValue['qus_id'] && strtolower($ansTxt['answer_text'])==strtolower($anyValue['ans']))
                                      {
                                        $maleCount = $anyValue['MALE'];
                                        $FmaleCount = $anyValue['Female'];
                                        $malePer = $anyValue['M_percent'];
                                        $FePer = $anyValue['F_percent'];
                                      }
                                    }
                                    
                                    /*for($r=0;$r<sizeof($qusValue['reviewQus'][$i]['ansOpt']);$r++){
                                        if($ansTxt['question_id']==$reviewAns[$r]['qus_id'] && $ansTxt['answer_text']==$reviewAns[$r]['ans']){
                                        $maleCount = $reviewAns[$r]['MALE'];
                                        $FmaleCount = $reviewAns[$r]['Female'];
                                        $malePer = $reviewAns[$r]['M_percent'];
                                        $FePer = $reviewAns[$r]['F_percent'];
                                      }
                                    }*/

                                      $maleCount = $maleCount ==''?'0':$maleCount;
                                      $FmaleCount = $FmaleCount ==''?'0':$FmaleCount;
                                      $malePer = $malePer ==''?'0.00%':$malePer;
                                      $FePer = $FePer ==''?'0.00%':$FePer;

                                    ?>
                                    <tr> 
                                      <input class="inputIndex" type="hidden" value = "<?= $j ?>">
                                      <td><div class="fixedWrap"><?= $ansTxt['answer_text']?></div></td>
                                      <td id = "ucount1_<?= $j ?>" class="text-center ucount"><?= $maleCount?></td>
                                      <td id = "first_<?= $j ?>" class="text-center percent"><?= $mPerc = $malePer==''?'0.00':sprintf('%0.2f', $malePer); ?>%</td>
                                      <td class="text-center"><?php $isShow = $maleCount=='0'?'user_review_off':'user_review_show'?><a href="javascript::void(0)" class="<?= $isShow?>" data-toggle="modal" mkCampaign="<?= $qusValue['campaign_name'] ?>" rel="<?= $ansTxt['question_id'] ?>" gender='1'>View</a></td>
                                      <td id = "ucount2_<?= $j ?>" class="text-center ucount"><?= $FmaleCount?></td>
                                      <td id = "second_<?= $j ?>"class="text-center percent"><?= $mPerc = $FePer==''?'0.00':sprintf('%0.2f', $FePer); ?>%</td>
                                      <td class="text-center"><?php $isShow = $FmaleCount=='0'?'user_review_off':'user_review_show'?><a href="#" class="<?= $isShow?>" data-toggle="modal" mkCampaign="<?= $qusValue['campaign_name'] ?>" rel="<?= $ansTxt['question_id'] ?> " gender='2' >View</a></td>
                                    </tr>
                                <?php  }?>                                  
                                </table></td></tr> 
                              <?php  }
                            }else{echo "<tr><td colspan='3' class='text-center'> No record found";}?>
                                                   
                        </table></td>
                    </tr>
              <?php  }
              }else{echo "<tr><td colspan='6' class='text-center'> No record found";}?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

