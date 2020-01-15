<div class="pageArea">
	<div class="pageHeader clearfix">
		<h2 class="float-left pageTitle"><span class="ml-2"><?= $title?></span></h2>
		<div class="float-right pageAction">
			<h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
			| <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#" /></a> </div>
		</div>
		<div class="pageTabs mt-1 extrawWide">
			<ul>
				<li><a href="<?php echo base_url('report') ;?>" class="active">Users</a></li>
				<li><a href="<?php echo base_url('campaign-report');?>">Campaigns</a></li>
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
					<input id="endDateInput" type="text" name="inputEdate" value=" " autocomplete="off" placeholder="End Date" class="form-control dateIcon" />
					<small id="endD_error" class="error"></small>
				</div>
				<div class="col-12 col-md-4 col-lg-4 pt-4 text-center">
					<div class="custom-control custom-radio font-weight-bold d-inline-block">
						<input type="radio" class="custom-control-input rangeFilter" checked="checked" name="duration" value="Daily" id="ft-01">
						<label class="custom-control-label" for="ft-01">Daily</label>
					</div>
					<div class="custom-control custom-radio font-weight-bold d-inline-block ml-5 ">
						<input type="radio" class="custom-control-input rangeFilter" name="duration" value="Weekly" id="ft-02">
						<label class="custom-control-label" for="ft-02">Weekly</label>
					</div>
					<div class="custom-control custom-radio font-weight-bold d-inline-block ml-5">
						<input type="radio" class="custom-control-input rangeFilter" name="duration" value="Monthly" id="ft-03">
						<label class="custom-control-label" for="ft-03">Monthly</label>
					</div>
				</div>
				<div class="col-12 col-md-2 col-lg-2 text-center">
					<button class="btn btn-primary text-uppercase mt-3" id="applyFilter">Apply</button>
				</div>
			</div>
		</div>
		<div class="row" id="page_body">
			<?php $this->load->view('report_body');?>
		</div>

		<!-- City MODAL -->
		<div class="modal fade" id="cityModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content ">
					<div class="modal-header">
						<h5 class="modal-title pt-1 text-primary" id="cityOf">Cities (India)</h5>
						<button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="pl-2 pr-2">
							<div class="tableWrap">
							<table class="scrollModalTbl">
								<thead>
									<tr>
										<th rowspan="2">City Name</th>
										<th rowspan="2">Total Users</th>
										<th class="text-center" colspan="2">Male</th>
										<th class="text-center" colspan="2">Female</th>
									</tr>
									<tr>
										<th>in no. </th>
										<th>in %</th>
										<th>in no.</th>
										<th>in %</th>
									</tr>
								</thead>
								<tbody id="table_body">

								</tbody>
							</table>
						</div>
						</div>
					</div>
						<div class="modal-footer">
							<button class="btn btn-style" type="button" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

			<!-- REVIEW RATING MODAL -->
			<div class="modal fade" id="reviewRateModal" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
					<div class="modal-content ">
						<div class="modal-header">
							<h5 class="modal-title pt-1 text-primary">Live Campaigns-Reviews</h5>
							<button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							<div class="row pb-4">
								<div class="col-8">
									<h2 class="text-uppercase text-dark h6 font-weight-bold pb-0 m-0 pt-3 pl-1">Winter Special</h2>
									<small class="pl-1 d-block text-gray">Profiles-cummulative</small> </div>
									<div class="col-4">
										<div class="countStatus float-right">
											<div class="sampleCount  borderStyle">
												<ul>
													<li><span>125</span><small>Total reviews</small></li>
													<li><span>4.5</span><small>Avg. Rating</small></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<table>
									<thead>
										<tr>
											<th>UserName</th>
											<th>Review</th>
											<th>Rating</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><a href="users_detail.html" class="user-link"><span><img src="assets/img/profile/sample_04.jpg" alt="#"></span> Johnathan Pine</a></td>
											<td><p class="limitText">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p></td>
											<td>3.5</td>
											<td><a href="#" class="text-link">View Profile</a></td>
										</tr>
										<tr>
											<td><a href="users_detail.html" class="user-link"><span><img src="assets/img/profile/sample_04.jpg" alt="#"></span> Johnathan Pine</a></td>
											<td><p class="limitText">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p></td>
											<td>3.5</td>
											<td><a href="#" class="text-link">View Profile</a></td>
										</tr>
									</tbody>
								</table>
								<div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-style" type="button" data-dismiss="modal">Close</button>
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
							<div class="modal-body">
								<div class="row pb-4">
									<div class="col-8">
										<h2 class="text-uppercase text-dark h6 font-weight-bold pb-0 m-0 pt-3 pl-1">Winter Takes All</h2>
										<small class="pl-1 d-block text-gray">Male Profiles-cummulative</small>
										<h3 class="small font-weight-bold text-dark m-0 pt-3  pl-1">Question-Do you like the packaging of the product? </h3>
										<h3 class="small font-weight-bold text-dark m-0 pt-1  pl-1">Answer-Yes</h3>
									</div>
									<div class="col-4">
										<div class="countStatus float-right">
											<div class="sampleCount  borderStyle">
												<ul>
													<li><span>125</span><small>Total Users</small></li>
													<li><span>45%</span><small>User %</small></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<table>
									<thead>
										<tr>
											<th>UserName</th>
											<th>Age Group</th>
											<th>Joined On</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
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
								<div class="pt-4 pb-0 text-center"><a href="#" class="font-weight-bold text-link small text-uppercase">load more</a></div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-style" type="button" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>

				<!-- SCRIPT --> 
				<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 

				<script type="text/javascript">
					function piechartInit() {
						google.charts.load('current', {
							'packages': ['corechart']
						});

						google.charts.setOnLoadCallback(drawChart);

						function drawChart() {

							var data = google.visualization.arrayToDataTable([
								['Users', 'Numbers'],
								['Male', <?= $male_users?>],
								['Female', <?= $female_users?>]

								]);

							var options = {
								tooltip: {
									isHtml: true
								},
								colors: ['#bf1f81', '#80024f'],
								pieSliceText: 'none',
								pieHole: 0.6,
								chartArea: {
									width: '80%',
									height: '80%'
								},
								legend: {
									position: "none",
									alignment: 'center',
									textStyle: {
										color: 'black',
										fontSize: 10
									}
								},

							};

							var chart = new google.visualization.PieChart(document.getElementById('piechart'));

							chart.draw(data, options);
						}      

					}

      /// PROFILE CHARTS

      function profileChartInit(){
      	google.charts.load("current", {
      		packages: ['corechart']
      	});

      	google.charts.setOnLoadCallback(drawChart);

      	function drawChart() {
      		var data = google.visualization.arrayToDataTable(<?php if (!empty($profileChart)) {
      			echo json_encode($profileChart);
      		} else {
      			echo "[]";
      		} ?>);                
      		var view = new google.visualization.DataView(data);
      		var options = {
      			tooltip: {
      				isHtml: true
      			},
      			colors: ['#e4a33a'],
      			chartArea: {
      				left: 50,
      				top: 20,
      				width: '88%',
      				height: '80%'
      			},
      			bar: {
      				groupWidth: "40%"
      			},
      			isStacked: true,
      			legend: {
      				position: "none"
      			},
      			hAxis: {
      				title: 'Profile Completion (in %)',
      				minValue: 0,
      				slantedText: false,
      				textStyle: {
      					color: '#888',
      					fontSize: 10
      				}
      			},
      			vAxis: {
      				title: 'No. of people',
      				baselineColor: '#666',
      				gridlines: {
      					color: '#f9f9f9',
      					count: 8
      				},
      				textStyle: {
      					color: '#888',
      					fontSize: 10
      				}
      			}
      		};
      		var chart = new google.visualization.ColumnChart(document.getElementById("profilechart"));
      		chart.draw(view, options);
      	}
      }

      /// LINECHART INIT

      function linechartInit() {
      	google.charts.load("current", {
      		packages: ['corechart']
      	});

      	google.charts.setOnLoadCallback(drawChart);

      	function drawChart() {
      		var data = google.visualization.arrayToDataTable(<?php if (!empty($usages_trands)) {
      			echo json_encode($usages_trands);
      		} else {
      			echo "[]";
      		} ?>);

      		var view = new google.visualization.DataView(data);
      		var options = {
      			tooltip: {
      				isHtml: true
      			},
      			colors: ['#69003b', '#ff701a', '#eb4e00', '#c38625', '#ee219b', '#ff9d00', '#ac0bbb'],
      			chartArea: {
      				left: 50,
      				top: 20,
      				width: '88%',
      				height: '80%'
      			},
      			lineWidth: 2,
      			pointSize: 5,
      			legend: {
      				position: "none"
      			},
      			hAxis: {
      				title: 'Days',
      				minValue: 0,
      				slantedText: false,
      				textStyle: {
      					color: '#888',
      					fontSize: 10
      				}
      			},
      			vAxis: {
      				title: 'No. of people',
      				baselineColor: '#666',
      				gridlines: {
      					color: '#f9f9f9',
      					count: 8
      				},
      				textStyle: {
      					color: '#888',
      					fontSize: 10
      				}
      			}
      		};
      		var chart = new google.visualization.LineChart(document.getElementById("linechart"));
      		chart.draw(view, options);
      	}
      }
</script>

<script type="text/javascript">
 $(document).ready(function() {
    'use strict';
   
    // DATE PICKER
    var todayDate = moment().format('DD-MM-YYYY');    
    $('#endDateInput').attr('data-date', todayDate);
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
        endDate: todayDate
    });

    $(document).on('focus', '#endDateInput', function(e) {
        //var defaultDate = $(this).val();
        var startDate = $(this).attr('data-date');
        if (startDate == '') {
            var startDate = $(this).attr('data-date', todayDate);
        } else {
            var startDate = $(this).attr('data-date');
        }
        $(this).dateRangePicker({
            format: 'DD-MM-YYYY',
            autoClose: true,
            singleDate: true,
            showTopbar: false,
            singleMonth: true,
            changeMonth: true,
            selectBackward: false,
            selectForward: true,
            startDate: startDate,
            endDate: todayDate
        });
    });

    // TABLE SCROLL
    setTimeout(function() {
	 /*   $('.scrollTbl').tableScroll({
	        height: 175
	    }); */
	    $('.scrollTbl').floatThead({
                scrollContainer: function($table) {
                    return $table.closest('.tableWrap');
                }
            });

  }, 100);

    $('#cityModal').on('shown.bs.modal', function() {
        setTimeout(function() {
            $('.scrollTbl').floatThead({
                scrollContainer: function($table) {
                    return $table.closest('.tableWrap');
                }
            });
        }, 60);
    });

    piechartInit();
    profileChartInit();
    linechartInit();

	$('.rangeFilter').on('click', function() {
	    if ($("input[type='radio'][name='duration']").is(':checked')) {
	    	var todayDate = moment().format('D-MMM-Y');   	    	
	        $('#startDateInput').val('');
	        $('#endDateInput').val('');
	        //$('.cust_viewDtl').text('');
	        
	        $('.cust_viewDtl').text($(this).val());
	        $('#setDate').text(' ( '+todayDate+' ) ');
	    }
	});

	$('#startDateInput,#endDateInput').on('click', function() {
	    $("input[type='radio'][name='duration']").prop("checked", false);
	});


    $(document).on('click', '.city_view', function() {
        var country_id = $(this).attr('rel');
        var country_name = $(this).closest('tr').find('.viewAs').text();
        $('#cityOf').text('Cities ( ' + country_name + ' )');
        $.ajax({
            type: "POST",
            url: "<?php echo base_url('city-data'); ?>",
            data: {
                country_id: country_id
            },
            cache: false,
            dataType: 'json',
            success: function(result) {
                if (result.res == "success") {
                    //$('#cityModal').modal('show');                      
                    $("#table_body").html(result.topcity);
                    console.log(result);

                } else
                    $("#table_body").html('<tr><td colspan="6" class="text-center">'+result.topcity+'</td></tr>');
                $("#cityModal").modal("show");
            }
        });
    });

    $(document).on('click', '.data_export', function() {
        //alert('obob');
        var st = $('#startDateInput').val();
        var ed = $('#endDateInput').val();
        if ($("input[type='radio'][name='duration']").is(':checked')) {
            var range = $("input[type='radio'][name='duration']:checked").val();
        } else {
            var range = '';
        }

        $.ajax({
            url: "<?php echo base_url('export-data');?>",
            type: "POST",
            //async: false,
            data: {
                st: st,
                ed: ed,
                range: range
            },
            dataType: "json",
            success: function(rs) {
                console.log(rs);
                if (rs.msg == 'success') {
                    location.href = "<?= base_url('assets/files/');?>" + rs.file;
                    
                }

            }
        });


    });


    $(document).on('click', '#applyFilter', function() {
        var flag = 0;
        $('.error').text('');
        var stDate = $('#startDateInput').val();
        var edDate = $('#endDateInput').val();
        if ($("input[type='radio'][name='duration']").is(':checked')) {
            var filterRange = $("input[type='radio'][name='duration']:checked").val();
        } else {
            var filterRange = '-';
        }

        if ((stDate == '') && (filterRange == '-')) {
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

        if (flag == 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "report",
                data: {
                    stDate: stDate,
                    edDate: edDate,
                    range: filterRange
                },
                cache: false,
                dataType: 'json',
                success: function(result) {
                    if (result.type == "success") {

                        $("#page_body").html(result.view);

                       setTimeout(function() {
					  $('.scrollTbl').floatThead({
					                scrollContainer: function($table) {
					                    return $table.closest('.tableWrap');
					                }
					            });
					  }, 1000);
                        $('#cityModal').on('shown.bs.modal', function() {
                            setTimeout(function() {
                              $('.scrollTbl').floatThead({
				                scrollContainer: function($table) {
				                    return $table.closest('.tableWrap');
				                }
				            });
                            }, 60);
                        });

                        google.charts.load('current', {
                            'packages': ['corechart']
                        });

                        google.charts.setOnLoadCallback(drawChart);

                        function drawChart() {

                            var data = google.visualization.arrayToDataTable([
                                ['Users', 'Numbers'],
                                ['Male', result.data.male_users],
                                ['Female', result.data.female_users]

                            ]);

                            var options = {
                                tooltip: {
                                    isHtml: true
                                },
                                colors: ['#bf1f81', '#80024f'],
                                pieSliceText: 'none',
                                pieHole: 0.6,
                                chartArea: {
                                    width: '80%',
                                    height: '80%'
                                },
                                legend: {
                                    position: "none",
                                    alignment: 'center',
                                    textStyle: {
                                        color: 'black',
                                        fontSize: 10
                                    }
                                },

                            };

                            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                            chart.draw(data, options);
                        }

                        google.charts.setOnLoadCallback(profileChart);

                        function profileChart() {
                            var data = google.visualization.arrayToDataTable(result.data.profileChart);
                            var view = new google.visualization.DataView(data);
                            var options = {
                                tooltip: {
                                    isHtml: true
                                },
                                colors: ['#e4a33a'],
                                chartArea: {
                                    left: 50,
                                    top: 20,
                                    width: '88%',
                                    height: '80%'
                                },
                                bar: {
                                    groupWidth: "40%"
                                },
                                isStacked: true,
                                legend: {
                                    position: "none"
                                },
                                hAxis: {
                                    title: 'Profile Completion (in %)',
                                    minValue: 0,
                                    slantedText: false,
                                    textStyle: {
                                        color: '#888',
                                        fontSize: 10
                                    }
                                },
                                vAxis: {
                                    title: 'No. of people',
                                    baselineColor: '#666',
                                    gridlines: {
                                        color: '#f9f9f9',
                                        count: 8
                                    },
                                    textStyle: {
                                        color: '#888',
                                        fontSize: 10
                                    }
                                }
                            };
                            var chart = new google.visualization.ColumnChart(document.getElementById("profilechart"));
                            chart.draw(view, options);
                        }


                        google.charts.setOnLoadCallback(linechart);

                        function linechart() {

                            var data = google.visualization.arrayToDataTable(result.data.usages_trands);

                            var view = new google.visualization.DataView(data);
                            var options = {
                                tooltip: {
                                    isHtml: true
                                },
                                colors: ['#69003b', '#ff701a', '#eb4e00', '#c38625', '#ee219b', '#ff9d00', '#ac0bbb'],
                                chartArea: {
                                    left: 50,
                                    top: 20,
                                    width: '88%',
                                    height: '80%'
                                },
                                lineWidth: 2,
                                pointSize: 5,
                                legend: {
                                    position: "none"
                                },
                                hAxis: {
                                    title: 'Days',
                                    minValue: 0,
                                    slantedText: false,
                                    textStyle: {
                                        color: '#888',
                                        fontSize: 10
                                    }
                                },
                                vAxis: {
                                    title: 'No. of people',
                                    baselineColor: '#666',
                                    gridlines: {
                                        color: '#f9f9f9',
                                        count: 8
                                    },
                                    textStyle: {
                                        color: '#888',
                                        fontSize: 10
                                    }
                                }
                            };
                            var chart = new google.visualization.LineChart(document.getElementById("linechart"));
                            chart.draw(view, options);
                        }

                        $(".checkbox-legend li").each(function() {
				           var str = $(this).index();
				           $(this).attr("data-index", str);
				       });
                        $(".checkbox").change(function() {
						   var data = google.visualization.arrayToDataTable(result.data.usages_trands);
								var options = {
					                tooltip: {
					                    isHtml: true
					                },
					                colors: ['#69003b', '#ff701a', '#eb4e00', '#c38625', '#ee219b', '#ff9d00', '#ac0bbb'],
					                chartArea: {
					                    left: 50,
					                    top: 20,
					                    width: '88%',
					                    height: '80%'
					                },
					                lineWidth: 2,
					                pointSize: 5,
					                legend: {
					                    position: "none"
					                },
					                hAxis: {
					                    title: 'Days',
					                    minValue: 0,
					                    slantedText: false,
					                    textStyle: {
					                        color: '#888',
					                        fontSize: 10
					                    }
					                },
					                vAxis: {
					                    title: 'No. of people',
					                    baselineColor: '#666',
					                    gridlines: {
					                        color: '#f9f9f9',
					                        count: 8
					                    },
					                    textStyle: {
					                        color: '#888',
					                        fontSize: 10
					                    }
					                }
					            };
					           	var view = new google.visualization.DataView(data);
					           	var that = $(this);
					            var cols = [0];
					           	var par = that.closest("ul");
					           	//var mynum = that.closest("li").attr("data-index");
					           	var maxcheck = par.find(".checkbox").length - 1;
					           	var checked = par.find(".checkbox").filter(":checked").length;
					           if (checked > 1) {
					               par.find(".checkbox").prop("disabled", "");
					           } else {
					              // alert("Can't uncheck all");
					               par.find('.checkbox').filter(':checked').prop('disabled', 'disabled');
					           }
					           if ($(this).is(':checked')) {
					               //$(this).closest("li").insertBefore($("ul.checkbox-legend").children().eq(mynum));
					                $(this).closest("li").removeClass("disabled");
					           } else {
					                $(this).closest("li").addClass("disabled");
					           }
					            par.find(':checkbox:checked').each(function () {
					            console.log(this);
					            cols.push(parseInt($(this).attr('value')));
					            });
					        view.setColumns(cols);
							var chart = new google.visualization.LineChart(document.getElementById("linechart"));
					         chart.draw(view, options);
					       });
                    }
               		
                }
            });
        }
    });


});
</script> 
<script type="text/javascript">
	$(document).ready(function(){
		/*$(".checkbox-legend li").each(function() {
           var str = $(this).index();
           $(this).attr("data-index", str);
       });*/
	$(".checkbox").change(function() {
	   var data = google.visualization.arrayToDataTable(<?php if (!empty($usages_trands)) {
      			echo json_encode($usages_trands);
      		} else {
      			echo "[]";
      		} ?>);
			var options = {
                tooltip: {
                    isHtml: true
                },
                colors: ['#69003b', '#ff701a', '#eb4e00', '#c38625', '#ee219b', '#ff9d00', '#ac0bbb'],
                chartArea: {
                    left: 50,
                    top: 20,
                    width: '88%',
                    height: '80%'
                },
                lineWidth: 2,
                pointSize: 5,
                legend: {
                    position: "none"
                },
                hAxis: {
                    title: 'Days',
                    minValue: 0,
                    slantedText: false,
                    textStyle: {
                        color: '#888',
                        fontSize: 10
                    }
                },
                vAxis: {
                    title: 'No. of people',
                    baselineColor: '#666',
                    gridlines: {
                        color: '#f9f9f9',
                        count: 8
                    },
                    textStyle: {
                        color: '#888',
                        fontSize: 10
                    }
                }
            };
           	var view = new google.visualization.DataView(data);
           	var that = $(this);
            var cols = [0];
           	var par = that.closest("ul");
           	//var mynum = that.closest("li").attr("data-index");
           	var maxcheck = par.find(".checkbox").length - 1;
           	var checked = par.find(".checkbox").filter(":checked").length;
           if (checked > 1) {
               par.find(".checkbox").prop("disabled", "");
           } else {
              // alert("Can't uncheck all");
               par.find('.checkbox').filter(':checked').prop('disabled', 'disabled');
           }
           if ($(this).is(':checked')) {
               //$(this).closest("li").insertBefore($("ul.checkbox-legend").children().eq(mynum));
                $(this).closest("li").removeClass("disabled");
           } else {
               //$(this).closest("li").insertAfter("ul.checkbox-legend li:last");
                $(this).closest("li").addClass("disabled");
           }
            par.find(':checkbox:checked').each(function () {
            	console.log(this);
                cols.push(parseInt($(this).attr('value')));
            });
        view.setColumns(cols);
		var chart = new google.visualization.LineChart(document.getElementById("linechart"));
         chart.draw(view, options);
       });

});
</script>



<style type="text/css">
	.google-visualization-tooltip {
		padding: 4px 8px !important;
		border-radius: 6px;
		background-color: #e3e3e1;
		border: none !important;
	}

	.google-visualization-tooltip>ul>li>span {
		color: #444 !important;
		font-size: .75rem !important;
	}

	.google-visualization-tooltip-item-list,
	.google-visualization-tooltip-item {
		padding: 0px !important;
		margin: 0px !important;
	}
</style>