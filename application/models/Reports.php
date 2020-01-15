<?php 
ob_start();
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Reports extends Admin_Controller {
	
	public function __construct()
	{
		parent::__construct();
        //$this->load->model('login_model');
		ini_set('max_execution_time', 120);
		$this->load->model('report_model');
		$this->load->model('common_model');
		$this->load->library('Ajax_pagination');
		$this->load->helper('mail_helper');
		$this->load->helper('common_helper');
	}

	public function getWeekDays($stDate) {

	}
	//	USER REPORT VIEW CITY DATA
	public function getCityData()
	{
		$data = $this->input->post();
		$country_id = $data['country_id'];
		$data['radioCity']=$this->report_model->getProfileCityData(array('u.country_id'=>$country_id,'u.is_active'=>1));
		$data['topcity'] ='';
		if(!empty($data['radioCity'])){
			foreach ($data['radioCity'] as $key => $radioValue) {
				$data['topcity'] .="<tr><td class='font-weight-bold'>".$radioValue['city']."</td><td>".$radioValue['Total']."</td>
				<td>".$radioValue['MALE']."</td><td>".sprintf('%0.2f', $radioValue['Male_percent'])."%</td><td>".$radioValue['Female']."</td><td>".sprintf('%0.2f', $radioValue['Female_percent'])."%</td></tr>";
			}
			$data['res'] = 'success';
		}else{
			$data['topcity'] ='No record found';
			$data['res'] = 'error';
		}
		echo json_encode($data);die;
		//print_r($data['radioCity']);die;
	}

	//	VENDING MACHINE VIEW CITY STATE DATA

	public function getCityStateData()
	{
		$data = $this->input->post();
		$country_id = $data['country_id'];		
		$data['radioCity']=$this->report_model->getMachineCityStatus(array('country_id'=>$country_id,'is_active'=>1));
		//print_r($data['radioCity']);die;
		$data['topcity'] ='';
		if(!empty($data['radioCity'])){
			foreach ($data['radioCity'] as $key => $radioValue) {
				$cityVended = $this->report_model->getCityVended(array('city'=>$radioValue['city']));
				//print_r($cityVended);
				$data['topcity'] .="<tr><td colspan='3' class='pl-0 pr-0 pt-0'><table><colgroup><col style='width:25%;'><col style='width:60%;'><col style='width:15%;'></colgroup><tr class='border-top-0 border-bottom-0'><td class='text-dark font-weight-bold'><strong>".$radioValue['city_name']."</strong></td><td><div class='sampleCount xtraWide borderStyle'> <ul> <li><span>".$radioValue['campaigns']."</span><small>Total Campaigns</small></li>
				<li><span>".$radioValue['vending_machine']."</span><small>Total Vending Machines</small></li>
				<li><span>".$radioValue['Active']."</span><small>Active</small></li>
				<li><span>".$radioValue['Inactive']."</span><small>Inactive</small></li> </ul> </div></td> <td class='text-center'><a href='#' class='veiwDetail'>Detail</a></td> </tr> <tr class='border-top-0 border-bottom-0 expandTable'> <td></td> <td colspan='2'><table> <thead> <tr class='border-top-0'> <th>Machine ID</th>  <th class='text-center'>Status</th> <th>Location</th> <th>#Samples</th> <th></th> </tr> </thead> <tbody>"; 
				if(!empty($cityVended)){
					foreach ($cityVended as $key => $vended) {
						$isActive = $vended['is_active']==1?'ACTIVE':'DEACTIVE';
						$data['topcity'] .="<tr> <td class='vmc'>".$vended['vending_machine_code']."</td> <td class='text-center'><span class='statusMark active'><b></b>".$isActive."</span></td> <td>".$vended['location_address']."</td> <td class='sampleUsed'>".$vended['vend_no_of_sample_used']."</td> <td class='text-center'><a href='#' data-toggle='modal' class='codeUsage' invalid='".$vended['invalid_try']."' >Diagnostics</a></td> </tr> ";
					}
				}else{
					$data['topcity'] .="<tr><td colspan='5' class='text-center'> No record found </td>";
				}
				$data['topcity'] .="</tbody> </table></td> </tr> </table></td> </tr>";
			}
			$data['res'] = 'success';
		}else{
			$data['topcity'] ='<span class"text-center">No record found </span>';
			$data['res'] = 'error';
		}
		echo json_encode($data);die;
		//print_r($data['radioCity']);die;
	}
	/*********get Week Of month**************/
	function rangeWeek($datestr) {
		date_default_timezone_set(date_default_timezone_get());
		$dt = strtotime($datestr);
		return array("start" => date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt)),"end" => date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt)));
	}
	
	public function index()
	{
		ini_set('max_execution_time', 120);
		//echo"<pre>";print_r($this->input->post());die;
		$this->session->set_userdata(array('menu'=>'Report'));
		$data = array();
		$conditions = array('u.is_active'=>1);
		//$varCond['active'] = "u.is_active ='1'";
		$varCond= '';
		$data['range'] ='Daily';
		$currentDate = date('Y-m-d 00:00:00');
		//$status = " campVend.is_active='1'  AND vendM.is_active='1' AND campVend.campaign_id='".$campaign_id."'";
		$requestData = $this->input->post();
		if(!empty($requestData)) {
			//print_r($requestData);die;
			$start = $requestData['stDate'];
			$end = $requestData['edDate'];
			$range = $requestData['range'];
			if(!empty($range)) {
				if($range=='Daily') {
					//$start = $currentDate;
					//$end = $currentDate; 2019-09-26 12:08:46
					$start = date('Y-m-d 00:00:00');
					$end = date('Y-m-d 23:59:59');
				}else if($range=='Weekly') {					
					$stWeek = $this->rangeWeek($currentDate);					
					$start = date('Y-m-d',strtotime($stWeek['start']));
					$end = date('Y-m-d',strtotime($stWeek['end']));
				}else if($range=='Monthly') {
					$start = date('Y-m-1');
					$end = date('Y-m-t');
				}
				$data['range'] = $range;
			}
			if(!empty($start)) {
				//$conditions = array_merge($conditions,array("u.created_dttm >='".$start."'"));
				$startDt = date('Y-m-d 00:00:00',strtotime($start));
			}
			if(!empty($end)) {
				//$conditions =  array_merge($conditions,array("u.created_dttm <='".$end."'"));
				$endDt = date('Y-m-d 23:59:59',strtotime($end));
			}
			
		}

		$data['title'] = 'Reports';
		$endDt = isset($endDt)==''?date('Y-m-d 23:59:59'):$endDt;
		$startDt = isset($startDt)==''?date('Y-m-d 00:00:00'):$startDt;
		//===	ACTIVE USERS PieChart Start =======//
		$data['current_date'] = date('d M Y');
		$data['active_users'] = $this->report_model->userCount('id',array("u.is_active"=>1,"u.registration_status"=>2,"u.gender !="=>0,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		$data['male_users'] = $this->report_model->userCount('id',array("u.is_active"=>1,"u.registration_status"=>2,'gender'=>1,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		$data['female_users'] = $this->report_model->userCount('id',array("u.is_active'=>1","u.registration_status"=>2,'gender'=>2,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		if($data['active_users']!=0) {
			$data['male_percent'] = (100*$data['male_users'])/ $data['active_users'];
			$data['female_percent'] = (100*$data['female_users'])/ $data['active_users'];
		}else{
			$data['male_percent'] = 0;
			$data['female_percent'] = 0;
		}
		//echo '<pre>';print_r($data);die;
		//===	USER PROFILES (AGE) Start =======//
		
		$data['profile_age'] = $this->report_model->getProfileAge(array("u.is_active"=>1,"u.registration_status"=>2,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));

		//===	USER PROFILES (INTEREST) Start =======//

		$data['profile_interest'] = $this->report_model->getProfileInterest(array("u.is_active"=>1,"u.registration_status"=>2,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));		

		//===	USER PROFILES (COUNTRY) Start =======//

		$data['profile_country'] = $this->report_model->getProfileCountry(array("u.is_active"=>1,"u.registration_status"=>2,"u.gender !="=>0,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));		

		//===	USER PROFILES (PROFILE COMPLETION) Start =======//	

		$data['profile_arr'] = $this->report_model->getProfileCompletion(array("u.is_active"=>1,"u.registration_status"=>2,"u.gender !="=>0,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		//echo"<pre>";print_r($data['profile_arr']);die;
		$data['profileChart'][] = ['"Profile Completion"','Male','Female'];		
		$profileArr = [];		
		$mainArray = $data['profile_arr'];		
		$finalArray =array();
		if(empty($mainArray)){
			for ($percent = 10;$percent<=100;$percent=$percent+10) {            
            	//$data['profileChart'] = array('ProfileCompletion'=>$percent,'Male'=>'0','Female'=>'0');
				array_push($data['profileChart'], array($percent.'%',(int)0,(int)0));
			}            
		}else{

			$k=0;
			for ($percent = 10;$percent<=100;$percent=$percent+10) {    
				$isNotFind = 0;
				for ($j = 0;$j<count($mainArray);$j++) {
					if($percent!=$mainArray[$j]['ProfileCompletion']) {
						$isNotFind = $isNotFind+1;
					}else {
						$isNotFind = 0;
						break;
					}
				}
				if($isNotFind>0) {
					$tempArray = array('ProfileCompletion'=>$percent,'Male'=>'0','Female'=>'0');
					$finalArray[$k] = $tempArray;
				}
				$k++;
			}
        // add already data
			for ($m = 0;$m<count($mainArray);$m++) {
				$index = $mainArray[$m]['ProfileCompletion']/10-1;
				$finalArray[$index] = $mainArray[$m];
			}
			sort($finalArray);
			
			$profileArray = array();		
			
			foreach ($finalArray as $key => $dataValue) {			
				$G = $dataValue["Female"];	
				$B = $dataValue["Male"];
				array_push($data['profileChart'], array($dataValue['ProfileCompletion'].'%',(int)$B,(int)$G));

			}
		}
		//print_r($data['profileChart']);die;
		//===	USER PROFILES (PROFILE COMPLETION) End =======//

		//===	UAGE TRENDS Start =======//
		$SampleRedeemed = $this->report_model->getSampleRedeemed(USER_SAMPLES,array('status'=>3,'is_active'=>1,"created_dttm >="=>'2019-12-14',"created_dttm <="=>'2019-12-22'));
		$PromosRedeemed = $this->report_model->getSampleRedeemed(USER_PROMOCODES,array('status'=>3,'is_active'=>1,"created_dttm >="=>'2019-12-15',"created_dttm <="=>'2019-12-21'));
		$ReviewsGiven = $this->report_model->getSampleRedeemed(USER_REVIEW,array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));		       
		$Comments = $this->report_model->getSampleRedeemed(WALL_COMMENTS,array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));        
		$Likes = $this->report_model->getSampleRedeemed(WALL_LIKES,array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
       $Brand = $this->report_model->getTotalViewed(BRANDS,'brand_viewed',array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
       $Campaigns = $this->report_model->getTotalViewed(CAMPAIGNS,'campaign_viewed',array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
       
		$data['usages_trands'][] = array("Days", "Sample Redeemed", "Promos Redeemed", "Reviews Given", "Comments", "likes","Brand Pages Viewed","Campaigns Viewed");
		$day = array('Sunday'=>'Sun','Monday'=>'Mon','Tuesday'=>'Tue','Wednesday'=>'Wed','Thursday'=>'Thu','Friday'=>'Fri','Saturday'=>'Sat');
		//echo "<pre>";print_r($SampleRedeemed);die;

		for($i=0;$i<sizeof($day);$i++){
                	if($SampleRedeemed[$i]['Days'] =='') {						
                		$tmpTopic = array_merge($tmpTopic,$topics[$i]);
                		$topicArt[$i] = $tmpTopic;
                		$tmpTopic = array();

                	} else if($topics[$i]['Topic']['content_type'] ==2) {
                		$tmpTopic = array_merge($tmpTopic,$topics[$i]);
                		$topicMcq[$i] = $tmpTopic;
                		$tmpTopic = array();
                	}else if($topics[$i]['Topic']['content_type'] ==3) {
                		$tmpTopic = array_merge($tmpTopic,$topics[$i]);
                		$topicSaq[$i] = $tmpTopic;
                		$tmpTopic = array();
                	}else if($topics[$i]['Topic']['content_type'] ==4) {
                		$tmpTopic = array_merge($tmpTopic,$topics[$i]);
                		$topicCsq[$i] = $tmpTopic;
                		$tmpTopic = array();
                	} else if($topics[$i]['Topic']['content_type'] ==5) {
                		$tmpTopic = array_merge($tmpTopic,$topics[$i]);
                		$topicVideo[$i] = $tmpTopic;
                		$tmpTopic = array();
                	}else if($topics[$i]['Topic']['content_type'] ==6) {
                		$tmpTopic = array_merge($tmpTopic,$topics[$i]);
                		$topicPpt[$i] = $tmpTopic;
                		$tmpTopic = array();
                	}else {
                		$tmpTopic = array_merge($tmpTopic,$topics[$i]);
                		$topicArt[$i] = $tmpTopic;
                		$tmpTopic = array();
                	}

                }
		foreach ($day as $Dkey=>$value ) {
			
			if(!empty($SampleRedeemed))
			{
				foreach ($SampleRedeemed as $Skey => $sampleVal) {
					if($sampleVal['Days']==$Dkey){
						$uagSample = (int)$sampleVal['COUNT'];
					}else{
						$uagSample = 0;
					}
				}
				
				
			}else{
				$uagSample = 0;
			}
			if(!empty($PromosRedeemed))
			{
				foreach ($PromosRedeemed as $Pkey => $promoVal) {
					if($promoVal['Days']==$Dkey){
						$uagPromo = (int)$promoVal['COUNT'];
					}else{
						$uagPromo = 0;
					}
				}
			}else{
				$uagPromo = (int)0;
			}
			if(!empty($ReviewsGiven))
			{
				foreach ($ReviewsGiven as $Rekey => $reviewvalue) {
					if($reviewvalue['Days']==$Dkey){
						$uagReview = (int)$reviewvalue['COUNT'];
					}else{
						$uagReview = 0;
					}	
				}
							  	
			}else{
				$uagReview = (int)0;
			}
			
			if(!empty($Comments))
			{
				foreach ($Comments as $cmtkey => $commentvalue) {
					if($commentvalue['Days']==$Dkey){
						$uagComment = (int)$commentvalue['COUNT'];
					}else{
						$uagComment = 0;
					}
				}
				
			}else{
				$uagComment = (int)0;
			}
			if(!empty($Likes))
			{
				foreach ($Likes as $Likey => $likvalue) {
					if($likvalue['Days']==$Dkey){
						$uagLikes = (int)$likvalue['COUNT'];
					}else{
						$uagLikes = 0;
					}
				}
				
			}else{
				$uagLikes = (int)0;
			}
			if(!empty($Brand))
			{
				foreach ($Brand as $brkey => $brandvalue) {
					if($brandvalue['Days']==$Dkey){
						$uagBrand = (int)$brandvalue['cviews'];
					}else{
						$uagBrand = 0;
					}
				}
				
			}else{
				$uagBrand = (int)0;
			}
			if(!empty($Campaigns))
			{
				foreach ($Campaigns as $cmpkey => $campvalue) {
					if($campvalue['Days']==$Dkey){
						$uagCamp = (int)$campvalue['cviews'];
					}else{
						$uagCamp = 0;
					}
				}
				
			}else{
				$uagCamp = (int)0;
			}
			
			array_push($data['usages_trands'], array($day[$Dkey],$uagSample,$uagPromo,$uagReview,$uagComment,$uagLikes,$uagBrand,$uagCamp));
		}
		
		//echo"<pre>";print_r($data['usages_trands']);die;
		$this->load->view('common/header',$data);
		if($this->input->is_ajax_request())
		{				
			$reportData['type'] = 'success';
			$reportData['data'] = $data;
			$reportData['view'] = $this->load->view('report_body',compact('data'),true);						
			echo json_encode($reportData);die;
		}
		else{					
			$this->load->view('report',true);				
		}
		$this->load->view('common/footer');	

		
	}

	public function excelExport()
	{
		//print_r($_POST);die;
		require(APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php');
		$currentDate = date('Y-m-d 00:00:00');
		$lastDate = date('Y-m-d 23:59:59');
		$requestData = $this->input->post();

		if(!empty($requestData)) {			
			$start = $requestData['st'];
			$end = $requestData['ed'];
			$range = $requestData['range'];
			if(!empty($range)) {
				if($range=='Daily') {
					$start = $currentDate;
					$end = $lastDate;
				}else if($range=='Weekly') {					
					$stWeek = $this->rangeWeek($currentDate);					
					$start = date('Y-m-d',strtotime($stWeek['start']));
					$end = date('Y-m-d',strtotime($stWeek['end']));
				}else if($range=='Monthly') {
					$start = date('Y-m-1');
					$end = date('Y-m-t');
				}
				$data['range'] = $range;
			}
			if(!empty($start)) {
				$startDt = date('Y-m-d 00:00:00',strtotime($start));
			}
			if(!empty($end)) {
				$endDt = date('Y-m-d 23:59:59',strtotime($end));
			}
			
		}
		$endDt = isset($endDt)==''?date('Y-m-d 23:59:59'):$endDt;
		$startDt = isset($startDt)==''?date('Y-m-d 00:00:00'):$startDt;
		
		$fileName = "user_report.xlsx";
		$filePath = 'assets/files/';
		//print_r($filePath.$fileName);die;	    
		$spreadsheet = new Spreadsheet();
		$spreadsheet->createSheet(1);


		$activeUsers = $this->report_model->userCount('id',array("u.is_active"=>1,"u.registration_status"=>2,"u.gender !="=>0,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		$maleUsers = $this->report_model->userCount('id',array("u.is_active"=>1,"u.registration_status"=>2,'gender'=>1,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		$femaleUsers=$this->report_model->userCount('id',array("u.is_active"=>1,"u.registration_status"=>2,'gender'=>2,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));		
		if($activeUsers!=0) {
			$malePercent = (100*$maleUsers)/ $activeUsers;
			$femalePercent = (100*$femaleUsers)/ $activeUsers;
		}else{
			$malePercent = 0;
			$femalePercent = 0;
		}

		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$sheet->setTitle("Active Users");
        //$objPHPExcel->setActiveSheetIndex($sheetId);
        //$sheet->getActiveSheet()->setTitle("Active Users");
        //Create Styles Array
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:E1' )->applyFromArray($styleArrayFirstRow);
		$sheet->setCellValue('A1', 'Total Users');
		$sheet->setCellValue('B1', 'Male Users');
		$sheet->setCellValue('C1', 'Female Users');
		$sheet->setCellValue('D1', 'Male Percentage (%)');
		$sheet->setCellValue('E1', 'Female Percentage (%)');
		
		$rows = 2;
		$sheet->setCellValue('A' . $rows, $activeUsers);
		$sheet->setCellValue('B' . $rows, $maleUsers);
		$sheet->setCellValue('C' . $rows, $femaleUsers);
		$sheet->setCellValue('D' . $rows, $malePercent);
		$sheet->setCellValue('E' . $rows, $femalePercent);            
           // $rows++;

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
        //$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        //$writer = new Xlsx($spreadsheet,1);


		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName);
        //PROFILE
        //$profileAge = $this->report_model->getProfileAge(array("u.is_active'=>1","u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		$profileAge = $this->report_model->getProfileAge(array("u.is_active"=>1,"u.registration_status"=>2,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
		$spreadsheet->createSheet();

		$sheet = $spreadsheet->setActiveSheetIndex(1);
		$sheet->setTitle("User Profile (Age)");

        $styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:F1' )->applyFromArray($styleArrayFirstRow);


        $sheet->setCellValue('A1', 'AGE GROUP');
        $sheet->setCellValue('B1', 'TOTAL USERS');
        $sheet->setCellValue('C1', 'Male IN NO.');
        $sheet->setCellValue('D1', 'Male Percentage (%)');
        $sheet->setCellValue('E1', 'Female IN NO.');
        $sheet->setCellValue('F1', 'Female Percentage (%)');

        $rows= 2;
        if(!empty($profileAge)) {
        	foreach ($profileAge as $key => $ageValue) {
        		$sheet->setCellValue('A' . $rows, $ageValue->bracket);
        		$sheet->setCellValue('B' . $rows, $ageValue->Total);
        		$sheet->setCellValue('C' . $rows, $ageValue->MALE);
        		$sheet->setCellValue('D' . $rows, $ageValue->Male_percent);
        		$sheet->setCellValue('E' . $rows, $ageValue->Female);            
        		$sheet->setCellValue('F' . $rows, $ageValue->Female_percent);            
        		$rows++;
        	}
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath.$fileName); 

        /////	USERS INTEREST

        //$profileInterest = $this->report_model->getProfileInterest(array("u.is_active'=>1","u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
        
        $profileInterest = $this->report_model->getProfileInterest(array("u.is_active"=>1,"u.registration_status"=>2,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
        //echo"<pre>";print_r($profileInterest);die;
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(2);
        $sheet->setTitle("User Profiles (Interest)");
        $styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:F1' )->applyFromArray($styleArrayFirstRow);

        $sheet->setCellValue('A1', 'INTEREST');
        $sheet->setCellValue('B1', 'TOTAL USERS');
        $sheet->setCellValue('C1', 'Male IN NO.');
        $sheet->setCellValue('D1', 'Male Percentage (%)');
        $sheet->setCellValue('E1', 'Female IN NO.');
        $sheet->setCellValue('F1', 'Female Percentage (%)');

        $rows= 2;
        if(!empty($profileInterest)) {
        	foreach ($profileInterest as $key => $intrestValue) {
        		$sheet->setCellValue('A' . $rows, $intrestValue->intrest);
        		$sheet->setCellValue('B' . $rows, $intrestValue->Total);
        		$sheet->setCellValue('C' . $rows, $intrestValue->MALE);
        		$sheet->setCellValue('D' . $rows, $intrestValue->Male_percent);
        		$sheet->setCellValue('E' . $rows, $intrestValue->Female);            
        		$sheet->setCellValue('F' . $rows, $intrestValue->Female_percent);            
        		$rows++;
        	}
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath.$fileName); 

        //	USERS COUNTRY

        ///	PROFILE COUNTRY
        $profileCountry = $this->report_model->getProfileCountry(array("u.is_active"=>1,"u.registration_status"=>2,"u.gender !="=>0,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
        //echo"<pre>";print_r($profileCountry);die;
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(3);
        $sheet->setTitle("User Profiles (Country)");
        $styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:F1' )->applyFromArray($styleArrayFirstRow);
        $sheet->setCellValue('A1', 'COUNTRY');
        $sheet->setCellValue('B1', 'TOTAL USERS');
        $sheet->setCellValue('C1', 'Male IN NO.');
        $sheet->setCellValue('D1', 'Male Percentage (%)');
        $sheet->setCellValue('E1', 'Female IN NO.');
        $sheet->setCellValue('F1', 'Female Percentage (%)');

        $rows= 2;
        if(!empty($profileCountry)) {
        	foreach ($profileCountry as $key => $countryValue) {
        		$user_country = $countryValue->country ==''?'Unknown':$countryValue->country;
        		$sheet->setCellValue('A' . $rows, $user_country);
        		$sheet->setCellValue('B' . $rows, $countryValue->Total);
        		$sheet->setCellValue('C' . $rows, $countryValue->MALE);
        		$sheet->setCellValue('D' . $rows, $countryValue->Male_percent);
        		$sheet->setCellValue('E' . $rows, $countryValue->Female);            
        		$sheet->setCellValue('F' . $rows, $countryValue->Female_percent);            
        		$rows++;
        	}
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath.$fileName); 

        ///	PROFILE COMPLETION

        //$profileArr = $this->report_model->getProfileCompletion(array("u.is_active'=>1","u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
        $profileCompArr = $this->report_model->getProfileCompletion(array("u.is_active"=>1,"u.registration_status"=>2,"u.created_dttm >="=>$startDt,"u.created_dttm <="=>$endDt));
        $profileArr = [];		
        $mainArray = $profileCompArr;		
        $finalArray =array();
        if(empty($mainArray))
        	$mainArray = array();

        $k=0;
        for ($percent = 10;$percent<=100;$percent=$percent+10) {    
        	$isNotFind = 0;
        	for ($j = 0;$j<count($mainArray);$j++) {
        		if($percent!=$mainArray[$j]['ProfileCompletion']) {
        			$isNotFind = $isNotFind+1;
        		}else {
        			$isNotFind = 0;
        			break;
        		}
        	}
        	if($isNotFind>0) {
        		$tempArray = array('ProfileCompletion'=>$percent,'Male'=>'0','Female'=>'0');
        		$finalArray[$k] = $tempArray;
        	}
        	$k++;
        }
        // add already data
        for ($m = 0;$m<count($mainArray);$m++) {
        	$index = $mainArray[$m]['ProfileCompletion']/10-1;
        	$finalArray[$index] = $mainArray[$m];
        }
        sort($finalArray);
        
        $profileArray = array();		
        
        $excel['profileChart'][] = ['"Profile Completion"','Male','Female'];		
        foreach ($finalArray as $key => $dataValue) {			
        	$G = $dataValue["Male"];
        	$B = $dataValue["Female"];			
        	array_push($excel['profileChart'], array('"'.$dataValue['ProfileCompletion'].'%"',(int)$B,(int)$G));

        }
		//echo"<pre>";print_r($excel['profileChart']);die;
		//echo count($excel['profileChart']);die;

        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(4);   
        $sheet->setTitle("Profile Completion");   
        $styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:C1' )->applyFromArray($styleArrayFirstRow);
        $rows= 1;
        if(!empty($excel['profileChart'])) {
        	$countEx = count($excel['profileChart']);
        	for ($ex=0; $ex<$countEx; $ex++) {
        		$sheet->setCellValue('A' . $rows, str_replace('"',' ',$excel['profileChart'][$ex][0]));
        		$sheet->setCellValue('B' . $rows, str_replace('"',' ',$excel['profileChart'][$ex][1]));
        		$sheet->setCellValue('C' . $rows, str_replace('"',' ',$excel['profileChart'][$ex][2]));
        		
        		$rows++;
        	}
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath.$fileName);

        ///	UAGE TRANDS

        $SampleRedeemed = $this->report_model->getSampleRedeemed(USER_SAMPLES,array('status'=>3,'is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
		$PromosRedeemed = $this->report_model->getSampleRedeemed(USER_PROMOCODES,array('status'=>3,'is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
		$ReviewsGiven = $this->report_model->getSampleRedeemed(USER_REVIEW,array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));		        
		$Comments = $this->report_model->getSampleRedeemed(WALL_COMMENTS,array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));        
		$Likes = $this->report_model->getSampleRedeemed(WALL_LIKES,array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
       $Brand = $this->report_model->getTotalViewed(BRANDS,'brand_viewed',array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
       $Campaigns = $this->report_model->getTotalViewed(CAMPAIGNS,'campaign_viewed',array('is_active'=>1,"created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
        //echo"<pre>";print_r($Comments);die;
        $usagesTrands[] = array("Days", "Sample Redeemed", "Promos Redeemed", "Reviews Given", "Comments", "likes","Brand","Campaigns");
        $day = array('Sunday'=>'Sun','Monday'=>'Mon','Tuesday'=>'Tue','Wednesday'=>'Wed','Thursday'=>'Thu','Friday'=>'Fri','Saturday'=>'Sat');
        foreach ($day as $Dkey=>$value ) {
			
			if(!empty($SampleRedeemed))
			{
				foreach ($SampleRedeemed as $Skey => $sampleVal) {
					if($sampleVal['Days']==$Dkey){
						$uagSample = (int)$sampleVal['COUNT'];
					}else{
						$uagSample = 0;
					}
				}
				
				
			}else{
				$uagSample = 0;
			}
			if(!empty($PromosRedeemed))
			{
				foreach ($PromosRedeemed as $Pkey => $promoVal) {
					if($promoVal['Days']==$Dkey){
						$uagPromo = (int)$promoVal['COUNT'];
					}else{
						$uagPromo = 0;
					}
				}
			}else{
				$uagPromo = (int)0;
			}
			if(!empty($ReviewsGiven))
			{
				foreach ($ReviewsGiven as $Rekey => $reviewvalue) {
					if($reviewvalue['Days']==$Dkey){
						$uagReview = (int)$reviewvalue['COUNT'];
					}else{
						$uagReview = 0;
					}	
				}
							  	
			}else{
				$uagReview = (int)0;
			}
			if(!empty($Comments))
			{
				foreach ($Comments as $cmtkey => $commentvalue) {
					if($commentvalue['Days']==$Dkey){
						$uagComment = (int)$commentvalue['COUNT'];
					}else{
						$uagComment = 0;
					}
				}
				
			}else{
				$uagComment = (int)0;
			}
			if(!empty($Likes))
			{
				foreach ($Likes as $Likey => $likvalue) {
					if($likvalue['Days']==$Dkey){
						$uagLikes = (int)$likvalue['COUNT'];
					}else{
						$uagLikes = 0;
					}
				}
				
			}else{
				$uagLikes = (int)0;
			}
			if(!empty($Brand))
			{
				foreach ($Brand as $brkey => $brandvalue) {
					if($brandvalue['Days']==$Dkey){
						$uagBrand = (int)$brandvalue['cviews'];
					}else{
						$uagBrand = 0;
					}
				}
				
			}else{
				$uagBrand = (int)0;
			}
			if(!empty($Campaigns))
			{
				foreach ($Campaigns as $cmpkey => $campvalue) {
					if($campvalue['Days']==$Dkey){
						$uagCamp = (int)$campvalue['cviews'];
					}else{
						$uagCamp = 0;
					}
				}
				
			}else{
				$uagCamp = (int)0;
			}
			array_push($usagesTrands, array('"'.$day[$Dkey].'"',$uagSample,$uagPromo,$uagReview,$uagComment,$uagLikes,$uagBrand,$uagCamp));			
		}
		
        //echo"<pre>";print_r($usagesTrands);die;
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(5);
        $sheet->setTitle("Uage Trends ");
        $styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:G1' )->applyFromArray($styleArrayFirstRow);
        $rows= 1;
        if(!empty($usagesTrands)) {
        	$countUt = count($usagesTrands);
        	for ($ut=0; $ut<$countUt; $ut++) {
        		$sheet->setCellValue('A' . $rows, str_replace('"',' ',$usagesTrands[$ut][0]));
        		$sheet->setCellValue('B' . $rows, str_replace('"',' ',$usagesTrands[$ut][1]));
        		$sheet->setCellValue('C' . $rows, str_replace('"',' ',$usagesTrands[$ut][2]));
        		$sheet->setCellValue('D' . $rows, str_replace('"',' ',$usagesTrands[$ut][3]));
        		$sheet->setCellValue('E' . $rows, str_replace('"',' ',$usagesTrands[$ut][4]));
        		$sheet->setCellValue('F' . $rows, str_replace('"',' ',$usagesTrands[$ut][5]));
        		$sheet->setCellValue('G' . $rows, str_replace('"',' ',$usagesTrands[$ut][6]));
        		$sheet->setCellValue('G' . $rows, str_replace('"',' ',$usagesTrands[$ut][7]));
        		
        		
        		$rows++;
        	}
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $writer->save($filePath.$fileName);       
        $data['msg'] = 'success';
        $data['file'] = $fileName;
        echo json_encode($data);die;

    }

    public function campaignReportDetail($campaign_id='') {
		//
    	$campaignDetail = array();
    	$campaignDetail['summery'] = $this->report_model->getcampaignSummery(array('cb.cover_image' =>1,'cmp.id'=>$campaign_id));
		//echo"<pre>";print_r($campaignDetail['summery']);die;

		//	PROMO CODE ACTIVITY

    	$campaignDetail['promeActivity'] = $this->report_model->getPromoActivity(array('wp.has_promo'=>1,'campaign_id'=>$campaign_id));
		//echo"<pre>";print_r($campaignDetail['promeActivity']);die;
		//	PROMO POST LIKES

    	$campaignDetail['promeLike'] = $this->report_model->getPromoLike(array('wp.has_promo'=>1,'wp.campaign_id'=>$campaign_id));
		//echo"<pre>";print_r($campaignDetail['promeLike']);die;
		//	PROMO POST COMMENTS
    	$campaignDetail['promeComments'] = $this->report_model->getPromoComment(array('wp.has_promo'=>1,'wp.campaign_id'=>$campaign_id));

		///		REGULAR POST

    	$campaignDetail['regularPost'] = $this->report_model->getRegularPost(array('wp.has_promo'=>0,'wp.campaign_id'=>$campaign_id));
		//echo"<pre>";print_r($campaignDetail['regularPost']);die;
    	$campaignDetail['title'] = 'Campaign Detail';
    	$this->load->view('common/header',$campaignDetail);
    	$this->load->view('campaign_report_detail',true);			
    	$this->load->view('common/footer');	
    }

    public function campaignDetailExcelExport(){
		//echo 12;die;
    	require(APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php');
    	$requestData = $this->input->post();
    	$campaign_id = $requestData['campaign_id'];
		
		$fileName = "campaign_detail_report.xlsx";
		$filePath = 'assets/files/';		
		$spreadsheet = new Spreadsheet();
		$spreadsheet->createSheet(1);
	   		
		$campaignSummery = $this->report_model->getcampaignSummery(array('cb.cover_image' =>1,'cmp.id'=>$campaign_id));
		//echo"<pre>";print_r($campaignSummery);die;

		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$sheet->setTitle("Campaign Summery");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:G1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'Campaign Name');
		$sheet->setCellValue('B1', 'Created on');
		$sheet->setCellValue('C1', 'Total Samples');
		$sheet->setCellValue('D1', 'Sample Redeemed');
		$sheet->setCellValue('E1', 'Promotions Redeemed');
		$sheet->setCellValue('F1', 'Total Post');
		$sheet->setCellValue('G1', 'NPS');
		
		
		$rows = 2;
        //foreach ($campaignSummery as $key => $cs) {
		$sheet->setCellValue('A' . $rows, $campaignSummery['campaign_name']);
		$sheet->setCellValue('B' . $rows, date('M d, Y',strtotime($campaignSummery['created_dttm'])));
		$sheet->setCellValue('C' . $rows, $campaignSummery['total_campaign_samples']);
		$sheet->setCellValue('D' . $rows, $campaignSummery['total_campaign_samples_used']);
		$sheet->setCellValue('E' . $rows, $campaignSummery['promo']);
		$sheet->setCellValue('F' . $rows, $campaignSummery['wallpost']);
		$sheet->setCellValue('G' . $rows, NPS($campaignSummery['id']));
           	//$rows++;
        //}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName);
		
        ///	PROMO CODE ACTIVITY
		
		$promoActivity = $this->report_model->getPromoActivity(array('wp.has_promo'=>1,'campaign_id'=>$campaign_id));
        //echo"<pre>";print_r($promoActivity);die;
		$spreadsheet->createSheet();
		
		$sheet = $spreadsheet->setActiveSheetIndex(1);
		$sheet->setTitle("PROMO CODE ACTIVITY");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:F1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'Post Name');
		$sheet->setCellValue('B1', 'Post Type');
		$sheet->setCellValue('C1', 'Publish Date');
		$sheet->setCellValue('D1', 'Save');
		$sheet->setCellValue('E1', 'Redeemed');
		$sheet->setCellValue('F1', 'Total NPS');

		$rows= 2;
		if(!empty($promoActivity)) {
			foreach ($promoActivity as $key => $promoValue) {
				$postType = $promoValue['banner_type']==1?'Image':'Video';
				$promoDate = date('d M, Y',strtotime($promoValue['publish_date']));
				$totalNps = NPS($promoValue['id']);
				$sheet->setCellValue('A' . $rows, $promoValue['post_title']);
				$sheet->setCellValue('B' . $rows, $postType);
				$sheet->setCellValue('C' . $rows, $promoDate);
				$sheet->setCellValue('D' . $rows, $promoValue['Saved']);
				$sheet->setCellValue('E' . $rows, $promoValue['Redeemed']);
				$sheet->setCellValue('F' . $rows, $totalNps);
				$rows++;
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName); 

        /////	PROMO POST LIKES
		
		$campaignsPromoLike = $this->report_model->getPromoLike(array('wp.has_promo'=>1,'wp.campaign_id'=>$campaign_id));
        //echo"<pre>";print_r($campaignsPromoLike);die;
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->setActiveSheetIndex(2);
		$sheet->setTitle("PROMO POST LIKES");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:G1' )->applyFromArray($styleArrayFirstRow);
		$sheet->setCellValue('A1', 'Post Name');
		$sheet->setCellValue('B1', 'Post Type');
		$sheet->setCellValue('C1', 'Total Likes');
		$sheet->setCellValue('D1', 'Male In No');
		$sheet->setCellValue('E1', 'Male In %');
		$sheet->setCellValue('F1', 'Female In No');		
		$sheet->setCellValue('G1', 'Female In %');

		$rows= 2;
		if(!empty($campaignsPromoLike)) {
			foreach ($campaignsPromoLike as $key => $likeValue) {				
				$postType = $likeValue['banner_type']==1?'Image':'Video';
				$sheet->setCellValue('A' . $rows, $likeValue['post_title']);
				$sheet->setCellValue('B' . $rows, $postType);
				$sheet->setCellValue('C' . $rows, $likeValue['no_of_likes']);
				$sheet->setCellValue('D' . $rows, $likeValue['MALE']);
				$sheet->setCellValue('E' . $rows, $likeValue['M_percent']);
				$sheet->setCellValue('F' . $rows, $likeValue['Female']);
				$sheet->setCellValue('G' . $rows, $likeValue['F_percent']);
				$rows++;
				
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName); 

        //	PROMO POST COMMENTS

		$campaignsPromoComment = $this->report_model->getPromoComment(array('wp.has_promo'=>1,'wp.campaign_id'=>$campaign_id));
        //echo"<pre>";print_r($campaignsPromoComment);die;
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->setActiveSheetIndex(3);
		$sheet->setTitle("PROMO POST COMMENTS");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:G1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'Post Name');
		$sheet->setCellValue('B1', 'Post Type');
		$sheet->setCellValue('C1', 'Total Comments');
		$sheet->setCellValue('D1', 'Male In No.');
		$sheet->setCellValue('E1', 'Male In %');
		$sheet->setCellValue('F1', 'Female In No');
		$sheet->setCellValue('G1', 'Female In %');

		$rows= 2;
		if(!empty($campaignsPromoComment)) {
			foreach ($campaignsPromoComment as $key => $commentValue) {				
				$postType = $commentValue['banner_type']==1?'Image':'Video';
				$sheet->setCellValue('A' . $rows, $commentValue['post_title']);
				$sheet->setCellValue('B' . $rows, $postType);		           
				$sheet->setCellValue('C' . $rows, $commentValue['no_of_comments']);
				$sheet->setCellValue('D' . $rows, $commentValue['MALE']);
				$sheet->setCellValue('E' . $rows, $commentValue['M_percent']);
				$sheet->setCellValue('F' . $rows, $commentValue['Female']);
				$sheet->setCellValue('G' . $rows, $commentValue['F_percent']);
				$rows++;
				
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName); 

        //	REGULAR POST

		$campaignsRegularPost = $this->report_model->getRegularPost(array('wp.has_promo'=>0,'wp.campaign_id'=>$campaign_id));
        //echo"<pre>";print_r($campaignsRegularPost);die;
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->setActiveSheetIndex(4);
		$sheet->setTitle("REGULAR POST");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:K1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'Post Name');
		$sheet->setCellValue('B1', 'Total Likes');        
		$sheet->setCellValue('C1', 'Male In No.');
		$sheet->setCellValue('D1', 'Male In %');
		$sheet->setCellValue('E1', 'Female In No');
		$sheet->setCellValue('F1', 'Female In %');
		$sheet->setCellValue('G1', 'Total Comments');
		$sheet->setCellValue('H1', 'Male In No.');
		$sheet->setCellValue('I1', 'Male In %');
		$sheet->setCellValue('J1', 'Female In No');
		$sheet->setCellValue('K1', 'Female In %');

		$rows= 2;
		if(!empty($campaignsRegularPost)) {
			foreach ($campaignsRegularPost as $key => $regValue) {				
				$maleLike = $regValue['Likes'][0]['MALE']==''?'0':$regValue['Likes'][0]['MALE'];
				$maleLike_per = $regValue['Likes'][0]['MALE']==''?'0':$regValue['Likes'][0]['MALE'];
				$femaleLike = $regValue['Likes'][0]['Female']==''?'0':$regValue['Likes'][0]['Female'];
				$femaleLike_per = $regValue['Likes'][0]['F_percent']==''?'0':$regValue['Likes'][0]['F_percent'];
				
				$maleCmnt = $regValue['Comments'][0]['MALE']==''?'0':$regValue['Comments'][0]['MALE'];
				$maleCmnt_per = $regValue['Comments'][0]['M_percent']==''?'0':$regValue['Comments'][0]['M_percent'];
				$femaleCmnt = $regValue['Comments'][0]['Female']==''?'0':$regValue['Comments'][0]['Female'];
				$femaleCmnt_per = $regValue['Comments'][0]['F_percent']==''?'0':$regValue['Comments'][0]['F_percent'];

				$sheet->setCellValue('A' . $rows, $regValue['post_title']);
				$sheet->setCellValue('B' . $rows, $regValue['no_of_likes']);
				$sheet->setCellValue('C' . $rows, $maleLike);
				$sheet->setCellValue('D' . $rows, $maleLike_per);
				$sheet->setCellValue('E' . $rows, $femaleLike);
				$sheet->setCellValue('F' . $rows, $femaleLike_per);
				$sheet->setCellValue('G' . $rows, $regValue['no_of_comments']);
				$sheet->setCellValue('H' . $rows, $maleCmnt);
				$sheet->setCellValue('I' . $rows, $maleCmnt_per);
				$sheet->setCellValue('J' . $rows, $femaleCmnt);
				$sheet->setCellValue('K' . $rows, $femaleCmnt_per);
				$rows++;
				
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$spreadsheet->setActiveSheetIndex(0);
		$writer->save($filePath.$fileName);

		$data['msg'] = 'success';
		$data['file'] = $fileName;
		echo json_encode($data);die;
	}
	public function campaigns()
	{
		ini_set('max_execution_time', 120);
		//echo"<pre>";print_r($this->input->post());die;

		$campaignData = array();
		$conditions = array('u.is_active'=>1);
		//$varCond['active'] = "u.is_active ='1'";
		$varCond= '';
		$campaignData['range'] ='Daily';
		$currentDate = date('Y-m-d 00:00:00');
		$lastDate = date('Y-m-d 23:59:59');
		//$status = " campVend.is_active='1'  AND vendM.is_active='1' AND campVend.campaign_id='".$campaign_id."'";
		$requestData = $this->input->post();
		if(!empty($requestData)) {
			//print_r($requestData);die;
			$start = $requestData['stDate'];
			$end = $requestData['edDate'];
			$range = $requestData['range'];
			if(!empty($range)) {
				if($range=='Daily') {
					$start = $currentDate;
					$end = $lastDate;
				}else if($range=='Weekly') {					
					$stWeek = $this->rangeWeek($currentDate);					
					$start = date('Y-m-d',strtotime($stWeek['start']));
					$end = date('Y-m-d',strtotime($stWeek['end']));
				}else if($range=='Monthly') {
					$start = date('Y-m-1');
					$end = date('Y-m-t');
				}
				$campaignData['range'] = $range;
			}
			if(!empty($start)) {
				//$conditions = array_merge($conditions,array("u.created_dttm >='".$start."'"));
				$startDt = date('Y-m-d 00:00:00',strtotime($start));
			}
			if(!empty($end)) {
				//$conditions =  array_merge($conditions,array("u.created_dttm <='".$end."'"));
				$endDt = date('Y-m-d 23:59:59',strtotime($end));
			}
			
		}
		$endDt = isset($endDt)==''?date('Y-m-d 23:59:59'):$endDt;
		$startDt = isset($startDt)==''?date('Y-m-d 00:00:00'):$startDt;
		
		//	LIVE CAMPAIGNS CODE
		$campaignData['current_date'] = date('d M Y');
		$campaignData['code'] = $this->report_model->getCampaignCode(array("us.created_dttm >="=>$startDt,"us.created_dttm <="=>$endDt));
		//echo"<pre>";print_r($campaignData['current_date']);die;

		// 	LIVE CAMPAIGNS REVIEW

		$campaignData['reviews'] = $this->report_model->getCampaignReview(array("ur.created_dttm >="=>$startDt,"ur.created_dttm <="=>$endDt));
		//echo"<pre>";print_r($campaignData['reviews']);die;

		//	REVIEW QUESTION DISTRIBUTION

		$campaignData['reviews_question'] = $this->report_model->getCampaignReviewQus(array("us.created_dttm >="=>$startDt,"us.created_dttm <="=>$endDt),$startDt,$endDt);
		$reviews_campaign = $this->report_model->userReviewAns(array("u.is_active"=>1,"u.registration_status"=>2,"u.gender !="=>0),$startDt,$endDt);
		$reviewAns = array_chunk($reviews_campaign, 2);
		$campaignData['reviewAns'] = $reviewAns;


		//echo "<pre>";print_r($reviews_campaign);die;
	//	$campaignData['reviews_question'] = $this->report_model->getCampaignReviewQus(array("u.is_active"=>1,"u.registration_status"=>2,"u.gender !="=>0,"us.created_dttm >="=>$startDt,"us.created_dttm <="=>$endDt));
		//echo"<pre>";print_r($reviews_question);die;
	//	$reviews_question = $this->report_model->getReviewQestion('reviewQuest.*',array("reviewQuest.is_active"=>1,"camp.id"=>$reviews_campaign[0]['campaignId']));
	//	echo"<pre>";print_r($reviews_question);die;

		//echo "<pre>";print_r($campaignData['reviews_question']);die;
	/*	$i=0;
		foreach ($reviews_question as $key => $rqvalue) {
			$reviewQus_query[$i] = $this->report_model->getReviewQestion(array("review_id"=>$rqvalue['reviewId']));
		}*/
		$campaignData['title'] = 'Reports';
		$this->load->view('common/header',$campaignData);
		if($this->input->is_ajax_request())
		{				
			$campaignData['type'] = 'success';
			$campaignData['data'] = $campaignData;
			$campaignData['view'] = $this->load->view('report_campain_body',compact('campaignData'),true);						
			echo json_encode($campaignData);die;
		}
		else{					
			$this->load->view('campaign_report',true);				
		}
		$this->load->view('common/footer');	
	}

	public function campaignReviewData()
	{
		$data = $this->input->post();
		$campaign_id = $data['campaign_id'];		
		$data['reviewData']=$this->report_model->getCampaignReviewData(array('cmp.id'=>$campaign_id));
		//print_r($data['radioCity']);die;
		$data['topreview'] ='';
		$noImage =  base_url().'assets/img/users/female.jpg';
		if(!empty($data['reviewData'])){
			foreach ($data['reviewData'] as $key => $reviewValue) {
				$picName = $reviewValue["image"]==''? $noImage:$reviewValue["image"];
				$data['topreview'] .='<tr><td><a href="'.base_url('user-details/'.$reviewValue["user_id"]).'" class="user-link"><span><img src="'.$picName.' " alt="userimage"></span>'.$reviewValue["name"].'</a></td><td><p class="limitText">'.$reviewValue["review_text"].'</p></td> <td>'.$reviewValue["rating"].'</td> <td><a href="'.base_url('user-details/'.$reviewValue["user_id"]).'" target="_blank" class="text-link">View Profile</a></td></tr>';
			}
			$data['res'] = 'success';
		}else{
			$data['topreview'] ='<span class"text-center">No record found </span>';
			$data['res'] = 'error';
		}
		echo json_encode($data);die;
		//print_r($data['radioCity']);die;
	}

	public function userReviewData()
	{
		$data = $this->input->post();
		$qus_id = $data['qus_id'];
		$gender = $data['gender'];
		$data['userReviewData']=$this->report_model->getUserReviewData(array('ura.question_id'=>$qus_id,'u.gender'=>$gender));
		//print_r($data['userReviewData']);die;
		$data['topreview'] ='';
		if(!empty($data['userReviewData'])){
			foreach ($data['userReviewData'] as $key => $userValue) {
				$image = $userValue["image"]==''?'':'<img src="'.$userValue["image"].' " alt="userimage">';

				$data['topreview'] .='<tr>  <td><a href="'.base_url('user-details/'.$userValue["id"]).'" class="user-link"><span>'.$image.'</span>'.$userValue['name'].'</a></td><td>'.$userValue['age_bracket_desc'].' </td><td>'.date("d M Y",strtotime($userValue['created_dttm'])).'</td><td><a href="'.base_url('user-details/'.$userValue["id"]).'"  class="text-link">View Profile</a></td></tr>';
			}
			$data['res'] = 'success';
		}else{
			$data['topreview'] ='<span class"text-center">No record found </span>';
			$data['res'] = 'error';
		}
		echo json_encode($data);die;
		//print_r($data['radioCity']);die;
	}

	// CAMPAIGN REPORT EXPORT
	public function campaignExcelExport()
	{
		//print_r($_POST);die;
		require(APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php');
		
		$currentDate = date('Y-m-d 00:00:00');
		$lastDate = date('Y-m-d 23:59:59');
		$requestData = $this->input->post();

		if(!empty($requestData)) {			
			$start = $requestData['st'];
			$end = $requestData['ed'];
			$range = $requestData['range'];
			if(!empty($range)) {
				if($range=='Daily') {
					$start = $currentDate;
					$end = $lastDate;
				}else if($range=='Weekly') {					
					$stWeek = $this->rangeWeek($currentDate);					
					$start = date('Y-m-d',strtotime($stWeek['start']));
					$end = date('Y-m-d',strtotime($stWeek['end']));
				}else if($range=='Monthly') {
					$start = date('Y-m-1');
					$end = date('Y-m-t');
				}
				$data['range'] = $range;
			}
			if(!empty($start)) {
				$startDt = date('Y-m-d 00:00:00',strtotime($start));
			}
			if(!empty($end)) {
				$endDt = date('Y-m-d 23:59:59',strtotime($end));
			}
			
		}
		$endDt = isset($endDt)==''?date('Y-m-d 23:59:59'):$endDt;
		$startDt = isset($startDt)==''?date('Y-m-d 00:00:00'):$startDt;
		
		$fileName = "campaign_report.xlsx";
		$filePath = 'assets/files/';		
		$spreadsheet = new Spreadsheet();
		$spreadsheet->createSheet();
		$rsCampaignCode = $this->report_model->getCampaignCode(array("us.created_dttm >="=>$startDt,"us.created_dttm <="=>$endDt));		

		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$sheet->setTitle("LIVE CAMPAIGNS-CODES");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:C1' )->applyFromArray($styleArrayFirstRow);
		$sheet->setCellValue('A1', 'Campaign Name');
		$sheet->setCellValue('B1', 'Save');
		$sheet->setCellValue('C1', 'Redeemed');
		
		$rows = 2;
		foreach ($rsCampaignCode as $key => $cc) {
			$sheet->setCellValue('A' . $rows, $cc['compaign_name']);
			$sheet->setCellValue('B' . $rows, $cc['saved']);
			$sheet->setCellValue('C' . $rows, $cc['redeems']);
			$rows++;
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName);
		
        ///	CAMPAIGNS  REVIEWS
        
		$liveReviews = $this->report_model->getCampaignReview(array("ur.created_dttm >="=>$startDt,"ur.created_dttm <="=>$endDt));
        //echo"<pre>";print_r($liveReviews);die;
		$spreadsheet->createSheet();

		$sheet = $spreadsheet->setActiveSheetIndex(1);
		$sheet->setTitle("LIVE CAMPAIGNS-REVIEWS");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:C1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'Campaign Name');
		$sheet->setCellValue('B1', 'Reviews');
		$sheet->setCellValue('C1', 'AVG. Rating');

		$rows= 2;
		if(!empty($liveReviews)) {
			foreach ($liveReviews as $key => $revValue) {
				$sheet->setCellValue('A' . $rows, $revValue['campaign_name']);
				$sheet->setCellValue('B' . $rows, $revValue['avg_review']. ' Of '.$revValue['total_review']);
				$sheet->setCellValue('C' . $rows, $revValue['avg_rating']);
				$rows++;
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName); 

        /////	REVIEW QUESTION

		$campaignsReviewQus = $this->report_model->getCampaignReviewQus(array("us.created_dttm >="=>$startDt,"us.created_dttm <="=>$endDt),$startDt,$endDt);
        //echo"<pre>";print_r($campaignsReviewQus);die;
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->setActiveSheetIndex(2);
		$sheet->setTitle("REVIEW QUESTION DISTRIBUTION");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:E1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'CAMPAIGN NAME');
		$sheet->setCellValue('B1', 'Male In No');
		$sheet->setCellValue('C1', 'Male In %');
		$sheet->setCellValue('D1', 'Female In No');
		$sheet->setCellValue('E1', 'Female In %');

		$rows= 2;
		if(!empty($campaignsReviewQus)) {
			foreach ($campaignsReviewQus as $key => $campaignValue) {
				$sheet->setCellValue('A' . $rows, $campaignValue['campaign_name']);
				$sheet->setCellValue('B' . $rows, $campaignValue['MALE']);
				$sheet->setCellValue('C' . $rows, $campaignValue['M_percent']);
				$sheet->setCellValue('D' . $rows, $campaignValue['Female']);
				$sheet->setCellValue('E' . $rows, $campaignValue['F_percent']);
				$rows++;
				
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$spreadsheet->setActiveSheetIndex(0);
		$writer->save($filePath.$fileName); 

		$data['msg'] = 'success';
		$data['file'] = $fileName;
		echo json_encode($data);die;

	}

	public function vendingMachine()
	{
		ini_set('max_execution_time', 120);
		//echo"<pre>";print_r($this->input->post());die;
		$machineData = array();
		$conditions = array('u.is_active'=>1);		
		$varCond= '';
		$machineData['range'] ='Daily';
		$currentDate = date('Y-m-d 00:00:00');			
		$lastDate = date('Y-m-d 23:59:59');			
		$requestData = $this->input->post();
		if(!empty($requestData)) {			
			$start = $requestData['stDate'];
			$end = $requestData['edDate'];
			$range = $requestData['range'];
			if(!empty($range)) {
				if($range=='Daily') {
					$start = $currentDate;
					$end = $lastDate;
				}else if($range=='Weekly') {					
					$stWeek = $this->rangeWeek($currentDate);					
					$start = date('Y-m-d 00:00:00',strtotime($stWeek['start']));
					$end = date('Y-m-d 23:59:59',strtotime($stWeek['end']));
				}else if($range=='Monthly') {
					$start = date('Y-m-1');
					$end = date('Y-m-t');
				}
				$machineData['range'] = $range;
			}
			if(!empty($start)) {
				$startDt = date('Y-m-d 00:00:00',strtotime($start));
			}
			if(!empty($end)) {
				$endDt = date('Y-m-d 23:59:59',strtotime($end));
			}
			
		}

		$startDt = isset($startDt)==''?date('Y-m-d 00:00:00'):$startDt;
		$endDt = isset($endDt)==''?date('Y-m-d 23:59:59'):$endDt;		
		$machineData['current_date'] = date('d M Y');
		
		// 	MACHINE STATUS

		$machineData['machine_status'] = $this->report_model->getMachineStatus(array("created_dttm >="=>$startDt,"created_dttm <="=>$endDt,"is_active"=>1));
		
		//	ALERTS

		$machineData['machine_alerts'] = $this->report_model->getMachineAlerts(array("cv.created_dttm >="=>$startDt,"cv.created_dttm <="=>$endDt,"cv.is_active"=>1));
		
		//	MACHINE ACTIVITY BY CAMPAIGN

		$machineData['machine_activity'] = $this->report_model->getMachineActivity(array("created_dttm >="=>$startDt,"created_dttm <="=>$endDt));		
		
		$machineData['title'] = 'Reports';
		$this->load->view('common/header',$machineData);
		if($this->input->is_ajax_request())
		{				
			$machineData['type'] = 'success';
			$machineData['data'] = $machineData;
			$machineData['view'] = $this->load->view('report_vending_body',compact('machineData'),true);						
			echo json_encode($machineData);die;
		}
		else{					
			$this->load->view('vending_report',true);				
		}
		$this->load->view('common/footer');	
	}

	public function vendingExcelExport()
	{
		//print_r($_POST);die;
		require(APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php');
		
		$currentDate = date('Y-m-d 00:00:00');
		$lastDate = date('Y-m-d 23:59:59');
		$requestData = $this->input->post();

		if(!empty($requestData)) {			
			$start = $requestData['st'];
			$end = $requestData['ed'];
			$range = $requestData['range'];
			if(!empty($range)) {
				if($range=='Daily') {
					$start = $currentDate;
					$end = $lastDate;
				}else if($range=='Weekly') {					
					$stWeek = $this->rangeWeek($currentDate);					
					$start = date('Y-m-d',strtotime($stWeek['start']));
					$end = date('Y-m-d',strtotime($stWeek['end']));
				}else if($range=='Monthly') {
					$start = date('Y-m-1');
					$end = date('Y-m-t');
				}
				$data['range'] = $range;
			}
			if(!empty($start)) {
				$startDt = date('Y-m-d 00:00:00',strtotime($start));
			}
			if(!empty($end)) {
				$endDt = date('Y-m-d 23:59:59',strtotime($end));
			}
			
		}
		$endDt = isset($endDt)==''?date('Y-m-d 23:59:59'):$endDt;
		$startDt = isset($startDt)==''?date('Y-m-d 00:00:00'):$startDt;
		
		$fileName = "vending_report.xlsx";
		$filePath = 'assets/files/';
		$spreadsheet = new Spreadsheet();
		$spreadsheet->createSheet(1);

		$rsMachineStatus = $this->report_model->getMachineStatus(array("created_dttm >="=>$startDt,"created_dttm <="=>$endDt));
		
		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$sheet->setTitle("Machine Status");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:F1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'Country Name');
		$sheet->setCellValue('B1', 'Total Location');
		$sheet->setCellValue('C1', 'Total Campaigns');
		$sheet->setCellValue('D1', 'Total Vending Machines');
		$sheet->setCellValue('E1', 'Active');
		$sheet->setCellValue('F1', 'Inactive');
		
		$rows = 2;
		foreach ($rsMachineStatus as $key => $mv) {
			$sheet->setCellValue('A' . $rows, $mv['country_name']);
			$sheet->setCellValue('B' . $rows, $mv['total_location']);
			$sheet->setCellValue('C' . $rows, $mv['campaigns']);
			$sheet->setCellValue('D' . $rows, $mv['vending_machine']);
			$sheet->setCellValue('E' . $rows, $mv['Active']);
			$sheet->setCellValue('F' . $rows, $mv['Inactive']);
			$rows++;
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName);
		
        ///	MACHINE ALERTS
		$machineAlert = $this->report_model->getMachineAlerts(array("vm.created_dttm >="=>$startDt,"vm.created_dttm <="=>$endDt));
        //echo"<pre>";print_r($machineAlert);die;
		$spreadsheet->createSheet();

		$sheet = $spreadsheet->setActiveSheetIndex(1);
		$sheet->setTitle("Machine Alerts");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:B1' )->applyFromArray($styleArrayFirstRow);

		$sheet->setCellValue('A1', 'MACHINE ID');
		$sheet->setCellValue('B1', 'ALERT TYPE');

		$rows= 2;
		if(!empty($machineAlert)) {
			foreach ($machineAlert as $key => $altValue) {
				$sheet->setCellValue('A' . $rows, $altValue['vending_machine_code']);
				$sheet->setCellValue('B' . $rows, $altValue['sample_left'].' Samples Left');
				$rows++;
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$writer->save($filePath.$fileName); 

        /////	MACHINE ACTIVITY BY CAMPAIGN

		$campaignsActivity = $this->report_model->getMachineActivity(array("created_dttm >="=>$startDt,"created_dttm <="=>$endDt));        
		$spreadsheet->createSheet();
		$sheet = $spreadsheet->setActiveSheetIndex(2);
		$sheet->setTitle("Machine Activity by Campaign");
		$styleArrayFirstRow = [
		            'font' => [
		                'bold' => true,
		            ]
		        ];

		$sheet->getStyle('A1:E1' )->applyFromArray($styleArrayFirstRow);
		
		$sheet->setCellValue('A1', 'CAMPAIGN NAME');
		$sheet->setCellValue('B1', 'MACHINE ID');
		$sheet->setCellValue('C1', 'STATUS');
		$sheet->setCellValue('D1', 'LOCATION');
		$sheet->setCellValue('E1', 'SAMPLES VENDED');

		$rows= 2;
		if(!empty($campaignsActivity)) {
			foreach ($campaignsActivity as $key => $actValue) {
				foreach ($actValue['campaignVends'] as $key => $vends) {
					$activityStatus = $vends['machineVends'][0]['is_active']==1?'ACTIVE':'DEACTIVE';
					$sheet->setCellValue('A' . $rows, $actValue['campaign_name']);
					$sheet->setCellValue('B' . $rows, $vends['machineVends'][0]['vending_machine_code']);
					$sheet->setCellValue('C' . $rows, $activityStatus);
					$sheet->setCellValue('D' . $rows, $vends['machineVends'][0]['location_address']);
					$sheet->setCellValue('E' . $rows, $vends['vend_no_of_sample_used']);
					$rows++;
				}
				
			}
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');

        //$spreadsheet->createSheet();
		$writer = new Xlsx($spreadsheet);
		$spreadsheet->setActiveSheetIndex(0);
		$writer->save($filePath.$fileName); 

		$data['msg'] = 'success';
		$data['file'] = $fileName;
		echo json_encode($data);die;

	}

	public function trashFile()
	{
		$data = $this->input->post();		
		$filename = $data['fileName'];
		$filePath = 'assets/files/';
		unlink($filePath.$filename);
		$msg = 'success';die;

	}


	function rangePick()
	{
		$range['title'] = 'Reports';
		$this->load->view('range_picker',$range);				
		$this->load->view('common/footer');	
	}

}


?>