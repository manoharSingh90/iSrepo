    <div class="col-12">
      <?php if($range =='-'){
        $set_date = $start . '-' . $end;
      }else{
        $set_date = $current_date;
      } ?>
      <p class=" font-weight-bold mt-3 mb-3 tagWrap">View as <span class="cust_viewDtl"><?= $range?></span><span id="setDate"> (<?= $set_date;?>)</span></p><a href="javascript:void(0);" class="btn btn-primary ml-3 d-inline-block data_export">EXPORT DATA (.xls)</a> </div>
    <div class="col-8">
      <div class="roundBox">
        <div class="row">
          <div class="col-12">
            <h2 class="text-uppercase text-dark h6 font-weight-bold mb-3">Machine Status</h2>
          </div>
        </div>
        <div class="tableWrap pt-1">
          <table class="scrollTbl">
            <thead>
              <tr>
                <th colspan="3">Country Name</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($machine_status)){ 
                foreach ($machine_status as $key => $statusValue) { ?>
                <tr>
                  <td><span class="user-link font-weight-bold viewAs"><?= $statusValue['country_name']==''?'Unknown':$statusValue['country_name']?></span></td>
                  <td><div class="sampleCount xtraWide borderStyle">
                      <ul>
                        <li><span><?= $statusValue['total_location']?></span><small>Total Locations</small></li>
                        <li><span><?= $statusValue['campaigns'] ?></span><small>Total Campaigns</small></li>
                        <li><span><?= $statusValue['vending_machine']?></span><small>Total Vending Machines</small></li>
                        <li><span><?= $statusValue['Active']?></span><small>Active</small></li>
                        <li><span><?= $statusValue['Inactive']?></span><small>Inactive</small></li>
                      </ul>
                    </div></td>
                  <td class="text-center"><a href="#" class="city_view" data-toggle="modal" rel="<?= $statusValue['country']?>">View Details</a></td>
                </tr>
              <?php } }else{echo"<tr><td colspan='4' class='text-center'> No record found";} ?>
             
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-4">
      <div class="roundBox">
        <div class="row align-items-center">
          <div class="col-6">
            <h2 class="text-uppercase text-dark h6 font-weight-bold mb-0">Alerts</h2>
          </div>
          <div class="col-6">
            <div class="btn-group float-right">
              <!-- <button type="button" class="btn btn-link text-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> All Cities </button>
              <div class="dropdown-menu dropdown-menu-right small">
                <button class="dropdown-item small" type="button">City 01</button>
                <button class="dropdown-item small" type="button">City 02</button>
                <button class="dropdown-item small" type="button">City 03</button>
              </div> -->
            </div>
          </div>
        </div>
        <div class="tableWrap">
          <table class="scrollTbl">
            <thead>
              <tr>
                <th>Machine ID</th>
                <th>Alert Type</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($machine_alerts)){
                foreach ($machine_alerts as $key => $alertValue) { ?>
                  <tr>
                    <td><a href="#" class="user-link font-weight-bold"><?= $alertValue['vending_machine_code']?></a></td>
                    <td><span class="text-danger font-weight-bold"><?= $alertValue['sample_left']?> Samples Left</span></td>
                  </tr>
              <?php  }
              }else{echo"<tr><td colspan='4' class='text-center'> No record found";} ?>
              
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="roundBox mt-4">
        <h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">Machine activity by campaign</h2>
        <div class="tableWrap">
          <table class="scrollTbl">
            <colgroup>
            <col style="width:25%;">
            <col style="width:15%;">
            <col style="width:15%;">
            <col style="width:15%;">
            <col style="width:15%;">
            <col style="width:15%;">
            </colgroup>
            <thead>
              <tr>
                <th>Campaign Name</th>
                <th>Machine ID</th>
                <th class="text-center font-weight-normal">Status</th>
                <th>Location</th>
                <th>#Samples Vended</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($machine_activity)){
                foreach ($machine_activity as $key => $activityValue) { if(!empty($activityValue['campaignVends'])) {?>
                  <tr>
                    <td colspan="6" class="pl-0 pr-0 pt-0"><table>
                        <colgroup>
                        <col style="width:25%;">
                        <col style="width:15%;">
                        <col style="width:15%;">
                        <col style="width:15%;">
                        <col style="width:15%;">
                        <col style="width:15%;">
                        </colgroup>
                        <?php foreach ($activityValue['campaignVends'] as $key => $campaignValue) { ?>
                          <tr class="border-top-0 border-bottom-0">
                            <td class="text-dark font-weight-bold text-uppercase">
                              <div class="fixedWrap"><strong><?= $activityValue['campaign_name'];$activityValue['campaign_name']='';?></strong></div>
                            </td>
                            <td class="vmc"><?= $campaignValue['machineVends'][0]['vending_machine_code'] ?></td>
                            <td class="text-center">
                              <?php $isActive = $campaignValue['machineVends'][0]['is_active']==1?'ACTIVE':'DEACTIVE'?>
                              <span class="statusMark active"><b></b><?= $isActive?></span></td>
                            <td><?= $campaignValue['machineVends'][0]['location_address'] ?> <?= $campaignValue['machineVends'][0]['postal_code'] ?></td>
                            <td class="sampleUsed"><?= $campaignValue['vend_no_of_sample_used'] ?></td>
                            <td class="text-center"><a href="#" class="codeUsage" data-toggle="modal" invalid="<?= $campaignValue['machineVends'][0]['invalid_try'];?>" >Diagnostics</a></td>
                          </tr>
                      <?php  } ?>
                        
                      </table></td>
                  </tr>
              <?php  } }
              }else{echo "<tr><td colspan='6' class='text-center'> No record found";} ?>
             
            </tbody>
          </table>
        </div>
      </div>
    </div>