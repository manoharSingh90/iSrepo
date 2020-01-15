<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><span class="ml-2">REPORTS</span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#" /></a> </div>
  </div>
  <div class="pageTabs mt-1 extrawWide">
    <ul>
      <li><a href="<?php echo base_url('report') ;?>">Users</a></li>
      <li><a href="<?php echo base_url('campaign-report');?>" class="active">Campaigns</a></li>
      <li><a href="<?php echo base_url('vending-machine-report');?>">Vending Machines</a></li>
    </ul>
  </div>
  <div class="createInfo">
    <div class="row pb-2 align-items-center">
      <div class="col-12 col-md-3 col-lg-3">
        <label class="col-form-label-sm">Start Date</label>
        <input id="startDateInput" type="text" name="inputSdate" value=" " placeholder="Start Date" autocomplete="off" class="form-control dateIcon" />
        <small id="stD_error" class="error"></small>
      </div>
      <div class="col-12 col-md-3 col-lg-3">
        <label class="col-form-label-sm ">End Date</label>
        <input id="endDateInput" type="text" placeholder="End Date" value=" " name="inputEdate" autocomplete="off" class="form-control dateIcon" />
        <small id="endD_error" class="error"></small>
      </div>
      
      <div class="col-12 col-md-3 col-lg-4 pt-4 text-center">
        <div class="custom-control custom-radio font-weight-bold d-inline-block">
          <input type="radio" class="custom-control-input rangeFilter" checked="checked" name="duration" value="Daily" id="ft-01">
          <label class="custom-control-label" for="ft-01">Daily</label>
        </div>
        <div class="custom-control custom-radio font-weight-bold d-inline-block ml-5 ">
          <input type="radio" class="custom-control-input rangeFilter" value="Weekly" name="duration" id="ft-02">
          <label class="custom-control-label" for="ft-02">Weekly</label>
        </div>
        <div class="custom-control custom-radio font-weight-bold d-inline-block ml-5">
          <input type="radio" class="custom-control-input rangeFilter" name="duration" value="Monthly" id="ft-03">
          <label class="custom-control-label" for="ft-03">Monthly</label>
        </div>
      </div>
      <div class="col-12 col-md-2 col-lg-2 text-center">
        <button class="btn btn-primary text-uppercase mt-3 pr-4 pl-4" id="applyFilter">Apply</button>
      </div>
    </div>
  </div>  
    <div class="row" id="page_body">
      <?php $this->load->view('report_campain_body');?>
  </div>
</div>
<!-- TRENDING POST DETAILS (TOP 5) MODAL -->
<div class="modal fade" id="trendingPostModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">Trending Post Details (Top 5)</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table-sm">
          <thead>
            <tr>
              <th></th>
              <th>Activity</th>
              <th>Likes</th>
              <th>Shares</th>
              <th>Comments</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><a href="users_detail.html" class="user-link">Comingsoon_parfumdemeos.mp3</a></td>
              <td><div class="activityWrap">
                  <div class="activityBox">
                    <div class="activityBar pink-bg" style="width:80%;"><span>2500</span></div>
                  </div>
                  <div class="activityBox">
                    <div class="activityBar yellow-bg" style="width:55%;"><span>500</span></div>
                  </div>
                </div></td>
              <td class="text-primary">125</td>
              <td class="text-primary">125</td>
              <td class="text-primary">125</td>
            </tr>
            <tr>
              <td><a href="users_detail.html" class="user-link">Comingsoon_parfumdemeos.mp3</a></td>
              <td><div class="activityWrap">
                  <div class="activityBox">
                    <div class="activityBar pink-bg" style="width:80%;"><span>2500</span></div>
                  </div>
                  <div class="activityBox">
                    <div class="activityBar yellow-bg" style="width:55%;"><span>500</span></div>
                  </div>
                </div></td>
              <td class="text-primary">125</td>
              <td class="text-primary">125</td>
              <td class="text-primary">125</td>
            </tr>
          </tbody>
        </table>
        <!-- <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-link" type="button" data-dismiss="modal">Close</button>
        <button class="btn btn-style" type="button" data-dismiss="modal">View all details</button>
      </div>
    </div>
  </div>
</div>

<!-- REVIEW QUESTION MODAL -->
<div class="modal fade" id="reviewQusModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">Review Question Distribution</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body pt-1">
        <div class="row pb-3 align-items-center">
          <div class="col-8">
            <h2 class="text-uppercase text-dark h6 font-weight-bold pb-0 m-0 pl-1 mkcampaign">Winter Takes All</h2>
            <small class="pl-1 d-block text-gray">Male Profiles-cummulative</small>
            <h3 class="small font-weight-bold text-dark m-0 pt-3  pl-1 Qus1">Question-Do you like the packaging of the product? </h3>
            <h3 class="small font-weight-bold text-dark m-0 pt-1 Ans1 pl-1">Answer-Yes</h3>
          </div>
          <div class="col-4">
            <div class="countStatus float-right">
              <div class="sampleCount borderStyle">
                <ul>
                  <li><span class="total_user_count">125</span><small>Total Users</small></li>
                  <li><span class="total_user_pre">45%</span><small>User %</small></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="tableWrap">
        <table class="scrollModalTbl">
          <thead>
            <tr>
              <th>UserName</th>
              <th>Age Group</th>
              <th>Joined On</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="userRevBody">
            <tr>
              <td><a href="users_detail.html" class="user-link"><span><img src="assets/img/profile/sample_01.jpg" alt="#"></span>John Die</a></td>
              <td>25-30 </td>
              <td>12 Feb 2022</td>
              <td><a href="#" class="text-link">View Profile</a></td>
            </tr>
            <tr>
              <td><a href="users_detail.html" class="user-link"><span><img src="assets/img/profile/sample_01.jpg" alt="#"></span>John Die</a></td>
              <td>25-30 </td>
              <td>12 Feb 2022</td>
              <td><a href="#" class="text-link">View Profile</a></td>
            </tr>
          </tbody>
        </table>
      </div>
        <!-- <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-style" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- LIVE CAMPAIGNS REVIEW MODAL -->
<div class="modal fade" id="reviewRateModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title pt-1 text-primary">Live Campaigns Review</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row pb-3 align-items-center">
          <div class="col-7">
            <h2 class="text-uppercase text-dark h6 font-weight-bold pb-0 m-0 pl-1 mkCampainName">Winter Special</h2>
            <small class="pl-1 d-block text-gray">Profiles-Cummulative</small> </div>
          <div class="col-5">
            <div class="countStatus float-right  w-100">
              <div class="sampleCount borderStyle w-100">
                <ul>
                  <li><span class="ttlrv">1995</span><small>Total Reviews</small></li>
                  <li><span class="ttlrt">4.5</span><small>Avg. Rating (1-10)</small></li>
                  <li><span class="tlNps">+125</span><small>NPS</small></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="tableWrap">
        <table class="scrollReviewTbl">
          <thead>
            <tr>
              <th>UserName</th>
              <th>Review</th>
              <th>Rating</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="reviewBody">
            <tr>
              <td><a href="users_detail.html" class="user-link"><span><img src="assets/img/profile/sample_01.jpg" alt="#"></span>John Die</a></td>
              <td><p class="limitText">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p></td>
              <td>3.5</td>
              <td><a href="#" class="text-link">View Profile</a></td>
            </tr>
            <tr>
              <td><a href="users_detail.html" class="user-link"><span><img src="assets/img/profile/sample_01.jpg" alt="#"></span>John Die</a></td>
              <td><p class="limitText">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p></td>
              <td>4.5</td>
              <td><a href="#" class="text-link">View Profile</a></td>
            </tr>
            
          </tbody>
        </table>
        </div>
        <!-- <div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div> -->
      </div>
      <div class="modal-footer">
        <!-- <button class="btn btn-style" type="button" data-dismiss="modal">Export Data(.exl)</button>
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Share Date</button> -->
        <button class="btn btn-link" type="button" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT --> 

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
                    url:  "<?php echo base_url(); ?>" + "campaign-report",  
                    data: {stDate:stDate,edDate:edDate,range:filterRange},  
                    cache: false,
                    dataType:'json',
                    success: function(result){  
                        if(result.type=="success"){  
                          //$('#page_body').html('');
                          $("#page_body").html(result.view);                          
                          console.log(result);
                          DonutWidget.draw();
                        }  
                        /*else  
                            jQuery("div#err_msg").show();  
                            jQuery("div#msg").html(result);  */
                    }  
                }); 
            }
          })

  $('.scrollTbl').floatThead({
    scrollContainer: function($table) {
      return $table.closest('.tableWrap');
    }
  });

    DonutWidget.draw();

    $(document).on('click', '.expandMark', function(e) {
        $(this).toggleClass('active');
        var defaultTR = $(this).closest('tr');
        var nextTR = defaultTR.next('tr');
        //nextTR.toggleClass('expandTable');
        nextTR.slideToggle();
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
              url:"<?php echo base_url('campaign-export-data');?>",
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
    $(document).on('click','.user_review_show',function(){

      var index_val = $(this).closest('tr').find('.inputIndex').val();
      var qus_id = $(this).attr('rel');
      var gen = $(this).attr('gender');
      var CampaignsName = $(this).attr('mkcampaign');
      var qus = $(this).closest('tbody').find('.qusTxt').text();
      var ans = $(this).closest('tr').find('.fixedWrap').text();
      var precM = $(this).closest('tr').find('#first_'+index_val).text();
      var precF = $(this).closest('tr').find('#second_'+index_val).text();
      var tuserM = $(this).closest('tr').find('#ucount1_'+index_val).text();
      var tuserF = $(this).closest('tr').find('#ucount2_'+index_val).text();
      //alert(gen);
      if(gen==1){
        $('.total_user_count').text(tuserM);
        $('.total_user_pre').text(precM);
      }else{
        $('.total_user_count').text(tuserF);
        $('.total_user_pre').text(precF);
      }
      $('.mkcampaign').text(CampaignsName);
      $('.Qus1').text('Question-'+qus);
      $('.Ans1').text('Answer-'+ans);
    //  $('.total_user_count').text(tuser);
    //  $('.total_user_pre').text(prec);
      $.ajax({
          type: "POST",
          url:  "<?php echo base_url('user-review-data'); ?>",
          data: {qus_id:qus_id,gender:gen},
          cache: false,
          dataType:'json',
          success: function(result){
              if(result.res=="success"){
                //$('#cityStatsModal').modal('show');
                $("#userRevBody").html(result.topreview);
                console.log(result);
              }
              else
                $("#userRevBody").html(result.topcity);
                $("#reviewQusModal").modal("show");
                $('#reviewQusModal').on('shown.bs.modal', function() {
                  setTimeout(function(){ 
                                          console.log('USER');

                    $('.scrollModalTbl').tableScroll({height:300});
             }, 100);

          });
          }
      });

      
    })
    $(document).on('click','.user_review_off',function(){
      var index_val = $(this).closest('tr').find('.inputIndex').val();
      var qus_id = $(this).attr('rel');
      var CampaignsName = $(this).attr('mkcampaign');
      var qus = $(this).closest('tbody').find('.qusTxt').text();
      var ans = $(this).closest('tr').find('.fixedWrap').text();
      var prec = $(this).closest('tr').find('#second_'+index_val).text();
      var tuser = $(this).closest('tr').find('#ucount2_'+index_val).text();
      $('.mkcampaign').text(CampaignsName);
      $('.Qus1').text('Question-'+qus);
      $('.Ans1').text('Answer-'+ans);
      $('.total_user_count').text(tuser);
      $('.total_user_pre').text(prec);
      $("#userRevBody").html("<tr><td colspan='4' class='text-center'> No record found");
      $("#reviewQusModal").modal("show");
    })

    $(document).on('click','.rate_review',function(){
      
      var campaign_id = $(this).attr('rel');
      var mkCampain = $(this).closest('tr').find('.mk_campaign').text();
      var t_review = $(this).closest('tr').find('.reviewBox').find('.avrev').text();
      var rate = $(this).closest('tr').find('.activityBar').text();
      var nps_val = $(this).closest('td').find('.getNps').val();
      var totalNps = '<?= NPS(4);?>';
      //alert(totalNps);

      $('.mkCampainName').text(mkCampain);
      $('.ttlrv').text(t_review);
      $('.ttlrt').text(rate);    
      $('.tlNps').text(nps_val);    
      $.ajax({
          type: "POST",
          url:  "<?php echo base_url('campaign-review-data'); ?>",
          data: {campaign_id:campaign_id},
          cache: false,
          dataType:'json',
          success: function(result){
              if(result.res=="success"){
                //$('#cityStatsModal').modal('show');
                $("#reviewBody").html(result.topreview);
                console.log(result);
              }
              else {
                $("#reviewBody").html(result.topcity);
              }
                $("#reviewRateModal").modal("show");

                  $('#reviewRateModal').on('shown.bs.modal', function() {
                    setTimeout(function(){ 
                      console.log('REVIEW');
                      $('.scrollReviewTbl').tableScroll({height:300});
             }, 100);

          });



          }
      });
    })

    $('#cityModal').on('shown.bs.modal', function() {
        setTimeout(function() {
            $('.scrollTbl').floatThead({
                scrollContainer: function($table) {
                    return $table.closest('.tableWrap');
                }
            });
        }, 60);
    });

    $(document).on('click','.no_data',function(){
      var mkCampain = $(this).closest('tr').find('.mk_campaign').text();
      var t_review = $(this).closest('tr').find('.reviewBox').find('.avrev').text();
      var rate = $(this).closest('tr').find('.activityBar').text();
      var nps_val = $(this).closest('td').find('.getNps').val();
      $('.mkCampainName').text(mkCampain);
      $('.ttlrv').text(t_review);
      $('.ttlrt').text(rate);
      $('.tlNps').text(nps_val);
      $("#reviewBody").html("<tr><td colspan='4' class='text-center'> No record found");
      $("#reviewRateModal").modal("show");
    })

    

})(jQuery);
</script>
