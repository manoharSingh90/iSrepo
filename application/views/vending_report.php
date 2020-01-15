<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><span class="ml-2"><?= $title?></span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#" /></a> </div>
  </div>
  <div class="pageTabs mt-1 extrawWide">
    <ul>
      <li><a href="<?php echo base_url('report') ;?>">Users</a></li>
      <li><a href="<?php echo base_url('campaign-report');?>">Campaigns</a></li>
      <li><a href="<?php echo base_url('vending-machine-report');?>" class="active">Vending Machines</a></li>
    </ul>
  </div>
  <div class="createInfo">
    <div class="row pb-2 align-items-center">
      <div class="col-12 col-md-3 col-lg-3">
        <label class="col-form-label-sm">Start Date</label>
        <input id="startDateInput" type="text" name="inputSdate" value=" " autocomplete="off" placeholder="Start Date" class="form-control dateIcon" />
        <small id="stD_error" class="error"></small>

      </div>
      <div class="col-12 col-md-3 col-lg-3">
        <label class="col-form-label-sm ">End Date</label>
        <input id="endDateInput" type="text" name="inputEdate" value=" " autocomplete="off" placeholder="End Date" class="form-control dateIcon" />
        <small id="endD_error" class="error"></small>
      </div>
      
      <div class="col-12 col-md-3 col-lg-4 pt-4 text-center">
        <div class="custom-control custom-radio font-weight-bold d-inline-block">
          <input type="radio" class="custom-control-input rangeFilter" checked="checked" value="Daily" name="duration" id="ft-01">
          <label class="custom-control-label" for="ft-01">Daily</label>
        </div>
        <div class="custom-control custom-radio font-weight-bold d-inline-block ml-5 ">
          <input type="radio" class="custom-control-input rangeFilter" value="Weekly" name="duration" id="ft-02">
          <label class="custom-control-label" for="ft-02">Weekly</label>
        </div>
        <div class="custom-control custom-radio font-weight-bold d-inline-block ml-5">
          <input type="radio" class="custom-control-input rangeFilter" value="Monthly" name="duration" id="ft-03">
          <label class="custom-control-label" for="ft-03">Monthly</label>
        </div>
      </div>
      <div class="col-12 col-md-2 col-lg-2 text-center">
        <button class="btn btn-primary text-uppercase mt-3 pr-4 pl-4" id="applyFilter">Apply</button>
      </div>
    </div>
  </div>
    <div class="row" id="page_body">
      <?php $this->load->view('report_vending_body');?>
  </div>
</div>

<!-- DIAGNOSTICS MODAL -->
<div class="modal fade" id="diagnosticsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">Diagnostics</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body  pt-0">
        <div class="row pb-2">
          <div class="col-12">
            <h2 class="text-uppercase text-dark h6 font-weight-bold pb-0 m-0 pt-3 pl-1 machineCode">1200-323-VENDOO</h2>
            <small class="pl-1 d-block text-gray">QR Code Usage</small> </div>
        </div>
        <table class="text-uppercase small">
          <tr class="border-top-0">
            <td>#Codes Tried</td>
            <td class="tried">2500</td>
          </tr>
          <tr>
            <td>#Sample Vended</td>
            <td class="vended">2500</td>
          </tr>
          <tr>
            <td>#Invalid Codes</td>
            <td class="Invalid">2500</td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button class="btn btn-style" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- CITY STATISTICS MODAL -->
<div class="modal fade" id="cityStatsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary" id="cityOf">Cities (India)</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body tablescroll">
        <table class="table-sm tablescroll_head">
          <colgroup>
          <col style="width:25%;">
          <col style="width:60%;">
          <col style="width:15%;">
          </colgroup>
          <thead>
            <tr>
              <th>City Name</th>
              <th>Statistics</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="city_table_body">
            <tr>
              <td colspan="3" class="pl-0 pr-0 pt-0"><table>
                  <colgroup>
                  <col style="width:25%;">
                  <col style="width:60%;">
                  <col style="width:15%;">
                  </colgroup>
                  <tr class="border-top-0 border-bottom-0">
                    <td class="text-dark font-weight-bold"><strong>New Delhi</strong></td>
                    <td><div class="sampleCount xtraWide borderStyle">
                        <ul>
                          <li><span>34</span><small>Total Campaigns</small></li>
                          <li><span>234</span><small>Total Vending Machines</small></li>
                          <li><span>62</span><small>Active</small></li>
                          <li><span>62</span><small>Inactive</small></li>
                        </ul>
                      </div></td>
                    <td class="text-center"><a href="#" class="veiwDetail">Detail</a></td>
                  </tr>
                  <tr class="border-top-0 border-bottom-0 expandTable">
                    <td></td>
                    <td colspan="2"><table>
                        <thead>
                          <tr class="border-top-0">
                            <th>Machine ID</th>
                            <th class="text-center">Status</th>
                            <th>Location</th>
                            <th>#Samples</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>2388 - 8882 VEND</td>
                            <td class="text-center"><span class="statusMark active"><b></b>ACTIVE</span></td>
                            <td>Pacific Mall, Atlantic City,USA</td>
                            <td>3881</td>
                            <td class="text-center"><a href="#" data-toggle="modal" data-target="#diagnosticsModal">Diagnostics</a></td>
                          </tr>
                          <tr>
                            <td>2388 - 8882 VEND</td>
                            <td class="text-center"><span class="statusMark active"><b></b>ACTIVE</span></td>
                            <td>Pacific Mall, Atlantic City,USA</td>
                            <td>3881</td>
                            <td class="text-center"><a href="#" data-toggle="modal" data-target="#diagnosticsModal">Diagnostics</a></td>
                          </tr>
                          <tr>
                            <td>2388 - 8882 VEND</td>
                            <td class="text-center"><span class="statusMark active"><b></b>ACTIVE</span></td>
                            <td>Pacific Mall, Atlantic City,USA</td>
                            <td>3881</td>
                            <td class="text-center"><a href="#" data-toggle="modal" data-target="#diagnosticsModal">Diagnostics</a></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td colspan="3" class="pl-0 pr-0 pt-0"><table>
                  <colgroup>
                  <col style="width:25%;">
                  <col style="width:60%;">
                  <col style="width:15%;">
                  </colgroup>
                  <tr class="border-top-0 border-bottom-0">
                    <td class="text-dark font-weight-bold"><strong>New Delhi</strong></td>
                    <td><div class="sampleCount xtraWide borderStyle">
                        <ul>
                          <li><span>34</span><small>Total Campaigns</small></li>
                          <li><span>234</span><small>Total Vending Machines</small></li>
                          <li><span>62</span><small>Active</small></li>
                          <li><span>62</span><small>Inactive</small></li>
                        </ul>
                      </div></td>
                    <td class="text-center"><a href="#" class="veiwDetail">Detail</a></td>
                  </tr>
                  <tr class="border-top-0 border-bottom-0 expandTable">
                    <td></td>
                    <td colspan="2"><table>
                        <thead>
                          <tr class="border-top-0">
                            <th>Machine ID</th>
                            <th class="text-center">Status</th>
                            <th>Location</th>
                            <th>#Samples</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>2388 - 8882 VEND</td>
                            <td class="text-center"><span class="statusMark active"><b></b>ACTIVE</span></td>
                            <td>Pacific Mall, Atlantic City,USA</td>
                            <td>3881</td>
                            <td class="text-center"><a href="#" data-toggle="modal" data-target="#diagnosticsModal">Diagnostics</a></td>
                          </tr>
                          <tr>
                            <td>2388 - 8882 VEND</td>
                            <td class="text-center"><span class="statusMark active"><b></b>ACTIVE</span></td>
                            <td>Pacific Mall, Atlantic City,USA</td>
                            <td>3881</td>
                            <td class="text-center"><a href="#" data-toggle="modal" data-target="#diagnosticsModal">Diagnostics</a></td>
                          </tr>
                          <tr>
                            <td>2388 - 8882 VEND</td>
                            <td class="text-center"><span class="statusMark active"><b></b>ACTIVE</span></td>
                            <td>Pacific Mall, Atlantic City,USA</td>
                            <td>3881</td>
                            <td class="text-center"><a href="#" data-toggle="modal" data-target="#diagnosticsModal">Diagnostics</a></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                </table></td>
            </tr>
          </tbody>
        </table>
        <!-- <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-style" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT --> 
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.4.1.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/popper/popper.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/bootstrap-4.3.1/dist/js/bootstrap.min.js"></script> 
<script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/jquery-date-range-picker-master/dist/jquery.daterangepicker.min.js"></script> 
<script type="text/javascript" src="assets/dependencies/floatThead/dist/jquery.floatThead.min.js"></script> 
<script type="text/javascript">
(function($) {
    'use strict';

    // DATE PICKER
    var dateTime = new Date();
    dateTime = moment(dateTime).format('DD-MM-YYYY');
    $('#endDateInput').attr('data-date', dateTime);     
    $('#startDateInput').dateRangePicker({
        format: 'DD-MM-YYYY',
        autoClose: true,
        singleDate: true,
        showTopbar: false,
        singleMonth: true,
        selectForward: false,
        selectBackward: true,
        setValue: function(s) {
            if (!$(this).attr('readonly') && !$(this).is(':disabled') && s != $(this).val()) {
                $(this).val(s);
                $('#endDateInput').attr('data-date', s).val('');
            }
        },
        endDate: dateTime
    });
    $(document).on('focus', '#endDateInput', function(e) {
        var defaultDate = $(this).val();
        var startDate = $(this).attr('data-date');
        if (startDate == '') {
            var startDate = $(this).attr('data-date', dateTime);
        } else {
            var startDate = $(this).attr('data-date');
        }
        $(this).dateRangePicker({
            format: 'DD-MM-YYYY',
            autoClose: true,
            singleDate: true,
            showTopbar: false,
            singleMonth: true,
            selectBackward: false,
            selectForward: true,
            startDate: startDate,
            endDate: dateTime
        });
    });

      $('.scrollTbl').floatThead({
              scrollContainer: function($table) {
                      return $table.closest('.tableWrap');
              }
      });
      $('.rangeFilter').on('click',function() {
              if ($("input[type='radio'][name='duration']").is(':checked')) {
                var todayDate = moment().format('D-MMM-Y');  
                 $('#startDateInput').val('');
                 $('#endDateInput').val('');
                 $('.cust_viewDtl').text($(this).val());
                  $('#setDate').text(' ( '+todayDate+' ) ');
              }
            });

            $('#startDateInput,#endDateInput').on('click',function() {
              $("input[type='radio'][name='duration']").prop("checked", false);
          });
        $(document).on('click','#applyFilter',function(){
            //alert('lolo');
            $('.error').text('');
            var flag = 0;
            var stDate = $('#startDateInput').val();
            var edDate = $('#endDateInput').val();
            if ($("input[type='radio'][name='duration']").is(':checked')) {
              var filterRange = $("input[type='radio'][name='duration']:checked").val();
            }else{
              var filterRange='-';
            }
            
            if((stDate=='') && (filterRange=='-')) {
              flag = 1;
            }
            if (filterRange == '-' && stDate != '' && edDate == '') {
              flag = 1;
              $('#endD_error').text('This field is required.');
            }
            if (filterRange == '-' && edDate != '' && stDate == '') {
              flag = 1;
             $('#stD_error').text('This field is required.');
            }
            if(flag==0) {
              $.ajax({  
                    type: "POST",  
                    url:  "<?php echo base_url(); ?>" + "vending-machine-report",  
                    data: {stDate:stDate,edDate:edDate,range:filterRange},  
                    cache: false,
                    dataType:'json',
                    success: function(result){  
                        if(result.type=="success"){  
                          //$('#page_body').html('');
                          $("#page_body").html(result.view);
                          //$("#page_body").append(result.view);
                          //alert(result.vidw)
                          console.log(result);                          
                        }  
                        /*else  
                            jQuery("div#err_msg").show();  
                            jQuery("div#msg").html(result);  */
                    }  
                }); 
            }
          })
          
      $(document).on('click','.codeUsage',function(){
        var machinID = $(this).closest('tr').find('.vmc').text();
        var vSample = $(this).closest('tr').find('.sampleUsed').text();
        var cInvalid = $(this).attr('invalid');
        var codeTry = parseInt(vSample) + parseInt(cInvalid);
        $('.machineCode').text(machinID);
        $('.tried').text(codeTry);
        $('.vended').text(vSample);
        $('.Invalid').text(cInvalid);
        
        $("#diagnosticsModal").modal("show");
      });
      $(document).on('click', '.veiwDetail', function(e) {
              $(this).toggleClass('active');
              $(this).text($(this).text() == 'Detail' ? 'Close' : 'Detail');
              var defaultTR = $(this).closest('tr');
              var nextTR = defaultTR.next('tr');
              nextTR.slideToggle();
      });

      $(document).on('click','.city_view',function(){
            var country_id = $(this).attr('rel');
            var country_name = $(this).closest('tr').find('.viewAs').text();
            $('#cityOf').text('Cities ( '+ country_name +' )');
            $.ajax({
                type: "POST",  
                url:  "<?php echo base_url('city-state-data'); ?>",  
                data: {country_id:country_id},  
                cache: false,
                dataType:'json',
                success: function(result){                    
                    if(result.res=="success"){
                      //$('#cityStatsModal').modal('show');                      
                      $("#city_table_body").html(result.topcity);                    
                      console.log(result);
                      
                    }  
                    else  
                        $("#city_table_body").html('<tr><td colspan="3" class="text-center">'+result.topcity+'</td></tr>');

                      $("#cityStatsModal").modal("show");

                    $('#cityStatsModal').on('shown.bs.modal', function() {
                      setTimeout(function(){ 
                           $('.scrollModalTbl').tableScroll({height:300});
                       }, 60);

                    });
                }
            });
            //alert(country_id);
          })
      $(document).on('show.bs.modal', '.modal', function (event) {
           var zIndex = 1040 + (10 * $('.modal:visible').length);
           $(this).css('z-index', zIndex);
           setTimeout(function() {
               $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
           }, 0);
       });
      $(document).on('click','.data_export',function(){
            //alert('obob');
            var st = $('#startDateInput').val();
            var ed = $('#endDateInput').val();
            if ($("input[type='radio'][name='duration']").is(':checked')) {
              var range = $("input[type='radio'][name='duration']:checked").val();
            }else{
              var range='';
            }
            
            $.ajax({
              url:"<?php echo base_url('vending-export-data');?>",
              type:"POST",
              data:{st:st,ed:ed,range:range},
              dataType:"json",
              success: function(rs){
                console.log(rs);
                if(rs.msg=='success'){                  
                  location.href="<?= base_url('assets/files/');?>"+rs.file;
                  setTimeout(function(){ 
                    $.ajax({
                      url:"<?php echo base_url('trash-file');?>",
                      type:"POST",
                      data:{fileName:rs.file},

                      })                     
                 }, 60000);
                }
              }
            })
            

          })

})(jQuery);
</script>