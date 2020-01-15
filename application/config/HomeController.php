<?php
ob_start();
App::uses('AppController', 'Controller');
class HomeController extends AppController {
var $name =	'home';
var $components = array('Cookie','Email','Session','RequestHandler'); 
var $helpers = array('Html', 'Js', 'Form','Session','Paginator','Common');
var $uses=array('AppUser');
	public function beforeFilter()
	{   
		parent::beforeFilter();
		$ses=$this->Session->read('admin'); 
		$action=$this->Session->read('action_to');
		if(!$this->RequestHandler->isAjax()) 
		{    
			$action=$this->Session->read('action_to');
			if($ses && in_array($this->request->action,array('index')))
			{  
				$this->redirect(array('controller'=>'home','action' =>'customers'));
			}
			else if(empty($ses) && !in_array($this->request->action,array('index')))
			{     
				if($_GET['email_req'])
				{
					$this->Session->write('action_to', $this->request->action);					
				}
				
				$this->redirect(array('controller'=>'home','action' => 'index'));
			}
			else if($action!='' && !empty($ses))
			{    
				$this->Session->write('action_to', '');
				$this->redirect(array('controller'=>'home','action' =>$action));
			}
		}
	}
	function index()
	{	
	}
	function login()
	{
		$this->loadModel('AppUser');
		$this->loadModel('UserCredential');
		$this->loadModel('UserPermission');
		$this->loadModel('Permission');
		$this->loadModel('BusinessLine');
		$this->loadModel('Subvertical');
		
		$data=$this->data;
		$email=$data['email'];
		$pass=md5($data['password']);
		
		$login_detail=$this->AppUser->find('first',array( 'recursive'=>'-1','field'=>array('AppUser.id','AppUser.user_type','AppUser.role_id'),'conditions'=>array('AppUser.user_email'=>$email)));
		if($login_detail)
		{   
			$log_detail=$this->UserCredential->find('first',array( 'recursive'=>'-1','field'=>array('UserCredential.id'),'conditions'=>array('UserCredential.credential'=>$pass,'UserCredential.app_user_id'=>$login_detail['AppUser']['id'])));
			if(!empty($log_detail))
			{
				$u_role=$this->UserPermission->find('list',array('recursive'=>'-1','field'=>array('UserPermission.permission_id'),'conditions'=>array('UserPermission.app_user_id'=>$log_detail['UserCredential']['app_user_id'])));
				$perm_role=$this->Permission->find('all',array('recursive'=>'-1','field'=>array('Permission.id','Permission.permission_desc'),'conditions'=>array('Permission.id'=>$u_role)));
			   $count_role=$this->UserPermission->find('count',array('recursive'=>'-1','field'=>array('UserPermission.permission_id'),'conditions'=>array('UserPermission.app_user_id'=>$log_detail['UserCredential']['app_user_id'])));
			   $user_role=$this->UserPermission->find('all',array('recursive'=>'-1','field'=>array('UserPermission.permission_id'),'conditions'=>array('UserPermission.app_user_id'=>$log_detail['UserCredential']['app_user_id'])));
			   //pre($user_role); die;
			    if($count_role >= 2){
			    	foreach ($user_role as $key => $role) {
			    		$permission_data=$this->Permission->find('first',array('recursive'=>'-1','field'=>array('Permission.id','Permission.permission_desc'),'conditions'=>array('Permission.id'=>$role['UserPermission']['permission_id'])));
			    		$bisiness_data=$this->BusinessLine->find('first',array('recursive'=>'-1','field'=>array('BusinessLine.id','BusinessLine.bl_name'),'conditions'=>array('BusinessLine.id'=>$role['UserPermission']['business_line_id'])));
			    		$subvertical_data=$this->Subvertical->find('first',array('recursive'=>'-1','field'=>array('Subvertical.id','Subvertical.sv_name'),'conditions'=>array('Subvertical.id'=>$role['UserPermission']['subvertical_id'])));
			    		$roles.='<li>
								<label class="clicklable managerclick" for="managerrole">
								<input type="hidden" name="hidden_u_id" value="'.$login_detail['AppUser']['id'].'">
									<div class="customcheckbox">
										<label>
											<input type="radio" name="roleselect" id="'.$role['UserPermission']['permission_id'].'" value="'.$role['UserPermission']['permission_id'].'" class="addClickadd"> <b></b></label>
									</div>
									<div class="rolecategoery">
										<h3>'.$permission_data['Permission']['permission_desc'].'</h3>
									   <div class="roleca-use"><span class="vartical" id="'.$bisiness_data['BusinessLine']['id'].'">'.$bisiness_data['BusinessLine']['bl_name'].'</span><span class="subvertical" id="'.$subvertical_data['Subvertical']['id'].'">'.$subvertical_data['Subvertical']['sv_name'].'</span></div>
									</div>
								</label>
							</li>';			    	
			        }
			    $msg['roles']=$roles;
                $msg['count_role']=$count_role;
				$msg['data']=$perm_role;
				$msg['msg']='admin_success'; 
				//$msg['role_id']=$user_role[0]['UserPermission']['permission_id'];
				echo json_encode($msg);die; 

			    }else{

                $msg['count_role']=$count_role;
				$msg['data']=$perm_role;
				$msg['u_type']=$login_detail['AppUser']['user_type'];
				$admin['Admin']=$login_detail;
				$admin['Role']=$user_role[0]['UserPermission']['permission_id'];
				$admin['vartical']=$user_role[0]['UserPermission']['business_line_id'];
				$admin['subvertical']=$user_role[0]['UserPermission']['subvertical_id'];
				$this->Session->write('admin', $admin);
				$msg['msg']='admin_success'; 
				echo json_encode($msg);die; 
			    }

			}
			else
			{
				$msg['msg']='not_match';
				echo json_encode($msg);die;
			}
		}
		else
		{
			$msg['msg']='not_match';
			echo json_encode($msg);die;
		}
	}
	function login_with_role(){
		$this->loadModel('AppUser');
		$this->loadModel('UserCredential');
		$this->loadModel('UserPermission');
		$data=$this->request->data;
		$pass=md5($data['pass']);
		
		$login_detail=$this->AppUser->find('first',array( 'recursive'=>'-1','field'=>array('AppUser.id','AppUser.user_type','AppUser.role_id'),'conditions'=>array('AppUser.user_email'=>$data['email'])));

		$user_var_sub_data=$this->UserPermission->find('first',array('recursive'=>'-1','field'=>array('UserPermission.business_line_id','UserPermission.subvertical_id'),'conditions'=>array('UserPermission.app_user_id'=>$login_detail['AppUser']['id'],'UserPermission.permission_id'=>$data['select_role_id'])));

		if($login_detail)
		{  
			$log_detail=$this->UserCredential->find('first',array( 'recursive'=>'-1','field'=>array('UserCredential.id'),'conditions'=>array('UserCredential.app_user_id'=>$login_detail['AppUser']['id'],'UserCredential.credential'=>$pass)));


			if(!empty($log_detail))
			{ 				
				$msg['u_type']=$login_detail['AppUser']['user_type'];
				$admin['Admin']=$login_detail;
				$admin['Role']=$data['select_role_id'];
				$admin['vartical']=$user_var_sub_data['UserPermission']['business_line_id'];
				$admin['subvertical']=$user_var_sub_data['UserPermission']['subvertical_id'];
				$this->Session->write('admin', $admin);
				$msg['msg']='admin_success'; 
				echo json_encode($msg);die; 
			}else{
				$msg['msg']='error'; 
				echo json_encode($msg);die; 
			}
		}
		

	}
	/////////////////////////   switch role function ////////////////////////////////
	function change_role() {
		$this->loadModel('AppUser');
		$this->loadModel('UserCredential');
		$this->loadModel('UserPermission');
		$this->loadModel('Permission');
		$this->loadModel('BusinessLine');
		$this->loadModel('Subvertical');

		$data = $this->request->data;
		$login_detail=$this->AppUser->find('first',array( 'recursive'=>'-1','field'=>array('AppUser.id','AppUser.user_type','AppUser.role_id'),'conditions'=>array('AppUser.user_email'=>$data['email'])));
		//pre($login_detail);
		$log_detail=$this->UserCredential->find('first',array( 'recursive'=>'-1','field'=>array('UserCredential.id'),'conditions'=>array('UserCredential.app_user_id'=>$login_detail['AppUser']['id'])));
		//pre($log_detail);die;
		$u_role=$this->UserPermission->find('list',array('recursive'=>'-1','field'=>array('UserPermission.permission_id'),'conditions'=>array('UserPermission.app_user_id'=>$log_detail['UserCredential']['app_user_id'])));
		$perm_role=$this->Permission->find('all',array('recursive'=>'-1','field'=>array('Permission.id','Permission.permission_desc'),'conditions'=>array('Permission.id'=>$u_role)));
		$count_role=$this->UserPermission->find('count',array('recursive'=>'-1','field'=>array('UserPermission.permission_id'),'conditions'=>array('UserPermission.app_user_id'=>$log_detail['UserCredential']['app_user_id'])));
		$user_role=$this->UserPermission->find('all',array('recursive'=>'-1','field'=>array('UserPermission.permission_id'),'conditions'=>array('UserPermission.app_user_id'=>$log_detail['UserCredential']['app_user_id'])));

		//pre($user_role); die;
			    if($count_role >= 2){
			    	foreach ($user_role as $key => $role) {
			    		$permission_data=$this->Permission->find('first',array('recursive'=>'-1','field'=>array('Permission.id','Permission.permission_desc'),'conditions'=>array('Permission.id'=>$role['UserPermission']['permission_id'])));
			    		$bisiness_data=$this->BusinessLine->find('first',array('recursive'=>'-1','field'=>array('BusinessLine.id','BusinessLine.bl_name'),'conditions'=>array('BusinessLine.id'=>$role['UserPermission']['business_line_id'])));
			    		$subvertical_data=$this->Subvertical->find('first',array('recursive'=>'-1','field'=>array('Subvertical.id','Subvertical.sv_name'),'conditions'=>array('Subvertical.id'=>$role['UserPermission']['subvertical_id'])));
			    		$roles.='<li>
								<label class="clicklable managerclick" for="managerrole">
								<input type="hidden" name="hidden_u_id" value="'.$login_detail['AppUser']['id'].'">
									<div class="customcheckbox">
										<label>
											<input type="radio" name="roleselect" id="'.$role['UserPermission']['permission_id'].'" value="'.$role['UserPermission']['permission_id'].'" class="addClickadd"> <b></b></label>
									</div>
									<div class="rolecategoery">
										<h3>'.$permission_data['Permission']['permission_desc'].'</h3>
									   <div class="roleca-use"><span class="vartical" id="'.$bisiness_data['BusinessLine']['id'].'">'.$bisiness_data['BusinessLine']['bl_name'].'</span><span class="subvertical" id="'.$subvertical_data['Subvertical']['id'].'">'.$subvertical_data['Subvertical']['sv_name'].'</span></div>
									</div>
								</label>
							</li>';			    	
			        }
			    $msg['roles']=$roles;
                $msg['count_role']=$count_role;
				$msg['data']=$perm_role;
				$msg['msg']='admin_success'; 
				//$msg['role_id']=$user_role[0]['UserPermission']['permission_id'];
				echo json_encode($msg);die; 

			    }else{

                $msg['count_role']=$count_role;
				$msg['data']=$perm_role;
				$msg['u_type']=$login_detail['AppUser']['user_type'];
				$admin['Admin']=$login_detail;
				$admin['Role']=$user_role[0]['UserPermission']['permission_id'];
				$admin['vartical']=$user_role[0]['UserPermission']['business_line_id'];
				$admin['subvertical']=$user_role[0]['UserPermission']['subvertical_id'];
				$this->Session->write('admin', $admin);
				$msg['msg']='admin_success'; 
				echo json_encode($msg);die; 
			    }
				
	}

	function switch_role()
	{
		$this->loadModel('AppUser');
		$this->loadModel('UserCredential');
		$this->loadModel('UserPermission');
		$data=$this->request->data;
		
		$login_detail=$this->AppUser->find('first',array( 'recursive'=>'-1','field'=>array('AppUser.id','AppUser.user_type','AppUser.role_id'),'conditions'=>array('AppUser.user_email'=>$data['email'])));

		$user_var_sub_data=$this->UserPermission->find('first',array('recursive'=>'-1','field'=>array('UserPermission.business_line_id','UserPermission.subvertical_id'),'conditions'=>array('UserPermission.app_user_id'=>$login_detail['AppUser']['id'],'UserPermission.permission_id'=>$data['select_role_id'])));
		$log_detail=$this->UserCredential->find('first',array( 'recursive'=>'-1','field'=>array('UserCredential.id'),'conditions'=>array('UserCredential.app_user_id'=>$login_detail['AppUser']['id'])));
			if(!empty($log_detail))
			{ 				
				$this->Session->delete($Admin['Role']);
				$this->Session->delete($Admin['vartical']);
				$this->Session->delete($Admin['subvertical']);
				$msg['u_type']=$login_detail['AppUser']['user_type'];
				$admin['Admin']=$login_detail;
				$admin['Role']=$data['select_role_id'];
				$admin['vartical']=$user_var_sub_data['UserPermission']['business_line_id'];
				$admin['subvertical']=$user_var_sub_data['UserPermission']['subvertical_id'];
				$this->Session->write('admin', $admin);
				$msg['msg']='admin_success'; 
				echo json_encode($msg);die; 
			}else{
				$msg['msg']='error'; 
				echo json_encode($msg);die; 
			}
		
	}

	////////////////////////   switch role function  ///////////////////////////////
	function logout()
	{ 
		$this->Session->destroy();
		$this->redirect(array('controller'=>'home','action'=>'login'));
	}
	function select_role()
	{
		$data=$this->data;
		$sesn=$this->Session->read('user');
		$sesn['role']=$data['role'];
		$this->Session->write('user', $sesn);
		echo 'success';die;
	}
	function cms()
	{
		$conditions= '';
		$filters= array();
		$this->set('title','Arise | CMS');		
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin'];
		//$userid=$sesn['Admin']['AppUser']['id'];
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
		$this->loadModel('Invoice');
		$this->loadModel('Entitie');
		$this->loadModel('AppUser');
		$this->loadModel('ArCategory');
		$this->loadModel('InvoiceStage');
		$this->loadModel('DunningStepMaster');
		$this->loadModel('MasterDataDetail');
		$data = $this->request->data;

		$short = array('Invoice.id'=>'DESC');
			
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Entitie.entitiy_name'=>$shortVal);
		}
		
		if(isset($data['dunningSteps']) && !empty($data['dunningSteps'])){			
			$filters = array_merge($filters,array('Invoice.dunning_attempt_no'=>$data['dunningSteps']));
			
		}

		if(isset($data['categoryWise']) && !empty($data['categoryWise'])){			
			$filters = array_merge($filters,array('Invoice.ar_cat_id'=>$data['categoryWise']));
			
		}

		if(isset($data['invoiceStages']) && !empty($data['invoiceStages'])){			
			$filters = array_merge($filters,array('Invoice.invoice_stage'=>$data['invoiceStages']));
			
		}
		if(isset($data['cmsStart']) && !empty($data['cmsStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['cmsStart']));
			$filters = array_merge($filters,array('Invoice.invoice_date >= '=>$begin_data));
			
		}

		if( isset($data['cmsEnd']) && !empty($data['cmsEnd']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['cmsEnd']));
		 	$filters = array_merge($filters,array('Invoice.invoice_date <= '=>$close_date));
		 	
		 }

		 if(isset($data['ageing']) && !empty($data['ageing'])){	
		 $start =date("Y-m-d");
			$end = date('Y-m-d', strtotime($start. ' - '. end($data['ageing']) .'day'));
			if($start > $end){				 
				$filters = array_merge($filters,array('Invoice.invoice_due_dt <= ' => $start,'Invoice.invoice_due_dt >= ' => $end));
			}

		}

		if(isset($data['exclude']) && !empty($data['exclude'])){			
			$filters = array_merge($filters,array('Invoice.dunning_status'=>$data['exclude']));
			//pre($filters);die;
			
		}

		if(isset($data['search_id']) && !empty($data['search_id'])){			
			$filters = array_merge($filters,array('Invoice.entity_id'=>$data['search_id']));
			
		}

		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Customer')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
		
        $total_ar = $this->Invoice->find('all',array('fields'=>array('sum(Invoice.invoice_amount)  AS total_ar'),'conditions'=>array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6)));
        $invoi_data = $this->Invoice->find('all',array('fields'=>array('Invoice.invoice_due_dt','Invoice.invoice_amount'),'conditions'=>array($filters)));       
	    $total_ninty_plus_due='';
	    $total_over_due='';
	    $total_invoice_amount='';
        foreach ($invoi_data as $key => $invoice_overdues){

				  	$due_date= date('y-m-d', strtotime($invoice_overdues['Invoice']['invoice_due_dt']));        	
				    $start =time();
					$end = strtotime($due_date);					
					$days_between = ceil(abs($end - $start) / 86400);
                    if($start > $end){
					if($days_between >= 90){					     
					     $total_ninty_plus_due += $invoice_overdues['Invoice']['invoice_amount'];
					}
					if($days_between >= 1){

						$total_over_due += $invoice_overdues['Invoice']['invoice_amount'];
					}
				}else{
					$total_invoice_amount += $invoice_overdues['Invoice']['invoice_amount'];
				}					 
			}

		/*		
		//pre($total_ar); die();
       if(isset($data['search_id']) && !empty($data['search_id']) && empty($data['overdue_90']) && empty($data['overdue']) &&  empty($data['notdue'])){
       	
       	     $invoice_overdue = $this->Invoice->find('all', array('all','fields'=>array('Invoice.*'), 'conditions'=>array($filters,'Invoice.entity_id'=>$data['search_id'])));
			 $invoice_id = [];
			 $ninty_plus_due='';
			 //$invoice_amount='';
			 $total_amount='';
			 $over_due='';

			 foreach ($invoice_overdue as $key => $invoice_overdues){

				  	$due_date= date('y-m-d', strtotime($invoice_overdues['Invoice']['invoice_due_dt']));        	
				    $start =time();
					$end = strtotime($due_date);					
					$days_between = ceil(abs($end - $start) / 86400);
					$total_amount += $invoice_overdues['Invoice']['invoice_amount'];
                    if($start > $end){
					if($days_between >= 90){					     
					     $ninty_plus_due += $invoice_overdues['Invoice']['invoice_amount'];
					}
					if($days_between >= 1){

						$over_due += $invoice_overdues['Invoice']['invoice_amount'];
					}
				}else{
					$invoice_amount += $invoice_overdues['Invoice']['invoice_amount'];
				}					 
			}	
			
			$this->paginate = array('limit'=>20,'field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array('Invoice.entity_id'=>$data['search_id'],'Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6));				  

		}elseif(isset($data['overdue_90'])  && !empty($data['overdue_90']) && empty($data['search_id']) &&  empty($data['overdue']) &&  empty($data['notdue'])){

		    $invoice_overdue = $this->Invoice->find('all', array('all','fields'=>array('Invoice.*'), 'conditions'=>array($filters)));
			 $invoice_id = [];
			 $ninty_plus_due='';
			 //$invoice_amount='';
			 $total_amount='';
			 $over_due='';
			foreach ($invoice_overdue as $key => $invoice_overdues){
				 	$due_date= date('y-m-d', strtotime($invoice_overdues['Invoice']['invoice_due_dt']));
				    $start =time();
					$end = strtotime($due_date);					
					$days_between = ceil(abs($end - $start) / 86400);
				    
                    if($start > $end){
					if($days_between >= 90){

					     array_push($invoice_id, $invoice_overdues['Invoice']['id']);
					     $ninty_plus_due += $invoice_overdues['Invoice']['invoice_amount'];
					     $over_due += $invoice_overdues['Invoice']['invoice_amount'];
					     $total_amount += $invoice_overdues['Invoice']['invoice_amount'];
					}					
				}					 
			}	
				$this->paginate = array('limit'=>20,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6,'Invoice.id'=>$invoice_id));			 					 	
		}elseif(isset($data['search_id'])  && !empty($data['search_id']) && !empty($data['overdue_90']) && empty($data['overdue']) &&  empty($data['notdue'])){
		    $invoice_overdue = $this->Invoice->find('all', array('all','fields'=>array('Invoice.*'), 'conditions'=>array($filters,'Invoice.entity_id'=>$data['search_id'])));
			$invoice_id = [];
			$ninty_plus_due='';
			//$invoice_amount='';
			$total_amount='';
			$over_due='';
			foreach ($invoice_overdue as $key => $invoice_overdues){
				 	$due_date= date('y-m-d', strtotime($invoice_overdues['Invoice']['invoice_due_dt']));
				    $start =time();
					$end = strtotime($due_date);					
					$days_between = ceil(abs($end - $start) / 86400);
				    
                    if($start > $end){
					if($days_between >= 90){

					     array_push($invoice_id, $invoice_overdues['Invoice']['id']);
					     $ninty_plus_due += $invoice_overdues['Invoice']['invoice_amount'];
					     $over_due += $invoice_overdues['Invoice']['invoice_amount'];
					     $total_amount += $invoice_overdues['Invoice']['invoice_amount'];
					}					
				}					 
			}	
				$this->paginate = array('limit'=>20,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6,'Invoice.id'=>$invoice_id,'Invoice.entity_id'=>$data['search_id']));			 					 	
		}elseif(isset($data['notdue'])  && !empty($data['notdue']) && empty($data['overdue_90']) && empty($data['overdue']) &&  empty($data['search_id'])){

		     $invoice_notdue = $this->Invoice->find('all', array('all','fields'=>array('Invoice.*'), 'conditions'=>array($filters)));
			  $invoice_id = [];			  
			  $invoice_amount='';
			  $total_amount='';
			 

			foreach ($invoice_notdue as $key => $invoice_notdues){
				 	$due_date= date('y-m-d', strtotime($invoice_notdues['Invoice']['invoice_due_dt']));
				    $start =time();
					$end = strtotime($due_date);										
                    if($start > $end){					
					}else{

						array_push($invoice_id, $invoice_notdues['Invoice']['id']);
						$invoice_amount += $invoice_notdues['Invoice']['invoice_amount'];
						$total_amount += $invoice_notdues['Invoice']['invoice_amount'];

					}					 
			}	
				$this->paginate = array('limit'=>20,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6,'Invoice.id'=>$invoice_id));			 					 	
		}elseif(isset($data['notdue'])  && !empty($data['notdue']) && empty($data['overdue_90']) && empty($data['overdue']) &&  !empty($data['search_id'])){

		     $invoice_notdue = $this->Invoice->find('all', array('all','fields'=>array('Invoice.*'), 'conditions'=>array($filters,'Invoice.entity_id'=>$data['search_id'])));
			  $invoice_id = [];
			  $total_amount='';
			  $invoice_amount='';

			foreach ($invoice_notdue as $key => $invoice_notdues){
				 	$due_date= date('y-m-d', strtotime($invoice_notdues['Invoice']['invoice_due_dt']));
				    $start =time();
					$end = strtotime($due_date);										
                    if($start > $end){					
					}else{

						array_push($invoice_id, $invoice_notdues['Invoice']['id']);
						$invoice_amount += $invoice_notdues['Invoice']['invoice_amount'];
						$total_amount += $invoice_notdues['Invoice']['invoice_amount'];

					}					 
			}	
				$this->paginate = array('limit'=>20,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6,'Invoice.id'=>$invoice_id,'Invoice.entity_id'=>$data['search_id']));			 					 	
		}elseif(isset($data['overdue']) && empty($data['search_id']) && empty($data['overdue_90']) && !empty($data['overdue']) &&  empty($data['notdue'])){

		    $invoice_overdue = $this->Invoice->find('all', array('all','fields'=>array('Invoice.*'), 'conditions'=>array($filters)));
		    
			 $invoice_id = [];
			 $ninty_plus_due='';
			 $total_amount='';
			 $over_due='';
			foreach ($invoice_overdue as $key => $invoice_overdues){
				 	$due_date= date('y-m-d', strtotime($invoice_overdues['Invoice']['invoice_due_dt']));
				    $start =time();
					$end = strtotime($due_date);					
					$days_between = ceil(abs($end - $start) / 86400);
                    if($start > $end){
                    	if($days_between >= 90){
					     
					     $ninty_plus_due += $invoice_overdues['Invoice']['invoice_amount'];
					}
					if($days_between >= 1){
						
						array_push($invoice_id, $invoice_overdues['Invoice']['id']);
						$over_due += $invoice_overdues['Invoice']['invoice_amount'];
						$total_amount += $invoice_overdues['Invoice']['invoice_amount'];
					}	
                }									 
			}	
				$this->paginate = array('limit'=>20,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6,'Invoice.id'=>$invoice_id));			 					 	
		}elseif(isset($data['overdue']) && !empty($data['search_id']) && empty($data['overdue_90']) && !empty($data['overdue']) &&  empty($data['notdue'])){

		    $invoice_overdue = $this->Invoice->find('all', array('all','fields'=>array('Invoice.*'), 'conditions'=>array($filters,'Invoice.entity_id'=>$data['search_id'])));
			 $invoice_id = [];
			 $days_betweens = '';
			 $ninty_plus_due='';
			 $total_amount='';
			 $over_due='';
			foreach ($invoice_overdue as $key => $invoice_overdues){
				 	$due_date= date('y-m-d', strtotime($invoice_overdues['Invoice']['invoice_due_dt']));
				    $start =time();
					$end = strtotime($due_date);					
					$days_between = ceil(abs($end - $start) / 86400);
                    if($start > $end){
                    	if($days_between >= 90){
					     
					     $ninty_plus_due += $invoice_overdues['Invoice']['invoice_amount'];
					}
					if($days_between >= 1){

						array_push($invoice_id, $invoice_overdues['Invoice']['id']);
						$over_due += $invoice_overdues['Invoice']['invoice_amount'];
						$total_amount += $invoice_overdues['Invoice']['invoice_amount'];
					}	
                    }
									 
			}	
				$this->paginate = array('limit'=>20,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6,'Invoice.id'=>$invoice_id,'Invoice.entity_id'=>$data['search_id']));			 					 	
		}else{

				$filters = array_merge($filters,array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6));					
				$this->paginate = array('limit'=>20,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array($filters));	

			
		}
		*/

		$filters = array_merge($filters,array('Invoice.dunning_status'=>NULL,'Invoice.ar_cat_id !='=>6));					
				$this->paginate = array('limit'=>20,'order'=>$short,'field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array($filters));

		$invoice_data = $this->paginate('Invoice');	
		$this->set('total_ar',$total_ar);
        $this->set('total_ninty_plus_due',$total_ninty_plus_due);
        $this->set('total_over_due',$total_over_due);
        $this->set('total_invoice_amount',$total_invoice_amount);		
		
		
		//pre($invoice_data); die;  
        $ar_category = $this->ArCategory->find('all',array('fields'=>array('ArCategory.*')));
		$user_role = $this->AppUser->find('all',array('joins' => array(array('table' => 'permissions',
			'alias' => 'Permission','type' => 'LEFT','conditions' => array('Permission.id = 
				AppUser.role_id'))),
		'fields'=>array('Permission.permission_desc'),'conditions'=>array('AppUser.id'=>$userid['AppUser']['id'])));		
		$role = $user_role[0]['Permission']['permission_desc'];
		$contacts_role = $this->MasterDataDetail->find('all',array('joins'=>array(array('table'=>'contacts','alias'=>'Contact',
		 	'type'=>'LEFT','conditions'=>array('Contact.contact_role = MasterDataDetail.id'))),
		 'fields'=>array('Contact.*'), 'conditions'=>array('MasterDataDetail.master_data_desc'=>array('Finance','Sales'))));


		///*************  Start Filters here By Manohar  *****************************//
		
		
		$allCustomers = $this->Entitie->find('all',array('recursive'=>-1, 'group'=>'Entitie.entitiy_name','fields'=>array('Entitie.credit_period','Entitie.entitiy_name','Entitie.id'),'conditions'=>array('Entitie.status'=>'Active'),'order'=>'Entitie.entitiy_name asc'));
		$allCatID = $this->ArCategory->find('all',array('fields'=>array('ArCategory.id','ArCategory.ar_cat'),'conditions'=>array('ArCategory.is_active'=>1)));

		$invoice_stages = $this->InvoiceStage->find('all',array('group'=>'InvoiceStage.stage_desc','fields'=>array('InvoiceStage.stage_desc','InvoiceStage.id'),'conditions'=>array('InvoiceStage.is_active'=>1)));

		
		
		//	$allCustomers = $this->Entitie->find('list',array('group'=>'Entitie.credit_period','fields'=>array('Entitie.id','Entitie.credit_period'),'conditions'=>array('Entitie.status'=>'Active')));


		//	$allTask = $this->ProjectTask->find('list',array('group'=>'ProjectTask.project_id','fields'=>array('ProjectTask.id','ProjectTask.project_id'),'conditions'=>array('ProjectTask.id'=>$allPrice)));

		$this->set(compact('allCustomers','allCatID','invoice_stages'));

		///*************  End Filters here By Manohar  *****************************//


		$this->set('role',$role);
		$this->set('ar_category',$ar_category);
		$this->set('contacts_role',$contacts_role);
		$this->set('excess_permission',$excess_permission);

		$pag_det=$this->params['paging'];
		$parem=$pag_det['Invoice'];
		$total_page=$parem['pageCount'];
		$current_page=$parem['page'];
		$records=$parem['current'];
		$total_records=$parem['count'];
		
		if($current_page==1)
		{
			$pgdetl='Showing 1 to '.$records.' of '.$total_records.' contest';
		}
		else if($total_records==0)
		{
			$pgdetl='';
		}
		else 
		{
			$start=(($current_page-1)*9)+1;
			$pgdetl='Showing '.$start.' to '.(($start+$records)-1).' of '.$total_records.' contest';
		}
		$this->set('pageinfo',$pgdetl);		

		if($this->RequestHandler->isAjax())
           {

                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);
                //$html['abc']=$abc;
                                          
                if(!empty($invoice_data))
                {
                        $view->set('invoice_data',$invoice_data);
                        $html['html'] = $view->render("cms");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                        $html['ninty_plus_due'] = money_format('%!i', $ninty_plus_due);
                        $html['over_due'] = money_format('%!i', $over_due);
                        $html['not_due'] = money_format('%!i', $invoice_amount);
                        $html['total_amount'] = money_format('%!i', $total_amount);
                }
                else
                {
                        $html['message'] ='error';
                }

                echo json_encode($html);die;
            }else{

            	$this->set('invoice_data',$invoice_data);
            }
                
	}

	function dunning_steps(){
		$this->loadModel('DunningStepMaster');
		$data = $this->request->data;
		if($data !=''){
			$select_dunning_steps = $this->DunningStepMaster->find("all",array('recursive'=>'-1','fields'=>array('DunningStepMaster.dunning_step_no','DunningStepMaster.id'),'conditions'=>array('DunningStepMaster.is_active'=>1,'DunningStepMaster.credit_period'=>$data['cust_id'])));
		}
		//pre($select_dunning_steps);die;

		foreach($select_dunning_steps as $k=>$dunning_steps)
		 {
		 	// $li.='<li ><a href="#" class="select_entite" id="'.$entity_name['Entitie']['id'].'">'.$entity_name['Entitie']['entitiy_name'].'</a></li>';
		 	 $li .= '<li class="filterlist"><div class="customcheckbox"><label><input type="checkbox" name="filterDunningSteps" value="'.$dunning_steps['DunningStepMaster']['dunning_step_no'].'" class="select_filter_items select_filter_dunningSteps" > <b></b><span class="filter-show">'.$dunning_steps['DunningStepMaster']['dunning_step_no'].' </span> </label></div></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;




	}
	public function serch_entity_by_invoice(){

		$this->loadModel('Invoice');
		$data = $this->request->data;
         $li = '';
        if($data !=''){

         	$search_entity_name = $this->Invoice->find("all",array('recursive'=>'-1',
         		'fields'=>array('Entitie.entitiy_name','Entitie.id'),
         		'conditions'=>array('Invoice.is_active'=>1,
         				'OR' => array('Entitie.entitiy_name LIKE'=>'%'.$data['search_key'].'%',
         				'Invoice.invoice_number LIKE' => '%' . $data['search_key'] . '%'))));

         }
		
		//pre($search_entity_name) ;die();
		foreach($search_entity_name as $k=>$entity_name)
		 {
		 	 $li.='<li ><a href="#" class="select_entite" id="'.$entity_name['Entitie']['id'].'">'.$entity_name['Entitie']['entitiy_name'].'</a></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	}
	
	public function serch_entity(){

		$this->loadModel('Entitie');
		$data = $this->request->data;
         $li = '';
        if($data !=''){

         	$search_entity_name = $this->Entitie->find("all",array('recursive'=>'-1','fields'=>array('Entitie.entitiy_name','Entitie.id'),'conditions'=>array('Entitie.entitiy_name LIKE'=>'%'.$data['search_key'].'%')));

         }
		
		//pre($search_entity_name) ;die();
		foreach($search_entity_name as $k=>$entity_name)
		 {
		 	 $li.='<li ><a href="#" class="select_entite" id="'.$entity_name['Entitie']['id'].'">'.$entity_name['Entitie']['entitiy_name'].'</a></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	}
	public function serch_entity_pnding(){

		$this->loadModel('Entitie');
		$data = $this->request->data;
         $li = '';
        if($data !=''){

         	$search_entity_name = $this->Entitie->find("all",
         		array('recursive'=>'-1',
         			'fields'=>array('Entitie.entitiy_name','Entitie.id'),
         			'conditions'=>array('Entitie.status'=>'Inactive',
         				'OR' => array('Entitie.entitiy_name LIKE'=>'%'.$data['search_key'].'%',
         				'Entitie.entity_pan LIKE' => '%' . $data['search_key'] . '%',
						'Entitie.entity_gst LIKE' => '%' . $data['search_key'] . '%',
						'Entitie.entity_id LIKE' => '%' . $data['search_key'] . '%')
         		)));
         }
		
		//pre($search_entity_name) ;die();
		foreach($search_entity_name as $k=>$entity_name)
		 {
		 	 $li.='<li ><a href="#" class="select_entite" id="'.$entity_name['Entitie']['id'].'">'.$entity_name['Entitie']['entitiy_name'].'</a></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	}
	public function serch_entity_active(){

		$this->loadModel('Entitie');
		$data = $this->request->data;
         $li = '';
        if($data !=''){

         	$search_entity_name = $this->Entitie->find("all",
         		array('recursive'=>'-1',
         			'fields'=>array('Entitie.entitiy_name','Entitie.id'),
         			'conditions'=>array('Entitie.status'=>'Active',
         				'OR' => array('Entitie.entitiy_name LIKE' => '%' . $data['search_key'] . '%',
									'Entitie.entity_pan LIKE' => '%' . $data['search_key'] . '%',
									'Entitie.entity_gst LIKE' => '%' . $data['search_key'] . '%',
									'Entitie.entity_id LIKE' => '%' . $data['search_key'] . '%')
         				
         			)));
         }
		
		//pre($search_entity_name) ;die();
		foreach($search_entity_name as $k=>$entity_name)
		 {
		 	 $li.='<li ><a href="#" class="select_entite" id="'.$entity_name['Entitie']['id'].'">'.$entity_name['Entitie']['entitiy_name'].'</a></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	}

	public function cms_excluded(){

        $conditions= array();
		$this->set('title','Arise | CMS');		
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin'];
		$this->loadModel('Invoice');
		$this->loadModel('Entitie');
		$this->loadModel('ArCategory');
		$this->loadModel('InvoiceStage');
		$this->loadModel('AppUser');

		$data = $this->request->data;

		$short = array('Invoice.id'=>'DESC');
			
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Entitie.entitiy_name'=>$shortVal);
		}
		

		if(isset($data['dunningSteps']) && !empty($data['dunningSteps'])){			
			$conditions = array_merge($conditions,array('Invoice.dunning_attempt_no'=>$data['dunningSteps']));
			
		}

		if(isset($data['categoryWise']) && !empty($data['categoryWise'])){			
			$conditions = array_merge($conditions,array('Invoice.ar_cat_id'=>$data['categoryWise']));
			
		}

		if(isset($data['invoiceStages']) && !empty($data['invoiceStages'])){			
			$conditions = array_merge($conditions,array('Invoice.invoice_stage'=>$data['invoiceStages']));
			
		}
		if(isset($data['cmsStart']) && !empty($data['cmsStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['cmsStart']));
			$conditions = array_merge($conditions,array('Invoice.invoice_date >= '=>$begin_data));
			
		}

		if( isset($data['cmsEnd']) && !empty($data['cmsEnd']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['cmsEnd']));
		 	$conditions = array_merge($conditions,array('Invoice.invoice_date <= '=>$close_date));
		 	
		 }

		 if(isset($data['ageing']) && !empty($data['ageing'])){	
		 $start =date("Y-m-d");
			$end = date('Y-m-d', strtotime($start. ' - '. end($data['ageing']) .'day'));
			if($start > $end){				 
				$conditions = array_merge($conditions,array('Invoice.invoice_due_dt <= ' => $start,'Invoice.invoice_due_dt >= ' => $end));
			}

		}

		if(isset($data['exclude']) && !empty($data['exclude'])){			
			$conditions = array_merge($conditions,array('Invoice.dunning_status'=>$data['exclude']));
			//pre($conditions);die;
			
		}
		if(isset($data['search_id']) && !empty($data['search_id'])){			
			$conditions = array_merge($conditions,array('Invoice.entity_id'=>$data['search_id']));
			
		}

		$conditions = array_merge($conditions,array('OR' => array(array('Invoice.dun_pause_exclude_reason'=>'Approved','Invoice.dunning_status'=>'Excluded'),
            'Invoice.ar_cat_id'=>6,'Invoice.dunning_status'=>'Paused')));	

	/*	if(isset($data['search_key']) && !empty($data['search_key'])){
			
			$conditions = array_merge($conditions,array('OR'=>array('Entitie.entitiy_name LIKE'=>'%'.
				$data['search_key'].'%',array('Invoice.dunning_status'=>'Excluded',
				'Invoice.dun_pause_exclude_reason'=>'Approved','Invoice.ar_cat_id'=>6,'Invoice.dunning_status'=>'Paused'))));
		}else{

			//$conditions = array_merge($conditions,array(array('Invoice.dunning_status'=>
			//	'Excluded','Invoice.dun_pause_exclude_reason'=>'Approved')));
			
            $conditions = array_merge($conditions,array('OR' => array(array(
         'Invoice.dun_pause_exclude_reason'=>'Approved','Invoice.dunning_status'=>'Excluded'),
            'Invoice.ar_cat_id'=>6,'Invoice.dunning_status'=>'Paused')  
			 	));			

		}
	*/
		///*************  Start Filters here By Manohar  *****************************//
		
		
		$allCustomers = $this->Entitie->find('all',array('recursive'=>-1, 'group'=>'Entitie.entitiy_name','fields'=>array('Entitie.credit_period','Entitie.entitiy_name','Entitie.id'),'conditions'=>array('Entitie.status'=>'Active'),'order'=>'Entitie.entitiy_name asc'));
		$allCatID = $this->ArCategory->find('all',array('fields'=>array('ArCategory.id','ArCategory.ar_cat'),'conditions'=>array('ArCategory.is_active'=>1)));

		$invoice_stages = $this->InvoiceStage->find('all',array('group'=>'InvoiceStage.stage_desc','fields'=>array('InvoiceStage.stage_desc','InvoiceStage.id'),'conditions'=>array('InvoiceStage.is_active'=>1)));

		$this->set(compact('allCustomers','allCatID','invoice_stages'));

		///*************  End Filters here By Manohar  *****************************//


		 $this->paginate = array('limit'=>20,'order'=>$short,'field'=>array('Invoice.*','Entitie.entitiy_name'),'conditions'=>array($conditions)); 
		 $invoice_exclude_data = $this->paginate('Invoice');

		 //pre($invoice_exclude_data); die();
		 
		 $user_role = $this->AppUser->find('all',array('joins' => array(array('table' => 'permissions',
			'alias' => 'Permission','type' => 'LEFT','conditions' => array('Permission.id = 
				AppUser.role_id'))),
		'fields'=>array('Permission.permission_desc'),'conditions'=>array('AppUser.id'=>$userid['AppUser']['id'])));
		
		$role = $user_role[0]['Permission']['permission_desc'];
		//pre($role); die();
		$this->set('role',$role);

		if($this->RequestHandler->isAjax())
             {
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($invoice_exclude_data))
                {
                        $view->set('inv_exclude',$invoice_exclude_data);
                        $html['html'] = $view->render("cms_excluded");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                }
                else
                {
                        $html['message'] ='error';
                }

                echo json_encode($html);die;

                }
                $this->set('inv_exclude',$invoice_exclude_data);
		
	}
	function cms_requests()
	{
		$this->set('title','Arise | CMS');
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin'];
		$this->loadModel('Invoice');
		$this->loadModel('Entitie');
			$this->loadModel('AppUser');
		// $this->paginate = array('limit'=>15,'order'=>'Invoice.id asc','field'=>array('Invoice.*','Entitie.entitiy_name'),
		// 	'conditions'=>array('Invoice.dunning_status'=> 'Included','Invoice.dun_pause_exclude_reason '=> NULL));	   
		//  	$invoice_exclude_request = $this->paginate('Invoice');

		 	$this->paginate = array('limit'=>15,'order'=>'Invoice.id asc','field'=>
		 		array('Invoice.*','Entitie.entitiy_name'),
			'conditions'=>array('OR'=>array(array('Invoice.dun_pause_exclude_reason '=> NULL,
				'Invoice.dunning_status'=>'Excluded'),'Invoice.dunning_status'=>'Included')));	   
		 	$invoice_exclude_request = $this->paginate('Invoice');

		 	//pre($invoice_exclude_request); die();
		 	
		 $this->set('invoice_exclude_request',$invoice_exclude_request);

		 $user_role = $this->AppUser->find('all',array('joins' => array(array('table' => 'permissions',
			'alias' => 'Permission','type' => 'LEFT','conditions' => array('Permission.id = 
				AppUser.role_id'))),
		'fields'=>array('Permission.permission_desc'),'conditions'=>array('AppUser.id'=>$userid['AppUser']['id'])));
		
		$role = $user_role[0]['Permission']['permission_desc'];
		//pre($role); die();
		$this->set('role',$role);		
	}
	
	function forgot()
	{
		
	}
	function cms_select()
	{
		
	}
	function cms_action(){

		$this->loadModel('Invoice');
		$this->loadModel('InvoiceDunnings');
		$this->loadModel('InvoiceMovement');
		$data = $this->request->data;
		//pre($data); die;

		// $invoice_detail=$this->Invoice->find('first',array('field'=>array('Invoice.*',
		// 	'Entitie.entitiy_name'),'conditions'=>array('Invoice.id'=>$data['id'])));
		// pre($invoice_detail); die();

		$coming_stage  = $this->InvoiceDunnings->find('all',array('joins' => array(
		array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER',
			'conditions' => array('MasterDataDetails.id = InvoiceDunnings.dunning_mode'))),
		'fields'=>array('InvoiceDunnings.*','MasterDataDetails.master_data_desc'),'conditions'=>
		array('InvoiceDunnings.invoice_id'=>$data['id'],'InvoiceDunnings.step_exec_date'=>NULL,
			'InvoiceDunnings.due_overdue_flg'=>'0','InvoiceDunnings.is_skipped '=>NULL)));

		$spoc_data = $this->Invoice->find('all',array('joins'=>array(array('table'=>'contacts',
		 	'alias'=>'Contacts','type'=>'INNER','conditions'=>array('Contacts.entity_id = Invoice.entity_id')),
		array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER','conditions' => array('Contacts.contact_role = MasterDataDetails.id')),
	        ),
		 	'fields'=>array('Contacts.id','Contacts.contact_fname','Contacts.contact_email','Contacts.contact_role','Contacts.contact_phone','Contacts.primary','Contacts.contact_designation','MasterDataDetails.master_data_desc'),
		 	'conditions'=>array('Invoice.id'=>$data['id'])));
		//pre($spoc_data); die;

		$upcoming_invoice_stage = $this->InvoiceMovement->find('all',array('joins'=>
		 	array(array('table'=>'customer_invoice_stages','alias'=>'CustomerInvoiceStage',
		 		'type'=>'INNER','conditions'=>array('CustomerInvoiceStage.id=InvoiceMovement.customer_invoice_stage'))),
		 	'fields'=>array('InvoiceMovement.*','CustomerInvoiceStage.stage_desc'),
		 	'conditions'=>array('InvoiceMovement.invoice_id'=>$data['id'],'InvoiceMovement.actual_date'=>'',
		 		'InvoiceMovement.is_skipped'=>NULL,'InvoiceMovement.is_active'=>'1')));
		//pre($spoc_data); die;
		  $count = count($coming_stage);
		  $count_invoice_stag = count($upcoming_invoice_stage);
		  
		   foreach ($spoc_data as $key => $spoc_datail)
		 {
		 	 $spoc_datail.='<option value="'.$spoc_datail['Contacts']['id'].'">'.
		 	 $spoc_datail['Contacts']['contact_fname'].'('.$spoc_datail['Contacts']
		 	 	['contact_designation'].')'.'</option>';
		 }

		  foreach($upcoming_invoice_stage as $k=>$upcoming_invoice_stages)
		 {
		 	if($upcoming_invoice_stages['CustomerInvoiceStage']['stage_desc']!='Invoice Raised'){ 
		 	 $invoice_stages.='<option value="'.$upcoming_invoice_stages['InvoiceMovement']['customer_invoice_stage'].'">'.$upcoming_invoice_stages['CustomerInvoiceStage']['stage_desc'].'</option>';
		 }}

		foreach($coming_stage as $k=>$coming_stages)
		 {

		 	 $option.='<option value="'.$coming_stages['InvoiceDunnings']['dunning_step_no'].'">'.$coming_stages['InvoiceDunnings']['dunning_step_no'].'</option>';
		 }
		 $msg['option']  =	$option;
		 $msg['invoice_stages']   =	$invoice_stages;
		 $msg['count']   =	$count;
		 $msg['count_invoice_stag']   =	$count_invoice_stag;
		 $msg['spoc_datail']   =	$spoc_datail;
		 $msg['coming_stage'] = $coming_stage[0]['InvoiceDunnings']['dunning_step_no'];
		  echo json_encode($msg); die;
	}
	function cms_userdetails($id)
	{
		$ses=$this->Session->read('admin');
		$invoiceid=base64_decode($id);
		$this->loadModel('Invoice');
		$this->loadModel('Entitie');
		$this->loadModel('InvoiceDunnings');
		$this->loadModel('InvoiceMovements');
		$this->loadModel('InvoiceDunningComm');
		$this->loadModel('ArCategory');
		$this->loadModel('DocumentMaster');
		$this->loadModel('Contact');
		$this->loadModel('MasterDataDetail');
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Edit Dunning')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
		
		$invoice_detail=$this->Invoice->find('first',array('field'=>array('Invoice.*',
			'Entitie.entitiy_name'),'conditions'=>array('Invoice.id'=>$invoiceid)));
		//pre($invoice_detail); die;		

		$invoice_ar_category=$this->ArCategory->find('first',array('field'=>
			array('ArCategory.ar_cat'),'conditions'=>array('ArCategory.id'=>
				$invoice_detail['Invoice']['ar_cat_id'])));
		//pre($invoice_ar_category);die;
		$invoice_doc_type=$this->DocumentMaster->find('first',array('field'=>
			array('DocumentMaster.desc'),'conditions'=>array('DocumentMaster.id'=>$invoice_detail['Invoice']['document_type_id'])));
		
		$invoice_dunning_data = $this->InvoiceDunnings->find('all',array('field'=>
			array('InvoiceDunnings.id','InvoiceDunnings.dunning_step_id',
				'InvoiceDunnings.due_overdue_flg','InvoiceDunnings.dunning_type',
				'InvoiceDunnings.dunning_mode','InvoiceDunnings.dunning_intensity',
				'InvoiceDunnings.step_target_date','InvoiceDunnings.step_exec_date'),
			'conditions'=>array('InvoiceDunnings.invoice_id '=>$invoiceid,
				'InvoiceDunnings.due_overdue_flg'=>'0','InvoiceDunnings.step_exec_date'=>NULL,
				'InvoiceDunnings.is_skipped'=>NULL)));
		$invoice_dunning_data_for_step = $this->InvoiceDunnings->find('all',array('field'=>
			array('InvoiceDunnings.id','InvoiceDunnings.dunning_step_id',
				'InvoiceDunnings.due_overdue_flg','InvoiceDunnings.dunning_type',
				'InvoiceDunnings.dunning_mode','InvoiceDunnings.dunning_intensity',
				'InvoiceDunnings.step_target_date','InvoiceDunnings.step_exec_date'),
			'conditions'=>array('InvoiceDunnings.invoice_id '=>$invoiceid,
				'InvoiceDunnings.due_overdue_flg'=>'0','InvoiceDunnings.is_skipped'=>NULL)));
          //pre($invoice_dunning_data);die;

		$invoice_movement = $this->InvoiceMovements->find('all',array('joins' => array(
		array('table' => 'customer_invoice_stages','alias' => 'CustomerInvoiceStages',
			'type' => 'LEFT','conditions' => array('CustomerInvoiceStages.id = 
				InvoiceMovements.customer_invoice_stage'))),'fields'=>array('InvoiceMovements.*',
		'CustomerInvoiceStages.*'),'conditions'=>array('InvoiceMovements.invoice_id'=>$invoiceid)));
		//pre($invoice_movement); die();

		$company_address  = $this->Invoice->find('all',array('joins' => array(
		array('table' => 'contracts','alias' => 'Contracts','type' => 'INNER','conditions' => array('Contracts.cust_entity_id = Invoice.entity_id')),
		array('table' => 'company_addresses','alias' => 'CompanyAddresses','type' => 'INNER','conditions' => array('CompanyAddresses.id = Contracts.bill_from_address_id')),
		),
		'fields'=>array('CompanyAddresses.*'),'conditions'=>array('Invoice.id'=>$invoiceid)));

		$client_address  = $this->Invoice->find('all',array('joins' => array(
		array('table' => 'contracts','alias' => 'Contracts','type' => 'INNER','conditions' => array('Contracts.cust_entity_id = Invoice.entity_id')),
		array('table' => 'entity_addresses','alias' => 'EntityAddresses','type' => 'INNER','conditions' => array('EntityAddresses.id = Contracts.bill_to_address_id')),
		),
		'fields'=>array('EntityAddresses.*'),'conditions'=>array('Invoice.id'=>$invoiceid)));

		$ship_address  = $this->Invoice->find('all',array('joins' => array(
		array('table' => 'contracts','alias' => 'Contracts','type' => 'INNER','conditions' => array('Contracts.cust_entity_id = Invoice.entity_id')),
		array('table' => 'entity_addresses','alias' => 'EntityAddresses','type' => 'INNER','conditions' => array('EntityAddresses.id = Contracts.ship_to_address_id')),
		),
		'fields'=>array('EntityAddresses.*'),'conditions'=>array('Invoice.id'=>$invoiceid)));
		 $current = date('Y-m-d');
        
		 $upcoming_stage  = $this->InvoiceDunnings->find('all',array('joins' => array(
		array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER',
			'conditions' => array('MasterDataDetails.id = InvoiceDunnings.dunning_mode'))),
		'fields'=>array('InvoiceDunnings.*','MasterDataDetails.master_data_desc'),'conditions'=>
		array('InvoiceDunnings.invoice_id'=>$invoiceid,'InvoiceDunnings.step_exec_date'=>NULL,
			'InvoiceDunnings.due_overdue_flg'=>'0','InvoiceDunnings.is_skipped '=>NULL)));

		 $upcoming_invoice_stage = $this->InvoiceMovements->find('all',array('joins'=>
		 	array(array('table'=>'customer_invoice_stages','alias'=>'CustomerInvoiceStages',
		 		'type'=>'INNER','conditions'=>array('CustomerInvoiceStages.id=InvoiceMovements.customer_invoice_stage'))),
		 	'fields'=>array('InvoiceMovements.*','CustomerInvoiceStages.stage_desc'),
		 	'conditions'=>array('InvoiceMovements.invoice_id'=>$invoiceid,'InvoiceMovements.actual_date'=>'',
		 		'InvoiceMovements.is_skipped'=>NULL)));

		 $upcoming_stage_intensity  = $this->InvoiceDunnings->find('first',array('joins' => array(
		array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER','conditions' => array('MasterDataDetails.id = InvoiceDunnings.dunning_intensity'))),
		'fields'=>array('InvoiceDunnings.*','MasterDataDetails.master_data_desc'),'conditions'=>array('InvoiceDunnings.invoice_id'=>$invoiceid,'InvoiceDunnings.step_target_date >'=>'$current')));
		 
		// $upcoming_edit_dunning_step  = $this->InvoiceDunnings->find('all',array('joins' => array(
		// array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER','conditions' => array('MasterDataDetails.id = InvoiceDunnings.dunning_mode'))),
		// 'fields'=>array('InvoiceDunnings.*','MasterDataDetails.master_data_desc'),'conditions'=>array('InvoiceDunnings.invoice_id'=>$invoiceid,'InvoiceDunnings.step_target_date >'=>$current,'InvoiceDunnings.due_overdue_flg'=>'0')));

		 // $spoc_data = $this->Invoice->find('all',array('joins'=>array(array('table'=>'contacts','alias'=>'Contacts','type'=>'INNER','conditions'=>array('Contacts.entity_id = Invoice.entity_id'))),
		 // 	'fields'=>array('Contacts.contact_fname','Contacts.contact_email','Contacts.contact_role','Contacts.contact_phone','Contacts.contact_designation'),'conditions'=>array('Invoice.id'=>$invoiceid)));

		 $spoc_data = $this->Invoice->find('all',array('joins'=>array(array('table'=>'contacts',
		 	'alias'=>'Contacts','type'=>'INNER','conditions'=>array('Contacts.entity_id = Invoice.entity_id')),
		array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER','conditions' => array('Contacts.contact_role = MasterDataDetails.id')),
	        ),
		 	'fields'=>array('Contacts.id','Contacts.contact_fname','Contacts.contact_email','Contacts.contact_role','Contacts.contact_phone','Contacts.primary','Contacts.contact_designation','MasterDataDetails.master_data_desc'),
		 	'conditions'=>array('Invoice.id'=>$invoiceid)));
		 //pre($spoc_data); die();
		 		
		$dunning_credit  = $this->InvoiceDunnings->find('all',array('joins' => array(
		array('table' => 'dunning_step_masters','alias' => 'DunningStepMasters','type' => 'LEFT',
			'conditions' => array('DunningStepMasters.id = InvoiceDunnings.dunning_step_id'))),
		'fields'=>array('DunningStepMasters.credit_period'),'conditions'=>array('InvoiceDunnings.invoice_id'=>$invoiceid)));

		$cms_history = $this->InvoiceDunningComm->find('all',array('fields'=>array('InvoiceDunningComm.*'),
			'conditions'=>array('InvoiceDunningComm.invoice_id'=>$invoiceid,
				'InvoiceDunningComm.user_id'=>$ses['Admin']['AppUser']['id'])));

		$ar_category = $this->ArCategory->find('all',array('fields'=>array('ArCategory.*')));
		
		 // $MasterData_role = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.*'),
			// 'conditions'=>array('MasterDataDetail.master_data_desc'=>array('Finance','Sales'))));
		 // foreach ($MasterData_role as $key => $value) {

		 // $contacts_role = $this->Contact->find('all',array('fields'=>array('Contact.*'),
		 // 	'conditions'=>array('Contact.contact_role'=>)));
 			 	
		 // }
		//pre($cms_history); die();
		 $contacts_role = $this->MasterDataDetail->find('all',array('joins'=>array(array('table'=>'contacts','alias'=>'Contact',
		 	'type'=>'LEFT','conditions'=>array('Contact.contact_role = MasterDataDetail.id'))),
		 'fields'=>array('Contact.*'), 'conditions'=>array('MasterDataDetail.master_data_desc'=>array('Finance','Sales'))));
		 
        //pre($invoice_ar_category); die();
		 
		 //pre($upcoming_invoice_stage);die();invoice_ar_category
        $this->set('contacts_role',$contacts_role);
        $this->set('invoice_ar_category',$invoice_ar_category);		
        $this->set('invoice_doc_type',$invoice_doc_type);	
		$this->set('ar_category',$ar_category);		
		$this->set('invoice_detail',$invoice_detail);
		$this->set('dunning_data',$invoice_dunning_data);
		$this->set('invoice_movement',$invoice_movement);
		$this->set('company_address',$company_address);
		$this->set('client_address',$client_address);
		$this->set('dunning_peri',$dunning_credit);
		$this->set('upcoming_stage',$upcoming_stage);
	    $this->set('stage_intensity',$upcoming_stage_intensity);
		$this->set('spoc_data',$spoc_data);
		$this->set('cms_history',$cms_history);
		$this->set('upcom_inv_stage',$upcoming_invoice_stage);
		$this->set('invoice_dunning_data_for_step',$invoice_dunning_data_for_step);	
		$this->set('excess_permission',$excess_permission);	
		
	}
	function promise_pay()
    {
        $this->loadModel('PromiseToPay');
        $data = $this->request->data;
        $limits = sizeof($data['PromiseAmount']);
    //    pre($data);die;
        for($counter=0; $counter< $limits;$counter++)
        {
            $data['PromiseToPay']['entity_id'] = $data['entityId'];
            $data['PromiseToPay']['promise_date'] = date("Y-m-d", strtotime($data['PromiseDate'][$counter]));
            $data['PromiseToPay']['promise_amount'] = $data['PromiseAmount'][$counter];
            $data['PromiseToPay']['contact_person'] = $data['ContactPerson'];
            $data['PromiseToPay']['comment'] = $data['Comments'];
            $this->PromiseToPay->create();    
            $this->PromiseToPay->save($data);
        }
            $msg['msg']='Success';
            echo json_encode($msg);die;                
        
    }
	public function save_dunning()
	{  
		$ses=$this->Session->read('admin');
		$this->loadModel('InvoiceDunningComm');
		$this->loadModel('InvoiceDunnings');
		$this->loadModel('Invoices');
		$data = $this->request->data;
		$dunning_date = date('Y-m-d', strtotime($data['dunning_date']));
		$spoc_datas = explode('-', $data['spoc']);
		
		$duuning_save['InvoiceDunningComm']['user_id']            = $ses['Admin']['AppUser']['id'];  
		$duuning_save['InvoiceDunningComm']['invoice_id']         = $data['invoice_id'];
		$duuning_save['InvoiceDunningComm']['invoice_dunning_id'] = $data['dunning_step'];
		$duuning_save['InvoiceDunningComm']['communication_text'] = $data['communication_text'];
		$duuning_save['InvoiceDunningComm']['history_of'] = 'Invoice Dunnings';
		$duuning_save['InvoiceDunningComm']['comms_date']         = $dunning_date;
		if($data['calling_dunning_mode'] == 'true'){
			$calling_dunning_mode = '1';
		}else{
			$calling_dunning_mode = '0';
		}
		if($data['email_dunning_mode'] == 'true'){
			$email_dunning_mode = '1';
		}else{
			$email_dunning_mode = '0';
		}
		$duuning_save['InvoiceDunningComm']['is_telephone_conv']  = $calling_dunning_mode;
		$duuning_save['InvoiceDunningComm']['letter_sent']        = $email_dunning_mode;
		$duuning_save['InvoiceDunningComm']['dunning_status']     = $data['dunning_status'];
		$duuning_save['InvoiceDunningComm']['spoc_name']          = $spoc_datas[0];
		$duuning_save['InvoiceDunningComm']['spoc_email']         = $data['invoice_id'];
		$duuning_save['InvoiceDunningComm']['reason_id']          = $data['invoice_id'];
		if($data['email_sent_status'] == 'yes'){
			$email_sent_status = '1';
		}else{
			$email_sent_status = '0';
		}
		$duuning_save['InvoiceDunningComm']['email_sent']         = $email_sent_status;
		//$duuning_save['InvoiceDunningComm']['created_date']       = $data['current_date'];
		//print_r($duuning_save);die();
		  $this->InvoiceDunningComm->save($duuning_save);
//UPDATE Invoices table
		  $done_step_save['Invoices']['dunning_attempt_no'] = $data['dunning_step'];
		  $done_step_save['Invoices']['dunning_stage_date'] = $dunning_date;

		  $this->Invoices->id = $data['invoice_id'];
		  $this->Invoices->save($done_step_save);
//UPDATE InvoiceDunning table
		  
		  // $data_step = array('step_exec_date'=>$dunning_date); 
		  // //print_r($data_step); die;

		  // $this->InvoiceDunnings->updateAll($data_step, array('InvoiceDunnings.invoice_id'     => $data['invoice_id'],'InvoiceDunnings.dunning_step_no' => $data['dunning_step']));
		  if($data['dunning_status'] == 'Skipped'){
		  	$this->InvoiceDunnings->query("update invoice_dunnings set is_skipped = '1' where dunning_step_no='$data[dunning_step]' and invoice_id='$data[invoice_id]'" );

		  }elseif($data['dunning_status'] == 'Complete'){
		  	$this->InvoiceDunnings->query("update invoice_dunnings set step_exec_date ='$dunning_date' where dunning_step_no='$data[dunning_step]' and invoice_id='$data[invoice_id]'" );

		  }elseif ($data['dunning_status'] == '') {
		  	
		  }
		  // $this->InvoiceDunnings->query("update invoice_dunnings set step_exec_date ='$dunning_date' where dunning_step_no='$data[dunning_step]' and invoice_id='$data[invoice_id]'" );
		  
		 echo 'success';die;
	}
	public function find_spoc_contact(){
         
        $this->loadModel('Contact');
		$data = $this->request->data;
		$spoc_contact = explode('-', $data['spoc_value']);
		$spoc_cont = $this->Contact->find('all',array('fields'=>array('Contact.contact_phone'),
			'conditions'=>array('Contact.id'=>$spoc_contact[1])));

		// foreach($spoc_cont as $k=>$spoc_con)
		//  {
		//  	 $option.='<option value="'.$k.'">'.$spoc_con.'</option>';
		//  }
		//  $msg['option']=	$option;
		  echo json_encode($spoc_cont); die;
		
		//pre($spoc_cont); die();

	}
	public function edit_take_note(){

		$ses=$this->Session->read('admin');
		$this->loadModel('InvoiceDunningComm');
        $date = date('Y-m-d');
		$data = $this->request->data;
	    //pre($ses['Admin']['AppUser']['id']); die;
		$dunning_note_save['InvoiceDunningComm']['user_id']    = $ses['Admin']['AppUser']['id'];
		$dunning_note_save['InvoiceDunningComm']['invoice_id'] = $data['invoice_id'];
		$dunning_note_save['InvoiceDunningComm']['communication_text'] = $data['communication_text'];
		$dunning_note_save['InvoiceDunningComm']['history_of'] = 'Notes';
		$dunning_note_save['InvoiceDunningComm']['comms_date'] = $date;
		$dunning_note_save['InvoiceDunningComm']['created_date'] = $date;
		$this->InvoiceDunningComm->save($dunning_note_save);
		 echo 'success';die;		   
	}

public function edit_dunning_step(){

	$this->loadModel('InvoiceDunning');
	$this->loadModel('InvoiceDunningComm');
	$this->loadModel('MasterDataDetail');
	$this->loadModel('Contacts');
	$this->loadModel('Invoice');
	$data  = $this->request->data;
	$dunning_done_steps = $data['dunning_done_step'];
	$count_step = $data['count_step'];
	$invoices_id = $data['invoice_id'];
	$upcoming_stage = $data['upcoming_stage'];
	$skipped_steps = $dunning_done_steps-1;
	$date = date('Y-m-d');
	$invoice_length = count($data['invoice_id']);

    //pre($invoice_id); die;
    foreach ($invoices_id as $key => $invoice_id) {

    	for($i=$upcoming_stage; $i<=$skipped_steps; $i++){

		$dunning_steps_data = $this->InvoiceDunning->find('all',array('fields'=>array('InvoiceDunning.*'),
			'conditions'=>array('InvoiceDunning.dunning_step_no'=>$i,
				'InvoiceDunning.invoice_id'=>$invoice_id)));
		$dunning_modes = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.*'),
		'conditions'=>array('MasterDataDetail.id'=>$dunning_steps_data[0]['InvoiceDunning']['dunning_mode'])));

		$notes_history['InvoiceDunningComm']['invoice_id'] = $dunning_steps_data[0]['InvoiceDunning']['invoice_id'];
		$notes_history['InvoiceDunningComm']['invoice_dunning_id'] = $upcoming_stage;
		$notes_history['InvoiceDunningComm']['history_of'] = 'Invoice Dunnings';
		$notes_history['InvoiceDunningComm']['comms_date'] = $date;
		$notes_history['InvoiceDunningComm']['dunning_status'] = 'Skipped';
		if($dunning_modes[0]['MasterDataDetail']['master_data_desc'] == 'Call'){
			$calling_dunning_mode = '1';
		}else{
			$calling_dunning_mode = '0';
		}
		if($dunning_modes[0]['MasterDataDetail']['master_data_desc'] == 'Email'){
			$email_dunning_mode = '1';
		}else{
			$email_dunning_mode = '0';
		}
			$notes_history['InvoiceDunningComms']['is_telephone_conv'] = $calling_dunning_mode;
			$notes_history['InvoiceDunningComms']['letter_sent']       = $email_dunning_mode;
			$notes_history['InvoiceDunningComms']['created_date']      = $date;
			$this->InvoiceDunningComm->save($notes_history);
		}
		 
	    $dunning_step_date = $this->InvoiceDunning->find('first',array('fields'=>
	    	array('InvoiceDunning.step_target_date'),'conditions'=>
	    	array('InvoiceDunning.dunning_step_no'=>$dunning_done_steps)));	

	    $id = $this->InvoiceDunning->find('all',array('fields'=>array('InvoiceDunning.id',
			'InvoiceDunning.step_target_date'),'conditions'=>array('InvoiceDunning.invoice_id'=>
			$invoice_id,'InvoiceDunning.dunning_step_no'=>$dunning_done_steps)));
	
	    $step_id   = $id[0]['InvoiceDunning']['id'];
	    $step_date = $id[0]['InvoiceDunning']['step_target_date'];
		$dunning_step_dates = date('Y-m-d', strtotime($step_date));

		$update['InvoiceDunning']['step_target_date'] = date('Y-m-d');
		//$update['InvoiceDunning']['step_exec_date']   = date('Y-m-d');
		$this->InvoiceDunning->id = $step_id;
		$this->InvoiceDunning->save($update);

		if($step_id>0){
		    $count = $this->InvoiceDunning->find('count',array('conditions'=>
		    	array('InvoiceDunning.id <'=>$step_id,'InvoiceDunning.invoice_id' =>$invoice_id)));
		    for($i=1;$i<=$count;$i++){
		    	$prevId = $step_id-$i;

			$this->InvoiceDunning->updateAll(array('is_skipped'=>1), array('InvoiceDunning.id'=>
				$prevId,'InvoiceDunning.invoice_id' => $invoice_id,'InvoiceDunning.step_exec_date'=>NULL));
		    }			    	
		}
            $leave_step  = $count_step - $dunning_done_steps;
            $left_step   = $step_id + $leave_step;
            $counts_step = $step_id + 1;
           
            for($i=$counts_step; $i<=$left_step; $i++){

          	    $next_step_date  = $this->InvoiceDunning->find('all',array('fields'=>
          	    	array('InvoiceDunning.step_target_date'),'conditions'=>
          	    	array('InvoiceDunning.id'=>$i)));

          	    $next_step_dates = date('Y-m-d', strtotime($next_step_date[0]['InvoiceDunning']
          	    	['step_target_date']));

          	    $diff        = strtotime($next_step_dates) - strtotime($dunning_step_dates);
                $days        = floor($diff / (60*60*24)).' days';
                $date        = date('Y-m-d');  
                $update_date = date('Y-m-d', strtotime($date. ' +' .$days));

                $updates['InvoiceDunning']['step_target_date'] = $update_date;
		        $this->InvoiceDunning->id = $i;
		        $this->InvoiceDunning->save($updates);

            }
    	
    }   	
            echo "success"; die;                
	}
	// public function edit_dunning_step(){

	// $this->loadModel('InvoiceDunning');
	// $this->loadModel('InvoiceDunningComm');
	// $this->loadModel('MasterDataDetail');
	// $this->loadModel('Contacts');
	// $this->loadModel('Invoice');
	// $data  = $this->request->data;
	// $dunning_done_steps = $data['dunning_done_step'];
	// $count_step = $data['count_step'];
	// $invoice_id = $data['invoice_id'];
	// $upcoming_stage = $data['upcoming_stage'];
	// $skipped_steps = $dunning_done_steps-1;
	// $date = date('Y-m-d');
	// $invoice_length = count($data['invoice_id']);    
    
	// for($i=$upcoming_stage; $i<=$skipped_steps; $i++){

	// 	$dunning_steps_data = $this->InvoiceDunning->find('all',array('fields'=>array('InvoiceDunning.*'),
	// 		'conditions'=>array('InvoiceDunning.dunning_step_no'=>$i,
	// 			'InvoiceDunning.invoice_id'=>$invoice_id)));
	// 	$dunning_modes = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.*'),
	// 	'conditions'=>array('MasterDataDetail.id'=>$dunning_steps_data[0]['InvoiceDunning']['dunning_mode'])));

	// 	$notes_history['InvoiceDunningComm']['invoice_id'] = $dunning_steps_data[0]['InvoiceDunning']['invoice_id'];
	// 	$notes_history['InvoiceDunningComm']['invoice_dunning_id'] = $upcoming_stage;
	// 	$notes_history['InvoiceDunningComm']['history_of'] = 'Invoice Dunnings';
	// 	$notes_history['InvoiceDunningComm']['comms_date'] = $date;
	// 	$notes_history['InvoiceDunningComm']['dunning_status'] = 'Skipped';
	// 	if($dunning_modes[0]['MasterDataDetail']['master_data_desc'] == 'Call'){
	// 		$calling_dunning_mode = '1';
	// 	}else{
	// 		$calling_dunning_mode = '0';
	// 	}
	// 	if($dunning_modes[0]['MasterDataDetail']['master_data_desc'] == 'Email'){
	// 		$email_dunning_mode = '1';
	// 	}else{
	// 		$email_dunning_mode = '0';
	// 	}
	// 		$notes_history['InvoiceDunningComms']['is_telephone_conv'] = $calling_dunning_mode;
	// 		$notes_history['InvoiceDunningComms']['letter_sent']       = $email_dunning_mode;
	// 		$notes_history['InvoiceDunningComms']['created_date']      = $date;
	// 		$this->InvoiceDunningComm->save($notes_history);
	// 	}
		 
	//     $dunning_step_date = $this->InvoiceDunning->find('first',array('fields'=>
	//     	array('InvoiceDunning.step_target_date'),'conditions'=>
	//     	array('InvoiceDunning.dunning_step_no'=>$dunning_done_steps)));	

	//     $id = $this->InvoiceDunning->find('all',array('fields'=>array('InvoiceDunning.id',
	// 		'InvoiceDunning.step_target_date'),'conditions'=>array('InvoiceDunning.invoice_id'=>
	// 		$invoice_id,'InvoiceDunning.dunning_step_no'=>$dunning_done_steps)));
	
	//     $step_id   = $id[0]['InvoiceDunning']['id'];
	//     $step_date = $id[0]['InvoiceDunning']['step_target_date'];
	// 	$dunning_step_dates = date('Y-m-d', strtotime($step_date));

	// 	$update['InvoiceDunning']['step_target_date'] = date('Y-m-d');
	// 	//$update['InvoiceDunning']['step_exec_date']   = date('Y-m-d');
	// 	$this->InvoiceDunning->id = $step_id;
	// 	$this->InvoiceDunning->save($update);

	// 	if($step_id>0){
	// 	    $count = $this->InvoiceDunning->find('count',array('conditions'=>
	// 	    	array('InvoiceDunning.id <'=>$step_id,'InvoiceDunning.invoice_id' =>$invoice_id)));
	// 	    for($i=1;$i<=$count;$i++){
	// 	    	$prevId = $step_id-$i;

	// 	// $spoc_data = $this->Invoice->find('all',array('joins'=>array(array('table'=>'contacts','alias'=>'Contacts',
	// 	// 	'type'=>'INNER','conditions'=>array('Contacts.entity_id = Invoice.entity_id')),
	// 	//        array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER',
	// 	//        	'conditions' => array('Contacts.contact_role = MasterDataDetails.id')),),
	// 	//  	'fields'=>array('Invoice.*','Contacts.contact_fname','Contacts.contact_email','Contacts.contact_role','Contacts.contact_phone','Contacts.primary','Contacts.contact_designation','MasterDataDetails.master_data_desc'),
	// 	//  	'conditions'=>array('Invoice.id'=>$invoice_id)));
	// 	// pre($spoc_data);

	// 	// pre($spoc_data[$i]['Invoice']['id']);
		
	// 	    	// $updated['InvoiceDunningComm']['invoice_id']   = 'skipped';
	// 		    // $this->InvoiceDunning->id = $prevId;
	// 		    // $this->InvoiceDunning->save($updated);

	// 		$this->InvoiceDunning->updateAll(array('is_skipped'=>1), array('InvoiceDunning.id'=>
	// 			$prevId,'InvoiceDunning.invoice_id' => $invoice_id,'InvoiceDunning.step_exec_date'=>NULL));
	// 	    }			    	
	// 	}
 //            $leave_step  = $count_step - $dunning_done_steps;
 //            $left_step   = $step_id + $leave_step;
 //            $counts_step = $step_id + 1;
           
 //            for($i=$counts_step; $i<=$left_step; $i++){

 //          	    $next_step_date  = $this->InvoiceDunning->find('all',array('fields'=>
 //          	    	array('InvoiceDunning.step_target_date'),'conditions'=>
 //          	    	array('InvoiceDunning.id'=>$i)));

 //          	    $next_step_dates = date('Y-m-d', strtotime($next_step_date[0]['InvoiceDunning']
 //          	    	['step_target_date']));

 //          	    $diff        = strtotime($next_step_dates) - strtotime($dunning_step_dates);
 //                $days        = floor($diff / (60*60*24)).' days';
 //                $date        = date('Y-m-d');  
 //                $update_date = date('Y-m-d', strtotime($date. ' +' .$days));

 //                $updates['InvoiceDunning']['step_target_date'] = $update_date;
	// 	        $this->InvoiceDunning->id = $i;
	// 	        $this->InvoiceDunning->save($updates);

 //            }
 //            echo "success"; die;
                  
	// }
	public function edit_invoice_status(){

		 $ses=$this->Session->read('admin'); 	
	     $this->loadModel('InvoiceMovement');
	     $this->loadModel('InvoiceDunningComm');
	 	 $edit_invoice_data = $this->request->data;

	 	 $invoice_stage     = $edit_invoice_data['invoice_stage'];
	 	 $invoice_edit_date = $edit_invoice_data['invoice_edit_date'];
	 	 $invoice_note      = $edit_invoice_data['invoice_note'];
	 	 $invoices_id       = $edit_invoice_data['invoice_id'];
	 	 $count_step_inv    = $edit_invoice_data['count_step_invoice'];	 	
	 	 
	 	foreach ($invoices_id as $key => $invoice_id){

	 	 $id = $this->InvoiceMovement->find('all',array('fields'=>array('InvoiceMovement.id'),'conditions'=>array('InvoiceMovement.invoice_id'=>$invoice_id,'InvoiceMovement.customer_invoice_stage'=>$invoice_stage)));

	 	 $invoice_movement_id = $id[0]['InvoiceMovement']['id'];

	     $update_invoice_movement['InvoiceMovement']['actual_date'] = date('Y-m-d', strtotime($invoice_edit_date));
	     $update_invoice_movement['InvoiceMovement']['notes']       = $invoice_note;
	     $this->InvoiceMovement->id = $invoice_movement_id;
	     $this->InvoiceMovement->save($update_invoice_movement);

	     $invoice_inh['InvoiceDunningComm']['user_id']=$ses['Admin']['AppUser']['id'];
		 $invoice_inh['InvoiceDunningComm']['invoice_id']=$invoice_id; 
		 $invoice_inh['InvoiceDunningComm']['history_of']='Invoice Status';
		 $invoice_inh['InvoiceDunningComm']['communication_text']= $invoice_note;
		 $invoice_inh['InvoiceDunningComm']['comms_date']=date('Y-m-d', strtotime($invoice_edit_date));
		 $this->InvoiceDunningComm->create();
		 $this->InvoiceDunningComm->save($invoice_inh);

	    if($invoice_movement_id>0){
		    $counts = $this->InvoiceMovement->find('count',array('conditions'=>array('InvoiceMovement.id <'=>$invoice_movement_id,'InvoiceMovement.invoice_id'=>$invoice_id)));
		   
		  for($i=1; $i<=$counts; $i++){
		    	$prevIdInvoices = $invoice_movement_id-$i;
		    	$this->InvoiceMovement->updateAll(array('is_skipped'=>1), array('InvoiceMovement.id'=>$prevIdInvoices,'InvoiceMovement.invoice_id' => $invoice_id,'InvoiceMovement.actual_date'=>''));	       
		    }	
		    	
		}	 	 	
   }
	 	 echo "success";die;
}

	// public function edit_invoice_status(){

	// 	 $ses=$this->Session->read('admin'); 	
	//      $this->loadModel('InvoiceMovement');
	//      $this->loadModel('InvoiceDunningComm');
	//  	 $edit_invoice_data = $this->request->data;
	//  	 pre($edit_invoice_data); die;
	//  	 $invoice_stage     = $edit_invoice_data['invoice_stage'];
	//  	 $invoice_edit_date = $edit_invoice_data['invoice_edit_date'];
	//  	 $invoice_note      = $edit_invoice_data['invoice_note'];
	//  	 $invoice_id        = $edit_invoice_data['invoice_id'];
	//  	 $count_step_inv    = $edit_invoice_data['count_step_invoice'];
	 	 
	//  	 $id = $this->InvoiceMovement->find('all',array('fields'=>array('InvoiceMovement.id'),'conditions'=>array('InvoiceMovement.invoice_id'=>$invoice_id,'InvoiceMovement.customer_invoice_stage'=>$invoice_stage)));


	//  	 $invoice_movement_id = $id[0]['InvoiceMovement']['id'];

	//      $update_invoice_movement['InvoiceMovement']['actual_date'] = date('Y-m-d');
	//      $update_invoice_movement['InvoiceMovement']['notes']       = $invoice_note;
	//      $this->InvoiceMovement->id = $invoice_movement_id;
	//      $this->InvoiceMovement->save($update_invoice_movement);

	//      $invoice_inh['InvoiceDunningComm']['user_id']=$ses['Admin']['AppUser']['id'];
	// 	 $invoice_inh['InvoiceDunningComm']['invoice_id']=$invoice_id; 
	// 	 $invoice_inh['InvoiceDunningComm']['history_of']='Invoice Status';
	// 	 $invoice_inh['InvoiceDunningComm']['communication_text']= $invoice_note;
	// 	 $invoice_inh['InvoiceDunningComm']['comms_date']=date('Y-m-d', strtotime($invoice_edit_date));
	// 	 $this->InvoiceDunningComm->create();
	// 	 $this->InvoiceDunningComm->save($invoice_inh);

	//     if($invoice_movement_id>0){
	// 	    $counts = $this->InvoiceMovement->find('count',array('conditions'=>array('InvoiceMovement.id <'=>$invoice_movement_id,'InvoiceMovement.invoice_id'=>$invoice_id)));
		   

	// 	  for($i=1; $i<=$counts; $i++){
	// 	    	$prevIdInvoices = $invoice_movement_id-$i;
	// 	    	$this->InvoiceMovement->updateAll(array('is_skipped'=>1), array('InvoiceMovement.id'=>$prevIdInvoices,'InvoiceMovement.invoice_id' => $invoice_id,'InvoiceMovement.actual_date'=>''));
		    				       
	// 	    }	
		    	
	// 	}

	//  	 echo "success";die;
	//  }

	 public function extract_history()
	 {
	 	$detl = array();
	 	$this->loadModel('InvoiceDunningComm');
	 	 $data =$_GET['id'];
	 	
	 	CakePlugin::load('PHPExcel');
               App::uses('PHPExcel', 'PHPExcel.Classes');
                if (!class_exists('PHPExcel')) {
                        throw new CakeException('Vendor class PHPExcel not found!');
                }
                $fileName = 'CMS_History.xls'; 

                $cms_history_exl = $this->InvoiceDunningComm->find('all',array('fields'=>array('InvoiceDunningComm.*'),
			'conditions'=>array('InvoiceDunningComm.invoice_id'=>$data['id'])));
                //pre($cms_history_exl); die();
                
            foreach ($cms_history_exl as $key=>$cms_history_exls) {
            	
                $detl[] = array(
                'Date '=> date('d M Y',strtotime($cms_history_exls['InvoiceDunningComm']['comms_date'])),
                'Done By'=>'BPM',
                'Mode' =>  'Email' ,
                'Dunning Stage'  => $cms_history_exls['InvoiceDunningComm']['invoice_dunning_id'],
                'Dunning Status' => $cms_history_exls['InvoiceDunningComm']['dunning_status'],
                'Intensity'      =>'Soft',
                'Send To'        =>  $cms_history_exls['InvoiceDunningComm']['spoc_name'],
                'Notes'          =>  $cms_history_exls['InvoiceDunningComm']['communication_text'],
                'Reason'         =>  $cms_history_exls['InvoiceDunningComm']['reason_id']
                
            );

       }
       //pre($detl); die();
        function filterData(&$str){
                        $str = preg_replace("/\t/", "\\t", $str);
                        $str = preg_replace("/\r?\n/", "\\n", $str);
                        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
                    }
                    header("Content-Disposition: attachment; filename=$fileName");
                    header("Content-Type: application/vnd.ms-excel");

                $flag = false;
                foreach($detl as $row) {
                        if(!$flag) {
                            echo implode("\t", array_keys($row)) . "\n";
                            $flag = true;
                        }
                        array_walk($row, 'filterData');
                        echo implode("\t", array_values($row)) . "\n";
                    }
                    exit;
               
	 }

	public function find_dunning_step_mode(){
		     $this->loadModel('InvoiceDunnings');
		     $data_dunning_step = $this->request->data;
		     $step_number = $data_dunning_step['edit_step'];
		     $invoice_id  = $data_dunning_step['invoice_id'];

		     $step_mode_Intensity  = $this->InvoiceDunnings->find('first',array('joins' => array(
		     array('table' => 'master_data_details','alias' => 'MasterDataDetails','type' => 'INNER','conditions' => array('MasterDataDetails.id = InvoiceDunnings.dunning_mode'))),'fields'=>array('InvoiceDunnings.*','MasterDataDetails.master_data_desc'),'conditions'=>array('InvoiceDunnings.invoice_id'=>$invoice_id,'InvoiceDunnings.dunning_step_no'=>$step_number)));
		    // echo "<pre>";
	     //    print_r($step_mode_Intensity);die;  
	        //$this->set('step_mode_Intensity',$step_mode_Intensity);
	        $date_select_step = date('d F, Y', strtotime($step_mode_Intensity['InvoiceDunnings']['step_target_date']));
	        $new_date['date'] = $date_select_step;
	        $date_step = array_merge($new_date,$step_mode_Intensity);
	        //pre('date_step'); die();
		    echo json_encode($date_step); die;

	}
	public function find_invoice_step_mode(){

		     $this->loadModel('InvoiceMovement');
		     $data_invoice_step = $this->request->data;
		     $edit_invoice_step = $data_invoice_step['edit_invoice_step'];
		     $invoice_id        = $data_invoice_step['invoice_id'];

		     $invoice_step_date = $this->InvoiceMovement->find('first',array('joins' =>array(
		     array('table' => 'customer_invoice_stages','alias' => 'CustomerInvoiceStage','type' => 'INNER','conditions' => array('CustomerInvoiceStage.id = InvoiceMovement.customer_invoice_stage'))),'fields'=>array('InvoiceMovement.*','CustomerInvoiceStage.stage_desc'),'conditions'=>array('InvoiceMovement.invoice_id'=>$invoice_id,'InvoiceMovement.customer_invoice_stage'=>$edit_invoice_step)));

		     // $invoice_step_date = $this->InvoiceMovement->find('first',array('fields'=>array('InvoiceMovement.*'),'conditions'=>array('InvoiceMovement.invoice_id'=>$invoice_id,'InvoiceMovement.customer_invoice_stage'=>$edit_invoice_step)));
		    
	        //$this->set('step_mode_Intensity',$step_mode_Intensity);
	        $date_invoice_step = date('d F, Y', strtotime($invoice_step_date['InvoiceMovement']['target_date']));
	        $new_date['date']  = $date_invoice_step;
	        $date_step         = array_merge($new_date,$invoice_step_date);
		    echo json_encode($date_step); die;

		    // echo "<pre>";
	     //    print_r($date_invoice_step);die;  

	}

	public function pause_dunning(){

		    $this->loadModel('Invoice');
		    $this->loadModel('InvoiceDunningComm');
		    $ses=$this->Session->read('admin'); 
		    $pause_data       = $this->request->data;
		    $pause_start_date = date('Y-m-d', strtotime($pause_data['pause_start_date']));
		    $pause_end_date   = date('Y-m-d', strtotime($pause_data['pause_end_date']));
		    $pause_reason     = $pause_data['pause_reason'];
		    $pause_remark     = $pause_data['pause_remark'];
		    $invoice_id       = $pause_data['invoice_id'];
            $dunning_status   = 'Paused';

		    $update_pause['Invoice']['dun_pause_exclude_start_dt'] = $pause_start_date;
		    $update_pause['Invoice']['dun_pause_exclude_end_dt']   = $pause_end_date;
		    $update_pause['Invoice']['dun_pause_exclude_reason']   = $pause_reason;
		    $update_pause['Invoice']['dun_pause_exclude_remarks']  = $pause_remark;
		    $update_pause['Invoice']['dunning_status']             = $dunning_status;

		    $this->Invoice->id = $invoice_id;
		    $this->Invoice->save($update_pause);

		    $invoice_inh['InvoiceDunningComm']['user_id'] = $ses['Admin']['AppUser']['id'];
			$invoice_inh['InvoiceDunningComm']['invoice_id'] = $invoice_id; 
			$invoice_inh['InvoiceDunningComm']['history_of'] = 'Pause Dunning';
			$invoice_inh['InvoiceDunningComm']['reason_id'] = $pause_reason;
			$invoice_inh['InvoiceDunningComm']['communication_text'] = $pause_remark;
			$invoice_inh['InvoiceDunningComm']['comms_date'] = date('Y-m-d');
			$this->InvoiceDunningComm->create();
			$this->InvoiceDunningComm->save($invoice_inh);
		    echo 'success'; die;

		    // echo "<pre>";
		    // print_r($pause_data);die;
	}
	public function pause_dunning_for_mul(){

		    $this->loadModel('Invoice');
		    $this->loadModel('InvoiceDunningComm');
		    $ses=$this->Session->read('admin'); 
		    $pause_data       = $this->request->data;
		    //pre($pause_data);die();
		     
		    $pause_start_date = date('Y-m-d', strtotime($pause_data['pause_start_date']));
		    $pause_end_date   = date('Y-m-d', strtotime($pause_data['pause_end_date']));
		    $pause_reason     = $pause_data['pause_reason'];
		    $pause_remark     = $pause_data['pause_remark'];
		    $invoices_id      = $pause_data['id'];
            $dunning_status   = 'Paused';

		    foreach ($invoices_id as $key => $invoice_id) {

		    $update_pause['Invoice']['dun_pause_exclude_start_dt'] = $pause_start_date;
		    $update_pause['Invoice']['dun_pause_exclude_end_dt']   = $pause_end_date;
		    $update_pause['Invoice']['dun_pause_exclude_reason']   = $pause_reason;
		    $update_pause['Invoice']['dun_pause_exclude_remarks']  = $pause_remark;
		    $update_pause['Invoice']['dunning_status']             = $dunning_status;
		    $this->Invoice->id = $invoice_id;
		    $this->Invoice->save($update_pause);

		    $invoice_inh['InvoiceDunningComm']['user_id'] = $ses['Admin']['AppUser']['id'];
			$invoice_inh['InvoiceDunningComm']['invoice_id'] = $invoice_id; 
			$invoice_inh['InvoiceDunningComm']['history_of'] = 'Pause Dunning';
			$invoice_inh['InvoiceDunningComm']['reason_id'] = $pause_reason;
			$invoice_inh['InvoiceDunningComm']['communication_text'] = $pause_remark;
			$invoice_inh['InvoiceDunningComm']['comms_date'] = date('Y-m-d');
			$this->InvoiceDunningComm->create();
			$this->InvoiceDunningComm->save($invoice_inh);
		    	
		    }
		    
		    echo 'success'; die;

		    // echo "<pre>";
		    // print_r($pause_data);die;
	}
	public function resume_dunning(){

		    $this->loadModel('Invoice');
		    $pause_data   = $this->request->data;		    
		    $invoice_id   = $pause_data['invoice_id'];
           
		    $update_resume['Invoice']['dun_pause_exclude_start_dt'] = '';
		    $update_resume['Invoice']['dun_pause_exclude_end_dt']   = '';
		    $update_resume['Invoice']['dun_pause_exclude_reason']   = NULL;
		    $update_resume['Invoice']['dun_pause_exclude_remarks']  = NULL;
		    $update_resume['Invoice']['dunning_status']             = NULL;

		    $this->Invoice->id = $invoice_id;
		    $this->Invoice->save($update_resume);
		    echo 'success'; die;

		    // echo "<pre>";
		    //print_r($invoice_id );die;
	}

	public function exclude_dunning(){

		    $this->loadModel('Invoice');
		    $excldue_data      = $this->request->data;
		    $startDate_excldue = date('Y-m-d', strtotime($excldue_data['startDate_excldue']));
		    $endDate_excldue   = date('Y-m-d', strtotime($excldue_data['endDate_excldue']));
		    $excldue_remarks   = $excldue_data['excldue_remarks'];
		    $invoice_id        = $excldue_data['invoice_id'];
            $dunning_status    = 'Excluded';

		    $update_excldue['Invoice']['dun_pause_exclude_start_dt'] = $startDate_excldue;
		    $update_excldue['Invoice']['dun_pause_exclude_end_dt']   = $endDate_excldue;
		    $update_excldue['Invoice']['dun_pause_exclude_remarks']  = $excldue_remarks;
		    $update_excldue['Invoice']['dunning_status']             = $dunning_status;

		    $this->Invoice->id = $invoice_id;
		    $this->Invoice->save($update_excldue);
		    echo 'success'; die;

		    // echo "<pre>";
		    // print_r($pause_data);die;
	}
	public function exclude_dunning_for_all(){
		
		$ses=$this->Session->read('admin'); 
		$this->loadModel('Invoice');
		$this->loadModel('InvoiceDunningComm');
		$this->loadModel('AppUser');
		$excldue_data      = $this->request->data;
		
		$startDate_excldue = date('Y-m-d', strtotime($excldue_data['startDate_excldue']));
		$endDate_excldue   = date('Y-m-d', strtotime($excldue_data['endDate_excldue']));
		$excldue_remarks   = $excldue_data['excldue_remarks'];
		$invoices_id        = $excldue_data['id'];
		$dunning_status    = 'Excluded';
		foreach ($invoices_id  as $key => $invoice_id ) {
						
			$update_excldue['Invoice']['dun_pause_exclude_start_dt']= $startDate_excldue;
			$update_excldue['Invoice']['dun_pause_exclude_end_dt']=$endDate_excldue;
			$update_excldue['Invoice']['dun_pause_exclude_remarks'] = $excldue_remarks;
			$update_excldue['Invoice']['dunning_status']= $dunning_status;
			$this->Invoice->id = $invoice_id;
			$this->Invoice->save($update_excldue);
			$invoie = $this->Invoice->find('first', array('recursive'=>'-1','fields'=>array('Entitie.id'),'conditions' => array('Invoice.id' =>$invoice_id)));
			$userDetail = $this->AppUser->find('first', array('recursive'=>'-1','fields'=>array('AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions' =>array('AppUser.entity_id' =>$invoie['Entitie']['id'],'AppUser.role_id'=>7)));
			$link=HTTP_ROOT.'home/cms_requests?email_req=true';
			App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();
			$Email->config('gmail'); 
			$Email->emailFormat('html'); 
			$Email->to($userDetail['AppUser']['user_email']);
			//$Email->to('ramjee2443@gmail.com');
			$Email->subject('Exclusion request');
			$Email->template('exclusion_approval');
			$Email->viewVars(array('to'=>$userDetail['AppUser'],'from'=>$ses['Admin']['AppUser'],'link'=>$link));
			$Email->send();

			$invoice_inh['InvoiceDunningComm']['user_id'] = $ses['Admin']['AppUser']['id'];
			$invoice_inh['InvoiceDunningComm']['invoice_id'] = $invoice_id; 
			$invoice_inh['InvoiceDunningComm']['history_of'] = 'Exclude Dunning';
			$invoice_inh['InvoiceDunningComm']['communication_text'] = $excldue_remarks;
			$invoice_inh['InvoiceDunningComm']['comms_date'] = date('Y-m-d');
			$this->InvoiceDunningComm->create();
			$this->InvoiceDunningComm->save($invoice_inh);
		}
		
		echo 'success'; die;
		    
	}
		
	function customers()
	{		
		$conditions= array();
		$this->loadModel('Entitie');
		$this->loadModel('AppUser');
		$this->loadModel('EntityAddress');
		$this->loadModel('Contact');
		$this->loadModel('Contract');
		$this->loadModel('BusinessLine');
	/// *********** Start Add By Manohar Singh ************  ///
		$this->loadModel('Subvertical');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('ProfitCenter');
		$this->loadModel('DunningStepMaster');
		$this->loadModel('Project');
	/// *********** End Add By Manohar Singh ************  ///
		$data = $this->request->data;  

		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin']['AppUser']['id'];
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
			$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Customer')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
        $short = array('Entitie.id'=>'DESC');

				
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Entitie.entitiy_name'=>$shortVal);
		}
		
		if(isset($data['search_key']) && !empty($data['search_key'])){	
			$conditions = array_merge($conditions,array('OR'=>array('Entitie.entitiy_name LIKE'=>'%'.$data['search_key'].'%')));
		}
		if(isset($data['startfiletrdate']) && !empty($data['startfiletrdate'])) {
			$begin_data = date('Y-m-d',strtotime($data['startfiletrdate']));
			$conditions = array_merge($conditions,array('Entitie.created_date >= '=>$begin_data));
			
		}

		if( isset($data['endfilterdate']) && !empty($data['endfilterdate']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['endfilterdate']));
		 	$conditions = array_merge($conditions,array('Entitie.created_date <= '=>$close_date));
		 	
		 }
		 
		 if(isset($data['search_id']) && !empty($data['search_id'])){	
		 	$conditions = array_merge($conditions,array('Entitie.id'=>$data['search_id']));
		 }

		 if(isset($data['Credits']) && !empty($data['Credits'])){
		 		
		 	$conditions = array_merge($conditions,array('Entitie.credit_period'=>$data['Credits']));
		 }

		 if(isset($data['Sub_V']) && !empty($data['Sub_V'])){
		 	
		 	$cust_info = $this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.subvertical'=>$data['Sub_V'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		 }

		if(isset($data['Zone']) && !empty($data['Zone'])){
		 	
		 	$cust_info = $this->EntityAddress->find('list',array('group'=>'EntityAddress.entity_id','fields'=>array('EntityAddress.id','EntityAddress.entity_id'),'conditions'=>array('EntityAddress.zone'=>$data['Zone'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		}

		 if(isset($data['P_Center']) && !empty($data['P_Center'])){
		 	
		 	$cust_info = $this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.profit_center_id'=>$data['P_Center'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		 }


		  if(isset($data['B_Line']) && !empty($data['B_Line'])){
		 	$customer =$this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.business_line'=>$data['B_Line'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$customer));
		 }

		 if(isset($data['contractType']) && !empty($data['contractType'])){
		 	$customer =$this->Contract->find('list',array('group'=>'Contract.cust_entity_id','fields'=>array('Contract.id','Contract.cust_entity_id'),'conditions'=>array('Contract.contract_type'=>$data['contractType'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$customer));
		// 	pre($conditions);die;
		 }
		  if(isset($data['projectType']) && !empty($data['projectType']))
		{
			$cust_info = $this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.project_type'=>$data['projectType'])));
		//	pre($cust_info);die;
			$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		}

		 //$conditions = array_merge($conditions,array('Entitie.status '=>'Active'));
		 $reporting_id = $this->AppUser->find('all',array('fields'=>array('AppUser.id'),'conditions'=>array('AppUser.reporting_manager'=>$userid)));
				 
		 $reporting_ids=array();
		 foreach ($reporting_id as $key => $reporting_manager_id) {
		 	array_push($reporting_ids,$reporting_manager_id['AppUser']['id']);
		 	
		 }
		 array_push($reporting_ids,$userid);      
		 //$conditions = array_merge($conditions,array('Entitie.status '=>'Active','Entitie.created_by'=>$reporting_ids));
		  $conditions = array_merge($conditions,array('Entitie.status '=>'Active'));
		 
		 $this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>$short,
		      'fields'=>array('Entitie.entitiy_name','Entitie.id','Entitie.entity_id','Entitie.status','Entitie.created_date','Entitie.created_by','Entitie.rsm_email','Entitie.rsm_phone','BusinessLine.id','BusinessLine.id','BusinessLine.bl_name'),
		      'conditions'=>array($conditions));   
			
		  $cust_details = $this->paginate('Entitie');
		 
		/// ******************************* Manohars Code for Filters Start Here ******************************* ///

			$filterBusinessLine = $this->BusinessLine->find('all',array('fields'=>array('BusinessLine.id','BusinessLine.bl_name','BusinessLine.entity_id'),'group' => array('BusinessLine.bl_name'),'conditions'=>array('BusinessLine.is_active'=>1)));
			$filterSubVertical = $this->Subvertical->find('all',array('fields'=>array('Subvertical.id','Subvertical.sv_name'),'conditions'=>array('Subvertical.is_active'=>1),'group'=>array('Subvertical.sv_name')));
			
			$filterProfitCenters = $this->ProfitCenter->find('all',array('fields'=>array('ProfitCenter.id','ProfitCenter.pc_name'),'conditions'=>array('ProfitCenter.is_active'=>1),'group'=>array('ProfitCenter.pc_name')));
		
			$filterZone=$this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'entity_address(zone)','MasterDataDetail.is_active'=>1),'group'=>array('MasterDataDetail.master_data_desc')));

			$filterCredit = $this->DunningStepMaster->find('all',array('recursive'=>'-1','fields'=>array('DunningStepMaster.id','DunningStepMaster.credit_period'),'conditions'=>array('DunningStepMaster.is_active'=>1),'group'=>array('DunningStepMaster.credit_period')));
			//pre($filterCredit);die;
			$filterContractType=$this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'contract_type','MasterDataDetail.is_active'=>1),'group'=>array('MasterDataDetail.master_data_desc')));

			$this->set(compact('filterBusinessLine','filterSubVertical','filterProfitCenters','filterZone','filterCredit','filterContractType'));

		/// ******************************* Manohars Code for Filters End Here ******************************* ///
			 $count_active = $this->Entitie->find('count',array('conditions'=>array($conditions)));
		//$count_active = $this->Entitie->find('count',array('conditions'=>array('Entitie.status'=>'Active')));
		$count_inactive = $this->Entitie->find('count',array('conditions'=>array('Entitie.status'=>'Inactive')));
	
		$pag_det=$this->params['paging'];
		$parem=$pag_det['Entitie'];
		$total_page=$parem['pageCount'];
		$current_page=$parem['page'];
		$records=$parem['current'];
		$total_records=$parem['count'];		
		
		if($current_page==1)
		{
			$pgdetl='Showing 1 to '.$records.' of '.$total_records.' contest';
		}
		else if($total_records==0)
		{
			$pgdetl='';
		}
		else 
		{
			$start=(($current_page-1)*9)+1;
			$pgdetl='Showing '.$start.' to '.(($start+$records)-1).' of '.$total_records.' contest';
		}

		$this->set('pageinfo',$pgdetl);	
			

		if($this->RequestHandler->isAjax())
           {           	
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);
                                                          
                if(!empty($cust_details))
                {                	
                    $view->set('custmrdetail',$cust_details);
                    $view->set('userid',$userid);
                    $html['html'] = $view->render("custmer_element");
                    $view1->set('pageinfo',$pgdetl);
                    $html['pagination'] = $view1->render("pagination");
                    $html['message'] ='success';  
                    $html['totalCust'] =$count_active;                         
                }
                else
                {
                    $html['message'] ='error';
                    $html['totalCust'] =$count_active; 
                }

                echo json_encode($html);die;
            }else{
                 
            	$this->set('custmrdetail',$cust_details);
            }
        $this->set('userid',$userid);
        $this->set('count_active',$count_active);
        $this->set('count_inactive',$count_inactive);
        $this->set('excess_permission',$excess_permission);
	}
	function customers_pending()
	{
		$conditions= array();
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
		$this->loadModel('Entitie');
		$this->loadModel('EntityAddress');
		$this->loadModel('Contact');
		$this->loadModel('Contract');
		$this->loadModel('BusinessLine');
		$this->loadModel('Subvertical');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('DunningStepMaster');
		$this->loadModel('ProfitCenter');
		$this->loadModel('Project');
		$this->loadModel('AppUser');
		$data = $this->request->data;
		//pre($sesn); die;
		$short = array('Entitie.id'=>'DESC');
				
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Entitie.entitiy_name'=>$shortVal);
		}

						
		if(isset($data['search_key']) && !empty($data['search_key'])){	
			$conditions = array_merge($conditions,array('OR'=>array('Entitie.entitiy_name LIKE'=>'%'.$data['search_key'].'%')));
			
		}
		
		if(isset($data['startfiletrdate']) && !empty($data['startfiletrdate'])) {
			$begin_data = date('Y-m-d',strtotime($data['startfiletrdate']));
			$conditions = array_merge($conditions,array('Entitie.created_date >= '=>$begin_data));
			
		}

		if( isset($data['endfilterdate']) && !empty($data['endfilterdate']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['endfilterdate']));
		 	$conditions = array_merge($conditions,array('Entitie.created_date <= '=>$close_date));
		 	
		 }

		if(isset($data['Credits']) && !empty($data['Credits'])){
		 		
		 	$conditions = array_merge($conditions,array('Entitie.credit_period'=>$data['Credits']));
		 }

		 if(isset($data['Sub_V']) && !empty($data['Sub_V'])){
		 	
		 	$cust_info = $this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.subvertical'=>$data['Sub_V'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		 }

		if(isset($data['Zone']) && !empty($data['Zone'])){
		 	
		 	$cust_info = $this->EntityAddress->find('list',array('group'=>'EntityAddress.entity_id','fields'=>array('EntityAddress.id','EntityAddress.entity_id'),'conditions'=>array('EntityAddress.zone'=>$data['Zone'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		}

		 if(isset($data['P_Center']) && !empty($data['P_Center'])){
		 	
		 	$cust_info = $this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.profit_center_id'=>$data['P_Center'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		 }

		 if(isset($data['contractType']) && !empty($data['contractType'])){
		 	$customer =$this->Contract->find('list',array('group'=>'Contract.cust_entity_id','fields'=>array('Contract.id','Contract.cust_entity_id'),'conditions'=>array('Contract.contract_type'=>$data['contractType'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$customer));
		// 	pre($conditions);die;
		 }

		   if(isset($data['projectType']) && !empty($data['projectType']))
		{
			$cust_info = $this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.project_type'=>$data['projectType'])));
		//	pre($cust_info);die;
			$conditions = array_merge($conditions,array('Entitie.id '=>$cust_info));
		}
		 
		 if(isset($data['B_Line']) && !empty($data['B_Line'])){
		 	$customer =$this->Project->find('list',array('group'=>'Project.customer_entity_id','fields'=>array('Project.id','Project.customer_entity_id'),'conditions'=>array('Project.business_line'=>$data['B_Line'])));
		 	$conditions = array_merge($conditions,array('Entitie.id '=>$customer));
		 }
		 $reporting_id = $this->AppUser->find('all',array('fields'=>array('AppUser.id'),'conditions'=>array('AppUser.reporting_manager'=>$userid)));
				 
		 $reporting_ids=array();
		 foreach ($reporting_id as $key => $reporting_manager_id) {
		 	array_push($reporting_ids,$reporting_manager_id['AppUser']['id']);
		 	
		 }
		 array_push($reporting_ids,$userid);      
		$conditions = array_merge($conditions,array('Entitie.created_by'=>$reporting_ids));
		 
		 $this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>$short,
		      'fields'=>array('Entitie.entitiy_name','Entitie.id','Entitie.entity_id','Entitie.status','Entitie.created_date','Entitie.created_by','BusinessLine.id','BusinessLine.id','BusinessLine.bl_name'),'conditions'=>array('Entitie.status '=>array('Inactive','Rejected','Draft'),$conditions));   
			
		 $cust_pen_details = $this->paginate('Entitie');

		/*
		if($search==1){	
          $this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>'Entitie.id DESC',
		      'fields'=>array('Entitie.entitiy_name','Entitie.id','Entitie.entity_id','Entitie.status','Entitie.created_date','Entitie.created_by','BusinessLine.id','BusinessLine.id','BusinessLine.bl_name'),'conditions'=>array($conditions));   
		}else{			
         $this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>'Entitie.id DESC',
		'fields'=>array('Entitie.entitiy_name','Entitie.id','Entitie.entity_id','Entitie.status','Entitie.created_date','Entitie.created_by','BusinessLine.id','BusinessLine.id','BusinessLine.bl_name'),'conditions'=>array('Entitie.status'=>array('Inactive','Rejected','Draft')));
		}	

		*/
		$count_rejected = $this->Entitie->find('count',array('conditions'=>array('Entitie.status'=>'Rejected',$conditions)));
		$count_inactive = $this->Entitie->find('count',array('conditions'=>array('Entitie.status'=>'Inactive',$conditions)));
		$count_draft = $this->Entitie->find('count',array('conditions'=>array('Entitie.status'=>'Draft',$conditions)));	
		

		/// ******************************* Manohars Code for Filters Start Here ******************************* ///

			$filterBusinessLine = $this->BusinessLine->find('all',array('fields'=>array('BusinessLine.id','BusinessLine.bl_name','BusinessLine.entity_id'),'group' => array('BusinessLine.bl_name'),'conditions'=>array('BusinessLine.is_active'=>1)));
			
			$filterSubVertical = $this->Subvertical->find('all',array('fields'=>array('Subvertical.id','Subvertical.sv_name'),'conditions'=>array('Subvertical.is_active'=>1),'group'=>array('Subvertical.sv_name')));
			
			$filterProfitCenters = $this->ProfitCenter->find('all',array('fields'=>array('ProfitCenter.id','ProfitCenter.pc_name'),'conditions'=>array('ProfitCenter.is_active'=>1),'group'=>array('ProfitCenter.pc_name')));
		
			$filterZone=$this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'entity_address(zone)','MasterDataDetail.is_active'=>1),'group'=>array('MasterDataDetail.master_data_desc')));
			
			$filterCredit = $this->DunningStepMaster->find('all',array('recursive'=>'-1','fields'=>array('DunningStepMaster.id','DunningStepMaster.credit_period'),'conditions'=>array('DunningStepMaster.is_active'=>1),'group'=>array('DunningStepMaster.credit_period')));

			$filterContractType=$this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'contract_type','MasterDataDetail.is_active'=>1),'group'=>array('MasterDataDetail.master_data_desc')));


			$this->set(compact('filterBusinessLine','filterSubVertical','filterProfitCenters','filterZone','filterCredit','filterContractType'));

		/// ******************************* Manohars Code for Filters End Here ******************************* ///
		
		$pag_det=$this->params['paging'];
		$parem=$pag_det['Entitie'];
		$total_page=$parem['pageCount'];
		$current_page=$parem['page'];
		$records=$parem['current'];
		$total_records=$parem['count'];
		//pre($total_records);die;
		
		if($current_page==1)
		{
			$pgdetl='Showing 1 to '.$records.' of '.$total_records.' contest';
		}
		else if($total_records==0)
		{
			$pgdetl='';
		}
		else 
		{
			$start=(($current_page-1)*9)+1;
			$pgdetl='Showing '.$start.' to '.(($start+$records)-1).' of '.$total_records.' contest';
		}

		$this->set('pageinfo',$pgdetl);	
			
		if($this->RequestHandler->isAjax())
           {              
                             
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);               
                                          
                if(!empty($cust_pen_details))
                {
                	$view->set('userid',$userid);
			        $view->set('count_rejected',$count_rejected);
			        $view->set('count_inactive',$count_inactive);
			        $view->set('count_draft',$count_draft);               	
                    $view->set('cust_pen_details',$cust_pen_details);
                    $html['html'] = $view->render("custmer_pending_element");
                    $view1->set('pageinfo',$pgdetl);
                    $html['pagination'] = $view1->render("pagination");
                    $html['message'] ='success';
                    $html['count_rejected'] =$count_rejected;
                    $html['count_inactive'] =$count_inactive;
                    $html['count_draft'] =$count_draft;
                        
                }
                else
                {
                    $html['message'] ='error';
                    $html['count_rejected'] =$count_rejected;
                    $html['count_inactive'] =$count_inactive;
                    $html['count_draft'] =$count_draft;
                }

                echo json_encode($html);die;
            }else{
                 
            	$this->set('cust_pen_details',$cust_pen_details);
            }
        $this->set('userid',$userid);
        $this->set('count_rejected',$count_rejected);
        $this->set('count_inactive',$count_inactive);
        $this->set('count_draft',$count_draft);
	}


	function contracts()
	{
		$conditions= array();
		$this->loadModel('Contract');
		$this->loadModel('AppUser');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('Entitie');
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin']['AppUser']['id'];
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');

		$data = $this->request->data;
		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Contract')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];

		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
        $short = array('Contract.id'=>'DESC');

		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Contract.contract_title'=>$shortVal);
		}
		if(isset($data['search_key']) && !empty($data['search_key'])){			
			$conditions = array_merge($conditions,array('OR'=>array('Contract.contract_title LIKE'=>'%'.$data['search_key'].'%')));
			
		}
		if(isset($data['contaraceStartDat']) && !empty($data['contaraceStartDat'])) {
			$begin_data = date('Y-m-d',strtotime($data['contaraceStartDat']));
			$conditions = array_merge($conditions,array('Contract.contract_start_dt >= '=>$begin_data));
			
		}

		if( isset($data['contractEndDate']) && !empty($data['contractEndDate']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['contractEndDate']));
		 	$conditions = array_merge($conditions,array('Contract.contract_end_dt <= '=>$close_date));
		 	
		 }

		 /***Get Contract Expiry For Graph Data 17-02-2019***/
        if(isset($data['contractsExpiring']) && !empty($data['contractsExpiring'])) {
              $begin_data = date('Y-m-01',strtotime($data['contractsExpiring']));
              $close_date = date('Y-m-t',strtotime($data['contractsExpiring']));
              $conditions = array_merge($conditions,array('Contract.contract_end_dt >= '=>$begin_data));
		 	  $conditions = array_merge($conditions,array('Contract.contract_end_dt <= '=>$close_date));
        } 
	   

		if(isset($data['noticPeriod']) && !empty($data['noticPeriod'])){
		 	
		 	$conditions = array_merge($conditions,array('Contract.notice_period'=>$data['noticPeriod']));
		}


		if(isset($data['currency']) && !empty($data['currency'])){
		 	
		 	$conditions = array_merge($conditions,array('Contract.currency'=>$data['currency']));
		}

		if(isset($data['contractType']) && !empty($data['contractType'])){
		 	
		 	$conditions = array_merge($conditions,array('Contract.contract_type'=>$data['contractType']));
		}
		$reporting_id = $this->AppUser->find('all',array('fields'=>array('AppUser.id'),'conditions'=>array('AppUser.reporting_manager'=>$userid)));		
				 
		$reporting_ids=array();
		foreach ($reporting_id as $key => $reporting_manager_id) {
		 	array_push($reporting_ids,$reporting_manager_id['AppUser']['id']);		 	
		}
		array_push($reporting_ids,$userid);
		$conditions = array_merge($conditions,array('Contract.status'=>1));	

		//$conditions = array_merge($conditions,array('Contract.status'=>1));	
		$contract_active = $this->Contract->find('count',array('conditions'=>array($conditions)));	

		$this->paginate = array('limit'=>25,'group'=>'Contract.id','order'=>$short,
		'fields'=>array('Contract.id','Contract.status','Contract.creation_dttm','Contract.contract_start_dt ','Contract.parent_contract ','Contract.contract_number',
			'Contract.contract_type','Contract.contract_title','Contract.tot_ctrct_value',
			'MasterDataDetail.master_data_desc','Project.customer_entity_id','Project.subvertical',
			'Entitie.entitiy_name'),'conditions'=>array($conditions));

		$contract_detail = $this->paginate('Contract'); 
		/*
		if($search==1){	
          $this->paginate = array('limit'=>25,'group'=>'Contract.id','order'=>'Contract.id desc',
		'fields'=>array('Contract.id','Contract.status','Contract.creation_dttm','Contract.contract_start_dt ','Contract.parent_contract ','Contract.contract_number',
			'Contract.contract_type','Contract.contract_title','Contract.tot_ctrct_value',
			'MasterDataDetail.master_data_desc','Project.customer_entity_id','Project.subvertical',
			'Entitie.entitiy_name'),'conditions'=>array($conditions));   
		}else{			
         $this->paginate = array('limit'=>25,'group'=>'Contract.id','order'=>'Contract.id desc',
		'fields'=>array('Contract.id','Contract.status','Contract.creation_dttm',
			'Contract.contract_start_dt ','Contract.parent_contract ','Contract.contract_number',
			'Contract.contract_type','Contract.contract_title','Contract.tot_ctrct_value',
			'MasterDataDetail.master_data_desc','Project.customer_entity_id','Project.subvertical',
			'Entitie.entitiy_name'));
		}	
		*/		
		
		//$contract_detail = $this->paginate('Contract',$conditions);



		////**************************  Start Code for filters  *****************************//////

		$contract_type = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'contract_type','MasterDataDetail.is_active'=>1)));

		$this->set(compact('contract_type'));

		////**************************  End Code for filters  *****************************//////

	//	$contract_active = $this->Contract->find('count',array('group'=>'Contract.id','conditions'=>array('Contract.status'=>1)));
		$contract_inactive = $this->Contract->find('count',array('group'=>'Contract.id','conditions'=>array('Contract.status'=>0)));

	//	$contract_active = $this->Contract->find('count');		
		
		$this->set('contract_active',$contract_active);
		$this->set('contract_inactive',$contract_inactive);
		$this->set('excess_permission',$excess_permission);
		if($this->RequestHandler->isAjax())
             {
                //pre($data);die;
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($contract_detail))
                {
                    
                        $view->set('contract',$contract_detail);
                        $view->set('contract_active',$contract_active);
                        $view->set('contract_inactive',$contract_inactive);
                        $html['html'] = $view->render("contracts_data_page");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                        $html['contract_active'] =$contract_active;
                }
                else
                {
                        $html['message'] ='error';
                        $html['contract_active'] =$contract_active;
                }

                echo json_encode($html);die;

                }
                $this->set('contract',$contract_detail);

		//pre($contract_detail);die;
	}
	function contracts_pending()
	{
		$conditions= array();
		$this->loadModel('Contract');
		$this->loadModel('AppUser');
		$this->loadModel('MasterDataDetail');
		//$this->loadModel('Entitie');
		$sesn=$this->Session->read('admin');
		$userid = $sesn['Admin']['AppUser']['id'];
		$data = $this->request->data;

        $short = array('Contract.id'=>'DESC');
				
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Contract.contract_title'=>$shortVal);
		}
		if(isset($data['search_key']) && !empty($data['search_key'])){			
			$conditions = array_merge($conditions,array('OR'=>array('Contract.contract_title LIKE'=>'%'.$data['search_key'].'%')));
			
		}

		if(isset($data['contaraceStartDat']) && !empty($data['contaraceStartDat'])) {
			$begin_data = date('Y-m-d',strtotime($data['contaraceStartDat']));
			$conditions = array_merge($conditions,array('Contract.contract_start_dt >= '=>$begin_data));
			
		}

		if( isset($data['contractEndDate']) && !empty($data['contractEndDate']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['contractEndDate']));
		 	$conditions = array_merge($conditions,array('Contract.contract_end_dt <= '=>$close_date));		 	
		 }

		if(isset($data['noticPeriod']) && !empty($data['noticPeriod'])){
		 	
		 	$conditions = array_merge($conditions,array('Contract.notice_period'=>$data['noticPeriod']));
		}

		if(isset($data['currency']) && !empty($data['currency'])){
		 	
		 	$conditions = array_merge($conditions,array('Contract.currency'=>$data['currency']));
		}
		if(isset($data['contractType']) && !empty($data['contractType'])){
		 	
		 	$conditions = array_merge($conditions,array('Contract.contract_type'=>$data['contractType']));
		}

		//$conditions = array_merge($conditions,array('OR' => array('Contract.status'=>1)));
		$reporting_id = $this->AppUser->find('all',array('fields'=>array('AppUser.id'),'conditions'=>array('AppUser.reporting_manager'=>$userid)));
				 
		$reporting_ids=array();
		foreach ($reporting_id as $key => $reporting_manager_id) {
		 	array_push($reporting_ids,$reporting_manager_id['AppUser']['id']);		 	
		}
		array_push($reporting_ids,$userid);
		$conditions = array_merge($conditions,array('Contract.created_by'=>$reporting_ids));	

		$this->paginate = array('limit'=>20,'recursive'=>'-1','group'=>'Contract.id','order'=>$short,'fields'=>array('Contract.id','Contract.status','Contract.creation_dttm','Contract.contract_start_dt ','Contract.parent_contract ','Contract.contract_number','Contract.contract_type','Contract.cust_entity_id','Contract.created_by','Contract.contract_title','Contract.tot_ctrct_value','MasterDataDetail.master_data_desc','Project.customer_entity_id','Project.subvertical',
		 	'Entitie.entitiy_name','Entitie.id','Entitie.entity_id'),'conditions'=>array('Contract.status'=>array(0,2,3),$conditions));

		$contract_pending_detail = $this->paginate('Contract');
		//pre($contract_pending_detail); die;

		 /*
		 $conditions = array_merge($conditions,array('OR' => array('Contract.status'=>array(0,2,3))));	

		if(isset($data['search_id']) && !empty($data['search_id'])){
			
			$this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>'Contract.id desc','fields'=>array('Contract.id','Contract.status','Contract.creation_dttm','Contract.contract_start_dt ','Contract.parent_contract ','Contract.contract_number','Contract.contract_type','Contract.cust_entity_id','Contract.created_by','Contract.contract_title','Contract.tot_ctrct_value','MasterDataDetail.master_data_desc','Project.customer_entity_id','Project.subvertical',
		 	'Entitie.entitiy_name','Entitie.id'),'conditions'=>array('Contract.id'=>$data['search_id']));
		
		}else{

			$this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>'Contract.id desc','fields'=>array('Contract.id','Contract.status','Contract.creation_dttm','Contract.contract_start_dt ','Contract.parent_contract ','Contract.contract_number','Contract.contract_type','Contract.cust_entity_id','Contract.created_by','Contract.contract_title','Contract.tot_ctrct_value','MasterDataDetail.master_data_desc','Project.customer_entity_id','Project.subvertical',
		 	'Entitie.entitiy_name','Entitie.id','Entitie.entity_id'),'conditions'=>array('Contract.status'=>array(0,2,3)));
		
	   }
	   */

	   ////**************************  Start Code for filters  *****************************//////

		$contract_type = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'contract_type','MasterDataDetail.is_active'=>1)));

		$this->set(compact('contract_type'));

		////**************************  End Code for filters  *****************************//////

	   $count_rejected = $this->Contract->find('count',array('group'=>'Contract.id','conditions'=>array('Contract.status'=>3,$conditions)));
		$count_inactive = $this->Contract->find('count',array('group'=>'Contract.id','conditions'=>array('Contract.status'=>0,$conditions)));
		$count_draft = $this->Contract->find('count',array('group'=>'Contract.id','conditions'=>array('Contract.status'=>2,$conditions)));	

		$count_rejected = $count_rejected != ''? $count_rejected:0;
		$count_inactive = $count_inactive != ''? $count_inactive:0;
		$count_draft = $count_draft != ''? $count_draft:0;
	   
	    $this->set('userid',$userid);
	    $this->set('count_rejected',$count_rejected); 
	    $this->set('count_inactive',$count_inactive); 
	    $this->set('count_draft',$count_draft); 
	    //pre($contract_pending_detail); die;
		
		if($this->RequestHandler->isAjax())
            {
                
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($contract_pending_detail))
                {
                	    $view->set('userid',$userid); 
                	    $view->set('count_rejected',$count_rejected); 
	                    $view->set('count_inactive',$count_inactive); 
	                    $view->set('count_draft',$count_draft); 
                        $view->set('contract_pending_detail',$contract_pending_detail);
                        $html['html'] = $view->render("contract_pending_element");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                        $html['count_rejected'] =$count_rejected;
                        $html['count_inactive'] =$count_inactive;
                        $html['count_draft'] =$count_draft;
                }
                else
                {
                        $html['message'] ='error';
                        $html['count_rejected'] =$count_rejected;
                        $html['count_inactive'] =$count_inactive;
                        $html['count_draft'] =$count_draft;
                }

                echo json_encode($html);die;

            }else{
                $this->set('contract_pending_detail',$contract_pending_detail);
            }
                               
	}

	public function serch_contracts_active(){

		$this->loadModel('Contract');
		$data = $this->request->data;

         $li = '';
        if($data !=''){

         	$search_contract_name = $this->Contract->find("all",
         		array('recursive'=>'-1',
         			'fields'=>array('Contract.contract_title', 'Contract.id'),
         			'conditions'=>array('Contract.status'=>1,
         				'OR' => array('Contract.contract_title LIKE' => '%' . $data['search_key'] . '%')
         				
         			)));
        
        }
		
		
		foreach($search_contract_name as $k=>$contract_name)
		 {
		 	$li.='<li ><a href="#" class="select_contract conTitle" id="'.$contract_name['Contract']['id'].'">'.$contract_name['Contract']['contract_title'].'</a></li>';
		 	
		 }


		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	} 

	public function serch_contract_pending(){

		$this->loadModel('Contract');
		$data = $this->request->data;

         $li = '';
        if($data !=''){

         	$search_contract_name = $this->Contract->find("all",
         		array('recursive'=>'-1',
         			'fields'=>array('Contract.contract_title', 'Contract.id'),
         			'conditions'=>array('Contract.status'=>array(0,2,3),
         				'OR' => array('Contract.contract_title LIKE' => '%' . $data['search_key'] . '%',
									//'Contract.contact_type LIKE' => '%' . $data['search_key'] . '%',
									)
         				
         			)));
        
        }
		
		
		foreach($search_contract_name as $k=>$contract_name)
		 {
		 	$li.='<li ><a href="#" class="select_contract" id="'.$contract_name['Contract']['id'].'">'.$contract_name['Contract']['contract_title'].'</a></li>';
		 	
		 }


		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	}

		public function serch_project_active(){

		$this->loadModel('Project');
		$data = $this->request->data;
         $li = '';
        if($data !=''){

         	$search_projct_name = $this->Project->find("all",
         		array('recursive'=>'-1',
         			'fields'=>array('Project.project_title','Project.id'),
         			'conditions'=>array('Project.status'=>1,
         				'OR' => array('Project.project_title LIKE' => '%' . $data['search_key'] . '%',
									'Project.project_id LIKE' => '%' . $data['search_key'] . '%')
         				
         			)));
         }
		
		//pre($search_entity_name) ;die();
		foreach($search_projct_name as $k=>$pro_name)
		 {
		 	 $li.='<li ><a href="#" class="select_project" id="'.$pro_name['Project']['id'].'">'.$pro_name['Project']['project_title'].'</a></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	}

	function projects()
	{
		$conditions= array();
		$sesn   = $this->Session->read('admin');
		$userid = $sesn['Admin']['AppUser']['id'];
		$this->loadModel('Project');
		$this->loadModel('AppUser');
		$this->loadModel('Entitie');
		$this->loadModel('BusinessLine');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('ProjectTask');
		$this->loadModel('Pricing');
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Customer')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
		//pre($excess_permission); die;

		$data = $this->request->data;
		$short = array('Project.id'=>'DESC');
				
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Project.project_title'=>$shortVal);
		}


		if(isset($data['search_key']) && !empty($data['search_key'])){
			
			$conditions = array_merge($conditions,array('OR'=>array('Project.project_title LIKE'=>'%'.$data['search_key'].'%')));

		}

		if(isset($data['projectStart']) && !empty($data['projectStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['projectStart']));
			$conditions = array_merge($conditions,array('Project.start_date >= '=>$begin_data));
			
		}

		if( isset($data['projectEnd']) && !empty($data['projectEnd']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['projectEnd']));
		 	$conditions = array_merge($conditions,array('Project.start_date <= '=>$close_date));
		 	
		 }


      /***Data Filter By Graph In Project Vertical Wise Revenue***/
      if(isset($data['veticalWiseRevenue']) && !empty($data['veticalWiseRevenue'])){
      	 $condition = array_merge($conditions,array('Project.business_line'=>$data['veticalWiseRevenue']));
      }



		 if(isset($data['taskStart']) && !empty($data['taskStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['taskStart']));
			$allTaskStart = $this->ProjectTask->find('list',array('group'=>'ProjectTask.project_id','fields'=>array('ProjectTask.id','ProjectTask.project_id'),'conditions'=>array('ProjectTask.task_start_date >='=>$begin_data)));

			$conditions = array_merge($conditions,array('Project.id '=>$allTaskStart));
			
		}

		if(isset($data['taskEnd']) && !empty($data['taskEnd'])) {
			$begin_data = date('Y-m-d',strtotime($data['taskEnd']));
			$allTaskEnd = $this->ProjectTask->find('list',array('group'=>'ProjectTask.project_id','fields'=>array('ProjectTask.id','ProjectTask.project_id'),'conditions'=>array('ProjectTask.task_start_date <='=>$begin_data)));

			$conditions = array_merge($conditions,array('Project.id '=>$allTaskEnd));
			
		}

		if(isset($data['BillingType']) && !empty($data['BillingType'])) {
		 	
		$allPrice = $this->Pricing->find('list',array('group'=>'Pricing.task_id','fields'=>array('Pricing.id','Pricing.task_id'),'conditions'=>array('Pricing.billing_type'=>$data['BillingType'])));
		$allTask = $this->ProjectTask->find('list',array('group'=>'ProjectTask.project_id','fields'=>array('ProjectTask.id','ProjectTask.project_id'),'conditions'=>array('ProjectTask.id'=>$allPrice)));

		$conditions = array_merge($conditions,array('Project.id '=>$allTask));
		
		}

		if(isset($data['projectType']) && !empty($data['projectType']))
		{
			$conditions = array_merge($conditions,array('Project.project_type'=>$data['projectType']));
		}

		if(isset($data['verticals']) && !empty($data['verticals']))
		{
			$conditions = array_merge($conditions,array('Project.business_line'=>$data['verticals']));
		}


	//	$conditions = array_merge($conditions,array('OR' => array('Project.status'=>1)));
		$conditions = array_merge($conditions,array('Project.status'=>1));


		$this->paginate = array('limit'=>20,'order'=>$short,'group'=>'Project.id','field'=>array('Project.*'),'conditions'=>array($conditions));	   
		
		$project_data = $this->paginate('Project');

		$project_active = $this->Project->find('count',array('group'=>'Project.id','conditions'=>array($conditions)));
		

	//	$project_active = $this->Project->find('count',array('group'=>'Project.id','conditions'=>array('Project.status'=>1)));
		$project_inactive = $this->Project->find('count',array('group'=>'Project.id','conditions'=>array('Project.status'=>0)));


		///   **************   Manohar Code for Filters Start  ***************************** ///

			$filterBusinessLine = $this->BusinessLine->find('all',array('fields'=>array('BusinessLine.id','BusinessLine.bl_name','BusinessLine.entity_id'),'group' => array('BusinessLine.bl_name'),'conditions'=>array('BusinessLine.is_active'=>1)));

			$filterBillingType=$this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'billing_type','MasterDataDetail.is_active'=>1),'group'=>array('MasterDataDetail.master_data_desc')));

			$this->set(compact('filterBillingType','filterBusinessLine'));
			//pre($filterBillingType);die;


		///   **************   Manohar Code for Filters End   ***************************** ///
		
		$this->set('project_active',$project_active);
		$this->set('project_inactive',$project_inactive);
		$this->set('excess_permission',$excess_permission);
		if($this->RequestHandler->isAjax())
             {
                
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($project_data))
                {
                    //pre($data);die;
                        $view->set('project_detail',$project_data);
                        $html['html'] = $view->render("projects");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                        $html['project_active'] =$project_active;
                }
                else
                {
                        $html['message'] ='error';
                        $html['project_active'] =$project_active;
                }

                echo json_encode($html);die;

            }
                $this->set('project_detail',$project_data);
		//pre($project_data);die;
	}
	function project_completed()
	{
	}
	function project_pending()
	{
		$conditions= array();
		$sesn   = $this->Session->read('admin');
		$userid = $sesn['Admin']['AppUser']['id'];
		$this->loadModel('Project');
		$this->loadModel('AppUser');
		$this->loadModel('Entitie');
		$this->loadModel('Pricing');
		$this->loadModel('BusinessLine');
		$this->loadModel('MasterDataDetail');
		$data = $this->request->data;
		$short = array('Project.id'=>'DESC');				
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Project.project_title'=>$shortVal);
		}

		if(isset($data['search_key']) && !empty($data['search_key'])){
			
			$conditions = array_merge($conditions,array('OR'=>array('Project.project_title LIKE'=>'%'.$data['search_key'].'%')));
		}
		if(isset($data['projectStart']) && !empty($data['projectStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['projectStart']));
			$conditions = array_merge($conditions,array('Project.start_date >= '=>$begin_data));
			
		}

		if( isset($data['projectEnd']) && !empty($data['projectEnd']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['projectEnd']));
		 	$conditions = array_merge($conditions,array('Project.start_date <= '=>$close_date));
		 	
		 }

		 if(isset($data['taskStart']) && !empty($data['taskStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['taskStart']));
			$allTaskStart = $this->ProjectTask->find('list',array('group'=>'ProjectTask.project_id','fields'=>array('ProjectTask.id','ProjectTask.project_id'),'conditions'=>array('ProjectTask.task_start_date >='=>$begin_data)));

			$conditions = array_merge($conditions,array('Project.id '=>$allTaskStart));
			
		}

		if(isset($data['taskEnd']) && !empty($data['taskEnd'])) {
			$begin_data = date('Y-m-d',strtotime($data['taskEnd']));
			$allTaskEnd = $this->ProjectTask->find('list',array('group'=>'ProjectTask.project_id','fields'=>array('ProjectTask.id','ProjectTask.project_id'),'conditions'=>array('ProjectTask.task_start_date <='=>$begin_data)));

			$conditions = array_merge($conditions,array('Project.id '=>$allTaskEnd));
			
		}

		if(isset($data['BillingType']) && !empty($data['BillingType'])) {
		 	
		$allPrice = $this->Pricing->find('list',array('group'=>'Pricing.task_id','fields'=>array('Pricing.id','Pricing.task_id'),'conditions'=>array('Pricing.billing_type'=>$data['BillingType'])));
		$allTask = $this->ProjectTask->find('list',array('group'=>'ProjectTask.project_id','fields'=>array('ProjectTask.id','ProjectTask.project_id'),'conditions'=>array('ProjectTask.id'=>$allPrice)));

		$conditions = array_merge($conditions,array('Project.id '=>$allTask));
		
		}

		if(isset($data['projectType']) && !empty($data['projectType']))
		{
			$conditions = array_merge($conditions,array('Project.project_type'=>$data['projectType']));
		}

		if(isset($data['verticals']) && !empty($data['verticals']))
		{
			$conditions = array_merge($conditions,array('Project.business_line'=>$data['verticals']));
		}	
		$reporting_id = $this->AppUser->find('all',array('fields'=>array('AppUser.id'),'conditions'=>array('AppUser.reporting_manager'=>$userid)));
						 
		 $reporting_ids=array();
		 foreach ($reporting_id as $key => $reporting_manager_id) {
		 	array_push($reporting_ids,$reporting_manager_id['AppUser']['id']);		 	
		 }
		 //
		 array_push($reporting_ids,$userid);    
		//$conditions = array_merge($conditions,array('Project.status '=>array(0,2,3),'Project.created_by'=>$reporting_ids));

		$conditions = array_merge($conditions,array('AND'=>array('OR'=>array(
			array('Project.created_by'=>$reporting_ids),array('Project.project_mgr_id'=>$userid)))));

		//$conditions = array_merge($conditions, ));

		$this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>$short,'group'=>'Project.id','fields'=>array('Project.id','Project.project_title','ProjectTask.id','Project.creation_dttm','Project.start_date','Project.initial_end_date','Project.project_type','Project.project_value','Project.project_mgr_id','Project.subvertical','Project.created_by','Entitie.entitiy_name','Subvertical.sv_name','Project.created_date','Project.creation_dttm','Project.status'),'conditions'=>array('Project.status '=>array(0,2,3),$conditions));	
		//$this->paginate = array('limit'=>15,'order'=>'Project.id desc','group'=>'Project.id','fields'=>array(),'conditions'=>array($conditions));	     
		$project_pending_data = $this->paginate('Project');
		//pre($project_pending_data); die;		

		$project_rejected = $this->Project->find('count',array('recursive'=>-1, 'group'=>'Project.id','conditions'=>array('Project.status'=>3,$conditions)));
		$project_inactive = $this->Project->find('count',array('recursive'=>-1,'group'=>'Project.id','conditions'=>array('Project.status'=>0,$conditions)));
		$project_draft = $this->Project->find('count',array('recursive'=>-1,'group'=>'Project.id','conditions'=>array('Project.status'=>2,$conditions)));

		$project_rejected = $project_rejected != ''? $project_rejected:0;
		$project_inactive = $project_inactive != ''? $project_inactive:0;
		$project_draft = $project_draft != ''? $project_draft:0;

		///   **************   Manohar Code for Filters Start  ***************************** ///
			$filterBusinessLine = $this->BusinessLine->find('all',array('fields'=>array('BusinessLine.id','BusinessLine.bl_name','BusinessLine.entity_id'),'group' => array('BusinessLine.bl_name'),'conditions'=>array('BusinessLine.is_active'=>1)));
			$filterBillingType=$this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'billing_type','MasterDataDetail.is_active'=>1),'group'=>array('MasterDataDetail.master_data_desc')));
			$this->set(compact('filterBillingType','filterBusinessLine'));
			//pre($filterBillingType);die;
		///   **************   Manohar Code for Filters End   ***************************** ///
		
		$this->set('project_rejected',$project_rejected);
		$this->set('project_inactive',$project_inactive);
		$this->set('project_draft',$project_draft);
		$this->set('userid',$userid);
		if($this->RequestHandler->isAjax())
             {
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($project_pending_data))
                {
                        $view->set('project_pending_data',$project_pending_data);
                        $view->set('userid',$userid);
                        $html['html'] = $view->render("project_pending_element");
                       $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                        $html['project_rejected'] =$project_rejected;
                        $html['project_inactive'] =$project_inactive;
                        $html['project_draft'] =$project_draft;
                }
                else
                {
                        $html['message'] ='error';
                        $html['project_rejected'] =$project_rejected;
                        $html['project_inactive'] =$project_inactive;
                        $html['project_draft'] =$project_draft;
                }

                echo json_encode($html);die;

            }
            $this->set('project_pending_data',$project_pending_data);
		//pre($project_pending_data);die;
	}
	public function serch_project_pending(){

		$this->loadModel('Project');
		$data = $this->request->data;
         $li = '';
        if($data !=''){

         	$search_projct_name = $this->Project->find("all",
         		array('recursive'=>'-1',
         			'fields'=>array('Project.project_title','Project.id'),
         			'conditions'=>array('Project.status '=>array(0,2,3),
         				'OR' => array('Project.project_title LIKE' => '%' . $data['search_key'] . '%')
         			)));
         }
		
		//pre($search_entity_name) ;die();
		foreach($search_projct_name as $k=>$pro_name)
		 {
		 	 $li.='<li ><a href="#" class="select_project" id="'.$pro_name['Project']['id'].'">'.$pro_name['Project']['project_title'].'</a></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	}



	function opt_verification()
	{		
	}
	function reset_password()
	{		
	}
	function dashboard()
	{
	  $this->loadModel('Entitie');
	  $this->loadModel('Contract');
	  $this->loadModel('Project');
	  $this->loadModel('UserPermission');
      /**********************Starts Here Code Used For Active Customer Count******************/
          $totalCustomerCount=$this->Entitie->find('all',
          array('fields'=>array('Entitie.id'), 
          ));
          $this->set("totalCustomerCount",$totalCustomerCount);
       /*********************End Here Code Used For Active Customer Count*********************/
      
       /****************Starts Here Code Used For Active Contract Count****************/
          $totalContractCount=$this->Contract->find('all',
          array('fields'=>array('Contract.id'), 
          ));
          $this->set("totalContractCount",$totalContractCount);
       /****************End Here Code Used For Active Contract Count****************/

       /*****************Starts Here Code Used For Active Project Count*************/
          $totalProjectCount=$this->Project->find('all',
          array('fields'=>array('Project.id'),
          ));
          $this->set("totalProjectCount",$totalProjectCount);
       /****************End Here Code Used For Active Project Count*****************/

      /****************Getting Filter Values****************/
      if($this->request->is('post')){
      	 $dropdownFilter=$this->request->data['dropdownFilter'];
      	 $conditionArray=array();
      	 $conditionArray1=array();
      	 $conditionArray2=array();
      	 $topLocationextra=array();
         /**Date Range Code**/
      	 $filterSelectValue=0;
      	 if($dropdownFilter==1){
      	 	$currentMonth=date("m");
      	 	$currentYear=date("Y");
      	 	$filterSelectValue=1;
      	 	$conditionArray[]= "DATE_FORMAT(contracts.contract_start_dt,'%m')=".$currentMonth." && DATE_FORMAT(contracts.contract_start_dt,'%Y')=".$currentYear;
      	 	$conditionArray1[]= "DATE_FORMAT(entities.`created_date`,'%m')=".$currentMonth." && DATE_FORMAT(entities.`created_date`,'%Y')=".$currentYear;
      	 	$conditionArray2[]= "DATE_FORMAT(contracts.contract_end_dt,'%m')=".$currentMonth." && DATE_FORMAT(contracts.contract_end_dt,'%Y')=".$currentYear;
      	 }else if($dropdownFilter==2){
      	 	$filterSelectValue=2;
      	 	$quarterMonthArray=array(date("Y-m",strtotime("-1 month")),date("Y-m"),date("Y-m",strtotime("+1 month")));
      	 	$conditionArray[]="DATE_FORMAT(contracts.contract_start_dt,'%Y-%m') IN (".implode(",",$quarterMonthArray).")";
      	 	$conditionArray1[]="DATE_FORMAT(entities.`created_date`,'%Y-%m') IN (".implode(",",$quarterMonthArray).")";
      	 	$conditionArray2[]="DATE_FORMAT(contracts.contract_end_dt,'%Y-%m') IN (".implode(",",$quarterMonthArray).")";
      	 }else if($dropdownFilter==3){
      	 	$filterSelectValue=3;
      	 	$startDate=date('Y-04-1',strtotime('-1 year'));
      	    $endDate=date('Y-03-31');
      	    $conditionArray[]="contracts.contract_start_dt >='".$startDate."' && contracts.contract_start_dt <='".$endDate."'";
      	    $conditionArray1[]="entities.`created_date` >='".$startDate."' && entities.`created_date` <='".$endDate."'";
      	    $conditionArray2[]="contracts.contract_end_dt >='".$startDate."' && contracts.contract_end_dt <='".$endDate."'";
      	 }
      	/**Custom Date Range Code**/ 
      	if(!empty($this->request->data['stratDate'])&& !empty($this->request->data['endDate'])){
      		  $startDate=$this->request->data['stratDate'];
      		  $endDate=$this->request->data['endDate'];
      		  $conditionArray[]="DATE_FORMAT(contracts.contract_start_dt,'%Y/%m') >='".$startDate."' && DATE_FORMAT(contracts.contract_start_dt,'%Y/%m') <='".$endDate."'";
      	      $conditionArray1[]="DATE_FORMAT(entities.`created_date`,'%Y/%m') >='".$startDate."' && DATE_FORMAT(entities.`created_date`,'%Y/%m') <='".$endDate."'";
      	      $conditionArray2[]="DATE_FORMAT(contracts.contract_end_dt,'%Y/%m') >='".$startDate."' && DATE_FORMAT(contracts.contract_end_dt,'%Y/%m') <='".$endDate."'";
      	}
      }else{
      	$filterSelectValue=3;
      	$startDate=date('Y-04-1',strtotime('-1 year'));
      	$endDate=date('Y-03-31');
      	$conditionArray[]="contracts.contract_start_dt >='".$startDate."' && contracts.contract_start_dt <='".$endDate."'";
      	$conditionArray1[]="entities.`created_date` >='".$startDate."' && entities.`created_date` <='".$endDate."'";
      	$conditionArray2[]="contracts.contract_end_dt >='".$startDate."' && contracts.contract_end_dt <='".$endDate."'";
      	$topLocationextra[]=array("entities.`created_date` >="=>$startDate,"entities.`created_date` <="=>$endDate);
      }
      


      /*****************************************************************/
        /*Start Here Code Used For Top Ten Contracts Count*/
      /*****************************************************************/
	   $topTenContract=$this->Contract->query("SELECT MAX(contracts.tot_ctrct_value) AS Maxtot_ctrct_value,contracts.`contract_title`,contracts.contract_start_dt,contracts.status,contracts.id AS id FROM contracts  WHERE ".implode("",$conditionArray)." && contracts.status=1 GROUP BY contracts.id ORDER BY Maxtot_ctrct_value DESC LIMIT 10");	  

	  /*****************************************************************/
        /*End Here Code Used For Top Ten Contracts Count*/
      /*****************************************************************/

      /*****************************************************************/
        /*Start Here Code Used For Top Ten Coustomers Count*/
      /*****************************************************************/
	  $topTenCustomer=$this->Contract->query("SELECT COUNT(contracts.cust_entity_id) AS MAXCOUNT, SUM(contracts.`tot_ctrct_value`) AS Amount ,contracts.cust_entity_id,contracts.contract_start_dt,contracts.status,entities.`entitiy_name`,contracts.id AS contract_id FROM contracts INNER JOIN entities ON entities.id=contracts.`cust_entity_id` WHERE ".implode("",$conditionArray)." && contracts.status=1 && entities.`status` LIKE '%Active%' GROUP BY contracts.`cust_entity_id` ORDER BY MAXCOUNT DESC LIMIT 10");

	  /*****************************************************************/
        /*End Here Code Used For Top Ten Coustomers Count*/
      /*****************************************************************/

      /*****************************************************************/
        /*Start Here Code Used For Top Ten Location Count*/
      /*****************************************************************/
      $topTenLocation=array();
      $coustomerCount=$this->Contract->query("SELECT entities.id , entities.`entitiy_name`,entity_addresses.`city`, COUNT(entity_addresses.city) AS coustomerCount , entities.`created_date` FROM entities INNER JOIN entity_addresses ON entities.`id`=entity_addresses.`entity_id` WHERE ".implode("",$conditionArray1)." GROUP BY entity_addresses.city ORDER BY coustomerCount DESC LIMIT 10");
      for($i=0;$i<sizeof($coustomerCount);++$i){
        $contractCount=$this->Contract->query("SELECT entity_addresses.`city`,entity_addresses.id,COUNT(entity_addresses.city) AS contractCount, contracts.contract_start_dt FROM entity_addresses INNER JOIN contracts ON entity_addresses.id=contracts.`bill_to_address_id` WHERE ".implode("",$conditionArray)." && entity_addresses.`city` LIKE '%".$coustomerCount[$i]['entity_addresses']['city']."%'");
      	$topTenLocation[]=array("location"=>$coustomerCount[$i]['entity_addresses']['city']
      	,"customer_count"=>$coustomerCount[$i][0]['coustomerCount'],"contract_count"=>$contractCount[0][0]['contractCount']);
      }  
      
      /*****************************************************************/
        /*End Here Code Used For Top Ten Location Count*/
      /*****************************************************************/

      /*****************************************************************/
        /*Start Here Code Used For Contract Expiring Count*/
      /*****************************************************************/
     
      $contractsExpiring=$this->Contract->query("SELECT contracts.`contract_title`,COUNT(contracts.contract_end_dt) AS countExpringContract,SUM(contracts.`tot_ctrct_value`) AS expiringContractValue,contracts.id,DATE_FORMAT(DATE(contracts.contract_end_dt),' %M') AS csdate ,DATE_FORMAT(DATE(contracts.contract_end_dt),' %Y') AS csYear, contracts.status FROM contracts WHERE ".implode("",$conditionArray2)." && contracts.status=1 GROUP BY csdate");
        
      
      /*****************************************************************/
        /*End Here Code Used For Top Contract Expiring Count*/
      /*****************************************************************/ 

      /*****************************************************************/
        /*Start Here Code Used For Fiscal Year*/
      /*****************************************************************/
      $currentYear=date('Y',strtotime($endDate));
      $fiveYearBack=date('Y',strtotime($endDate))-5;
      $currentUserSession=$this->Session->read();
      $app_user_id=$currentUserSession['admin']['Admin']['AppUser']['id'];
      $user_var_sub_data=$this->UserPermission->find('all',array('recursive'=>'-1','field'=>array('UserPermission.business_line_id','UserPermission.subvertical_id'),'conditions'=>array('UserPermission.app_user_id'=>$app_user_id)));
      $businessLineIds=array();
      for($f=0;$f<sizeof($user_var_sub_data);++$f){
      	  $businessLineIds[]=$user_var_sub_data[$f]['UserPermission']['business_line_id'];
      }
      $fascialYear=$this->Project->query("SELECT SUM(invoices.invoice_amount) AS amountBusinessLine, business_lines.bl_name, (invoices.`fiscal_year`-1) AS prevYear,invoices.`fiscal_year` FROM projects LEFT JOIN business_lines ON business_lines.`id`=projects.`business_line` LEFT JOIN invoices ON invoices.entity_id=projects.customer_entity_id  WHERE invoices.`fiscal_year`>'".$fiveYearBack."' && invoices.fiscal_year<='".$currentYear."' && business_lines.`id` IN (".implode(",",$businessLineIds).") GROUP BY invoices.`fiscal_year`");
      $showingfascialYear=array();
      $showingfascialyearGraphtags=array();
      for($i=0;$i<sizeof($fascialYear);++$i)
      {
      	$showingfascialyearGraphtags[]=array("graphtags"=>$fascialYear[$i]['business_lines']['bl_name']);
      	$showingfascialYear[]=array("year_range"=>$fascialYear[$i][0]['prevYear']."-".$fascialYear[$i]['invoices']['fiscal_year'],"".$fascialYear[$i]['business_lines']['bl_name'].""=>$fascialYear[$i][0]['amountBusinessLine']);
      }

      /*****************************************************************/
        /*End Here Code Used For Fiscal Year*/
      /*****************************************************************/
      $this->set("topLocationextra",$topLocationextra);
      $this->set("showingfascialyearGraphtags",$showingfascialyearGraphtags);
      $this->set("showingfascialYear",$showingfascialYear);
      $this->set("contractsExpiring",$contractsExpiring);
      $this->set("topTenLocation",$topTenLocation);	
	  $this->set("topTenContract",$topTenContract);	  	
	  $this->set("topTenCustomer",$topTenCustomer);
	  $this->set("filterSelectValue",$filterSelectValue);
	}
	
	function create_group()
	{
		$this->loadModel('Group');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
		$data = $this->request->data;
		$date = date('Y-m-d');
        
		$update_group['Group']['group_name']  =  $data['newGroup'];
		$update_group['Group']['created_date'] = date('Y-m-d H:i:s');
		$update_group['Group']['created_by'] = $userid;
		$this->Group->save($update_group);

		$group_detail = $this->Group->find('all',array('recursive'=>'-1','fields'=>array('Group.id','Group.group_name')));

		foreach($group_detail as $k=>$group_details)
		 {		 
			$selc='';
		 	$optionspo.='<option '.$selc.' value="'.$group_details['Group']['id'].'">'.$group_details['Group']['group_name'].'</option>';
		 }
		 $msg['optionspo']=	$optionspo;
		echo json_encode($msg); die;  
		
	}
	function create_payment_terms()
	{
		$this->loadModel('PaymentTerm');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
		$data = $this->request->data;
		//pre($data); die;
		$update_payment_terms['PaymentTerm']['payment_terms'] =  $data['abbreviation'];
		$update_payment_terms['PaymentTerm']['description'] = $data['description'];
		$update_payment_terms['PaymentTerm']['created_by'] = $userid;
		$update_payment_terms['PaymentTerm']['created_date'] = date('Y-m-d H:i:s');
		$update_payment_terms['PaymentTerm']['is_active'] = 1;
		$this->PaymentTerm->save($update_payment_terms);

		$payment_term = $this->PaymentTerm->find('all',array('recursive'=>'-1','fields'=>array('PaymentTerm.id','PaymentTerm.payment_terms','PaymentTerm.description')));

		foreach($payment_term as $k=>$payment_terms)
		 {		 
			$selc='';
		 	$optionspo.='<option '.$selc.' value="'.$payment_terms['PaymentTerm']['id'].'">'.$payment_terms['PaymentTerm']['payment_terms'].'</option>';
		 }
		 $msg['optionspo']=	$optionspo;
		echo json_encode($msg); die; 		
		
	}
	function cmschart()
	{
		$this->loadModel('GraphPeriod');
		$this->loadModel('AppUser');
		$this->loadModel('GraphCollection');
		$this->loadModel('Invoice');
		$this->loadModel('Entity');
		$this->loadModel('Contract');
		$this->loadModel('Project');
		$totalCust = $this->Entity->find('count');
		$this->set('totalCust',$totalCust);
		$totalCont = $this->Contract->find('count');
		$this->set('totalCont',$totalCont);
		$totalProject = $this->Project->find('count');
		$this->set('totalProject',$totalProject);
		/************************AR Ageing Report Graph********************************/
		$arAreport=array();$arAreportCr=array();
		$periods=array(30,60,90,120,150,180,270,360,361);
		$filtrdate=@$_GET['date'];
		if($filtrdate!='')
		{ 
	
			$gexp=explode('-',$filtrdate);
			if(@$gexp[2]=='')
			{
				$pday=$this->getMonthDate($gexp[1]);
				$filtrdate=$filtrdate.'-'.$pday;
			}
			$currentDate=$filtrdate;
		}
		else
		{
			$currentDate=date("Y-m-d");
		}
		$collectionGrapgmnth=date('Y-m-01');
		if($filtrdate!='')
		$collectionGrapgmnth=$filtrdate;
		foreach($periods as $k=>$period)
		{   
			$oneYearAgo = date('Y-m-d',strtotime($currentDate . " - 365 day"));
			$quarterMonth = date("Y-m", strtotime($currentDate ."-3 months"));
			$qm=explode('-',$quarterMonth);
			$day=$this->getMonthDate($qm[1]);
			$quarterDate=$quarterMonth.'-'.$day;
			
			$prvMonth = date("Y-m", strtotime($currentDate . "-1 months"));
			$prv=explode('-',$prvMonth);
			$pday=$this->getMonthDate($prv[1]);
			$prvDate=$prvMonth.'-'.$pday;
			$thrtySMLY = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$oneYearAgo)));
			if(@$thrtySMLY[0]['total']=='')
			$thrtySMLY[0]['total']=0;
			$thrtySMLY=$thrtySMLY[0]['total'];
			$thrtySMLYcr = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$oneYearAgo)));
			if(@$thrtySMLYcr[0]['total']=='')
			$thrtySMLYcr[0]['total']=0;
			$thrtySMLYcr=$thrtySMLYcr[0]['total'];
			
			$quater = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$quarterDate)));
			if(@$quater[0]['total']=='')
			$quater[0]['total']=0;
			$quater=$quater[0]['total'];
			$quatercr = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$quarterDate)));
			if(@$quatercr[0]['total']=='')
			$quatercr[0]['total']=0;
			$quatercr=$quatercr[0]['total'];
			
			$prvs = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$prvDate)));
			if(@$prvs[0]['total']=='')
			$prvs[0]['total']=0;
			$prvs=$prvs[0]['total'];
			$prvscr = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$prvDate)));
			if(@$prvscr[0]['total']=='')
			$prvscr[0]['total']=0;
			$prvscr=$prvscr[0]['total'];
			
			$curent = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$currentDate)));
			
			if(@$curent[0]['total']=='')
			$curent[0]['total']=0;
			$curent=$curent[0]['total'];
			$curentcr = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.period'=>$period,'GraphPeriod.created_date'=>$currentDate)));
			
			if(@$curentcr[0]['total']=='')
			$curentcr[0]['total']=0;
			$curentcr=$curentcr[0]['total'];
			$netcurr=$thrtySMLY-$curent;
			if($netcurr==0 || $netcurr<0)
			{
				$netcurval='greenimg';
			}
			else
			{
				$netc=($netcurr*100)/$thrtySMLY;
				if($netc<10)
				$netcurval='orangeimg';
				else if($netc>10)
				$netcurval='';
				if($netc<0)
				$netcurval='greenimg';
			}
			
			$netqrtr=$thrtySMLY-$quater;
			if($netqrtr==0 || $netqrtr<0)
			{
				$netqrtrval='greenimg';
			}
			else
			{
				$netq=($netqrtr*100)/$thrtySMLY;
				if($netq<10)
				$netqrtrval='orangeimg';
				else if($netq>10)
				$netqrtrval='';
				if($netq<0)
				$netqrtrval='greenimg';
			}
			$netprv=$thrtySMLY-$prvs;
			if($netprv==0 || $netprv<0)
			{
				$netprvval='greenimg';
			}
			else
			{
				$netp=($netprv*100)/$thrtySMLY;
				if($netp<10)
				$netprvval='orangeimg';
				else if($netp>10)
				$netprvval='';
				if($netp<0)
				$netprvval='greenimg';
			}
			$arAreport[$period]['as_date']=number_format($curent);
			$arAreport[$period]['as_date_flag']=$netcurval;
			$arAreport[$period]['prv_date']=number_format($prvs);
			$arAreport[$period]['prv_date_flag']=$netprvval;
			$arAreport[$period]['qtr_date']=number_format($quater);
			$arAreport[$period]['qtr_date_flag']=$netqrtrval;
			$arAreport[$period]['lastyr']=number_format($thrtySMLY);
			
			$netcurrCr=$thrtySMLYcr-$curentcr;
			if($netcurrCr==0 || $netcurrCr<0)
			{
				$netcurvalcr='greenimg';
			}
			else
			{
				$netcr=($netcurrCr*100)/$thrtySMLYcr;
				if($netcr<10)
				$netcurvalcr='orangeimg';
				else if($netcr>10)
				$netcurvalcr='';
				if($netcr<0)
				$netcurvalcr='greenimg';
			}
			
			$netqrtrcr=$thrtySMLYcr-$quatercr;
			if($netqrtrcr==0 || $netqrtrcr<0)
			{
				$netqrtrvalcr='greenimg';
			}
			else
			{
				$netqcr=($netqrtrcr*100)/$thrtySMLYcr;
				if($netqcr<10)
				$netqrtrvalcr='orangeimg';
				else if($netqcr>10)
				$netqrtrvalcr='';
				if($netqcr<0)
				$netqrtrvalcr='greenimg';
			}
			$netprvcr=$thrtySMLYcr-$prvscr;
			if($netprvcr==0 || $netprvcr<0)
			{
				$netprvvalcr='greenimg';
			}
			else
			{
				$netpcr=($netprvcr*100)/$thrtySMLYcr;
				if($netpcr<10)
				$netprvvalcr='orangeimg';
				else if($netpcr>10)
				$netprvvalcr='';
				if($netpcr<0)
				$netprvvalcr='greenimg';
			}
			$arAreportCr[$period]['as_date']=number_format($curentcr);
			$arAreportCr[$period]['as_date_flag']=$netcurvalcr;
			$arAreportCr[$period]['prv_date']=number_format($prvscr);
			$arAreportCr[$period]['prv_date_flag']=$netprvvalcr;
			$arAreportCr[$period]['qtr_date']=number_format($quatercr);
			$arAreportCr[$period]['qtr_date_flag']=$netqrtrvalcr;
			$arAreportCr[$period]['lastyr']=number_format($thrtySMLYcr);
		}
		$this->set('arAreport',$arAreport);
		$this->set('arAreportCr',$arAreportCr);
		/************************AR Ageing Report Graph End***************************/
		/************************Collections Graph********************************/
		$months=array();
		for ($i = 0; $i < 6; $i++) 
		{
			$date=date("Y-m", strtotime( $collectionGrapgmnth." -$i months"));
			$month=date("m", strtotime( $collectionGrapgmnth." -$i months"));
			$day=$this->getMonthDate($month);
		   $months[] = $date.'-'.$day;
		}
		$months[0]=$currentDate; 
		$mntharray=array();
		foreach($months as $k=>$month)
		{
			$mnt=date("M y", strtotime($month));
			$mnt1=date("Y-m", strtotime($month));
			$strtdate=$mnt1.'-'.'01';
			$collectionmnth=$this->GraphCollection->find('first',array('fields'=>array('SUM(GraphCollection.total_paid) as startmnth'),'conditions'=>array('GraphCollection.created_date'=>$month)));
			if($collectionmnth[0]['startmnth']=='')
			$collectionmnth[0]['startmnth']=0;
			
			$totl_billing=$this->Invoice->find('first',array('fields'=>array('SUM(Invoice.invoice_amount) as billing'),'conditions'=>array('Invoice.invoice_date >='=>$strtdate,'Invoice.invoice_date <='=>$month)));
			if($totl_billing[0]['billing']=='')
			$totl_billing[0]['billing']=0;
			$closingarpaid=$this->GraphCollection->find('first',array('fields'=>array('SUM(GraphCollection.total_paid) as paid'),'conditions'=>array('GraphCollection.created_date'=>$month)));
			$closingarunpaid=$this->GraphCollection->find('first',array('fields'=>array('SUM(GraphCollection.total_unpaid) as unpaid'),'conditions'=>array('GraphCollection.created_date'=>$month)));
			$closingar=$closingarunpaid[0]['unpaid']-$closingarpaid[0]['paid'];
			$openingar=$this->GraphCollection->find('first',array('fields'=>array('SUM(GraphCollection.total_unpaid) as unpaid'),'conditions'=>array('GraphCollection.created_date'=>$strtdate)));
			if($openingar[0]['unpaid']=='')
			$openingar[0]['unpaid']=0;
			
			$mntharray[]="['".$mnt."',".round($collectionmnth[0]['startmnth']).",'".round($collectionmnth[0]['startmnth'])."',".round($openingar[0]['unpaid']).",'".round($openingar[0]['unpaid'])."',".round($totl_billing[0]['billing']).",'".round($totl_billing[0]['billing'])."',".round($closingar).",'".round($closingar)."']";
			
		}
		$collectiongraph=implode(',',$mntharray);
		$this->set('collectiongraph',$collectiongraph);
		/************************Collections Graph End********************************/
		$busnsline = $this->GraphPeriod->find('list',array('group'=>'GraphPeriod.business_line','fields'=>array('GraphPeriod.business_line')));
		$arAging=array();
		foreach($busnsline as $bl)
		{
			$isvalAR=0;$Iscreditag=0;$agingDate=array();$cragingDate=array();
			$thrty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>30,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($thrty) && $thrty[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($thrty[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
			}
			$crthrty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>30,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crthrty) && $crthrty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crthrty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$sixty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>60,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($sixty)&& $sixty[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($sixty[0]['total']);
				//$join.=$val.','.'$<div style="font-size:15px; color:#3693cf; text-align:center;"><strong>'.$val.'"</strong></div>$'.','; 
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
			}
			$crsixty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>60,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crsixty) && $crsixty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crsixty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$ninty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>90,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($ninty)&& $ninty[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($ninty[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
			}
			$crninty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>90,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crninty) && $crninty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crninty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$onetwenty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>120,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($onetwenty)&& $onetwenty[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($onetwenty[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
			}
			$crtwenty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>120,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crtwenty) && $crtwenty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crtwenty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$onefifty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>150,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($onefifty)&& $onefifty[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($onefifty[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
			}
			$crfifty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>150,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crfifty) && $crfifty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crfifty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$oneety = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>180,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($oneety)&& $oneety[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($oneety[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
			}
			$creti = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>180,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crfifty) && $crfifty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crfifty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$twoseventy = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>270,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($twoseventy)&& $twoseventy[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($twoseventy[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
			}
			$crtwosevnty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>270,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crtwosevnty) && $crtwosevnty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crtwosevnty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$treesisty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>360,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($treesisty)&& $treesisty[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($treesisty[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				$agingDate[]=$val;
				//$join.='0'.','.'$<div style="font-size:15px; color:#3693cf; text-align:center;"><strong>"0"</strong></div>$'.',';
			}
			$crtrsixty = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>360,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crtrsixty) && $crtrsixty[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crtrsixty[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$above = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>0,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>361,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($above)&& $above[0]['total']>0)
			{
				$isvalAR=1;
				$val=round($above[0]['total']);
				$agingDate[]=$val;
			}
			else
			{
				$val=0; 
				
				$agingDate[]=$val;
			}
			$crabove = $this->GraphPeriod->find('first',array('order'=>'GraphPeriod.created_date desc','fields'=>array('SUM(GraphPeriod.un_paid) AS total'),'conditions'=>array('GraphPeriod.type'=>1,'GraphPeriod.business_line'=>$bl,'GraphPeriod.period'=>361,'GraphPeriod.created_date'=>$currentDate)));
			if(!empty($crabove) && $crabove[0]['total']>0)
			{
				$Iscreditag=1;
				$val=round($crabove[0]['total']);
				$cragingDate[]=$val;
			}
			else
			{
				$val=0; 
				$cragingDate[]=$val;
			}
			$agin=implode(',',$agingDate);
			$arAging[]='["'.$bl.'",'.$agin.']';
			
			$aginCr=implode(',',$cragingDate);
			$arCreditAging[]='["'.$bl.'",'.$aginCr.']';
		}
		$arAging=implode(',',$arAging);
		$arCreditAging=implode(',',$arCreditAging);
		$this->set('arCreditAging',$arCreditAging);
		$this->set('arAging',$arAging);
		
		//$collector = $this->AppUser->find('all',array('fields'=>array('AppUser.*'),'conditions'=>array('AppUser.role_id'=>3)));// pre($collector);die;		
		
	}
	function getMonthDate($month=null)
	{
		if($month==1)
		$day=31;
		else if($month==2)
		$day=28;
		else if($month==3)
		$day=31;
		else if($month==4)
		$day=30;
		else if($month==5)
		$day=31;
		else if($month==6)
		$day=30;
		else if($month==7)
		$day=31;
		else if($month==8)
		$day=31;
		else if($month==9)
		$day=30;
		else if($month==10)
		$day=31;
		else if($month==11)
		$day=30;
		else if($month==12)
		$day=31;
		return $day;
	}
	function create_rate_card()
	{
		$this->loadModel('PricingSlabTemp'); 
		$this->loadModel('PricingSlab');
		$this->loadModel('SvcCatalogue');
		$data = $this->request->data;
		if(!empty($data))
		{
			$serviceType=$data['service_type'];
			$serviesd = $this->SvcCatalogue->find('first',array('recursive'=>'-1','fields'=>array('SvcCatalogue.id','SvcCatalogue.svc_desc'),'conditions'=>array('SvcCatalogue.id'=>$serviceType)));
			$msg['service_name']=$serviesd['SvcCatalogue']['svc_desc'];
			$msg['service_id']=$serviceType;
			if($data['project_id']!='')
			{
				$ses=$this->Session->read('admin'); 
				$this->Session->write('ratecatrt_id', '');
				$this->PricingSlab->deleteAll(array("PricingSlab.service_id"=>$serviceType,"PricingSlab.project_id"=>$data['project_id']));
				$total=count($data['ratevalue']);
				$aaray=array();
				for($i=0;$i<$total;$i++)
				{
					$isempty=0;
					if($data['urange_from'][$i]=='' || $data['urange_to'][$i]=='' ||$data['ratevalue'][$i]=='')
					{
						$isempty=1;
					}
					if($isempty==0)
					{
						
						$save['PricingSlab']['project_id']=$data['project_id'];
						$save['PricingSlab']['service_id']=$serviceType;
						$save['PricingSlab']['start_units']=$data['urange_from'][$i];
						$save['PricingSlab']['end_units']=$data['urange_to'][$i];
						$save['PricingSlab']['per_unit_rate']=$data['ratevalue'][$i];
						$save['PricingSlab']['created_date']=date('Y-m-d H:i:s');
						$save['PricingSlab']['modified_date']=date('Y-m-d H:i:s');
						$save['PricingSlab']['created_by']=$ses['Admin']['AppUser']['id'];
						$save['PricingSlab']['modified_by']=$ses['Admin']['AppUser']['id'];
						$this->PricingSlab->create();
						$this->PricingSlab->save($save);
						
						$detl['urange_from']=$data['urange_from'][$i];
						$detl['urange_to']=$data['urange_to'][$i];
						$detl['ratevalue']=$data['ratevalue'][$i];
						$aaray[]=$detl;
					}
				}
			}
			else
			{
				$rate_id=$this->Session->read('ratecatrt_id');
				if($rate_id=='')
				{
					$this->Session->write('ratecatrt_id', time());
					$rate_id=$this->Session->read('ratecatrt_id');
				}
				
				$this->PricingSlabTemp->deleteAll(array("PricingSlabTemp.service_id"=>$serviceType,"PricingSlabTemp.temp_id"=>$rate_id));
				$total=count($data['ratevalue']);
				$aaray=array();
				for($i=0;$i<$total;$i++)
				{
					$isempty=0;
					if($data['urange_from'][$i]=='' || $data['urange_to'][$i]=='' ||$data['ratevalue'][$i]=='')
					{
						$isempty=1;
					}
					if($isempty==0)
					{
						
						$save['PricingSlabTemp']['temp_id']=$rate_id;
						$save['PricingSlabTemp']['service_id']=$serviceType;
						$save['PricingSlabTemp']['start_units']=$data['urange_from'][$i];
						$save['PricingSlabTemp']['end_units']=$data['urange_to'][$i];
						$save['PricingSlabTemp']['per_unit_rate']=$data['ratevalue'][$i];
						$this->PricingSlabTemp->create();
						$this->PricingSlabTemp->save($save);
						
						$detl['urange_from']=$data['urange_from'][$i];
						$detl['urange_to']=$data['urange_to'][$i];
						$detl['ratevalue']=$data['ratevalue'][$i];
						$aaray[]=$detl;
					}
				}
				
				
			}
			$msg['status']=100;
			$msg['detail']=$aaray;
			echo json_encode($msg);die;
		}
		$pid=$_GET['project_id'];
		$rate_id=$this->Session->read('ratecatrt_id');
		$servies = $this->SvcCatalogue->find('list',array('fields'=>array('SvcCatalogue.id','SvcCatalogue.svc_desc')));
		$addedService=array();
		if($pid!='' || $rate_id!='')
		{
			foreach($servies as $k=>$sv)
			{
				if($pid!='')
				{
					$detl = $this->PricingSlab->find('all',array('recursive'=>'-1','fields'=>array('PricingSlab.*'),'conditions'=>array('PricingSlab.project_id'=>$pid,'PricingSlab.service_id'=>$k)));
					$addedService[$k]['s_name']=$sv;
					$addedService[$k]['s_id']=$k;
					$addedService[$k]['pricing']=$detl;
				}
				else
				{
					$detl = $this->PricingSlabTemp->find('all',array('recursive'=>'-1','fields'=>array('PricingSlabTemp.*'),'conditions'=>array('PricingSlabTemp.temp_id'=>$rate_id,'PricingSlabTemp.service_id'=>$k)));
					$addedService[$k]['s_name']=$sv;
					$addedService[$k]['s_id']=$k;
					$addedService[$k]['pricing']=$detl;
					
				}
			}
		}
		$this->set('addedService',$addedService);
		$this->set('servies',$servies);
	}
	function get_rate_card()
	{
		$this->loadModel('PricingSlabTemp'); 
		$this->loadModel('PricingSlab');
		$data = $this->request->data;
		if(!empty($data))
		{
			if($data['pid']!='')
			{
				$detl = $this->PricingSlab->find('first',array('recursive'=>'-1','fields'=>array('PricingSlab.*'),'conditions'=>array('PricingSlab.project_id'=>$data['pid'],'PricingSlab.service_id'=>$data['servctype'],'PricingSlab.start_units <='=>$data['qty'],'PricingSlab.end_units >='=>$data['qty']))); 
			
				if(!empty($detl))
				{
					$msg['rate']=$detl['PricingSlab']['per_unit_rate'];
					$msg['success']=1;
					$msg['rate_session_id']='';
				}
				else
				{
					$msg['rate']=0;
					$msg['success']=0;
					$msg['rate_session_id']='';
				}
			}
			else
			{
				$rate_id=$this->Session->read('ratecatrt_id');
				$detl = $this->PricingSlabTemp->find('first',array('recursive'=>'-1','fields'=>array('PricingSlabTemp.*'),'conditions'=>array('PricingSlabTemp.temp_id'=>$rate_id,'PricingSlabTemp.service_id'=>$data['servctype'],'PricingSlabTemp.start_units <='=>$data['qty'],'PricingSlabTemp.end_units >='=>$data['qty']))); 
				if(!empty($detl))
				{
					$msg['rate']=$detl['PricingSlabTemp']['per_unit_rate'];
					$msg['success']=1;
					$msg['rate_session_id']=$rate_id;
				}
				else
				{
					$msg['rate']=0;
					$msg['success']=0;
					$msg['rate_session_id']='';
				}
			}
			echo json_encode($msg);die;
		}
	}
	function forecasting()
	{
		/*This Code Used For Provision For Doubtful Debt In (Forecasting)*/
	   $this->loadModel('Entitie');
	   $this->loadModel('Contract');
	   $this->loadModel('Project');
       /*****************************************************************/
        /*Starts Here Code Used For Active Customer Count*/
       /*****************************************************************/
          $totalCustomerCount=$this->Entitie->find('all',
          array('fields'=>array('Entitie.id'),
          //'condition'=>array('Entitie.status LIKE'=>'%Active%')  
          ));
          $this->set("totalCustomerCount",$totalCustomerCount);
       /*****************************************************************/
        /*End Here Code Used For Active Customer Count*/
        /*****************************************************************/


       /*****************************************************************/
        /*Starts Here Code Used For Active Contract Count*/
       /*****************************************************************/
          $totalContractCount=$this->Contract->find('all',
          array('fields'=>array('Contract.id'),
          //'condition'=>array('Contract.status'=>1)  
          ));
          $this->set("totalContractCount",$totalContractCount);
      /*****************************************************************/
        /*End Here Code Used For Active Contract Count*/
      /*****************************************************************/


      /*****************************************************************/
        /*Starts Here Code Used For Active Project Count*/
      /*****************************************************************/
          $totalProjectCount=$this->Project->find('all',
          array('fields'=>array('Project.id'),
          //'condition'=>array('Project.status'=>1)  
          ));
          $this->set("totalProjectCount",$totalProjectCount);
      /*****************************************************************/
        /*End Here Code Used For Active Project Count*/
      /*****************************************************************/
	    if($this->request->is('post')){
      	    $daterangeArray=explode("|",$this->request->data['contestdate']);
      	    $startDate=$daterangeArray[0];
        	$endDate=$daterangeArray[1];
        }else{
      	    $startDate=date('Y-04-1',strtotime('-1 year'));
      	    $endDate=date('Y-03-31');
        }
        $provisionForDoubtfulDebtVerticalsArray=array();
        $currentUserSession=$this->Session->read();
        $businessLine=$currentUserSession['admin']['vartical'];
        for($month=0; $month<=5; ++$month){
        $provisionForDoubtfulDebtVerticals=$this->Project->query("SELECT projects.business_line, SUM(invoices.invoice_amount) AS amountBusinessLine, business_lines.bl_name, invoices.invoice_due_dt FROM projects LEFT JOIN business_lines ON business_lines.`id`=projects.`business_line` LEFT JOIN invoices ON invoices.entity_id=projects.customer_entity_id  WHERE DATE_FORMAT(invoices.`invoice_due_dt`,'%m')=".date('m',strtotime("-".$month." month"))." &&  DATE_FORMAT(invoices.`invoice_due_dt`,'%Y')=".date('Y',strtotime("-".$month." month"))." && business_lines.`id`=".$businessLine." &&  invoices.`ar_cat_id`=2 GROUP BY projects.business_line");
            if(!empty($provisionForDoubtfulDebtVerticals)){
        	 for($t=0;$t<sizeof($provisionForDoubtfulDebtVerticals);++$t){
        	 $provisionForDoubtfulDebtVerticalsArray[]=array(
                "business_line"=>!empty($provisionForDoubtfulDebtVerticals[$t]['business_lines']['bl_name'])?$provisionForDoubtfulDebtVerticals[$t]['business_lines']['bl_name']:"0",
                "recordValues"=>array(
	            "month_years"=>date("M",strtotime("-".$month."Month")),
	            "years"=>date("Y",strtotime("-".$month."Month")),
	            "amountBusinessLine"=>!empty($provisionForDoubtfulDebtVerticals[$t][0]['amountBusinessLine'])?$provisionForDoubtfulDebtVerticals[$t][0]['amountBusinessLine']:0)
	        	);
	          }	
	         }else{
	         	 $provisionForDoubtfulDebtVerticalsArray[]=array(
                "business_line"=>0,
                "recordValues"=>array(
	            "month_years"=>date("M",strtotime("-".$month."Month")),
	            "years"=>date("Y",strtotime("-".$month."Month")),
	            "amountBusinessLine"=>0)
	        );
	     }
       }
       /**This Code Used For Weighted Average Payment Projections Graph For Customer**/
       $weightedAveragePaymentProjectionsCustomer=$this->Project->query("SELECT entities.`entitiy_name`,entities.id,SUM(projects.`project_value`) AS projectValueData, SUM(promise_to_pays.`promise_amount`) AS promiseAmount FROM entities LEFT JOIN projects ON projects.`customer_entity_id`=entities.`id` LEFT JOIN invoices ON invoices.`entity_id`= entities.id LEFT JOIN promise_to_pays ON invoices.`entity_id`=promise_to_pays.`entity_id` WHERE projects.initial_end_date>='".$startDate."' && projects.initial_end_date<='".$endDate."' GROUP BY entities.id ORDER BY projectValueData DESC LIMIT 10");
       
      
      /**This Code Used For Weighted Average Payment Projections Graph For Contracts**/ 
       $weightedAveragePaymentProjectionsContract=$this->Project->query("SELECT contracts.contract_title, contracts.id, SUM(projects.project_value) AS projectTotalvalue, SUM(promise_to_pays.promise_amount) AS promiseAmount FROM contracts LEFT JOIN projects ON contracts.id=projects.contract_id LEFT JOIN invoices ON invoices.contract_id=contracts.id LEFT JOIN promise_to_pays ON promise_to_pays.entity_id=invoices.entity_id WHERE projects.initial_end_date>='".$startDate."' && projects.initial_end_date<='".$endDate."' GROUP BY contracts.id ORDER BY projectTotalvalue DESC LIMIT 10");

       
       /**Billing Shortfall IN Forecasting Section**/
       $billingShortfallForCustomer=array();
       for($month=0; $month<=5; ++$month){
          $billingShortfall=$this->Project->query("SELECT entities.`entitiy_name`,entities.`id`,SUM(projects.`project_value`) AS projectValue, SUM(pricings.`billing_type_val`) AS actualValue FROM entities LEFT JOIN contracts ON contracts.`cust_entity_id`=entities.`id` LEFT JOIN projects ON projects.`contract_id`= contracts.`id` LEFT JOIN project_tasks ON projects.id=project_tasks.`project_id` LEFT JOIN pricings ON pricings.`task_id`=projects.id WHERE DATE_FORMAT(projects.`initial_end_date`,'%m')=".date('m',strtotime("-".$month." month"))." && DATE_FORMAT(projects.`initial_end_date`,'%Y')=".date('Y',strtotime("-".$month." month"))."  GROUP BY entities.`entitiy_name` ORDER BY projectValue LIMIT 10");
          if(sizeof($billingShortfall)>0){
          	  break;
          }
       }
       for($month=0; $month<5; ++$month){
       	  for($t=0;$t<sizeof($billingShortfall);++$t){
       	        $billingShortfallAgain=$this->Project->query("SELECT entities.`entitiy_name`,entities.`id`,SUM(projects.`project_value`) AS projectValue, SUM(pricings.`billing_type_val`) AS actualValue FROM entities LEFT JOIN contracts ON contracts.`cust_entity_id`=entities.`id` LEFT JOIN projects ON projects.`contract_id`= contracts.`id` LEFT JOIN project_tasks ON projects.id=project_tasks.`project_id` LEFT JOIN pricings ON pricings.`task_id`=projects.id WHERE DATE_FORMAT(projects.`initial_end_date`,'%m')=".date('m',strtotime("-".$month." month"))." && DATE_FORMAT(projects.`initial_end_date`,'%Y')=".date('Y',strtotime("-".$month." month"))." && entities.id=".$billingShortfall[$t]['entities']['id']."  GROUP BY entities.`entitiy_name`");
       	    $projectValue=!empty($billingShortfallAgain[$t][0]['projectValue'])?$billingShortfallAgain[$t][0]['projectValue']:0;
            $actualValue=!empty($billingShortfallAgain[$t][0]['actualValue'])?$billingShortfallAgain[$t][0]['actualValue']:0;
            $diffBillingShort= $projectValue - $actualValue;
            $billingShortfallForCustomer[]=array(
               "customer_name"=>!empty($billingShortfallAgain[$t]['entities']['entitiy_name'])?$billingShortfallAgain[$t]['entities']['entitiy_name']:"",
               "month"=>date("M",strtotime("-".$month."Month")),
               "year"=>date("Y",strtotime("-".$month."Month")),
               "actualamount"=>$diffBillingShort);
	     }
       }




       /**Billing Shortfall IN Forecasting Section**/
       $billingShortfallForContract=array();
       for($month=0; $month<=5; ++$month){
          $billingShortfallContract=$this->Project->query("SELECT contracts.`contract_title`,contracts.`id`,SUM(projects.`project_value`) AS projectValue, SUM(pricings.`billing_type_val`) AS actualValue FROM entities LEFT JOIN contracts ON contracts.`cust_entity_id`=entities.`id` LEFT JOIN projects ON projects.`contract_id`= contracts.`id` LEFT JOIN project_tasks ON projects.id=project_tasks.`project_id` LEFT JOIN pricings ON pricings.`task_id`=projects.id WHERE DATE_FORMAT(projects.`initial_end_date`,'%m')=".date('m',strtotime("-".$month." month"))." && DATE_FORMAT(projects.`initial_end_date`,'%Y')=".date('Y',strtotime("-".$month." month"))."  GROUP BY contracts.`contract_title` ORDER BY projectValue LIMIT 10");
          if(sizeof($billingShortfallContract)>0){
          	  break;
          }
       }

       for($month=0; $month<5; ++$month){
       	  for($t=0;$t<sizeof($billingShortfallContract);++$t){
       	        $billingShortfallContractAgain=$this->Project->query("SELECT contracts.`contract_title`,contracts.`id`,SUM(projects.`project_value`) AS projectValue, SUM(pricings.`billing_type_val`) AS actualValue FROM entities LEFT JOIN contracts ON contracts.`cust_entity_id`=entities.`id` LEFT JOIN projects ON projects.`contract_id`= contracts.`id` LEFT JOIN project_tasks ON projects.id=project_tasks.`project_id` LEFT JOIN pricings ON pricings.`task_id`=projects.id WHERE DATE_FORMAT(projects.`initial_end_date`,'%m')=".date('m',strtotime("-".$month." month"))." && DATE_FORMAT(projects.`initial_end_date`,'%Y')=".date('Y',strtotime("-".$month." month"))." && contracts.id=".$billingShortfallContract[$t]['contracts']['id']."  GROUP BY contracts.`contract_title`");
       	    $projectValue=!empty($billingShortfallContractAgain[$t][0]['projectValue'])?$billingShortfallContractAgain[$t][0]['projectValue']:0;
            $actualValue=!empty($billingShortfallContractAgain[$t][0]['actualValue'])?$billingShortfallContractAgain[$t][0]['actualValue']:0;
            $diffBillingShort= $projectValue - $actualValue;
            $billingShortfallForContract[]=array(
               "contract_name"=>!empty($billingShortfallContractAgain[$t]['contracts']['contract_title'])?$billingShortfallContractAgain[$t]['contracts']['contract_title']:"",
               "month"=>date("M",strtotime("-".$month."Month")),
               "year"=>date("Y",strtotime("-".$month."Month")),
               "actualamount"=>$diffBillingShort);
	     }
       }


       $this->set("weightedAveragePaymentProjectionsCustomer",$weightedAveragePaymentProjectionsCustomer);
       $this->set("weightedAveragePaymentProjectionsContract",$weightedAveragePaymentProjectionsContract);
       $this->set("provisionForDoubtfulDebtVerticals",$provisionForDoubtfulDebtVerticalsArray);
       $this->set("billingShortfallForCustomer",$billingShortfallForCustomer);
       $this->set("billingShortfallForContract",$billingShortfallForContract);
	}
	function projectana()
	{
		
	}
	function projectsrevenue()
	{
		$this->loadModel('Entitie');
	    $this->loadModel('Contract');
	    $this->loadModel('Project');
	    $this->loadModel('UserPermission');
        /***********Starts Here Code Used For Active Customer Count***********/
          $totalCustomerCount=$this->Entitie->find('all',
          array('fields'=>array('Entitie.id')));
          $this->set("totalCustomerCount",$totalCustomerCount);
        /***********End Here Code Used For Active Customer Count***********/


      /***********Starts Here Code Used For Active Contract Count********/
          $totalContractCount=$this->Contract->find('all',
          array('fields'=>array('Contract.id')));
          $this->set("totalContractCount",$totalContractCount);
      /***********End Here Code Used For Active Contract Count***********/


      /*****************************************************************/
        /*Starts Here Code Used For Active Project Count*/
      /*****************************************************************/
          $totalProjectCount=$this->Project->find('all',
          array('fields'=>array('Project.id')));
          $this->set("totalProjectCount",$totalProjectCount);
      /*****************************************************************/
        /*End Here Code Used For Active Project Count*/
      /*****************************************************************/ 
        
        $currentUserSession=$this->Session->read();
        $app_user_id=$currentUserSession['admin']['Admin']['AppUser']['id'];
        $user_var_sub_data=$this->UserPermission->find('all',array('recursive'=>'-1','field'=>array('UserPermission.business_line_id','UserPermission.subvertical_id'),'conditions'=>array('UserPermission.app_user_id'=>$app_user_id)));
        $businessLineIds=array();
        for($f=0;$f<sizeof($user_var_sub_data);++$f){
      	    $businessLineIds[]=$user_var_sub_data[$f]['UserPermission']['business_line_id'];
        }

        if($this->request->is('post')){
          $conditionArray=array();
          $conditionArray1=array();
          $conditionArray2=array();
          $billingTrendMonthCount=0;
          $provisionForDoubtfulDebt=0;
	      $dropdownFilter=!empty($this->request->data['dropdownFilter'])?$this->request->data['dropdownFilter']:0;
	      $filterSelectValue;
	      if($dropdownFilter==1){
	      	$currentMonth=date("m");
      	 	$currentYear=date("Y");
      	 	$billingTrendMonthCount=1;
      	 	$provisionForDoubtfulDebt=1;
      	 	$filterSelectValue=1;
	      	$conditionArray[]="projects.start_date='".$currentMonth."' && projects.start_date='".$currentYear."'";
	      	$conditionArray1[]="invoices.invoice_date='".$currentMonth."' && invoices.invoice_date='".$currentYear."'";
	      	$conditionArray2[]="projects.`initial_end_date`='".$currentMonth."' && projects.`initial_end_date`='".$currentYear."'";
	      }else if($dropdownFilter==2){
	      	$billingTrendMonthCount=3;
	      	$provisionForDoubtfulDebt=3;
	      	$filterSelectValue=2;
	      	$quarterMonthArray=array(date("Y-m",strtotime("-1 month")),date("Y-m"),date("Y-m",strtotime("+1 month")));
	      	$conditionArray[]="DATE_FORMAT(projects.start_date,'%Y-%m') IN (".implode(",",$quarterMonthArray).")";
	      	$conditionArray1[]="DATE_FORMAT(invoices.invoice_date ,'%Y-%m') IN (".implode(",",$quarterMonthArray).")"; 
	      	$conditionArray2[]="DATE_FORMAT(projects.`initial_end_date` ,'%Y-%m') IN (".implode(",",$quarterMonthArray).")"; 
	      }else if($dropdownFilter==3){
	      	$billingTrendMonthCount=5;
	      	$provisionForDoubtfulDebt=12;
	      	$filterSelectValue=3;
	      	$startDate=date('Y-04-1',strtotime('-1 year'));
      	    $endDate=date('Y-03-31');
      	    $conditionArray[]="projects.start_date >='".$startDate."' && projects.start_date <='".$endDate."'";
      	    $conditionArray1[]="invoices.invoice_date >='".$startDate."' && invoices.invoice_date <='".$endDate."'";
      	    $conditionArray2[]="projects.`initial_end_date` >='".$startDate."' && projects.`initial_end_date` <='".$endDate."'";
	      }
         /**Custom Date Range Code**/ 
      	if(!empty($this->request->data['stratDate'])&& !empty($this->request->data['endDate'])){
      		  $startDate=$this->request->data['stratDate'];
      		  $endDate=$this->request->data['endDate'];
      		  $conditionArray[]="DATE_FORMAT(projects.start_date,'%Y/%m') >='".$startDate."' && DATE_FORMAT(projects.start_date,'%Y/%m') <='".$endDate."'";	
      	      $conditionArray1[]="DATE_FORMAT(invoices.invoice_date,'%Y/%m') >='".$startDate."' && DATE_FORMAT(invoices.invoice_date,'%Y/%m') <='".$endDate."'";
      	      $conditionArray2[]="DATE_FORMAT(projects.`initial_end_date`,'%Y/%m') >='".$startDate."' && DATE_FORMAT(projects.`initial_end_date`,'%Y/%m') <='".$endDate."'";
      	 } 
	    }else{
	    	$billingTrendMonthCount=5;
	    	$provisionForDoubtfulDebt=12;
	    	$filterSelectValue=3;
	        $startDate=date('Y-04-1',strtotime('-1 year'));
      	    $endDate=date('Y-03-31');
      	    $conditionArray[]="projects.start_date >='".$startDate."' && projects.start_date <='".$endDate."'";	
      	    $conditionArray1[]="invoices.invoice_date >='".$startDate."' && invoices.invoice_date <='".$endDate."'";
      	    $conditionArray2[]="projects.`initial_end_date` >='".$startDate."' && projects.`initial_end_date` <='".$endDate."'";
	    }


        /*This Code Used For Top 10 Projects In (Project & Billing Section)*/
		$toptenProjects=$this->Project->query("SELECT entities.`entitiy_name`,entity_addresses.`city`, SUM(projects.project_value) AS total_project_value, projects.`customer_entity_id`,projects.start_date,contracts.id AS contract_id FROM entities LEFT JOIN entity_addresses ON entities.id=entity_addresses.`entity_id` LEFT JOIN projects ON entities.`id`=projects.`customer_entity_id` LEFT JOIN contracts ON entities.id=contracts.`cust_entity_id`  WHERE ".implode("",$conditionArray)." && projects.status=1  GROUP BY projects.`customer_entity_id` ORDER BY total_project_value DESC LIMIT 10");


        /*This Code Used For Top 10 Locations (Billing) In (Project & Billing Section)*/
        $topLocationBilling=$this->Project->query("SELECT entities.`entitiy_name`,entity_addresses.`city`, SUM(invoices.invoice_amount) AS total_invoice_value, invoices.`entity_id`,invoices.invoice_date,contracts.id AS contract_id FROM entities LEFT JOIN entity_addresses ON entities.id=entity_addresses.`entity_id` LEFT JOIN projects ON entities.`id`=projects.`customer_entity_id` LEFT JOIN invoices ON entities.id=invoices.`entity_id` LEFT JOIN contracts ON entities.id=contracts.`cust_entity_id`  WHERE ".implode("",$conditionArray1)." && invoices.is_active=1  GROUP BY entity_addresses.`city`  ORDER BY total_invoice_value DESC LIMIT 10");


        /*This Code Used For Vertical Wise Revenue In (Project & Billing Section)*/
        $veticalWiseRevenue=$this->Project->query("SELECT projects.business_line, SUM(invoices.invoice_amount) AS amountBusinessLine, invoices.`is_active`, business_lines.bl_name, invoices.invoice_date FROM projects LEFT JOIN business_lines ON business_lines.`id`=projects.`business_line` LEFT JOIN invoices ON invoices.entity_id=projects.customer_entity_id  WHERE  ".implode("",$conditionArray1)." && invoices.is_active=1 && business_lines.`id` IN (".implode(",",$businessLineIds).")  GROUP BY projects.business_line");
        
 

        /*This Code Used For Provision For Doubtful Debt In (Project & Billing Section)*/
        $provisionForDoubtfulDebtVerticalsArray=array();
        for($month=0; $month<$provisionForDoubtfulDebt; ++$month){
        $provisionForDoubtfulDebtVerticals=$this->Project->query("SELECT projects.business_line, SUM(invoices.invoice_amount) AS amountBusinessLine,business_lines.bl_name, invoices.invoice_due_dt FROM projects LEFT JOIN business_lines ON business_lines.`id`=projects.`business_line` LEFT JOIN invoices ON invoices.entity_id=projects.customer_entity_id  WHERE DATE_FORMAT(invoices.`invoice_due_dt`,'%m')=".date('m',strtotime("-".$month." month"))." &&  DATE_FORMAT(invoices.`invoice_due_dt`,'%Y')=".date('Y',strtotime("-".$month." month"))." &&  invoices.`ar_cat_id`=2 GROUP BY projects.business_line");
            if(!empty($provisionForDoubtfulDebtVerticals)){
        	 for($t=0;$t<sizeof($provisionForDoubtfulDebtVerticals);++$t){
        	 $provisionForDoubtfulDebtVerticalsArray[]=array(
                "business_line"=>!empty($provisionForDoubtfulDebtVerticals[$t]['business_lines']['bl_name'])?$provisionForDoubtfulDebtVerticals[$t]['business_lines']['bl_name']:"0",
                "recordValues"=>array(
	            "month_years"=>date("M",strtotime("-".$month."Month")),
	            "years"=>date("Y",strtotime("-".$month."Month")),
	            "amountBusinessLine"=>!empty($provisionForDoubtfulDebtVerticals[$t][0]['amountBusinessLine'])?$provisionForDoubtfulDebtVerticals[$t][0]['amountBusinessLine']:0)
	        	);
	          }	
	         }else{
	         	 $provisionForDoubtfulDebtVerticalsArray[]=array(
                "business_line"=>0,
                "recordValues"=>array(
	            "month_years"=>date("M",strtotime("-".$month."Month")),
	            "years"=>date("Y",strtotime("-".$month."Month")),
	            "amountBusinessLine"=>0)
	        	);
	        }
         }


        /*This Code Used For Provision For Doubtful Debt In (Project & Billing Section)*/
        $provisionForDoubtfulDebtCustomerArray=array();
        for($month=0; $month<$billingTrendMonthCount; ++$month){
            $provisionForDoubtfulDebtCustomers=$this->Project->query("SELECT entities.`id`,entities.`entitiy_name`, SUM(invoices.`invoice_amount`) AS invoiceamount,invoices.`invoice_due_dt` FROM entities LEFT JOIN invoices ON entities.id=invoices.`entity_id` WHERE DATE_FORMAT(invoices.`invoice_due_dt`,'%m')=".date('m',strtotime("-".$month." month"))." && DATE_FORMAT(invoices.`invoice_due_dt`,'%Y')=".date('Y',strtotime("-".$month." month"))." && invoices.`ar_cat_id`=2 GROUP BY entities.`entitiy_name` ORDER BY invoiceamount DESC LIMIT 5");
            if(sizeof($provisionForDoubtfulDebtCustomers)>0){
            	break;
            }
        }  

        for($month=0; $month<$provisionForDoubtfulDebt; ++$month){
         for($t=0;$t<sizeof($provisionForDoubtfulDebtCustomers);++$t){
        	 $provisionForDoubtfulDebtCustomersData=$this->Project->query("SELECT entities.`entitiy_name`, SUM(invoices.`invoice_amount`) AS invoiceamount,invoices.`invoice_due_dt` FROM entities LEFT JOIN invoices ON entities.id=invoices.`entity_id` WHERE DATE_FORMAT(invoices.`invoice_due_dt`,'%m')=".date('m',strtotime("-".$month." month"))." && DATE_FORMAT(invoices.`invoice_due_dt`,'%Y')=".date('Y',strtotime("-".$month." month"))." && invoices.`ar_cat_id`=2 && entities.id=".$provisionForDoubtfulDebtCustomers[$t]['entities']['id']." GROUP BY entities.`entitiy_name`");

        	  $provisionForDoubtfulDebtCustomerArray[]=array(
              "customer_name"=>!empty($provisionForDoubtfulDebtCustomersData[$t]['entities']['entitiy_name'])?$provisionForDoubtfulDebtCustomersData[$t]['entities']['entitiy_name']:"0",
	          "month_years"=>date("M",strtotime("-".$month."Month")),
	          "years"=>date("Y",strtotime("-".$month."Month")),
	          "amountBusinessLine"=>!empty($provisionForDoubtfulDebtCustomersData[$t][0]['invoiceamount'])?$provisionForDoubtfulDebtCustomersData[$t][0]['invoiceamount']:0
	        );
	       }
	     }
       
       
        /*** Projected Vs. Actual For Vertical Graph IN (Projects & Billings) ***/
        $projectedVsActualVertical=$this->Project->query("SELECT business_lines.`bl_name`, business_lines.`id`, SUM(projects.`project_value`) AS projectValue, SUM(pricings.`billing_type_val`) AS actualValue, SUM(promise_to_pays.`promise_amount`) AS ptomiseToPay FROM business_lines LEFT JOIN projects ON business_lines.id=projects.`business_line` LEFT JOIN project_tasks ON projects.id=project_tasks.`project_id` LEFT JOIN pricings ON project_tasks.id= pricings.`task_id`LEFT JOIN promise_to_pays ON promise_to_pays.entity_id=projects.`customer_entity_id` WHERE ".implode("",$conditionArray2)." && business_lines.`id` IN (".implode(",",$businessLineIds).") GROUP BY business_lines.`id`");


       /*** Projected Vs. Actual For Projects Graph IN (Projects & Billings) ***/
        $projectedVsActualProjects=$this->Project->query("SELECT projects.`project_title`, business_lines.`id`, SUM(projects.`project_value`) AS projectValue,SUM(pricings.`billing_type_val`) AS actualValue, SUM(promise_to_pays.`promise_amount`) AS promiseToPay FROM business_lines LEFT JOIN projects ON business_lines.id=projects.`business_line` LEFT JOIN project_tasks ON projects.id=project_tasks.`project_id` LEFT JOIN pricings ON project_tasks.id= pricings.`task_id` LEFT JOIN promise_to_pays ON promise_to_pays.entity_id=projects.`customer_entity_id` WHERE ".implode("",$conditionArray2)." GROUP BY projects.`project_title` ORDER BY projectValue DESC LIMIT 10");
         
       
        $provisionForDoubtfulDebtCustomername=array();
         for($t=0;$t<sizeof($provisionForDoubtfulDebtCustomerArray); ++$t){ 
            if(!empty($provisionForDoubtfulDebtCustomerArray[$t]['customer_name'])){
               $provisionForDoubtfulDebtCustomername[]=$provisionForDoubtfulDebtCustomerArray[$t]['customer_name'];
            }
         }
        $this->set("billingTrendsCount",$billingTrendMonthCount); 
        $this->set("customerWiseArray",$provisionForDoubtfulDebtCustomerArray);
        // $this->set("provisionForDoubtfulDebtCustomername",array_unique($provisionForDoubtfulDebtCustomername));
        $this->set("provisionForDoubtfulDebtCustomer",$provisionForDoubtfulDebtCustomerArray);
        $this->set("provisionForDoubtfulDebtVerticals",$provisionForDoubtfulDebtVerticalsArray); 
        $this->set("veticalWiseRevenue",$veticalWiseRevenue);
        $this->set("projectedVsActualVertical",$projectedVsActualVertical);
        $this->set("projectedVsActualProjects",$projectedVsActualProjects);
        
        /*This Code Used For Billing Growth & Billing Trends In (Project & Billing Section)*/
        $billingGrowth=$this->Project->query("SELECT business_lines.bl_name, SUM(invoices.invoice_amount) AS amountBusinessLine,projects.business_line, invoices.invoice_date FROM projects LEFT JOIN business_lines ON business_lines.`id`=projects.`business_line` LEFT JOIN invoices ON invoices.entity_id=projects.customer_entity_id WHERE ".implode("",$conditionArray1)." && business_lines.`id` IN (".implode(",",$businessLineIds).") GROUP BY projects.business_line");


        $this->set("filterSelectValue",$filterSelectValue);
        $this->set("billingGrowths",$billingGrowth);
        $this->set("topLocationBilling",$topLocationBilling);
        $this->set("toptenProjects",$toptenProjects);
	}
	function view_userdetails($id)
	{
		$userid=base64_decode($id);
		$this->loadModel('Entitie');
		$sesn=$this->Session->read('admin');
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
		$aprroval_flag = $_GET['approval'];
		$this->loadModel('RemarksApproval');


        $remark_data =$this->RemarksApproval->find('all',array('conditions'=>array('RemarksApproval.section_id'=>$userid)));
		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'View Customer Information')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
		
		
		$entitie_detail=$this->Entitie->find('first',array('fields'=>array('Entitie.*'),'conditions'=>array('Entitie.id'=>$userid)));
	//pre($entitie_detail);die;
	 //$this->paginate = 
		$this->set('entitie_detail',$entitie_detail);
		$this->set('aprroval_flag',$aprroval_flag);
		$this->set('remark_data',$remark_data);
		
		// $group_name=$this->Group->find('first',array('conditions'=>array('Group.id'=>$entitie_detail['Entitie']['group_id'])));
		// //pre($group_name);die;
		// $this->set('group_name',$group_name);
		
		$comm_data=$this->AppUser->find('first',array('conditions'=>array('AppUser.entity_id'=>$userid)));
		$this->set('comm_data',$comm_data);
		$this->set('excess_permission',$excess_permission);			
	}	
	public function invoice()
	{
		$conditions= array();
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin'];
		$this->loadModel('Invoice');
		$this->loadModel('InvoiceStage');
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
			$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Upload Invoice Delivery Tracker')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
        $data = $this->request->data;

         $short = array('Invoice.id'=>'DESC');
			
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('Entitie.entitiy_name'=>$shortVal);
		}

		/*
		if(isset($data['search_key']) && !empty($data['search_key'])){
			
			$conditions = array_merge($conditions,array('OR'=>array('Invoice.contract_title LIKE'=>
				'%'.$data['search_key'].'%')));

		}
		*/
		if(isset($data['search_id']) && !empty($data['search_id'])){
			
			$conditions = array_merge($conditions,array('Invoice.entity_id'=>
				$data['search_id']));

		}

		if(isset($data['invoiceStartDate']) && !empty($data['invoiceStartDate'])) {
			
			$beginDate = date('Y-m-d',strtotime($data['invoiceStartDate']));
			$conditions = array_merge($conditions,array('Invoice.invoice_date >='=> $beginDate));
			
		}

		if(isset($data['invoiceEndDate']) && !empty($data['invoiceEndDate'])) {
			
			$conditions = array_merge($conditions,array('Invoice.invoice_date <='=>	date('Y-m-d',strtotime($data['invoiceEndDate']))));

		}
		if(isset($data['invoiceDueDate']) && !empty($data['invoiceDueDate'])){
			
			$conditions = array_merge($conditions,array('Invoice.invoice_due_dt >='=> date('Y-m-d',strtotime($data['invoiceDueDate']))));

		}
		if(isset($data['invoiceDueEnd']) && !empty($data['invoiceDueEnd'])) {
			
			$conditions = array_merge($conditions,array('Invoice.invoice_due_dt <='=> date('Y-m-d',strtotime($data['invoiceDueEnd']))));

		}

		$conditions = array_merge($conditions,array('Invoice.is_active'=>1));
		
		$this->paginate = array('limit'=>20,'recursive'=>'-1','order'=>$short ,'field'=>array('Invoice.*',
		'Entitie.entitiy_name'),'conditions'=>array($conditions));
         
		
		$invoice_data = $this->paginate('Invoice');
		

		if($this->RequestHandler->isAjax())
             {
                //pre($invoice_data);die;

                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($invoice_data))
                {
                        $view->set('invoice_data',$invoice_data);
                        $html['html'] = $view->render("invoice_element");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                }
                else
                {
                        $html['message'] ='error';
                }

                echo json_encode($html);die;

                }
                $this->set('invoice_data',$invoice_data);
                $this->set('excess_permission',$excess_permission);

		//pre($invoice_data);die;
	}
	function invoiceUploadTracker()
	{
		//
		$this->loadModel('Invoice');
		$this->loadModel('DunningStepMaster');
		$this->loadModel('InvoiceTracker');
		//ini_set('MAX_EXECUTION_TIME', -1);
		//print_r($this->request->data);die;

		if(@$type=='downloadexcel_error')
		{
			$filePath 		= '../webroot/files/'; 
			$fileName = "ProjectErrorSheet.xlsx";
			print_r(HTTP_ROOT.'files/'.$fileName);
			unlink($filePath.$fileName);die;
		}
		
		if($_FILES)
		{  
	
			$chkError=0;
			$errorData=array();
			$MediaName = $_FILES['uploadfile']['name']; 
			$MediaTempName 	= $_FILES['uploadfile']['tmp_name'];
			$MediaExtension = pathinfo($MediaName, PATHINFO_EXTENSION);
			$MediaNewName	= date("YmdHis").'.'.$MediaExtension;
			$filePath 		= '../webroot/files/'; 
			$fileNewPath 		= $filePath.$MediaNewName; 
			move_uploaded_file($MediaTempName, $fileNewPath); 
			$MediaNewName = str_replace(' ','',$MediaNewName); 
			//pre(1);die;
			/*Save Exl to data base start*/
			
			$this->layout = false;
			CakePlugin::load('PHPExcel');
			App::uses('PHPExcel', 'PHPExcel.Classes');
			$objPHPExcel = new PHPExcel();
			
			$input_file_type = PHPExcel_IOFactory::identify('../webroot/files/'.$MediaNewName); 
			$obj_reader = PHPExcel_IOFactory::createReader($input_file_type); 
			$obj_reader->setReadDataOnly(true); 
			
			$objPHPExcel = $obj_reader->load('../webroot/files/'.$MediaNewName); 
			$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
			$highest_row = $objWorksheet->getHighestRow();
			//echo $highest_row;die;
			$highest_col = $objWorksheet->getHighestColumn();
							
			$header_row = $objWorksheet->rangeToArray('A1:L1'); 
				//	pre($header_row);die;
			
				for($counter = 2; $counter <= $highest_row; $counter++)
				{ 
					$chkrowError=0;
					$row = $objWorksheet->rangeToArray('A'.$counter.':'.$highest_col.$counter);  
															
				    $Zone 			= 	$row[0][0];
					$CustomerId 	= 	$row[0][1];
					$BatchCode 		= 	$row[0][2];
					$InvoiceId 		= 	$row[0][3];
					$InvoiceDate 	= 	$row[0][4];
					$invoiceAmount 	= 	$row[0][5];
					$Status 		= 	$row[0][6];
					$DispatchDate 	= 	$row[0][7];
					$DeliveredDate 	= 	$row[0][8];
					$CourierNumber 	= 	$row[0][9];
					$AWBNo 			= 	$row[0][10];
					$CourrierLink 	= 	$row[0][11];

					//pre($row);die;
					if($Status =='Pending')
					{
						$DeliveredDate = '';
						$CourierNumber = '';
						$CourrierLink = '';
					}elseif($Status =='Dispatched' || $Status =='Delivered')
					{
						if($DispatchDate =='' || $DispatchDate == '' || $CourrierLink == '')
							$chkrowError==1;
						
					}

					$getTracker = $this->InvoiceTracker->find('first',array('conditions'=>array('InvoiceTracker.invoice_number'=>$InvoiceId)));
					//pre($getTracker);die;

					if(!(empty($getTracker))){
						$data['InvoiceTracker']['id'] = $getTracker['InvoiceTracker']['id'];
					}
					
									
					if($chkrowError==0)
					{	
						//echo 'bfhfbh';die;
						$data['InvoiceTracker']['zone_id']=$Zone;
						$data['InvoiceTracker']['entities_id']=$CustomerId;
						$data['InvoiceTracker']['batch_code']=$BatchCode;
						$data['InvoiceTracker']['invoice_number']=$InvoiceId;
						$data['InvoiceTracker']['invoice_date']=date("Y-m-d", strtotime($InvoiceDate));
						$data['InvoiceTracker']['invoice_amount']=$invoiceAmount;
						$data['InvoiceTracker']['status']=$Status;

						if(!(empty($DispatchDate))){
							$data['InvoiceTracker']['dispatch_date']=date("Y-m-d", strtotime($DispatchDate));
						}
						
						if(!(empty($DeliveredDate))){
							$data['InvoiceTracker']['delivered_date']=date("Y-m-d", strtotime($DeliveredDate));
						}
						if(!(empty($CourierNumber))){
							$data['InvoiceTracker']['courier_number']=$CourierNumber;
						}
						if(!(empty($CourrierLink))){
							$data['InvoiceTracker']['courier_link']=$CourrierLink;
						}
						
						$data['InvoiceTracker']['awb_no']=$AWBNo;
						
						

						//pre($data);
							$this->InvoiceTracker->create();	
							$this->InvoiceTracker->save($data);
												
								$row = reset($row); 
					}

					
				}
				//die;
				
				unlink('../webroot/files/'.$MediaNewName);
			if($chkError==0)
			{
				$msg['msg']='Success';
				echo json_encode($msg);die;						
			//echo ('Success');die;
			}			
		}
	}

	function find_all_invoice(){

		$this->loadModel('Invoice');
		$this->loadModel('Entitie');
		
        $data = $this->request->data;
         $invices_number = $this->Invoice->find('all',array('recursive'=>'-1','fields'=>array('Invoice.invoice_number','Invoice.id','Invoice.entity_id'),
         	'conditions'=>array('Invoice.entity_id'=>$data['entity_id'])));

         $entity_name= $this->Entitie->find('all',array('recursive'=>'-1','fields'=>array('Entitie.entitiy_name'),
         	'conditions'=>array('Entitie.id'=>$invices_number[0]['Invoice']['entity_id'])));

         //pre($entity_name); die();
         foreach($invices_number as $k=>$invice_number)
		 {
			
		 	$optinvoice.='<option  value="'.$invice_number['Invoice']['id'].'">'.$invice_number['Invoice']['invoice_number'].'</option>';
		 }
		 $msg['option']     =	$optinvoice;
		 $msg['entity_name']=	$entity_name[0]['Entitie']['entitiy_name'];

		echo json_encode($msg); die;  
	}
	
	function invoice_dispatch()
	{
	}	
	function invoice_detail($id=null)
	{
		$this->loadModel('Invoice');
		$this->loadModel('Document');
		$this->loadModel('Project');
		$this->loadModel('ProjectMilestone');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('AppUser');
		$this->loadModel('Permission');
		$this->loadModel('Contact');
		$this->loadModel('ProjectTask');
		
		$invoice_id=base64_decode($id);
		          
		$invoice_data=$this->Invoice->find('first',array('recursive'=>'-1','field'=>array('Invoice.*',
			'Entitie.entitiy_name'),'conditions'=>array('Invoice.id '=>$invoice_id)));  
		$this->set('invoice_data',$invoice_data);
        //pre($invoice_data);die;
				
		$document_detail=$this->Document->find('all',array('recursive'=>'-1','field'=>array('Document.
			doc_dms_url'),'conditions'=>array('Document.id'=>$invoice_data['Invoice']['document_id'])));
		foreach($document_detail as $document_detai){ $documents = $document_detai; }
		$document_type=$this->MasterDataDetail->find('all',array('field'=>array('MasterDataDetail.
			master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$documents['Document']['document_type'])));        
        foreach($document_type as $document_types){ $document_typess = $document_types; }
		$this->set('document_type',$document_typess);
		
		$project_data=$this->Project->find('all',array('field'=>array('recursive'=>'-1','Project.project_value'),
		 	'conditions'=>array('Project.id'=>$invoice_data['Invoice']['project_id'])));
		$this->set('project_data',$project_data);
		 //pre($project_data);die(); 

		$zone_from=$this->MasterDataDetail->find('all',array('field'=>array(
			'MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>
			$project_data[0]['CompanyAddress']['zone'])));
		$this->set('zone_from',$zone_from);

		$zone_to=$this->MasterDataDetail->find('all',array('field'=>array(
			'MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>
			$project_data[0]['BilltoAddress']['zone'])));
		$this->set('zone_to',$zone_to);

		$zone_ship_to=$this->MasterDataDetail->find('all',array('field'=>array(
			'MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>
			$project_data[0]['EntityAddress']['zone'])));
		$this->set('zone_ship_to',$zone_ship_to);
		 //pre($project_data); die();

		 $primary_contact_from=$this->AppUser->find('first',array('recursive'=>'-1','field'=>array('AppUser.*'),
		 	'conditions'=>array('AppUser.entity_id '=>$invoice_data['Invoice']['entity_id'])));  
		$this->set('primary_contact_from',$primary_contact_from);
		 //pre($primary_contact_from); 

		 $user_role=$this->Permission->find('first',array('recursive'=>'-1','field'=>array('Permission.*'),
		 	'conditions'=>array('Permission.id '=>$primary_contact_from['AppUser']['role_id'])));  
		$this->set('user_role',$user_role);
		$user_type=$this->MasterDataDetail->find('first',array('recursive'=>'-1','field'=>array('MasterDataDetail.*'),
		 	'conditions'=>array('MasterDataDetail.id '=>$primary_contact_from['AppUser']['user_type'])));  
		$this->set('user_type',$user_type);

		$primary_contact_to=$this->Contact->find('first',array('recursive'=>'-1','field'=>array('Contact.*'
			),'conditions'=>array('Contact.entity_id '=>$invoice_data['Invoice']['entity_id'])));  
		$this->set('primary_contact_to',$primary_contact_to);

		$contact_role=$this->MasterDataDetail->find('first',array('recursive'=>'-1','field'=>array('MasterDataDetail.*'),
		 	'conditions'=>array('MasterDataDetail.id '=>$primary_contact_to['Contact']['contact_role'])));  
		$this->set('contact_role',$contact_role);
		 //pre($invoice_data); die();
		// $contact_role=$this->Projectasks->find('first',array('recursive'=>'-1','field'=>array('MasterDataDetail.*'),
		//  	'conditions'=>array('MasterDataDetail.id '=>$primary_contact_to['Contact']['contact_role'])));  
		// $this->set('contact_role',$contact_role);
        				
		// $p_detail=$this->Project->find('first',array('joins' => array(
		// array('table' => 'project_milestones','alias' => 'ProjectMilestone','type' => 'INNER',
		// 	'conditions' => array('ProjectMilestone.project_id = Project.id')),
		// array('table' => 'project_tasks','alias' => 'projectTask','type' => 'LEFT','conditions' => 
		// 	array('projectTask.project_id = Project.id')),
		// ),
		// 'fields'=>array('ProjectMilestone.*','projectTask.*'),'conditions'=>array('Project.
		// 	customer_entity_id'=>$invoice_data['Invoice']['entity_id'])));
		// $this->set('p_detail',$p_detail);

		// $p_detail = $this->ProjectTask->find('first',array('joins'=>array(array('table'=>
		// 	'project_tasks',
		// 	'alias'=>'ProjectTask','type'=>'INNER','conditions'=>array('ProjectTask.project_id'=>
		// 	$invoice_data['Invoice']['project_id']))),'fields'=>array('ProjectTask.*')));
		// pre($p_detail); die();		
	}
	

	function contract_details($id=null)
	{		
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin'];
		$contract_id=base64_decode($id);
		$this->loadModel('Contract');
		$this->loadModel('ContractSchedule');
		$this->loadModel('Entitie');
		$this->loadModel('EntityAddress');
		$this->loadModel('Group');
		$this->loadModel('CompanyAddress');
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
		$aprroval_flag = $_GET['approval'];
		$this->loadModel('RemarksApproval');
		$this->loadModel('MasterDataDetail');	
		
        $remark_data =$this->RemarksApproval->find('all',array('conditions'=>array('RemarksApproval.section_id'=>$contract_id)));
		
		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'View Contract Details')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));

		$contract_detail=$this->Contract->find('first',array('recursive'=>'-1',
			'conditions'=>array('Contract.id'=>$contract_id)));
		$from_address=$this->CompanyAddress->find('first',array('recursive'=>'-1',
			'conditions'=>array('CompanyAddress.id'=>$contract_detail['Contract']['bill_from_address_id'])));
		$bill_address=$this->EntityAddress->find('first',array('recursive'=>'-1',
			'conditions'=>array('EntityAddress.id'=>$contract_detail['Contract']['bill_to_address_id'])));
		$ship_address=$this->EntityAddress->find('first',array('recursive'=>'-1',
			'conditions'=>array('EntityAddress.id'=>$contract_detail['Contract']['ship_to_address_id'])));
		
		$entites_data=$this->Entitie->find('first',array('fields'=>array('Entitie.entitiy_name',
			'Entitie.group_id','Entitie.entity_pan','Entitie.entity_tan','Entitie.entity_gst',
			'Entitie.credit_period','Entitie.credit_limit','Entitie.entity_turnover'),
		'conditions'=>array('Entitie.id'=>$contract_detail['Contract']['cust_entity_id'])));
		//pre($from_address); die();
	
		$bill_to_zone_detail=$this->MasterDataDetail->find('first',array('conditions'=>
			array('MasterDataDetail.id'=>$contract_detail['EntityAddress']['zone'])));

		$this->set('bill_to_zone_detail',$bill_to_zone_detail);
		
		$contract_schedule=$this->ContractSchedule->find('all',array('recursive'=>'-1',
			'conditions'=>array('ContractSchedule.contract_id'=>$contract_id),'fields'=>
			array('ContractSchedule.start_date','ContractSchedule.end_date',
				'ContractSchedule.contract_value','ContractSchedule.increment',
				'ContractSchedule.adjusted_contract_value')));

		$this->set('contract_schedule',$contract_schedule);
	
		$group_name=$this->Group->find('first',array('conditions'=>array('Group.id'=>
			$entites_data['Entitie']['group_id'])));

		$this->set('group_name',$group_name);
		$this->set('entites_data',$entites_data);
		$this->set('from_address',$from_address);
		$this->set('bill_address',$bill_address);
		$this->set('ship_address',$ship_address);
		$this->set('excess_permission',$excess_permission);
		$this->set('aprroval_flag',$aprroval_flag);
		$this->set('remark_data',$remark_data);		
		$this->set('contract',$contract_detail);
	}
	
	function project_details($id=null)
	{		
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin']['AppUser']['id'];
		//$projectid=base64_decode($id);
		$this->loadModel('Entitie');
		$this->loadModel('Project');
		$this->loadModel('ProjectTask');
		$this->loadModel('Group');
		$this->loadModel('RolePermission');
		$this->loadModel('SvcCatalogue');
		$role_id=$sesn['Role'];
		$this->loadModel('Pricing');
		$this->loadModel('ProjectPage');
		$this->loadModel('ProjectTaskUpdateHistory');
		$aprroval_flag = $_GET['approval'];
		$projectid = base64_decode($_GET['id']);
		$this->loadModel('RemarksApproval');
		
        $remark_data =$this->RemarksApproval->find('all',array('conditions'=>array('RemarksApproval.section_id'=>$projectid )));
		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'View Customer Information')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
					
		$entites_data=$this->Entitie->find('first',array('fields'=>array('Entitie.entitiy_name',
			'Entitie.group_id','Entitie.entity_pan','Entitie.entity_tan','Entitie.entity_gst',
			'Entitie.credit_period','Entitie.credit_limit','Entitie.entity_turnover'),
		'conditions'=>array('Entitie.id'=>$userid)));

		$project_detail=$this->Project->find('first',array('recursive'=>'-1','fields'=>array('Project.id','Project.project_title','Project.project_type','Project.start_date','Project.brief_description','Project.project_id','Project.status','Project.project_mgr_id','Project.sales_mgr_id','Project.initial_end_date','Project.ship_to_address_id','Project.project_value','Entitie.entitiy_name','Entitie.entity_turnover','Entitie.entity_pan','Entitie.group_id','Entitie.entity_tan','Entitie.entity_gst','ProjectTask.id','ProjectTask.task_description','CompanyAddress.address_line_1','CompanyAddress.address_line_2','CompanyAddress.city','CompanyAddress.state','CompanyAddress.postal_code','CompanyAddress.zone','CompanyAddress.address_type','BilltoAddress.address_line_1','BilltoAddress.address_line_2','BilltoAddress.city','BilltoAddress.address_type','BilltoAddress.state','BilltoAddress.postal_code','BilltoAddress.zone','EntityAddress.address_type','EntityAddress.address_line_1','EntityAddress.address_line_2','EntityAddress.city','EntityAddress.state','EntityAddress.postal_code','EntityAddress.zone','ProjectTask.id','AppUser.*','SalesUser.*','BusinessLine.*','Subvertical.*','ProfitCenter.*'),'conditions'=>
			array('Project.id'=>$projectid)));
		//pre($project_detail); die;

		$servies = $this->SvcCatalogue->find('list',array('fields'=>array('SvcCatalogue.id','SvcCatalogue.svc_desc')));
		$this->set('servies',$servies);

	    $p_task = $this->ProjectTask->find('list',array('recursive'=>'-1','fields'=>array('ProjectTask.id'),'conditions'=>array('ProjectTask.project_id'=>$projectid)));
        	       
		  $pricingval1 = $this->Pricing->find('all',array('group'=>'Pricing.billing_type','recursive'=>'-1','fields'=>array('Pricing.*','MasterDataDetail.master_data_desc'),'conditions'=>array('Pricing.task_id'=>$p_task,'Pricing.billing_type !='=>4),'joins'=>array(array('table'=>'master_data_details','alias'=>'MasterDataDetail','type'=>'LEFT','conditions'=>array('Pricing.billing_type = MasterDataDetail.id')))));
		  
		 $pricingval2 = $this->Pricing->find('all',array('group'=>'Pricing.billing_type','recursive'=>'-1','fields'=>array('Pricing.*','MasterDataDetail.master_data_desc'),'conditions'=>array('Pricing.task_id'=>$p_task,'Pricing.billing_type'=>4),'joins'=>array(array('table'=>'master_data_details','alias'=>'MasterDataDetail','type'=>'LEFT','conditions'=>array('Pricing.billing_type = MasterDataDetail.id')))));
		 $pricingval=array_merge($pricingval1,$pricingval2);
	     //pre($project_detail); die;	 
	
		$task_update_data = $this->ProjectTaskUpdateHistory->find('first',array('recursive'=>'-1','order'=>$short,'fields'=>array('ProjectTaskUpdateHistory.*'),'conditions'=>array('ProjectTaskUpdateHistory.project_id'=>$projectid,'ProjectTaskUpdateHistory.status'=>1)));
		//pre($task_update_data); die;
		
		$group_name=$this->Group->find('first',array('conditions'=>array('Group.id'=>$project_detail['Entitie']['group_id'])));
		//pre($group_name); die;
		$this->set('group_name',$group_name);
		$this->set('aprroval_flag',$aprroval_flag);
		$this->set('remark_data',$remark_data);
		$this->set('excess_permission',$excess_permission);
		$this->set('project_detail',$project_detail);
		$this->set('entites_data',$entites_data);
		$this->set('task_update',$task_update_data);	
		$this->set('userid',$userid);	
		$this->set('pricingval',$pricingval);
	
	}
	function project_inprogress_update(){
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin']['AppUser']['id'];
		//$projectid=base64_decode($id);
		$this->loadModel('Project');
		$this->loadModel('ProjectTask');
		$this->loadModel('RolePermission');
		$this->loadModel('SvcCatalogue');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('ProjectTaskUpdateHistory');
		
		$role_id=$sesn['Role'];
		$this->loadModel('Pricing');
		//$this->loadModel('ProjectPage');
		$sales_manager_id =  base64_decode($_GET['SM']);
		$project_manager_id =  base64_decode($_GET['PM']);
		$project_task_id =  base64_decode($_GET['task_id']);
		$projectid = base64_decode($_GET['id']);
		$status = $_GET['status'];
		//$this->loadModel('RemarksApproval');
		//pre($project_task_id); die;
		$short = array('ProjectTaskUpdateHistory.id'=>'DESC');
        $project_data = $this->Project->find('first',array('recursive'=>'-1','fields'=>array('Project.brief_description','Project.id','Project.status'),'conditions'=>array('Project.id'=>$projectid)));
		$pro_task = $this->ProjectTask->find('first',array('recursive'=>'-1','fields'=>array('ProjectTask.*'),'conditions'=>array('ProjectTask.id'=>$project_task_id,'ProjectTask.project_id'=>$projectid)));
		$servies = $this->SvcCatalogue->find('first',array('fields'=>array('SvcCatalogue.id','SvcCatalogue.svc_desc'),'conditions'=>array('SvcCatalogue.id'=>$pro_task['ProjectTask']['svc_ctlg_id'])));
		$pricing = $this->Pricing->find('first',array('fields'=>array('Pricing.*'),'conditions'=>array('Pricing.task_id'=>$project_task_id)));
		$billing_type = $this->MasterDataDetail->find('first',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$pricing['Pricing']['billing_type'])));
		//reason
		$pro_task_history = $this->ProjectTaskUpdateHistory->find('all',array('recursive'=>'-1','order'=>$short,'fields'=>array('ProjectTaskUpdateHistory.*'),'conditions'=>array('ProjectTaskUpdateHistory.project_id'=>$projectid,'ProjectTaskUpdateHistory.task_id'=>$project_task_id,'ProjectTaskUpdateHistory.status'=>1)));
		//pre($pro_task_history); die;
					
        $this->set('project_data',$project_data);
        $this->set('pro_task',$pro_task);
        $this->set('servies',$servies);
        $this->set('pricing',$pricing);
        $this->set('billing_type',$billing_type);
        $this->set('status',$status);
        $this->set('pro_task_history',$pro_task_history);
	}	

	function approve_project(){
		$this->loadModel('Project');
		$this->loadModel('Contact');
		$this->loadModel('BillingInput');
		$this->loadModel('RemarksApproval');
		$this->loadModel('ProjectTask');
		$this->loadModel('Pricing');
		$this->loadModel('ProjectTaskUpdateHistory');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
		$data = $this->request->data; 
		//pre($data); die;
		if($this->request->is('ajax')){
		    if(!empty($data)){
		    	$task_update = $this->ProjectTaskUpdateHistory->find('first',array('recursive'=>'-1','order'=>$short,'fields'=>array('ProjectTaskUpdateHistory.*'),'conditions'=>array('ProjectTaskUpdateHistory.project_id'=>$data['project_id'],'ProjectTaskUpdateHistory.status'=>1)));

                //APPROVE PROJECT
				$approve_pro['Project']['status']=$data['project_status'];		 
				$this->Project->id=$data['project_id'];
				$this->Project->save($approve_pro);

				 $customer_id = $this->Project->find('first', array('recursive'=>'-1','fields'=>
	    		array('Project.customer_entity_id','Project.project_id'),'conditions' => array('Project.id' =>$data['project_id'])));
				 $pricing_data = $this->Pricing->find('first', array('recursive'=>'-1','fields'=>
	    		 array('Pricing.per_unit_rate','Pricing.id'),'conditions' => array('Pricing.task_id' =>$task_update['ProjectTaskUpdateHistory']['task_id'])));
               if($task_update['ProjectTaskUpdateHistory']['id'] != '' && $data['project_status'] == 1){

	               	//BILLING INPUT UPDATE
				  if($task_update['ProjectTaskUpdateHistory']['task_status']==1){
	               	$approve_bill['BillingInput']['billing_approval_date']=date('Y-m-d');
	               	$approve_bill['BillingInput']['approved_by']=$sesn['Admin']['AppUser']['first_name']. ''.$sesn['Admin']['AppUser']['last_name'];
	               	$approve_bill['BillingInput']['status']=1;		 
					$this->BillingInput->project_id=$customer_id['Project']['project_id'];
					$this->BillingInput->task_id=$task_update['ProjectTaskUpdateHistory']['task_id'];
					$this->BillingInput->save($approve_bill);
                  }
                    $task_hist['ProjectTaskUpdateHistory']['status']=0;
	               	$this->ProjectTaskUpdateHistory->id=$task_update['ProjectTaskUpdateHistory']['id'];
					$this->ProjectTaskUpdateHistory->save($task_hist);


                   //PROJECT TASK UPDATE
                   if($task_update['ProjectTaskUpdateHistory']['status_desc']==1){
	               	$task_data['ProjectTask']['task_description']=$task_update['ProjectTaskUpdateHistory']['task_update_description'];
	               }
	               if($task_update['ProjectTaskUpdateHistory']['status_start_date']==1){
	               	$task_data['ProjectTask']['task_start_date']=$task_update['ProjectTaskUpdateHistory']['start_update_date'];
	               }
	               if($task_update['ProjectTaskUpdateHistory']['status_end_date']==1){

	               	$task_data['ProjectTask']['task_actual_end_date']=$task_update['ProjectTaskUpdateHistory']['scheduled_update_end_date'];
	               }
	               if($task_update['ProjectTaskUpdateHistory']['task_status']==1){
	               	$task_data['ProjectTask']['completion_dt']=date('Y-m-d');
	               }
	               	$task_data['ProjectTask']['is_completed']=$task_update['ProjectTaskUpdateHistory']['task_status'];
	               	$task_data['ProjectTask']['status']=0;
	               	$task_data['ProjectTask']['modified_by']=$userid;
	               	$task_data['ProjectTask']['modified_date']=date('Y-m-d');
	              		 
					$this->ProjectTask->project_id=$data['project_id'];
					$this->ProjectTask->id=$task_update['ProjectTaskUpdateHistory']['task_id'];
					$this->ProjectTask->save($task_data);

					//PRICING UPDATE
					if($task_update['ProjectTaskUpdateHistory']['status_qty'] == 1 ){
					    $total_task_amount = $task_update['ProjectTaskUpdateHistory']['update_qty'] * $pricing_data['Pricing']['per_unit_rate'];  
						$pric_data['Pricing']['total_task_amount']= $total_task_amount;
						$pric_data['Pricing']['modified_by']=$userid;
						$pric_data['Pricing']['modified_date']=date('Y-m-d');
						$this->Pricing->id= $pricing_data['Pricing']['id'];
					    $this->Pricing->save($pric_data);

					}
					$link=HTTP_ROOT.'home/project_pending';
					App::uses('CakeEmail', 'Network/Email');
					$Email = new CakeEmail();
					$Email->config('gmail'); 
					$Email->emailFormat('html'); 
					$Email->to($customer_email['Contact']['contact_email']);
					//$Email->to('ramjee2443@gmail.com');
				   if($data['project_status'] ==3){
					  $Email->subject('Projects Send For Remarks Response');	
					}else{
						$Email->subject('Projects Approval Response');
					}
					$Email->template('projects_approval');
					$Email->viewVars(array('from'=>$ses['Admin']['AppUser'],'status'=>$data['project_status'],'link'=>$link));
					$Email->send();

                }
				if($data['project_status'] == 3){
			    	$remarks_approve['RemarksApproval']['section']="project";
			    	$remarks_approve['RemarksApproval']['section_id']=$data['project_id'];
			    	$remarks_approve['RemarksApproval']['remarks_desc']=$data['remarks_project'];
			    	$remarks_approve['RemarksApproval']['remarks_by']=$userid;		 
			    	$remarks_approve['RemarksApproval']['created_date']=date('Y-m-d');
			    	$remarks_approve['RemarksApproval']['tab']='pro';
			    	$this->RemarksApproval->create();
			        $this->RemarksApproval->save($remarks_approve);

	            $customer_id = $this->Project->find('first', array('recursive'=>'-1','fields'=>
	    		array('Project.customer_entity_id'),'conditions' => array('Project.id' =>$data['project_id'])));

		        $customer_email = $this->Contact->find('first', array('recursive'=>'-1','fields'=>
	    		array('Contact.contact_email'),'conditions' => array('Contact.entity_id' =>$customer_id['Project']['customer_entity_id'])));

		        $link=HTTP_ROOT.'home/project_pending';
				App::uses('CakeEmail', 'Network/Email');
				$Email = new CakeEmail();
				$Email->config('gmail'); 
				$Email->emailFormat('html'); 
				//$Email->to($customer_email['Contact']['contact_email']);
				$Email->to('ramjee2443@gmail.com');
			   if($data['project_status'] ==3){
				  $Email->subject('Projects Send For Remarks Response');	
				}else{
					$Email->subject('Projects Approval Response');
				}
				$Email->template('projects_approval');
				$Email->viewVars(array('from'=>$ses['Admin']['AppUser'],'status'=>$data['project_status'],'link'=>$link));
				$Email->send();
			    }				
		    }
		    
		        echo "success"; die;
		}else{
		        echo "error"; die;
		}
		
	}
	function create_project_task_history(){
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin']['AppUser']['id'];
		$this->loadModel('ProjectTaskUpdateHistory');
		$this->loadModel('Project');
		$this->loadModel('Contract');
		$this->loadModel('Pricing');
		$this->loadModel('Document');
		$this->loadModel('ProfitCenter');
		$this->loadModel('Entity');
		$this->loadModel('ProjectTask');
		$this->loadModel('PaymentTerm');
		$this->loadModel('BillingInput');
		$this->loadModel('AppUser');
		$this->loadModel('BillingSetup');
		$this->loadModel('Company');
		$this->loadModel('MasterDataDetail');
		$data = $this->request->data;
		//pre($data['documents_file']); die;
		
		 if(!empty($data['documents_file'])){

		    for($i=0;$i<count($data['documents_file']);$i++){
		        // File upload configuration
	            $targetDir = HTTP_ROOT.'app/webroot/project_task_doc_file/';
	            //$allowTypes = array('jpg','png','jpeg','gif');
	            $data = $data['documents_file'][$i];
	            pre($data); die;
	            $image_parts = explode(";base64,", $data);
	            $image_type_aux = explode("image/", $image_parts[0]);
	            $image_type = $image_type_aux[1];
	            $image_base64 = base64_decode($image_parts[1]);
	            pre($image_base64);die;          
	            $uniueId = uniqid();
	            $file = $targetDir . $uniueId . '.'.$image_type;
	            $fileName = $uniueId . '.'.$image_type;
	            $path = HTTP_ROOT.'app/webroot/project_task_doc_file/'.$fileName;
	            pre($path);
	            file_put_contents($file, $image_base64);

			     $create_document['Document']['contract_id']=$contract_id;
				 $create_document['Document']['document_type']=7;
				 $create_document['Document']['doc_dms_url']= $path ;
				 $create_document['Document']['created_by']= $sesn['Admin']['AppUser']['id'];
				 $create_document['Document']['created_date']=date('Y-m-d H:i:s');
				 $create_document['Document']['is_active']=0;
				 pre($create_document); 
				 $this->Document->create();
				 $this->Document->save($create_document);
	           
	            }
		}

		//BILLING INPUT AFTER COMPLETED THE TASK
		if($data['task_status'] == 1){
		 	 $project_data  = $this->Project->find('first',array('joins' => array(
		      array('table' => 'entities','alias' => 'Entity','type' => 'INNER','conditions' => array('Entity.id = Project.customer_entity_id')),array('table' => 'contracts','alias' => 'Contract','type' => 'INNER','conditions' => array('Contract.id = Project.contract_id')),),'fields'=>array('Project.*','Entity.*','Contract.*'),'conditions'=>array('Project.id'=>$data['project_id'],'Project.status'=>1)));
		   $project_task_data = $this->ProjectTask->find('first',array('recursive'=>'-1','fields'=>array('ProjectTask.*'),'conditions'=>array('ProjectTask.project_id'=>$data['project_id'],'ProjectTask.id'=>$data['task_id'])));

		   $pricing_data = $this->Pricing->find('first',array('recursive'=>'-1','fields'=>array('Pricing.*'),'conditions'=>array('Pricing.task_id'=>$data['task_id'])));
		   $qty=$pricing_data['Pricing']['total_task_amount']/$pricing_data['Pricing']['per_unit_rate'];
		   $profit_center_id = $this->ProfitCenter->find('first',array('recursive'=>'-1','fields'=>array('ProfitCenter.pc_name'),'conditions'=>array('ProfitCenter.id'=>$project_data['Project']['profit_center_id'])));
		   $payment_term_ = $this->PaymentTerm->find('first',array('recursive'=>'-1','fields'=>array('PaymentTerm.payment_terms','PaymentTerm.description'),'conditions'=>array('PaymentTerm.id'=>$project_data['Contract']['payment_term_id'])));
		   $project_mgr = $this->AppUser->find('first',array('recursive'=>'-1','fields'=>array('AppUser.first_name','AppUser.last_name'),'conditions'=>array('AppUser.id'=>$project_data['Project']['project_mgr_id'])));
		   $billing_setup_data = $this->BillingSetup->find('first',array('recursive'=>'-1','fields'=>array('BillingSetup.dt_of_mth'),'conditions'=>array('BillingSetup.contract_id'=>$project_data['Project']['contract_id'])));
		   $company_name = $this->Company->find('first',array('recursive'=>'-1','fields'=>array('Company.company_name'),'conditions'=>array('Company.id'=>1)));
		   $user_name = $this->AppUser->find('first',array('recursive'=>'-1','fields'=>array('AppUser.first_name','AppUser.last_name'),'conditions'=>array('AppUser.id'=>$userid)));
		   $contract_type = $this->MasterDataDetail->find('first',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$project_data['Contract']['contract_type'] ))); 	
          //BILLING INPUT AFTER COMPLETED TASK    
		  $billing_input['BillingInput']['company_name']=$company_name['Company']['company_name'];		  
		  $billing_input['BillingInput']['customer_name']=$project_data['Entity']['entitiy_name'];
		  $billing_input['BillingInput']['customer_code']=$project_data['Entity']['entity_id'];
		  $billing_input['BillingInput']['contract_type']=$contract_type['MasterDataDetail']['master_data_desc'];
		  $billing_input['BillingInput']['project_id']=$project_data['Project']['project_id'];
		  $billing_input['BillingInput']['task_id']=$data['task_id'];
		  $billing_input['BillingInput']['task_description']=$data['pre_task_description'];
		  $billing_input['BillingInput']['task_qty']=$qty;
		  $billing_input['BillingInput']['qty_price']=$pricing_data['Pricing']['per_unit_rate'];
		  $billing_input['BillingInput']['task_actual_start_date']=$project_task_data['ProjectTask']['task_start_date'];
		  $billing_input['BillingInput']['task_original_end_date']=date("Y-m-d", strtotime(str_replace('/', '-',$project_task_data['ProjectTask']['task_end_date'])));
		  $billing_input['BillingInput']['task_actual_end_date']=date("Y-m-d", strtotime(str_replace('/', '-',$data['sche_update_endDate'])));
		  $billing_input['BillingInput']['service_name']=$data['services'];
		  $billing_input['BillingInput']['billing_type']=$data['task_type'];
		  $billing_input['BillingInput']['amount']=$pricing_data['Pricing']['total_task_amount'];
		  $billing_input['BillingInput']['doc_currency']='INR';
		  $billing_input['BillingInput']['billing_input_date']=date('Y-m-d H:i:s');
		  $billing_input['BillingInput']['billed_date']=date('Y-m-d H:i:s');
		  $billing_input['BillingInput']['project_manager']=$project_mgr['AppUser']['first_name'].''.$project_mgr['AppUser']['last_name'];
		  //$billing_input['BillingInput']['project_manager_id']=$project_data['Project']['project_mgr_id'];
		  //$billing_input['BillingInput']['created_by_id']=$project_data['Project']['project_mgr_id'];
		  $billing_input['BillingInput']['profit_center_name']=$profit_center_id['ProfitCenter']['pc_name'];
		  $billing_input['BillingInput']['customer_creation_date']=$project_data['Entity']['created_date'];
		  $billing_input['BillingInput']['payment_terms']=$payment_term_['PaymentTerm']['payment_terms'];
		  $billing_input['BillingInput']['payment_terms_description']=$payment_term_['PaymentTerm']['description'];
		  $billing_input['BillingInput']['credit_limit_amount']=$project_data['Entity']['credit_limit'];
		  $billing_input['BillingInput']['created_by']=$user_name['AppUser']['first_name'].''.$user_name['AppUser']['last_name'];
		  $billing_input['BillingInput']['created_by_id']=$userid;
		  $billing_input['BillingInput']['created_date']=date('Y-m-d H:i:s');
		  $billing_input['BillingInput']['remarks']=$data['remarks'];
		  $billing_input['BillingInput']['contract_id']=$project_data['Contract']['contract_number'];
		  //pre($billing_input); die;
		  $this->BillingInput->create();
		  $this->BillingInput->save($billing_input);
		}
		//HISTORY OF TASK UPDATE
		if($data['task_status'] == 1){
           $history_status = 0;
		} 
		   $history_status = 1;

            $pro_task_history['ProjectTaskUpdateHistory']['project_id']=$data['project_id'];
		    $pro_task_history['ProjectTaskUpdateHistory']['task_id']=$data['task_id'];
		 if($data['task_dec_status'] == 1){
		 	$pro_task_history['ProjectTaskUpdateHistory']['task_pre_description']=$data['pre_task_description'];
		    $pro_task_history['ProjectTaskUpdateHistory']['task_update_description']=$data['task_desc'];
		    $pro_task_history['ProjectTaskUpdateHistory']['task_desc_reason']=$data['task_des_reason'];
		    $pro_task_history['ProjectTaskUpdateHistory']['status_desc']=$data['task_dec_status'];
		 }
		 if($data['start_date_status'] == 1){
		 	$pro_task_history['ProjectTaskUpdateHistory']['start_pre_date']=date("Y-m-d", strtotime(str_replace('/', '-', $data['prev_start_date'])));
		    $pro_task_history['ProjectTaskUpdateHistory']['start_update_date']=date("Y-m-d", strtotime(str_replace('/', '-', $data['update_startedDate'])));
		    $pro_task_history['ProjectTaskUpdateHistory']['start_date_reason']=$data['start_date_reason_desc'];
		    $pro_task_history['ProjectTaskUpdateHistory']['status_start_date']=$data['start_date_status'];
		 }

		if($data['end_date_status'] == 1){
			$pro_task_history['ProjectTaskUpdateHistory']['scheduled_pre_end_date']=date("Y-m-d", strtotime(str_replace('/', '-', $data['end_date'])));
		    $pro_task_history['ProjectTaskUpdateHistory']['scheduled_update_end_date']=date("Y-m-d", strtotime(str_replace('/', '-',$data['sche_update_endDate'])));
		    $pro_task_history['ProjectTaskUpdateHistory']['end_date_reason']=$data['end_date_reason_desc'];
		    $pro_task_history['ProjectTaskUpdateHistory']['status_end_date']=$data['end_date_status'];
		}
		 if($data['qty_status']==1){
		 	$pro_task_history['ProjectTaskUpdateHistory']['pre_qty']=$data['pre_qty'];
		    $pro_task_history['ProjectTaskUpdateHistory']['update_qty']=$data['update_quantity'];
		    $pro_task_history['ProjectTaskUpdateHistory']['qty_reason']=$data['qty_chnage_reasone'];
		    $pro_task_history['ProjectTaskUpdateHistory']['status_qty']=$data['qty_status'];
		 }	
		    $pro_task_history['ProjectTaskUpdateHistory']['task_status']=$data['task_status'];		 
		    $pro_task_history['ProjectTaskUpdateHistory']['remarks']=$data['remarks'];
		    $pro_task_history['ProjectTaskUpdateHistory']['created_by']=$userid;
		    $pro_task_history['ProjectTaskUpdateHistory']['created_date']=date('Y-m-d H:i:s');
		    $pro_task_history['ProjectTaskUpdateHistory']['status']=$history_status;
		
			 $this->ProjectTaskUpdateHistory->create();
			 $this->ProjectTaskUpdateHistory->save($pro_task_history);

			 //project status change for update
			 $pro_status['Project']['id']=$data['project_id'];
			 $pro_status['Project']['status']=0;
			 $this->Project->save($pro_status);
			  //project status change for update
			 $pro_task_status['ProjectTask']['project_id']=$data['project_id'];
			 $pro_task_status['ProjectTask']['id']=$data['task_id'];
			 $pro_task_status['ProjectTask']['status']=2;
			 $this->ProjectTask->save($pro_task_status);
		    	
		 echo "success";die;

	}
	function datamove()
	{
		$this->loadModel('DataTable');
		$data=$this->DataTable->find('all',array(
		'joins' => array(
		array('table' => 'entities','alias' => 'Entitie','type' => 'LEFT','conditions' => array('Entitie.entity_id = DataTable.customerid')),
		
		),
		'fields'=>array('Entitie.entitiy_name','DataTable.DocDate','DataTable.GST_ExciseInvDate')));
		//pre($data);die;
		$this->set('data',$data);
	}	
	


	function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$@&';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	
	
	function data_upload_d($type=null) 
	{ 
		ini_set('memory_limit','4096M');
		ini_set('max_execution_time', 300);
		$this->loadModel('DataTable');
		
		
		if($type=='downloadexcel_error')
		{
			$filePath 		= '../webroot/files/'; 
			$fileName = "ProjectErrorSheet.xlsx";
			print_r(HTTP_ROOT.'files/'.$fileName);
			unlink($filePath.$fileName);die;
		}
		
		if($_FILES)
		{  
	
			$chkError=0;
			$errorData=array();
			$MediaName = $_FILES['uploadfile']['name']; 
			$MediaTempName 	= $_FILES['uploadfile']['tmp_name'];
			$MediaExtension = pathinfo($MediaName, PATHINFO_EXTENSION);
			$MediaNewName	= date("YmdHis").'.'.$MediaExtension;
			$filePath 		= '../webroot/files/'; 
			$fileNewPath 		= $filePath.$MediaNewName; 
			move_uploaded_file($MediaTempName, $fileNewPath); 
			$MediaNewName = str_replace(' ','',$MediaNewName); 
			//pre(1);die;
			/*Save Exl to data base start*/
			
			$this->layout = false;
			CakePlugin::load('PHPExcel');
			App::uses('PHPExcel', 'PHPExcel.Classes');
			$objPHPExcel = new PHPExcel();
			
			$input_file_type = PHPExcel_IOFactory::identify('../webroot/files/'.$MediaNewName); 
			$obj_reader = PHPExcel_IOFactory::createReader($input_file_type); 
			$obj_reader->setReadDataOnly(true); 
			
			$objPHPExcel = $obj_reader->load('../webroot/files/'.$MediaNewName); 
			$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
			$highest_row = $objWorksheet->getHighestRow();
			//echo $highest_row;die;
			$highest_col = $objWorksheet->getHighestColumn();
							
			$header_row = $objWorksheet->rangeToArray('A1:AQ1'); 
					pre($header_row);die;
		/*	if($header_row[0][0]!='CompanyCode' || $header_row[0][0]=='' || $header_row[0][1]!='CompanyName' 
			|| $header_row[0][1]=='' || $header_row[0][2]!='ProfitCenter' || $header_row[0][2]==''|| 
			$header_row[0][3]!='ProfitCenterName' || $header_row[0][3]=='' || $header_row[0][4]!='SalesDivisionCode' || 
			$header_row[0][4]==''|| $header_row[0][5]!='SalesDivisionName' || $header_row[0][5]=='' || 
			$header_row[0][6]!='CustomerCode' || $header_row[0][6]=='' || $header_row[0][7]!='CustomerName' || 
			$header_row[0][7]=='' || $header_row[0][8]!='FiscalYear' || $header_row[0][8]=='' || 
			$header_row[0][9]!='SaleOrderNo' || $header_row[0][9]=='' || $header_row[0][10]!='CustomerCreationDate' || 
			$header_row[0][10]=='' || $header_row[0][11]!='AccountingDoc' || $header_row[0][11]=='' ||
			$header_row[0][12]!='DocDate' || $header_row[0][12]==''|| 
			$header_row[0][13]!='GST/ExciseInvoiceNo' || $header_row[0][13]=='' || 
			$header_row[0][14]!='GST/ExciseInvDate' || $header_row[0][14]=='' ||
			$header_row[0][15]!='DocType' || $header_row[0][15]=='' ||
			$header_row[0][16]!='DocDescription' || $header_row[0][16]==''|| 
			$header_row[0][17]!='DocPostingDate' || $header_row[0][17]=='' || 
			$header_row[0][18]!='PaymentTerms' || $header_row[0][18]=='' ||
			$header_row[0][19]!='PaymentTermsDescription' || $header_row[0][19]=='' || 
			$header_row[0][20]!='CreditLimitAmount' || $header_row[0][20]=='' || 
			$header_row[0][21]!='SpecialGL' || $header_row[0][21]=='' || 
			$header_row[0][22]!='GLAccountNo' || $header_row[0][22]=='' || 
			$header_row[0][23]!='GLDescription' || $header_row[0][23]=='' || 
			$header_row[0][24]!='RefrenceKey' || $header_row[0][24]=='' || 
			$header_row[0][25]!='ARPledgingIndicator' || $header_row[0][25]=='' || 
			$header_row[0][26]!='OriginalAmount' || $header_row[0][26]=='' || 
			$header_row[0][27]!='DebitAmount' || $header_row[0][27]=='' || 
			$header_row[0][28]!='CreditAmount' || $header_row[0][28]=='' || 
			$header_row[0][29]!='Netamountoutstanding' || $header_row[0][29]=='' ||
			$header_row[0][30]!='AmountInDocCurrency' || $header_row[0][30]=='' || 
			$header_row[0][31]!='InvRef' || $header_row[0][31]=='' || 
			$header_row[0][32]!='InvRefyear' || $header_row[0][32]=='' || 
			$header_row[0][33]!='DocCurrency' || $header_row[0][33]=='' || 
			$header_row[0][34]!='CurrencyExchangeRate' || $header_row[0][34]=='' || 
			$header_row[0][35]!='Advance/EMD/Securities' || $header_row[0][35]=='' || 
			$header_row[0][36]!='UnadjustedPayment' || $header_row[0][36]=='' || 
			$header_row[0][37]!='DueDate' || $header_row[0][37]=='' || 
			$header_row[0][38]!='CustomerType' || $header_row[0][38]==''  || 
			$header_row[0][39]!='DomesticCustomer' || $header_row[0][39]=='' || 
			$header_row[0][40]!='OverseasCustomer' || $header_row[0][40]=='' || 
			$header_row[0][41]!='NotDue' || $header_row[0][41]==''  || 
			$header_row[0][42]!='TotalOverdue' || $header_row[0][42]=='')
			{
				echo 'invalid';die;						
			}*/
					
				$m=1;$n=1;$unsuccess='';$success='';
				$count_team=0;$memb=array();
			
				for($counter = 2; $counter <= $highest_row; $counter++)
				{ 
					$chkrowError=0;
					$row = $objWorksheet->rangeToArray('A'.$counter.':'.$highest_col.$counter);  
															
				    $CompanyCode   = $row[0][0];
					$CompanyName        = $row[0][1];
					$ProfitCenter             = $row[0][2];
					$ProfitCenterName        = $row[0][3];
					$SalesDivisionCode       = $row[0][4];
					$SalesDivisionName       = $row[0][5];
					$CustomerCode      = $row[0][6];
					$CustomerName             =$row[0][7];
					$FiscalYear         = $row[0][8];
					$SaleOrderNo   = $row[0][9];
					$CustomerCreationDate   = $row[0][10];
					$AccountingDoc = $row[0][11];
					$DocDate    = $row[0][12];
					$GSTExciseInvoiceNo = $row[0][13];
					$GSTExciseInvDate = $row[0][14];
					$DocType    = $row[0][15];
					$DocDescription =$row[0][16];
					$DocPostingDate = $row[0][17];
					$PaymentTerms = $row[0][18];
					$PaymentTermsDescription  = $row[0][19];
					$CreditLimitAmount= $row[0][20];
					
					$SpecialGL  =$row[0][21];
					$GLAccountNo=$row[0][22];
					$GLDescription=$row[0][23];
					$RefrenceKey =$row[0][24];
					$ARPledgingIndicator = $row[0][25];
					$OriginalAmount = $row[0][26];
					
					$DebitAmount=$row[0][27];
					$CreditAmount=$row[0][28];
					
					$Netamountoutstanding  = $row[0][29];
					$AmountInDocCurrency = $row[0][30];
					$InvRef= $row[0][31];
					
					$InvRefyear=$row[0][32];
					$DocCurrency =$row[0][33];
				
					$CurrencyExchangeRate=$row[0][34];
					$AdvanceEMDSecurities =$row[0][35];
					$UnadjustedPayment  =$row[0][36];
					
					$DueDate=$row[0][37];
					$CustomerType=$row[0][38];
					
					$DomesticCustomer=$row[0][39];
					$OverseasCustomer=$row[0][40];
					$NotDue=$row[0][41];
					
					$TotalOverdue=$row[0][42];
					//pre($row);die;
									
					if($chkrowError==0)
					{
							
					//echo 'bfhfbh';die;
												
						$data['DataTable']['CompanyCode']=$CompanyCode;
						
						$data['DataTable']['CompanyName']=$CompanyName;
						$data['DataTable']['ProfitCenter']=$ProfitCenter;
						$data['DataTable']['ProfitCenterName']=$ProfitCenterName;
						$data['DataTable']['SalesDivisionCode']=$SalesDivisionCode;
						$data['DataTable']['SalesDivisionName']=$SalesDivisionName;
						$data['DataTable']['CustomerCode']=$CustomerCode;
						$data['DataTable']['CustomerName']=$CustomerName;
						$data['DataTable']['FiscalYear']=$FiscalYear;
						$data['DataTable']['SalesOrderNo']=$SaleOrderNo;
						$data['DataTable']['CustomerCreationDate']=$CustomerCreationDate;
						$data['DataTable']['AccountingDoc']=$AccountingDoc;
						$data['DataTable']['DocDate']=date("Y-m-d", strtotime($DocDate));
						$data['DataTable']['GST/ExciseInvoiceNo']=$GSTExciseInvoiceNo;
						$data['DataTable']['GST/ExciseInvDate']=date("Y-m-d", strtotime($GSTExciseInvDate));
						$data['DataTable']['DocType']=$DocType;
						$data['DataTable']['DocDescription']=$DocDescription;
						$data['DataTable']['DocPostingDate']=$DocPostingDate;
						$data['DataTable']['PaymentTerms']=$PaymentTerms;
						$data['DataTable']['PaymentTermsDescription']=$PaymentTermsDescription;
						$data['DataTable']['CreditLimitAmount']=$CreditLimitAmount;
						$data['DataTable']['SpecialGL']=$SpecialGL;
						$data['DataTable']['GLAccountNo']=$GLAccountNo;
						$data['DataTable']['GLDescription']=$GLDescription;
						$data['DataTable']['RefrenceKey']=$RefrenceKey;
						$data['DataTable']['ARPledgingIndicator']=$ARPledgingIndicator;
						$data['DataTable']['OriginalAmount']=$OriginalAmount;
						$data['DataTable']['DebitAmount']=$DebitAmount;
						$data['DataTable']['CreditAmount']=$CreditAmount;
						$data['DataTable']['Netamountoutstanding']=$Netamountoutstanding;
						$data['DataTable']['AmountInDocCurrency']=$AmountInDocCurrency;
						$data['DataTable']['InvRef']=$InvRef;
						$data['DataTable']['InvRefyear']=$InvRefyear;
						$data['DataTable']['DocCurrency']=$DocCurrency;
						$data['DataTable']['CurrencyExchangeRate']=$CurrencyExchangeRate;
						$data['DataTable']['Advance/EMD/Securities']=$AdvanceEMDSecurities;
						$data['DataTable']['UnadjustedPayment']=$UnadjustedPayment;
						$data['DataTable']['DueDate']=date("Y-m-d", strtotime($DueDate));
						$data['DataTable']['CustomerType']=$CustomerType;
						$data['DataTable']['DomesticCustomer']=$DomesticCustomer;
						$data['DataTable']['OverseasCustomer']=$OverseasCustomer;
						$data['DataTable']['NotDue']=$NotDue;
						$data['DataTable']['TotalOverdue']=$TotalOverdue;
						
						//pre($data);die;
								$this->DataTable->create();	
								$this->DataTable->save($data);
												
								$row = reset($row); 
					}
					
				}
				
				unlink('../webroot/files/'.$MediaNewName);
			if($chkError==0)
			{
							
		echo ('Success');die;
			}			
		}
	}
	
	function dataupload()
	{
	}	
	function transaction()
	{
		$this->loadModel('OtherTransaction');
		$this->loadModel('Entitie');
		$this->loadModel('ArCategory');
		$this->loadModel('Contact');
		$this->loadModel('MasterDataDetail');

		$this->paginate = array('limit'=>'10','field'=>array('OtherTransaction.*'));	   
		$other_data = $this->paginate('OtherTransaction');

        $ar_category = $this->ArCategory->find('all',array('fields'=>array('ArCategory.*')));
        $contacts_role = $this->MasterDataDetail->find('all',array('joins'=>array(array('table'=>'contacts','alias'=>'Contact',
		 	'type'=>'LEFT','conditions'=>array('Contact.contact_role = MasterDataDetail.id'))),
		 'fields'=>array('Contact.*'), 'conditions'=>array('MasterDataDetail.master_data_desc'=>array('Finance','Sales'))));
		 
		//pre($other_data);die;
		$this->set('contacts_role',$contacts_role);	
		$this->set('invoices_data',$other_data);
		$this->set('ar_category',$ar_category);
	}
	public function find_ar_sub_category()
	{
		$this->loadModel('ArSubCategory');
		  $data = $this->request->data;
        $option='';
		 $ar_sub_category = $this->ArSubCategory->find('list',array('fields'=>array('ArSubCategory.ar_sub_cat'),
		 	'conditions'=>array('ArSubCategory.ar_cat_id'=>$data['ar_category_id'])));
		 foreach($ar_sub_category as $k=>$sub)
		 {
		 	 $option.='<option value="'.$k.'">'.$sub.'</option>';
		 }
		 $msg['option']=	$option;
		  echo json_encode($msg); die;
	}
	public function find_data_invoice()
	{
		 $data = $this->request->data;
		 //pre($data);die();
	}
	function createworkflow()
	{   
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin']['AppUser']['id'];
		$role_id=$sesn['Role'];
		//pre($role_id); die;
		$this->loadModel('SvcCatalogue');
		$this->loadModel('Group');
		$this->loadModel('Permission');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('PaymentTerm');
		$this->loadModel('Entitie');
		$this->loadModel('State');
		$this->loadModel('AppUser');
		$this->loadModel('InvoiceStage');
		$this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
		$this->loadModel('ProfitCenter');
		$tab = $_GET['page'];
		$get_id = $_GET['id'];
		$cus_tab = $_GET['tab'];
		$entity_id=base64_decode($get_id);

		$customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Customer')));
		 $cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id['ProjectPage']['id']);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' => 1 ,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));
		//pre($excess_permission); die;
			
		if($entity_id != ''){
			$entity_detail = $this->Entitie->find('first',array('conditions'=>array('Entitie.id'=>$entity_id)));
		}				
		$groups = $this->Group->find('all',array('recursive'=>'-1','fields'=>array('Group.group_name','Group.id')));
		$address_type = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'Address Type','MasterDataDetail.is_active'=>1)));
		$contract_type = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'contract_type','MasterDataDetail.is_active'=>1)));
		$permission = $this->Permission->find('all',array('recursive'=>'-1','fields'=>array('Permission.id','Permission.permission_desc'),'conditions'=>array('Permission.is_active'=>1)));
		$payment_term = $this->PaymentTerm->find('all',array('recursive'=>'-1','fields'=>array('PaymentTerm.payment_terms','PaymentTerm.id'),'conditions'=>array('PaymentTerm.is_active'=>1)));
		$customers = $this->Entitie->find('all',array('recursive'=>'-1','fields'=>array('Entitie.id','Entitie.entitiy_name')));
		$State = $this->State->find('all',array('recursive'=>'-1','fields'=>array('State.id','State.state_name'),'conditions'=>array('State.status'=>1)));
		//========== find all collector by robin on 15-1-19 ==========//
		$permissionData = $this->Permission->find('first',array('fields'=>array('Permission.id','Permission.permission_desc'),'conditions'=>array('Permission.permission_desc'=>'Collector')));
		
		$collectorData = $this->AppUser->find('all',array('fields'=>array('AppUser.id','AppUser.entity_id','AppUser.login_id','AppUser.role_id',
		                 'AppUser.first_name','AppUser.last_name','AppUser.user_mobile','AppUser.user_email'),
		                'conditions'=>array('AppUser.role_id'=>$permissionData['Permission']['id'])));
		
		$invoiceData = $this->InvoiceStage->find('all',array('fields'=>array('InvoiceStage.id','InvoiceStage.stage_desc','InvoiceStage.seq_no'),
		                          'conditions'=>array('InvoiceStage.id !='=>21)));
		$billing_freq = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'Billing freq','MasterDataDetail.is_active'=>1)));
		//pre($invoiceData);die;
		//=========== END collector =========================//

		//pre($excess_permission);die;
		$this->set('$excess_permission',$excess_permission);
		$this->set('entity_detail',$entity_detail);
		$this->set('cus_tab',$cus_tab);
		$this->set('tab',$tab);
		$this->set('State',$State);
		$this->set('groups',$groups);
		$this->set('address_type',$address_type);
		$this->set('contract_type',$contract_type);
		$this->set('permission',$permission);
	    $this->set('payment_term',$payment_term);
	    $this->set('customers',$customers);
	    $this->set('collectorData',$collectorData);
		$this->set('invoiceData',$invoiceData);
		//billing
		$this->set('billing_freq',$billing_freq);
		/**************Neeraj On Project**************/
		$billing_type = $this->MasterDataDetail->find('list',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'billing_type','MasterDataDetail.is_active'=>1)));
		$this->set('billing_type',$billing_type);
		$servies = $this->SvcCatalogue->find('list',array('fields'=>array('SvcCatalogue.id','SvcCatalogue.svc_desc')));
		$this->set('servies',$servies);
		$salemanger = $this->AppUser->find('all',array('recursive'=>'-1','fields'=>array('AppUser.id','AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions'=>array('AppUser.role_id'=>14)));
		$projectmanger = $this->AppUser->find('all',array('recursive'=>'-1','fields'=>array('AppUser.id','AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions'=>array('AppUser.role_id'=>1)));
		$profitcenter = $this->ProfitCenter->find('list',array('recursive'=>'-1','fields'=>array('ProfitCenter.id','ProfitCenter.pc_name'),'conditions'=>array('ProfitCenter.is_active'=>1)));
		$this->set('profitcenter',$profitcenter);
		$this->set('salemanger',$salemanger);
		$this->set('projectmanger',$projectmanger);
		/*******************End***********************/
		
	}

	function createworkflow_edit()
	{
		$this->loadModel('Group');
		$this->loadModel('Permission');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('PaymentTerm');
		$this->loadModel('Entitie');
		$this->loadModel('State');
		$this->loadModel('RemarksApproval');
		$sesn=$this->Session->read('admin');
		$tab = $_GET['page'];
		$remarks = $_GET['remarks'];
		$get_id = $_GET['id'];
		$cus_tab = $_GET['tab'];
		$entity_id=base64_decode($get_id);		
        $remark_data =$this->RemarksApproval->find('all',array('conditions'=>array('RemarksApproval.section_id'=>$entity_id,'RemarksApproval.tab'=>$cus_tab)));
        if($cus_tab!='pro')
		{
		if($entity_id != ''){
			$entity_detail = $this->Entitie->find('first',array('conditions'=>array('Entitie.id'=>$entity_id)));
		}				
		$groups = $this->Group->find('all',array('recursive'=>'-1','fields'=>array('Group.group_name','Group.id')));
		$address_type = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'Address Type','MasterDataDetail.is_active'=>1)));
		$contract_type = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'contract_type','MasterDataDetail.is_active'=>1)));
		$permission = $this->Permission->find('all',array('recursive'=>'-1','fields'=>array('Permission.id','Permission.permission_desc'),'conditions'=>array('Permission.is_active'=>1)));
		$payment_term = $this->PaymentTerm->find('all',array('recursive'=>'-1','fields'=>array('PaymentTerm.payment_terms','PaymentTerm.id'),'conditions'=>array('PaymentTerm.is_active'=>1)));
		$customers = $this->Entitie->find('all',array('recursive'=>'-1','fields'=>array('Entitie.id','Entitie.entitiy_name')));
		$State = $this->State->find('all',array('recursive'=>'-1','fields'=>array('State.id','State.state_name'),'conditions'=>array('State.status'=>1)));
        }
		//pre($entity_detail);die;
		$this->set('entity_detail',$entity_detail);
		$this->set('cus_tab',$cus_tab);
		$this->set('tab',$tab);
		$this->set('State',$State);
		$this->set('groups',$groups);
		$this->set('address_type',$address_type);
		$this->set('contract_type',$contract_type);
		$this->set('permission',$permission);
	    $this->set('payment_term',$payment_term);
	    $this->set('customers',$customers);
	    $this->set('remarks',$remarks);
	    $this->set('remark_data',$remark_data);
	    //pre($remark_data); die;
	  

	     if($cus_tab=='pro')
		{
			$projict_id=base64_decode($get_id); 
			$this->loadModel('Pricing');
			$this->loadModel('Project');
			$this->loadModel('ProjectTask');
			$this->loadModel('Contract');
			$this->loadModel('ProfitCenter');
			$this->loadModel('MasterDataDetail');
			$this->loadModel('SvcCatalogue');
			$customers = $this->Entitie->find('all',array('recursive'=>'-1','fields'=>array('Entitie.id','Entitie.entitiy_name')));
			$project = $this->Project->find('first',array('recursive'=>'-1','fields'=>array('Project.*'),'conditions'=>array('Project.id'=>$projict_id)));
			$this->set('project',$project['Project']);
			
			$contracts_data = $this->Contract->find('all',array('group'=>'Contract.id','recursive'=>'-1','fields'=>array('Contract.contract_title','Contract.id','Contract.credit_limit','Contract.credit_period','Contract.contract_number'),'conditions'=>array('Contract.cust_entity_id'=>$project['Project']['customer_entity_id']))); 
			$this->set('contracts_data',$contracts_data);
			$profitcenter = $this->ProfitCenter->find('list',array('recursive'=>'-1','fields'=>array('ProfitCenter.id','ProfitCenter.pc_name'),'conditions'=>array('ProfitCenter.is_active'=>1)));
		    $this->set('profitcenter',$profitcenter);
			$salemanger = $this->AppUser->find('all',array('recursive'=>'-1','fields'=>array('AppUser.id','AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions'=>array('AppUser.role_id'=>14)));
			$projectmanger = $this->AppUser->find('all',array('recursive'=>'-1','fields'=>array('AppUser.id','AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions'=>array('AppUser.role_id'=>1)));
			$this->set('salemanger',$salemanger);
			$this->set('projectmanger',$projectmanger);
			$billing_type = $this->MasterDataDetail->find('list',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'billing_type','MasterDataDetail.is_active'=>1)));
			$this->set('billing_type',$billing_type);
			$servies = $this->SvcCatalogue->find('list',array('fields'=>array('SvcCatalogue.id','SvcCatalogue.svc_desc'))); //pre($servies);die;
			$this->set('servies',$servies);
			$p_task = $this->ProjectTask->find('list',array('recursive'=>'-1','fields'=>array('ProjectTask.id','ProjectTask.id'),'conditions'=>array('ProjectTask.project_id'=>$projict_id)));
			$pricingval1 = $this->Pricing->find('all',array('group'=>'Pricing.billing_type','recursive'=>'-1','fields'=>array('Pricing.*'),'conditions'=>array('Pricing.task_id'=>$p_task,'Pricing.billing_type !='=>9)));
			$pricingval2 = $this->Pricing->find('all',array('group'=>'Pricing.billing_type','recursive'=>'-1','fields'=>array('Pricing.*'),'conditions'=>array('Pricing.task_id'=>$p_task,'Pricing.billing_type'=>9)));
			$pricingval=array_merge($pricingval1,$pricingval2);
			$this->set('pricingval',$pricingval);
		}
		$customers = $this->Entitie->find('all',array('recursive'=>'-1','fields'=>array('Entitie.id','Entitie.entitiy_name')));
		$this->set('customers',$customers);
		$this->set('cus_tab',$cus_tab);	
		//$this->set('tab',$tab);
		//$this->set('remark_data',$remark_data);

		
	}
	function duplicacy_check_pan(){
		$this->loadModel('Entitie');
		$data=$this->request->data;		
		$duplicacy_pan = $this->Entitie->find('first',array('recursive'=>'-1','fields'=>array('Entitie.id'),'conditions'=>array('Entitie.entity_pan'=>$data['pan_num'])));		
		if($duplicacy_pan['Entitie']['id'] !='' ){
			echo "duplicacy";die;
		}else{
			echo "success";die;
		}		
	}
	function duplicacy_check_tan(){
		$this->loadModel('Entitie');
		$data=$this->request->data;
		//pre($data); die;
		$duplicacy_tan = $this->Entitie->find('first',array('recursive'=>'-1','fields'=>array('Entitie.id'),'conditions'=>array('Entitie.entity_tan'=>$data['tan_number'])));
		if($duplicacy_tan['Entitie']['id'] !='' ){
			echo "duplicacy";die;
		}else{
			echo "success";die;
		}		
	}
	function duplicacy_check_gst(){
		$this->loadModel('Entitie');
		$data=$this->request->data;
		//pre($data); die;
		$duplicacy_gst = $this->Entitie->find('first',array('recursive'=>'-1','fields'=>array('Entitie.id'),'conditions'=>array('Entitie.entity_gst'=>$data['gst_number'])));
		if($duplicacy_gst['Entitie']['id'] !='' ){
			echo "duplicacy";die;
		}else{
			echo "success";die;
		}		
	}
	function get_address($type=null)
	{
		$this->loadModel('Company');
		$this->loadModel('CompanyAddress');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('Contract');
		$this->loadModel('EntityAddresse');
		$msg='';$from_address='';
		$data=$this->request->data;
		if($type=='from_add')
		{
			$companie_detail = $this->Company->find('first',array('recursive'=>'-1','fields'=>array('Company.id')));
			$comp_add = $this->CompanyAddress->find('all',array('recursive'=>'-1','fields'=>array('CompanyAddress.*'),'conditions'=>array('CompanyAddress.company_id'=>$companie_detail['Company']['id'])));
						
			foreach($comp_add as $k=>$comp_address)			
			{	
				$chk="checked='checked'";
				$address_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$comp_address['CompanyAddress']['address_type'],'MasterDataDetail.is_active'=>1)));

				$zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$comp_address['CompanyAddress']['zone'])));
				if($data['val']!=$comp_address['CompanyAddress']['id'])
				$chk='';
				$from_address.='<li><label class="clicklable" for="managerrole"><div class="customcheckbox"><label><input '.$chk.' type="radio" name="addess_radio" value="'.$comp_address['CompanyAddress']['id'].'"  class="addClickadd from_to_radio"> <b></b></label></div><div class="rolecategoery"><h3>'.$address_type['MasterDataDetail']['master_data_desc'].'</h3><p>'.$comp_address['CompanyAddress']['address_line_1'].','.$comp_address['city'].','.$comp_address['CompanyAddress']['postal_code'].'</p><p>Zone :'.$zone['MasterDataDetail']['master_data_desc'].'</p></div></label></li>';
			}
			$msg['from_address'] = $from_address;
		}
		else if($type=='bill_shp_add')
		{
			$contrct = $this->Contract->find('first',array('recursive'=>'-1','fields'=>array('Contract.cust_entity_id'),'conditions'=>array('Contract.id'=>$data['contract_id'])));
			$addrss = $this->EntityAddresse->find('all',array('recursive'=>'-1','fields'=>array('EntityAddresse.id','EntityAddresse.address_line_1','EntityAddresse.address_line_2','EntityAddresse.state','EntityAddresse.city','EntityAddresse.country','EntityAddresse.postal_code','EntityAddresse.zone'),'conditions'=>array('EntityAddresse.entity_id'=>$contrct['Contract']['cust_entity_id'])));
			foreach($addrss as $add)
			{
				$zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$add['EntityAddresse']['zone'])));
				$chk="checked='checked'";
				if($data['val']!=$add['EntityAddresse']['id'])
				$chk='';
				$from_address.='<li><label class="clicklable" for="managerrole"><div class="customcheckbox"><label><input '.$chk.' type="radio" name="addess_radio" value="'.$add['EntityAddresse']['id'].'"  class="addClickadd from_to_radio"> <b></b></label></div><div class="rolecategoery"><h3>'.$add['EntityAddresse']['address_line_1'].'</h3><p>'.$add['EntityAddresse']['address_line_2'].' '.$add['EntityAddresse']['city'].','.$add['EntityAddresse']['state'].','.$add['EntityAddresse']['country'].'</p><p>Postal Code :'.$add['EntityAddresse']['postal_code'].'</p><p>Zone :'.$zone['MasterDataDetail']['master_data_desc'].'</p></div></label></li>';
			}
			$msg['from_address'] = $from_address;
		}
		echo json_encode($msg); die;	
	}
	function find_last_customer(){
		$this->loadModel('Companie');
		$this->loadModel('CompanyAddress');
		$this->loadModel('Entitie');
		$this->loadModel('MasterDataDetail');

		$last_entity_id  = $this->Entitie->find('first',array('order'=>'Entitie.id DESC','fields'=>array('Entitie.id','Entitie.entity_id','Entitie.credit_period','Entitie.credit_limit','Entitie.entity_turnover')));
		$companie_detail = $this->Companie->find('first',array('fields'=>array('Companie.id')));
		$comp_add = $this->CompanyAddress->find('all',array('fields'=>array('CompanyAddress.*'),'conditions'=>array('CompanyAddress.company_id'=>$companie_detail['Companie']['id'])));	
		foreach($comp_add as $k=>$comp_address)			
		 {	
		 	$address_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$comp_address['CompanyAddress']['address_type'],'MasterDataDetail.is_active'=>1)));

		 	$zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$comp_address['CompanyAddress']['zone'])));
		 	
            $from_address.='<li><label class="clicklable" for="managerrole"><div class="customcheckbox"><label><input type="radio" name="addess_radio" value="'.$comp_address['CompanyAddress']['id'].'"  class="addClickadd from_to_radio"> <b></b></label></div><div class="rolecategoery"><h3>'.$address_type['MasterDataDetail']['master_data_desc'].'</h3><p>'.$comp_address['CompanyAddress']['address_line_1'].','.$comp_address['city'].','.$comp_address['CompanyAddress']['postal_code'].'</p><p>Zone :'.$zone['MasterDataDetail']['master_data_desc'].'</p></div></label></li>';
		 }		
		foreach($last_entity_id['EntityAddress'] as $k=>$last_entity_add)
		 {		 	
		 	$address_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$last_entity_add['address_type'],'MasterDataDetail.is_active'=>1)));
		 	$zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$last_entity_add['zone'])));
            $address.='<li><label class="clicklable" for="managerrole"><div class="customcheckbox"><label><input type="radio" name="addess_radio" value="'.$last_entity_add['id'].'"  class="addClickadd bill_to_radio"> <b></b></label></div><div class="rolecategoery"><h3>'.$address_type['MasterDataDetail']['master_data_desc'].'</h3><p>'.$last_entity_add['address_line_1'].','.$last_entity_add['city'].','.$last_entity_add['postal_code'].'</p><p>Zone :'.$zone['MasterDataDetail']['master_data_desc'].'</p></div></label></li>';
		 }
		  $msg['address'] = $address;
		  $msg['from_address'] = $from_address;
		  $msg['last_entity_id']=$last_entity_id;
		  echo json_encode($msg); die;
	}
	function count_cotract_val()
	{
		$this->loadModel('Contract');
		$this->loadModel('Project');
		$data = $this->request->data;
		$contractval  = $this->Contract->find('first',array('fields'=>array('Contract.tot_ctrct_value'),'conditions'=>array('Contract.id'=>$data['contract_id'])));
		$project_val  = $this->Project->find('first',array('fields'=>array('Project.project_value'),'conditions'=>array('Project.customer_entity_id'=>$data['customer_id'],'Project.contract_id'=>$data['contract_id'])));
		$remnval=$contractval['Contract']['tot_ctrct_value']-$project_val['Project']['project_value'];
		$msg['remnval'] = $remnval;		
		echo json_encode($msg); die;
	}
	function find_select_customer(){
		
		$this->loadModel('Companie');
		$this->loadModel('CompanyAddress');
		$this->loadModel('Entitie');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('Contract');
		$this->loadModel('CustomerInvoiceStage');
		$data = $this->request->data;

		$companie_detail = $this->Companie->find('first',array('fields'=>array('Companie.id')));
		$comp_add = $this->CompanyAddress->find('all',array('fields'=>array('CompanyAddress.*'),'conditions'=>array('CompanyAddress.company_id'=>$companie_detail['Companie']['id'])));	
		foreach($comp_add as $k=>$comp_address)			
		 {	
		 	$address_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$comp_address['CompanyAddress']['address_type'],'MasterDataDetail.is_active'=>1)));

		 	$zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$comp_address['CompanyAddress']['zone'])));
		 	
            $from_address.='<li><label class="clicklable" for="managerrole"><div class="customcheckbox"><label><input type="radio" name="addess_radio" value="'.$comp_address['CompanyAddress']['id'].'"  class="addClickadd from_to_radio"> <b></b></label></div><div class="rolecategoery"><h3>'.$address_type['MasterDataDetail']['master_data_desc'].'</h3><p>'.$comp_address['CompanyAddress']['address_line_1'].','.$comp_address['city'].','.$comp_address['CompanyAddress']['postal_code'].'</p><p>Zone :'.$zone['MasterDataDetail']['master_data_desc'].'</p></div></label></li>';
		 }		
	
		$select_entity_id  = $this->Entitie->find('first',array('fields'=>array('Entitie.id','Entitie.entity_id','Entitie.credit_period','Entitie.credit_limit','Entitie.entity_turnover'),'conditions'=>array('Entitie.id'=>$data['customer_id'])));
		 $contracts_data = $this->Contract->find('all',array('fields'=>array('Contract.contract_title','Contract.bill_status','Contract.id'),'conditions'=>array('Contract.cust_entity_id'=>$data['customer_id'])));
		 		
        $option_bill.='<option value="" selected>Select Contract</option>';
		$option.='<option value="" selected>Select Contract</option>';
		foreach ($contracts_data as $key => $contract_data) {

		 	$option.='<option  value="'.$contract_data['Contract']['id'].'">'.$contract_data['Contract']['contract_title'].'</option>';
		 	if($contract_data['Contract']['bill_status'] == 0){
		 		$option_bill.='<option  value="'.$contract_data['Contract']['id'].'">'.$contract_data['Contract']['contract_title'].'</option>';
		 	}
		 }			 			 		
		foreach($select_entity_id['EntityAddress'] as $k=>$select_entity_add){		 	
		 	$address_type = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$select_entity_add['address_type'],'MasterDataDetail.is_active'=>1)));
		 	$zone_type = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$select_entity_add['zone'])));
            foreach ($address_type as $key => $value) {
            	$address.='<li><label class="clicklable" for="managerrole"><div class="customcheckbox"><label><input type="radio" name="addess_radio" value="'.$select_entity_add['id'].'"  class="addClickadd bill_to_radio"> <b></b></label></div><div class="rolecategoery"><h3>'.$value['MasterDataDetail']['master_data_desc'].'</h3><p>'.$select_entity_add['address_line_1'].','.$select_entity_add['city'].','.$select_entity_add['postal_code'].'</p><p>Zone :'.$zone_type[0]['MasterDataDetail']['master_data_desc'].'</p></div></label></li>';
            }		 	  
		 }
		 $customer_stages_status = $this->CustomerInvoiceStage->find('first',array('recursive'=>'-1','fields'=>array('CustomerInvoiceStage.id',),'conditions'=>array('CustomerInvoiceStage.entity_id'=>$data['customer_id'])));
		 $msg['address'] = $address;
		 $msg['from_address'] = $from_address;
		 $msg['option'] = $option;
		 $msg['option_bill'] = $option_bill;
		 $msg['customer_stages_status'] = $customer_stages_status;
		 $msg['last_entity_id']=$select_entity_id; 
		 echo json_encode($msg); die;
	}

	function find_parent_contract(){
		$this->loadModel('Contract');
		$data=$this->request->data;
		$contracts_data = $this->Contract->find('first',array( 'recursive'=>'-1','fields'=>array('Contract.contract_number'),'conditions'=>array('Contract.id'=>$data['parent_id'])));
		//pre($contracts_data); die;
		// foreach ($contracts_data as $key => $contract_data) {		 			
		//  	$option.='<option  value="'.$contract_data['Contract']['contract_number'].'">'.$contract_data['Contract']['contract_number'].'</option>';
		//  }	
		$msg['contract_number']=$contracts_data['Contract']['contract_number']; 
		 echo json_encode($msg); die;

	}
	
	function find_select_contract(){
			
		$this->loadModel('Contract');
		$this->loadModel('CustomerInvoiceStage');
		$data = $this->request->data;
		
		$contracts_data = $this->Contract->find('all',array('group'=>'Contract.id','recursive'=>'-1','fields'=>array('Contract.contract_title','Contract.id','Contract.credit_limit','Contract.credit_period','Contract.contract_number','Contract.bill_status'),'conditions'=>array('Contract.cust_entity_id'=>$data['sel_customer_id'])));		
		
		foreach ($contracts_bill as $key => $contract_data) {		 			
		 	$option_bill.='<option rel="'.$contract_data['Contract']['contract_number'].'" value="'.$contract_data['Contract']['id'].'">'.$contract_data['Contract']['contract_title'].'</option>';
		}	
		$customer_stages_status = $this->CustomerInvoiceStage->find('first',array('recursive'=>'-1','fields'=>array('CustomerInvoiceStage.id',),'conditions'=>array('CustomerInvoiceStage.entity_id'=>$data['sel_customer_id'])));
		  
		$option.='<option value="">Select Contract</option>';
		$option_bill.='<option value="">Select Contract</option>';		
		foreach ($contracts_data as $key => $contract_data) {		 			
		 	$option.='<option rel="'.$contract_data['Contract']['contract_number'].'" value="'.$contract_data['Contract']['id'].'">'.$contract_data['Contract']['contract_title'].'</option>';
		 	if($contract_data['Contract']['bill_status'] == 0){
               $option_bill.='<option rel="'.$contract_data['Contract']['contract_number'].'" value="'.$contract_data['Contract']['id'].'">'.$contract_data['Contract']['contract_title'].'</option>';
		 	}
		}			 			 					
		 $msg['option'] = $option;
		 $msg['customer_stages_status'] = $customer_stages_status;
		 $msg['option_bill'] = $option_bill;
		 $msg['credit_limit'] = $contracts_data[0]['Contract']['credit_limit'];	
		 $msg['credit_period'] = $contracts_data[0]['Contract']['credit_period'];
		 $msg['entite_id'] = $data['sel_customer_id'];			
		 echo json_encode($msg); die;

		
	}

	function find_select_address(){
		
		$this->loadModel('EntityAddress');
		$this->loadModel('MasterDataDetail');		
		$data = $this->request->data;
	
		$select_entity_add  = $this->EntityAddress->find('first',array('fields'=>array('EntityAddress.address_line_1','EntityAddress.city','EntityAddress.postal_code','EntityAddress.zone','EntityAddress.zone','EntityAddress.address_type'),'conditions'=>array('EntityAddress.id'=>$data['bill_to_id'])));

		$address_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$select_entity_add['EntityAddress']['address_type'],'MasterDataDetail.is_active'=>1)));

		$zone_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$select_entity_add['EntityAddress']['zone'])));
		 	
        $address_select.='<div class="showaddress remove_add"><span class="remove_address">Remove</span><h3>'.$address_type['MasterDataDetail']['master_data_desc'].'</h3><p>'.$select_entity_add['EntityAddress']['address_line_1'].','.$select_entity_add['EntityAddress']['city'].','.$select_entity_add['EntityAddress']['postal_code'].'</p><p>Zone :'.$zone_type['MasterDataDetail']['master_data_desc'].'</p></div>'; 		 	  
	
		 $msg['address'] = $address_select;
		 echo json_encode($msg); die;
	}
	function find_from_add(){
		
		$this->loadModel('CompanyAddress');
		$this->loadModel('MasterDataDetail');		
		$data = $this->request->data;
	
		$select_comp_add  = $this->CompanyAddress->find('first',array('fields'=>array('CompanyAddress.address_line_1','CompanyAddress.city','CompanyAddress.postal_code','CompanyAddress.zone','CompanyAddress.zone','CompanyAddress.address_type'),'conditions'=>array('CompanyAddress.id'=>$data['from_to_id'])));

		$address_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$select_comp_add['CompanyAddress']['address_type'],'MasterDataDetail.is_active'=>1)));

		$zone_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$select_comp_add['CompanyAddress']['zone'])));
		 	
        $address_select.='<div class="showaddress remove_add"><span class="remove_address">Remove</span><h3>'.$address_type['MasterDataDetail']['master_data_desc'].'</h3><p>'.$select_comp_add['CompanyAddress']['address_line_1'].','.$select_comp_add['CompanyAddress']['city'].','.$select_comp_add['CompanyAddress']['postal_code'].'</p><p>Zone :'.$zone_type['MasterDataDetail']['master_data_desc'].'</p></div>'; 		 	  
	
		 $msg['address'] = $address_select;
		 echo json_encode($msg); die;
	}


	function create_customer(){

		$this->loadModel('Entitie');
		$this->loadModel('Contact');
		$this->loadModel('EntityAddress');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
        $data = $this->request->data;  
        //pre($data); die;
        if($data['custu_id'] == ''){
        	$last_entity_id  = $this->Entitie->find('first',array('recursive'=>'-1','order'=>'Entitie.id DESC','fields'=>array('Entitie.entity_id'))); 
		    $entity_id = $last_entity_id['Entitie']['entity_id']+1;	
        }else{

        	$entity_id = $data['cust_edit_id'];
        }
        
                		 
		if($data['flag'] == 1){
			if($data['status']=='Inactive'){

	        	$link=HTTP_ROOT.'home/customers_pending';
				App::uses('CakeEmail', 'Network/Email');
				$Email = new CakeEmail();
				$Email->config('gmail'); 
				$Email->emailFormat('html'); 
				$Email->to($sesn['Admin']['AppUser']['user_email']);
				//$Email->to('ramjee2443@gmail.com');
				$Email->subject('Customers Approval request');
				$Email->template('customers_approval');
				$Email->viewVars(array('from'=>$ses['Admin']['AppUser'],'link'=>$link));
				$Email->send();
            }
		    $create_cust['Entitie']['entitiy_name']=$data['customer_name'];
			$create_cust['Entitie']['entity_id']=$entity_id;
			$create_cust['Entitie']['group_id']=$data['group'];
			$create_cust['Entitie']['is_customer']= 1;
			//$create_cust['Entitie']['parent_entity_id']=;
			//$create_cust['Entitie']['key_customer']=$data[''];
			$create_cust['Entitie']['entity_currency']=$data['currency'];
			$create_cust['Entitie']['credit_period']=$data['cradit_period'];
			$create_cust['Entitie']['credit_limit']=$data['credit_limit'];
			$create_cust['Entitie']['entity_turnover']=$data['annual_value'];
			//$create_cust['Entitie']['entity_industry']=$data[''];
			$create_cust['Entitie']['creation_dttm']=date('Y-m-d H:i:s');
			$create_cust['Entitie']['entity_pan']=$data['pan_number'];
			$create_cust['Entitie']['entity_tan']=$data['tan_number'];
			$create_cust['Entitie']['entity_gst']=$data['gst_num'];
			$create_cust['Entitie']['rsm_phone']=$data['comm_phone'];
			$create_cust['Entitie']['rsm_email']=$data['comm_email'];
			$create_cust['Entitie']['status']=$data['status'];
			$create_cust['Entitie']['is_deleted']=0;
			$create_cust['Entitie']['created_by']=$sesn['Admin']['AppUser']['id'];
			$create_cust['Entitie']['created_date']=date('Y-m-d H:i:s');
			if($data['custu_id']==''){
              $this->Entitie->create();
			  $this->Entitie->save($create_cust);
			}else{
			  $create_cust['Entitie']['id']=$data['custu_id'];
			  $this->Entitie->save($create_cust);
			  $this->Contact->deleteAll(array("Contact.entity_id"=>$data['custu_id']));
			  $this->EntityAddress->deleteAll(array("EntityAddress.entity_id"=>$data['custu_id']));	
			}	
			echo "success"; die;
		}	
                       
		if($data['flag'] == 2){
			if($data['custu_id'] ==''){

				$last_id = $this->Entitie->find('first',array('recursive'=>'-1','order'=>'Entitie.id DESC','fields'=>array('Entitie.id'))); 
			    $last_ent_id = $last_id['Entitie']['id'];         
			}else{             
              $last_ent_id = $data['custu_id'];
			}
			//Customer Address Save			
			 $add_cust['EntityAddress']['entity_id']= $last_ent_id;
			 $add_cust['EntityAddress']['address_type']=$data['address_type'];
			 $add_cust['EntityAddress']['address_line_1']=$data['company_address_line_1'];
			 $add_cust['EntityAddress']['address_line_2']= $data['company_address_line_2'];
			 $add_cust['EntityAddress']['state']=$data['company_state'];
			 $add_cust['EntityAddress']['city']=$data['company_city'];
			 $add_cust['EntityAddress']['country']='India';
			 $add_cust['EntityAddress']['postal_code']=$data['company_pin_code'];
			 $add_cust['EntityAddress']['zone']=$data['company_zone'];
			 $add_cust['EntityAddress']['GST']=$data['company_gst_num'];
			 $add_cust['EntityAddress']['branch_office']=1;
			 $add_cust['EntityAddress']['created_by']=$sesn['Admin']['AppUser']['id'];
			 $add_cust['EntityAddress']['created_date']=date('Y-m-d H:i:s');
			 $this->EntityAddress->create();
			 $this->EntityAddress->save($add_cust);

			 if($data['custu_id']  ==''){
                 $last_add_id = $this->EntityAddress->getInsertID();
			 }else{
			 	$last_add_id = $this->EntityAddress->getInsertID();
			 	$last_ent_id = $data['custu_id'];		 	
			 }			 			 
            //Customer Contact Save	
			 $contact_pri['Contact']['entity_id']=$last_ent_id;
			 $contact_pri['Contact']['address_id']=$last_add_id ;
			 $contact_pri['Contact']['contact_fname']=$data['pri_contact_name'];
			 //$contact_pri['Contact']['contact_mname']= $data['company_address_line_2'];
			 //$contact_pri['Contact']['contact_lname']= $data['company_address_line_2'];;
			 $contact_pri['Contact']['contact_email']=$data['pri_contact_email'];
			 $contact_pri['Contact']['contact_role']=$data['pri_contact_role_type'];
			 $contact_pri['Contact']['contact_phone']=$data['pri_contact_phone_num'];
			 $contact_pri['Contact']['contact_designation']=$data['pri_contact_designation'];
			 $contact_pri['Contact']['contact_type']='Primary Contact';
			 $contact_pri['Contact']['primary']=1;
			 $contact_pri['Contact']['created_by']=$sesn['Admin']['AppUser']['id'];
		     $contact_pri['Contact']['created_date']=date('Y-m-d H:i:s');			 
			 $this->Contact->create();
			 $this->Contact->save($contact_pri);			
			 $count  = count($data['other_name']);

	    for($i=0;$i<$count;$i++){

			 $contact_other['Contact']['entity_id']=$last_ent_id;
			 $contact_other['Contact']['address_id']=$last_add_id ;
			 $contact_other['Contact']['contact_fname']=$data['other_name'][$i];
			 //$contact_other['Contact']['contact_mname']= $data['company_address_line_2'];
			 //$contact_other['Contact']['contact_lname']= $data['company_address_line_2'];;
			 $contact_other['Contact']['contact_email']=$data['other_email'][$i];
			 $contact_other['Contact']['contact_role']=$data['other_role'][$i];
			 $contact_other['Contact']['contact_phone']=$data['other_phone'][$i];
			 $contact_other['Contact']['contact_designation']=$data['other_designation'][$i];
			 $contact_other['Contact']['contact_type']='Other Contact';
			 $contact_other['Contact']['created_by']=$sesn['Admin']['AppUser']['id'];
		     $contact_other['Contact']['created_date']=date('Y-m-d H:i:s');					 
			 $this->Contact->create();
			 $this->Contact->save($contact_other);			  
	    }	    
	  } 
	  echo "success"; die; 
	}
	function update_customer(){
		$this->loadModel('Entitie');
		$this->loadModel('Contact');
		$this->loadModel('EntityAddress');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
        $data = $this->request->data;
       
		if($data['flag'] == 1){
		    $create_cust['Entitie']['entitiy_name']=$data['customer_name'];
			$create_cust['Entitie']['entity_id']=$data['cust_edit_id'];
			$create_cust['Entitie']['group_id']=$data['group'];
			$create_cust['Entitie']['is_customer']= 1;
			//$create_cust['Entitie']['parent_entity_id']=;
			//$create_cust['Entitie']['key_customer']=$data[''];
			$create_cust['Entitie']['entity_currency']=$data['currency'];
			$create_cust['Entitie']['credit_period']=$data['cradit_period'];
			$create_cust['Entitie']['credit_limit']=$data['credit_limit'];
			$create_cust['Entitie']['entity_turnover']=$data['annual_value'];
			//$create_cust['Entitie']['entity_industry']=$data[''];
			$create_cust['Entitie']['creation_dttm']=date('Y-m-d H:i:s');
			$create_cust['Entitie']['entity_pan']=$data['pan_number'];
			$create_cust['Entitie']['entity_tan']=$data['tan_number'];
			$create_cust['Entitie']['entity_gst']=$data['gst'];
			$create_cust['Entitie']['rsm_phone']=$data['com_ph_num'];
			$create_cust['Entitie']['rsm_email']=$data['com_email'];
			$create_cust['Entitie']['status']="Inactive";
			$create_cust['Entitie']['is_deleted']=0;
			$create_cust['Entitie']['created_by']=$sesn['Admin']['AppUser']['id'];
			$create_cust['Entitie']['created_date']=date('Y-m-d H:i:s');
			$create_cust['Entitie']['id']=$data['custu_id'];
			$this->Entitie->save($create_cust);
			echo "success"; die;
		}	
                
		if($data['flag'] == 2){		
			 $add_cust['EntityAddress']['entity_id']= $data['entity_id'];
			 $add_cust['EntityAddress']['address_type']=$data['address_type'];
			 $add_cust['EntityAddress']['address_line_1']=$data['company_address_line_1'];
			 $add_cust['EntityAddress']['address_line_2']= $data['company_address_line_2'];
			 $add_cust['EntityAddress']['state']=$data['company_state'];
			 $add_cust['EntityAddress']['city']=$data['company_city'];
			 $add_cust['EntityAddress']['country']='India';
			 $add_cust['EntityAddress']['postal_code']=$data['company_pin_code'];
			 $add_cust['EntityAddress']['zone']=$data['company_zone'];
			 $add_cust['EntityAddress']['GST']=$data['company_gst_num'];
			 $add_cust['EntityAddress']['branch_office']=1;
			 $add_cust['EntityAddress']['created_by']=$sesn['Admin']['AppUser']['id'];
			 $add_cust['EntityAddress']['created_date']=date('Y-m-d H:i:s');
			 //$add_cust['EntityAddress']['id']= $data['entity_add_id'];
		     //$this->EntityAddress->save($add_cust);		    

			 if($data['entity_add_id'] == ''){ 
			 	$this->EntityAddress->create();			 	 
			 	$this->EntityAddress->save($add_cust); 
			 }else{
			 	$add_cust['EntityAddress']['id']= $data['entity_add_id'];
		       $this->EntityAddress->save($add_cust);
			 } 
		       
            if($data['entity_add_id'] ==''){   

            	$last_add_id = $this->EntityAddress->getInsertID();
            }else{
            	$last_add_id = $data['entity_add_id'];
            }
          
			 $contact_pri['Contact']['entity_id']=$data['entity_id'];
			 $contact_pri['Contact']['address_id']=$last_add_id;
			 $contact_pri['Contact']['contact_fname']=$data['pri_contact_name'];
			 $contact_pri['Contact']['contact_email']=$data['pri_contact_email'];
			 $contact_pri['Contact']['contact_role']=$data['pri_contact_role_type'];
			 $contact_pri['Contact']['contact_phone']=$data['pri_contact_phone_num'];
			 $contact_pri['Contact']['contact_designation']=$data['pri_contact_designation'];
			 $contact_pri['Contact']['contact_type']='Primary Contact';
			 $contact_pri['Contact']['primary']=1;
			 $contact_pri['Contact']['created_by']=$sesn['Admin']['AppUser']['id'];
		     $contact_pri['Contact']['created_date']=date('Y-m-d H:i:s');
		     //$contact_pri['Contact']['id']=$data['pri_cont_id'];
	          //$this->Contact->save($contact_pri);
            
		     if($data['entity_add_id']==''){ 
			 	$this->Contact->create();			 	 
			 	$this->Contact->save($contact_pri); 
			 }else{
			 	$contact_pri['Contact']['id']=$data['pri_cont_id'];
		        $this->Contact->save($contact_pri);
			  } 	     
			   $count = count($data['other_name']);

	    for($i=0;$i<$count;$i++){

			 $contact_other['Contact']['entity_id']=$data['entity_id'];
			 $contact_other['Contact']['address_id']=$last_add_id;
			 $contact_other['Contact']['contact_fname']=$data['other_name'][$i];
			 //$contact_other['Contact']['contact_mname']= $data['company_address_line_2'];
			 //$contact_other['Contact']['contact_lname']= $data['company_address_line_2'];;
			 $contact_other['Contact']['contact_email']=$data['other_email'][$i];
			 $contact_other['Contact']['contact_role']=$data['other_role'][$i];
			 $contact_other['Contact']['contact_phone']=$data['other_phone'][$i];
			 $contact_other['Contact']['contact_designation']=$data['other_designation'][$i];
			 $contact_other['Contact']['contact_type']='Other Contact';
			 $contact_other['Contact']['primary']=0;
			 $contact_other['Contact']['created_by']=$sesn['Admin']['AppUser']['id'];
		     $contact_other['Contact']['created_date']=date('Y-m-d H:i:s');
		     //$contact_other['Contact']['id']=$data['other_cont_id'][$i];
	         //$this->Contact->save($contact_other);

		     if($data['entity_add_id']=='' || $data['other_cont_id'] == ''){ 
			 	$this->Contact->create();			 	 
			 	$this->Contact->save($contact_other); 
			 }else{
			 	$contact_other['Contact']['id']=$data['other_cont_id'][$i];
		        $this->Contact->save($contact_other);
			 } 				 			 			 			  
	      }		      
	   }	   
   
	  echo "success"; die; 

	}
	function contract_edit(){
		
		$this->loadModel('Contract');
		$this->loadModel('EntityAddress');
		$this->loadModel('ContractSchedule');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('PaymentTerm');
		$this->loadModel('Entitie');
		$this->loadModel('CompanyAddress');
		$this->loadModel('Companie');
		$this->loadModel('NoticePeriod');
		$this->loadModel('RemarksApproval');
		$con_id = $_GET['con_id'];
		$get_id = $_GET['id'];
		$remarks = $_GET['remarks'];
		$cus_tab = $_GET['tab'];
		$entity_id=base64_decode($get_id);
		$contract_id=base64_decode($con_id);
		
        $remark_data =$this->RemarksApproval->find('all',array('recursive'=>'-1','fields'=>array('RemarksApproval.id','RemarksApproval.section','RemarksApproval.section_id','RemarksApproval.remarks_desc','RemarksApproval.created_date','RemarksApproval.remarks_by'),'conditions'=>array('RemarksApproval.section_id'=>$contract_id)));	
        //pre($remark_data); die;	
		$increment_per = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'increment')));
		$NoticePeriod = $this->NoticePeriod->find('all',array('fields'=>array('NoticePeriod.*'),'conditions'=>array('NoticePeriod.is_active'=>1)));
		$companie_detail = $this->Companie->find('first',array('fields'=>array('Companie.id')));
		$comp_add = $this->CompanyAddress->find('all',array('fields'=>array('CompanyAddress.*'),'conditions'=>array('CompanyAddress.company_id'=>$companie_detail['Companie']['id'])));		
		$entity_detail = $this->Entitie->find('first',array('conditions'=>array('Entitie.id'=>$entity_id)));		
		$contracts_data = $this->Contract->find('all',array('recursive'=>'-1','fields'=>array('Contract.*','EntityAddress.*'),'conditions'=>array('Contract.id'=>$contract_id)));		
		$contract_schedule = $this->ContractSchedule->find('all',array('recursive'=>'-1','fields'=>array('ContractSchedule.*'),'conditions'=>array('ContractSchedule.contract_id'=>$contracts_data[0]['Contract']['id'])));
		  $parent_data = $this->Contract->find('all',array('recursive'=>'-1','fields'=>array('Contract.contract_title','Contract.id'),'conditions'=>array('Contract.cust_entity_id'=>$contracts_data[0]['Contract']['provr_entity_id'])));
	    $comp_address = $this->CompanyAddress->find('all',array('fields'=>array('CompanyAddress.*'),'conditions'=>array('CompanyAddress.id'=>$contracts_data[0]['Contract']['bill_from_address_id'])));
	    $bill_address = $this->EntityAddress->find('all',array('fields'=>array('EntityAddress.*'),'conditions'=>array('EntityAddress.id'=>$contracts_data[0]['Contract']['bill_to_address_id'])));
	    $ship_address = $this->EntityAddress->find('all',array('fields'=>array('EntityAddress.*'),'conditions'=>array('EntityAddress.id'=>$contracts_data[0]['Contract']['ship_to_address_id'])));
		$address_type = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$select_entity_add['address_type'],'MasterDataDetail.is_active'=>1)));				
		$contract_type = $this->MasterDataDetail->find('all',array('recursive'=>'-1','fields'=>array('MasterDataDetail.master_data_desc','MasterDataDetail.id'),'conditions'=>array('MasterDataDetail.master_data_type'=>'contract_type','MasterDataDetail.is_active'=>1)));		
		$payment_term = $this->PaymentTerm->find('all',array('recursive'=>'-1','fields'=>array('PaymentTerm.payment_terms','PaymentTerm.id'),'conditions'=>array('PaymentTerm.is_active'=>1)));		
		//pre($parent_data);die;
		$this->set('remarks',$remarks);
		$this->set('remark_data',$remark_data);
		$this->set('parent_data',$parent_data);
		$this->set('contract_schedule',$contract_schedule);
		$this->set('increment_per',$increment_per);
		$this->set('NoticePeriod',$NoticePeriod);
		$this->set('comp_address',$comp_address);
		$this->set('comp_add',$comp_add);
		$this->set('bill_address',$bill_address);
		$this->set('ship_address',$ship_address);
		$this->set('entity_detail',$entity_detail);
		$this->set('cus_tab',$cus_tab);
		$this->set('tab',$tab);
		$this->set('contracts_data',$contracts_data);
		$this->set('contract_type',$contract_type);		
	    $this->set('payment_term',$payment_term);	   
	}
		
	function create_contract()
	{
		$this->loadModel('Contract');
		$this->loadModel('Entitie');
		$this->loadModel('ContractSchedule');
		$this->loadModel('Document');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
        $data = $this->request->data;       
      
        if($data['contract_id']==''){		 
        	$contract_number  = $this->Contract->find('first',array('recursive'=>'-1','order'=>'Contract.id DESC','fields'=>array('Contract.contract_number'))); 
		    $contract_number_id = $contract_number['Contract']['contract_number']+1;		   			
        }else{		           
		     $contract_number_id= $data['contracts_num'];		   		
        } 
		$create_contract['Contract']['contract_number']=$contract_number_id;
		$create_contract['Contract']['contract_type']=$data['contract_type'];
		$create_contract['Contract']['contract_title']=$data['contract_title'];
		$create_contract['Contract']['currency']= $data['contract_currency'];
		$create_contract['Contract']['tot_ctrct_value']=$data['contract_value'];
		//$create_contract['Contract']['annual_ctrct_value']=$data['contract_value'];
		$create_contract['Contract']['parent_contract']=$data['parent_contract'];
		$create_contract['Contract']['contract_start_dt']=$data['startDate'];
		$create_contract['Contract']['contract_end_dt']=$data['endDate'];
		$create_contract['Contract']['cust_entity_id']=$data['entitie_id'];		
		$create_contract['Contract']['provr_entity_id']=$data['entitie_id'];				
		//$create_contract['Contract']['advance_amt_recd']=$data[''];
		$create_contract['Contract']['payment_term_id']=$data['payment_terms'];
		$create_contract['Contract']['credit_limit']=$data['cred_limit'];
		$create_contract['Contract']['bill_from_address_id']=$data['from_address_id'];
		$create_contract['Contract']['bill_to_address_id']=$data['bill_address_id'];
		$create_contract['Contract']['ship_to_address_id']=$data['ship_address_id'];
		$create_contract['Contract']['credit_period']=$data['credit_pri'];
		$create_contract['Contract']['notice_period']=$data['contract_notice_period'];
		$create_contract['Contract']['status']=$data['status_flag'];
		$create_contract['Contract']['increment']=$data['contract_increment'];
		$create_contract['Contract']['created_by']=$sesn['Admin']['AppUser']['id'];
		$create_contract['Contract']['creation_dttm']=date('Y-m-d H:i:s');
		//pre($create_contract); die;
		if($data['contract_id']==''){
			$this->Contract->create();
		    $this->Contract->save($create_contract);	
		}else{
			$create_contract['Contract']['id']=$data['contract_id'];
		    $this->Contract->save($create_contract);	
		}
			
		 if($data['contract_id']==''){
            $contract_id = $this->Contract->getInsertID();
		 }else{
		 	$this->ContractSchedule->deleteAll(array("ContractSchedule.contract_id"=>$data['contract_id']));
		 	$contract_id=$data['contract_id']; 
		 }
		 $count  = count($data['cal_year']);
		 $s_no = 1;
		 for($i=0;$i<$count;$i++){

			$Contrac_schedule['ContractSchedule']['contract_id']=$contract_id;
			$Contrac_schedule['ContractSchedule']['s_no']= $s_no;
			$Contrac_schedule['ContractSchedule']['start_date']=$data['contract_startDate'][$i];
			$Contrac_schedule['ContractSchedule']['end_date']= $data['contract_endDate'][$i];
			$Contrac_schedule['ContractSchedule']['contract_value']= $data['contr_value'][$i];
			$Contrac_schedule['ContractSchedule']['adjusted_contract_value']=$data['adjusted_contract_value'][$i];
			$Contrac_schedule['ContractSchedule']['increment']=$data['increment_value'][$i];
			 //$Contrac_schedule['ContractSchedule']['is_deleted']=$data['other_phone'][$i];
			$Contrac_schedule['ContractSchedule']['created_by']=$sesn['Admin']['AppUser']['id'];
			$Contrac_schedule['ContractSchedule']['created_date']=date('Y-m-d H:i:s');	
            $this->ContractSchedule->create();
			$this->ContractSchedule->save($Contrac_schedule);          	 
			
			$s_no++;
	    }	
	    if($data['status_flag']=='0'){
        	$Entitie_update['Entitie']['id']=$data['entitie_id'];
        	$Entitie_update['Entitie']['status']='Inactive';
        	$this->Entitie->save($Entitie_update);   

        	$msg['last_contract_id']='success';		 
		     echo json_encode($msg); die;

        }else{
        	 $contract_option  = $this->Contract->find('first',array('recursive'=>'-1','fields'=>array('Contract.contract_title','Contract.id'),'conditions'=>array('Contract.id'=>$contract_id))); 
        	 
        	 $cont_option.='<option value="'.$contract_option['Contract']['id'].'">'.$contract_option['Contract']['contract_title'].'</option>';
        	 $msg['last_contract_id']=$contract_id;
        	 $msg['billing_contract']=$cont_option;
	         $msg['last_contract_num']=$contract_number_id;	
	         $msg['entitie_id']=$data['entitie_id'];	 
		     echo json_encode($msg); die;
        }
	    
	 //    if(!empty($data['documents_file'])){
		//     for($i=0;$i<count($data['documents_file']);$i++){
		//         // File upload configuration
	 //            $targetDir = HTTP_ROOT.'app/webroot/contract_doc_file/';
	 //            //$allowTypes = array('jpg','png','jpeg','gif');
	 //            $data = $data['documents_file'][$i];
	            
	 //            $image_parts = explode(";base64,", $data);
	 //            $image_type_aux = explode("image/", $image_parts[0]);
	           
	 //            $image_type = $image_type_aux[1];
	 //            $image_base64 = base64_decode($image_parts[1]);          
	 //            $uniueId = uniqid();
	 //            $file = $targetDir . $uniueId . '.'.$image_type;
	 //            $fileName = $uniueId . '.'.$image_type;
	 //            $path = HTTP_ROOT.'app/webroot/contract_doc_file/'.$fileName;
	            
	 //            file_put_contents($file, $image_base64);

		// 	     $create_document['Document']['contract_id']=$contract_id;
		// 		 $create_document['Document']['document_type']=7;
		// 		 $create_document['Document']['doc_dms_url']= $path ;
		// 		 $create_document['Document']['created_by']= $sesn['Admin']['AppUser']['id'];
		// 		 $create_document['Document']['created_date']=date('Y-m-d H:i:s');
		// 		 $create_document['Document']['is_active']=0;
		// 		 $this->Document->create();
		// 		 $this->Document->save($create_document);
	           
	 //            }
		// }

		
	    //echo 'success'; die;   
	}
	function find_contract_data(){
		$this->loadModel('Contract');
		$last_contract_id  = $this->Contract->find('first',array('recursive'=>'-1','order'=>'Contract.id DESC','fields'=>array('Contract.id','Contract.contract_number'))); 
		//pre($contract_number); die;
		 $msg['last_contract_id']=$last_contract_id['Contract']['id'];
		  $msg['last_contract_number']=$last_contract_id['Contract']['contract_number'];
		 echo json_encode($msg); die;

	}
	function find_contract(){

		$this->loadModel('Contract');
		$data=$this->request->data;

		 $contracts_data = $this->Contract->find('all',array('fields'=>array('Contract.contract_title','Contract.id'),'conditions'=>array('Contract.cust_entity_id'=>$data['enti_id'])));

		    $option.='<option value="" selected>Select Contract</option>';
		foreach ($contracts_data as $key => $contract_data) {

		 	$option.='<option  value="'.$contract_data['Contract']['id'].'">'.$contract_data['Contract']['contract_title'].'</option>';
		 }	
		  $option['contract'] = $option;				
		  echo json_encode($option);die;	

	}
	function update_contract()
	{
		$this->loadModel('Contract');
		$this->loadModel('ContractSchedule');
		$this->loadModel('Document');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
        $data = $this->request->data;
       
        $contract_number  = $this->Contract->find('first',array('recursive'=>'-1','order'=>'Contract.id DESC','fields'=>array('Contract.contract_number'))); 		
		if($data['hidden_contract_id'] ==''){			
			$contract_number_id = $contract_number['Contract']['contract_number']+1;
		}else{
            $contract_number_id = $data['hidden_contract_number'];
		}		
		$create_contract['Contract']['contract_number']=$contract_number_id;
		$create_contract['Contract']['contract_type']=$data['contract_type'];
		$create_contract['Contract']['contract_title']=$data['contract_title'];
		$create_contract['Contract']['currency']= $data['contract_currency'];
		$create_contract['Contract']['tot_ctrct_value']=$data['contract_value'];
		//$create_contract['Contract']['annual_ctrct_value']=$data['contract_value'];
		$create_contract['Contract']['parent_contract']=$data['parent_contract'];
		$create_contract['Contract']['contract_start_dt']=$data['startDate'];
		$create_contract['Contract']['contract_end_dt']=$data['endDate'];
		$create_contract['Contract']['cust_entity_id']=$data['hidden_entity_id'];		
		$create_contract['Contract']['provr_entity_id']=$data['hidden_entity_id'];				
		//$create_contract['Contract']['advance_amt_recd']=$data[''];
		$create_contract['Contract']['payment_term_id']=$data['payment_terms'];
		$create_contract['Contract']['credit_limit']=$data['hidden_credit_limit'];
		$create_contract['Contract']['bill_from_address_id']=$data['from_address_id'];
		$create_contract['Contract']['bill_to_address_id']=$data['bill_address_id'];
		$create_contract['Contract']['ship_to_address_id']=$data['ship_address_id'];
		$create_contract['Contract']['credit_period']=$data['hidden_credit_period'];
		$create_contract['Contract']['notice_period']=$data['contract_notice_period'];
		$create_contract['Contract']['status']=0;
		$create_contract['Contract']['increment']=$data['contract_increment'];
		$create_contract['Contract']['created_by']=$sesn['Admin']['AppUser']['id'];
		$create_contract['Contract']['creation_dttm']=date('Y-m-d H:i:s');		
		if($data['hidden_contract_id'] ==''){
			$this->Contract->create();
		    $this->Contract->save($create_contract);
		}else{			
           $create_contract['Contract']['id']=$data['hidden_contract_id'];
		   $this->Contract->save($create_contract);
		}
	//CONTRACT SCHEDULE
		if($data['hidden_contract_id'] ==''){
             $contract_id = $this->Contract->getInsertID();
		}else{
             $contract_id = $data['hidden_contract_id'];
		}	
		$this->ContractSchedule->deleteAll(array("ContractSchedule.contract_id"=>$contract_id));				
		 $count  = count($data['cal_year']);
		 $s_no = 1;
		 for($i=0;$i<$count;$i++){

			$Contrac_schedule['ContractSchedule']['contract_id']=$contract_id;
			$Contrac_schedule['ContractSchedule']['s_no']= $s_no;
			$Contrac_schedule['ContractSchedule']['start_date']=$data['contract_startDate'][$i];
			$Contrac_schedule['ContractSchedule']['end_date']= $data['contract_endDate'][$i];
			$Contrac_schedule['ContractSchedule']['contract_value']= $data['contr_value'][$i];
			$Contrac_schedule['ContractSchedule']['adjusted_contract_value']=$data['adjusted_contract_value'][$i];
			$Contrac_schedule['ContractSchedule']['increment']=$data['increment_value'][$i];
			 //$Contrac_schedule['ContractSchedule']['is_deleted']=$data['other_phone'][$i];
			$Contrac_schedule['ContractSchedule']['created_by']=$sesn['Admin']['AppUser']['id'];
			$Contrac_schedule['ContractSchedule']['created_date']=date('Y-m-d H:i:s');
			//pre($Contrac_schedule); 				
            $this->ContractSchedule->create();
			$this->ContractSchedule->save($Contrac_schedule);
					 			
			$s_no++;
	    }	
	    if(!empty($data['documents_file'])){

		    for($i=0;$i<count($data['documents_file']);$i++){
		        // File upload configuration
	            $targetDir = HTTP_ROOT.'app/webroot/contract_doc_file/';
	            //$allowTypes = array('jpg','png','jpeg','gif');
	            $data = $data['documents_file'][$i];
	            
	            $image_parts = explode(";base64,", $data);
	            $image_type_aux = explode("image/", $image_parts[0]);
	           
	            $image_type = $image_type_aux[1];
	            $image_base64 = base64_decode($image_parts[1]);
	            //pre($image_base64);die;          
	            $uniueId = uniqid();
	            $file = $targetDir . $uniueId . '.'.$image_type;
	            $fileName = $uniueId . '.'.$image_type;
	            $path = HTTP_ROOT.'app/webroot/contract_doc_file/'.$fileName;
	            //pre($path);die;
	            file_put_contents($file, $image_base64);

			     $create_document['Document']['contract_id']=$contract_id;
				 $create_document['Document']['document_type']=7;
				 $create_document['Document']['doc_dms_url']= $path ;
				 $create_document['Document']['created_by']= $sesn['Admin']['AppUser']['id'];
				 $create_document['Document']['created_date']=date('Y-m-d H:i:s');
				 $create_document['Document']['is_active']=0;
				 pre($create_document); die;
				 $this->Document->create();
				 $this->Document->save($create_document);
	           
	            }
		}
	 
	    echo 'success'; die;   
	}	
	function get_vertical_sub_vertical($profit_id=null)
	{
		$this->loadModel('Subvertical');
		$this->loadModel('ProfitCenter');
		$this->loadModel('BusinessLine');
		$data = $this->request->data;
		if($profit_id!='')
		$data['profit_center']=$profit_id;
		$p_centr=$this->ProfitCenter->find('first',array('recursive'=>'-1','conditions'=>array('ProfitCenter.id'=>$data['profit_center']),'fields'=>array('ProfitCenter.subvertical_id')));
		$subvertical=$this->Subvertical->find('first',array('conditions'=>array('Subvertical.id'=>$p_centr['ProfitCenter']['subvertical_id']),'fields'=>array('Subvertical.id','Subvertical.business_line_id','Subvertical.sv_name')));
		$vertical=$this->BusinessLine->find('first',array('conditions'=>array('BusinessLine.id'=>$subvertical['Subvertical']['business_line_id']),'fields'=>array('BusinessLine.id','BusinessLine.bl_name')));
		$msg['vertical']=$vertical['BusinessLine']['bl_name'];
		$msg['vertical_id']=$vertical['BusinessLine']['id'];
		$msg['subvertical']=$subvertical['Subvertical']['sv_name'];
		$msg['sbvertical_id']=$subvertical['Subvertical']['id'];
		if($profit_id!='')
		{
			return $msg;
		}
		echo json_encode($msg);die;
	}
	function deletetask($type=null)
	{
		$this->loadModel('ProjectTask');
		$this->loadModel('Pricing');
		$data = $this->request->data;
		if(!empty($data))
		{
			if($type=='task')
			{
				$this->Pricing->deleteAll(array("Pricing.task_id"=>$data['task_id']));
				$this->ProjectTask->deleteAll(array("ProjectTask.id"=>$data['task_id']));
			}
			else if($type=='billing')
			{  
				
				$taskdetals=$this->Pricing->find('all',array('fields'=>array('Pricing.id','ProjectTask.id'),'conditions'=>array('Pricing.billing_type'=>$data['billing_id'],'ProjectTask.project_id'=>$data['project_id']),'joins' => array(array('table' => 'project_tasks','alias' => 'ProjectTask','type' => 'LEFT','conditions' => array('ProjectTask.id = Pricing.task_id')))));
				foreach($taskdetals as $detl)
				{ 
					$this->Pricing->deleteAll(array("Pricing.id"=>$detl['Pricing']['id']));
					$this->ProjectTask->deleteAll(array("ProjectTask.id"=>$detl['ProjectTask']['id']));
				}
			}
		}
		echo 'success';die;
	}
	function save_project($type=null)
	{
		$this->loadModel('Project');
		$this->loadModel('ProjectTask');
		$this->loadModel('Entitie');
		$this->loadModel('Pricing');
		$this->loadModel('PricingSlab');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('Contract');
		$this->loadModel('ProfitCenter');
		$this->loadModel('Subvertical');
		$this->loadModel('BusinessLine');
		$this->loadModel('EntityDivision');
		$this->loadModel('AppUser');
		$this->loadModel('Document');
		$this->loadModel('ProjectDocument');
		$this->loadModel('PricingSlabTemp');
		$data = $this->request->data;
		$ses=$this->Session->read('admin'); 
		if(!empty($data))
		{   
			$entitydiv_id=$this->EntityDivision->find('first',array('fields'=>array('EntityDivision.id'),'conditions'=>array('EntityDivision.entity_id'=>$data['Project']['customer_entity_id'])));
			if($entitydiv_id['EntityDivision']['id']=='')
			$entitydiv_id['EntityDivision']['id']=1;
			if($data['Project']['id']!='')
			$project_d['Project']['id']=$data['Project']['id'];
		    if($type==1)
			$project_d['Project']['status']=2;
			else if($type==2)
			$project_d['Project']['status']=1;
			$project_d['Project']['customer_entity_id']=$data['Project']['customer_entity_id'];
			$project_d['Project']['contract_id']=$data['Project']['contract_id'];
			$project_d['Project']['profit_center_id']=$data['Project']['profit_center_id'];
			$project_d['Project']['business_line']=$data['Project']['business_line'];
			$project_d['Project']['subvertical']=$data['Project']['subvertical'];
			$project_d['Project']['division_id']=@$entitydiv_id['EntityDivision']['id'];
			$project_d['Project']['project_type']=$data['Project']['projects_type'];
			$project_d['Project']['project_value']=$data['Project']['project_value'];
			$sdate=explode('/',$data['Project']['start_date']);
			$edate=explode('/',$data['Project']['end_date']);
			$project_d['Project']['start_date']=$sdate[2].'-'.$sdate[1].'-'.$sdate[0];
			$project_d['Project']['initial_end_date']=$edate[2].'-'.$edate[1].'-'.$edate[0];;
			$project_d['Project']['project_title']=$data['Project']['title'];
			if($data['Project']['id']=='')
			{
				$c_id=$this->Contract->find('first',array('fields'=>array('Contract.contract_number'),'conditions'=>array('Contract.id'=>$data['Project']['contract_id'])));
				$p_count=$this->Project->find('count',array('conditions'=>array('Project.customer_entity_id'=>$data['Project']['customer_entity_id'],'Project.contract_id'=>$data['Project']['contract_id'])));
				$project_d['Project']['project_id']=$c_id['Contract']['contract_number'].'-'.(101+$p_count);
			}
			$project_d['Project']['brief_description']=$data['Project']['desc'];
			$project_d['Project']['bill_from_address_id']=$data['Project']['bill_from_address_id'];
			$project_d['Project']['bill_to_address_id']=$data['Project']['bill_to_address_id'];
			$project_d['Project']['ship_to_address_id']=$data['Project']['ship_to_address_id'];
			$project_d['Project']['creation_dttm']=date("Y-m-d");
			$project_d['Project']['project_mgr_id']=$data['project_manager_id'];
			$project_d['Project']['sales_mgr_id']=$data['sales_manager_id'];
			$project_d['Project']['modified_date']=date('Y-m-d H:i:s');
			$project_d['Project']['modified_by']=$ses['Admin']['AppUser']['id'];
			if($data['Project']['id']=='')
			{
				$project_d['Project']['created_date']=date('Y-m-d H:i:s');
				$project_d['Project']['created_by']=$ses['Admin']['AppUser']['id'];
			}					
			$this->Project->save($project_d);
			if($data['Project']['id']=='')
			$project_id=$this->Project->getLastInsertId();
			else
			$project_id=$data['Project']['id'];
			$totaltask=count($data['billing_type']);
			for($i=0;$i<$totaltask;$i++)
			{  
				if($data["taskqty"][$i]!='')
				{
					$project_t['ProjectTask']['id']=$data["task_id"][$i];
					$project_t['ProjectTask']['project_id']=$project_id;
					$project_t['ProjectTask']['task_title']=$data["task_title"][$i];
					$project_t['ProjectTask']['svc_ctlg_id']=$data["service_type"][$i];
					$project_t['ProjectTask']['task_description']=$data["task_desc"][$i];
					$project_t['ProjectTask']['is_supporting_doc']=$data["supporting_doc"][$i];
					$project_t['ProjectTask']['is_completed']='0';
					if($data["task_start_date"][$i]!='')
					{
						$sdat1=explode('/',$data["task_start_date"][$i]);
						$project_t['ProjectTask']['task_start_date']=$sdat1[2].'-'.$sdat1[1].'-'.$sdat1[0];
					}
					$edate1=explode('/',$data["task_start_end"][$i]);
					$project_t['ProjectTask']['task_end_date']=$edate1[2].'-'.$edate1[1].'-'.$edate1[0];
					$project_t['ProjectTask']['modified_date']=date('Y-m-d H:i:s');
					$project_t['ProjectTask']['modified_by']=1;
					if($project_t['ProjectTask']['id']=='')
					{
						$project_t['ProjectTask']['created_date']=date('Y-m-d H:i:s');
						$project_t['ProjectTask']['created_by']=$ses['Admin']['AppUser']['id'];
						$this->ProjectTask->create();	
					}						
					$this->ProjectTask->save($project_t);
					if($project_t['ProjectTask']['id']=='')
					$project_task_id=$this->ProjectTask->getLastInsertId();
					else
					$project_task_id=$project_t['ProjectTask']['id'];
					$this->Pricing->deleteAll(array("Pricing.task_id"=>$project_task_id));
					if($data["tasktotal_val"][$i]!='')
					{
						if($data["is_rate"][$i]==1)
						$pricing_t['Pricing']['is_rate_card']=1;
						else
						$pricing_t['Pricing']['is_rate_card']=0;
						$pricing_t['Pricing']['task_id']=$project_task_id;
						$pricing_t['Pricing']['billing_type']=$data["billing_type"][$i];
						$pricing_t['Pricing']['billing_type_val']=$data["billingtypeval"][$i];
						$pricing_t['Pricing']['basis_units']=$data["task_unit"][$i];
						$pricing_t['Pricing']['total_task_amount']=$data["tasktotal_val"][$i];
						$pricing_t['Pricing']['taxes_incl']=$data["is_task_tax"][$i];
						$pricing_t['Pricing']['per_unit_rate']=$data["task_rate"][$i];
						$pricing_t['Pricing']['created_date']=date('Y-m-d H:i:s');
						$pricing_t['Pricing']['created_by']=$ses['Admin']['AppUser']['id'];
						$this->Pricing->create();	
						
						$pricing_t['Pricing']['modified_date']=date('Y-m-d H:i:s');
						$this->Pricing->save($pricing_t);
						 
					}
					if($data["rate_temp_id"][$i]!='')
					{
						$rateTemp = $this->PricingSlabTemp->find('all',array('recursive'=>'-1','fields'=>array('PricingSlabTemp.*'),'conditions'=>array('PricingSlabTemp.temp_id'=>$data["rate_temp_id"][$i])));
						if(!empty($rateTemp))
						{
							foreach($rateTemp as $temp)
							{
								$saveprc['PricingSlab']['project_id']=$project_id;
								$saveprc['PricingSlab']['service_id']=$temp['PricingSlabTemp']['service_id'];
								$saveprc['PricingSlab']['start_units']=$temp['PricingSlabTemp']['start_units'];
								$saveprc['PricingSlab']['end_units']=$temp['PricingSlabTemp']['end_units'];
								$saveprc['PricingSlab']['per_unit_rate']=$temp['PricingSlabTemp']['per_unit_rate'];
								$saveprc['PricingSlab']['created_date']=date('Y-m-d H:i:s');
								$saveprc['PricingSlab']['modified_date']=date('Y-m-d H:i:s');
								$saveprc['PricingSlab']['created_by']=$ses['Admin']['AppUser']['id'];
								$saveprc['PricingSlab']['modified_by']=$ses['Admin']['AppUser']['id'];
								$this->PricingSlab->create();
								$this->PricingSlab->save($saveprc);
							}
							$this->PricingSlabTemp->deleteAll(array("PricingSlabTemp.temp_id"=>$data["rate_temp_id"][$i]));
						}
						 
					}
				}
				
				
			}
			if($data['Project']['id']!='')
			{
				$msg['is_edit']=1;
			}
			else
			{
				$msg['is_edit']=1;
			}
			$msg['msg']='success';
			echo json_encode($msg);die;
		}
       
	}	
	function document_file_upload(){
		echo "document_file_upload"; die;
	}
	function update_billing()
	{
		$sesn = $this->Session->read('admin');
		$user_id = $sesn['Admin']['AppUser']['id'];
		$this->loadModel('BillingSetup');
        $data = $this->request->data; 
        //pre($data); die;
        if($this->request->isAjax()){
		  if(!empty($data)){
         	$update_billing['BillingSetup']['contract_id']=$data['select_contract'];
			$update_billing['BillingSetup']['billing_start_dt']=$data['bilstartdate'];
			$update_billing['BillingSetup']['billing_freq']= $data['billing_frequency'];
			$update_billing['BillingSetup']['dt_of_mth']= $data['day_of_month']; 
			$update_billing['BillingSetup']['mth']= '';
			$update_billing['BillingSetup']['day_of_week']= $data['working_day'];
			$update_billing['BillingSetup']['billing_address']= $data['bill_add_id'];
			$update_billing['BillingSetup']['created_by']=$user_id;
			$update_billing['BillingSetup']['created_date']=date('Y-m-d H:i:s');
			$update_billing['BillingSetup']['status']=$data['billing_status'];	
			$update_billing['BillingSetup']['id']=$data['bill_id'];	
			$update_billing['BillingSetup']['is_active']='1';
			$this->BillingSetup->save($update_billing);

			echo "success"; die;
		}else{
            echo "error"; die;
		}
	}
         
	}
	function billing()
	{
		$sesn=$this->Session->read('admin');
		$userid=$sesn['Admin']['AppUser']['id'];
		$role_id=$sesn['Role'];
        $this->loadModel('ProjectPage');
		$this->loadModel('RolePermission');
        $this->loadModel('BillingSetup');
        $conditions = array();
        $data = $this->request->data;
        $customer_page_id =$this->ProjectPage->find('first',array('conditions'=>array('ProjectPage.pages'=>'Customer')));
		$cus_page_id = $customer_page_id['ProjectPage']['id'];
			//pre($customer_page_id);die;
		$excess_permission = $this->RolePermission->find('all',array('recursive'=>'-1','fields'=>array('RolePermission.excess_id'),'conditions' => array('RolePermission.permission_id' =>$role_id,'FIND_IN_SET(\''. $cus_page_id .'\',RolePermission.pages)')));

		$short = array('BillingSetup.id'=>'DESC');
				
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('BillingSetup.id'=>$shortVal);
		}
		/*
		if(isset($data['search_key']) && !empty($data['search_key'])){			
			$conditions = array_merge($conditions,array('OR'=>array('Entitie.entitiy_name LIKE'=>'%'.$data['search_key'].'%')));
		}
		*/
		
		if(isset($data['invoiveStart']) && !empty($data['invoiveStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['invoiveStart']));
			$conditions = array_merge($conditions,array('BillingSetup.created_date >= '=>$begin_data));

		}
		if( isset($data['invoiceEnd']) && !empty($data['invoiceEnd']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['invoiceEnd']));
		 	$conditions = array_merge($conditions,array('BillingSetup.created_date <= '=>$close_date));
		 	
		 }

		$conditions = array_merge($conditions,array('BillingSetup.is_active'=>1));
		$conditions = array_merge($conditions,array('BillingSetup.status'=>array(1)));	

		$this->paginate = array('limit'=>25,'order'=>$short,
		'fields'=>array('BillingSetup.*'),'conditions'=>array($conditions));
		//$Billing_data  = $this->BillingSetup->find('all',array('order'=>'BillingSetup.id DESC','fields'=>array('BillingSetup.*'),'conditions'=>array('BillingSetup.is_active'=>1,'BillingSetup.status'=>array(1))));

		$Billing_data = $this->paginate('BillingSetup'); 
		$billing_active = $this->BillingSetup->find('count',array('conditions'=>array($conditions)));

	//	$billing_active = $this->BillingSetup->find('count',array('conditions'=>array('BillingSetup.status'=>1)));
		$billing_inactive = $this->BillingSetup->find('count',array('conditions'=>array('BillingSetup.status'=>0)));

		//pre($Billing_data);die;

		
		$this->set('billing_active',$billing_active);
		$this->set('billing_inactive',$billing_inactive);
		$this->set('excess_permission',$excess_permission);

		if($this->RequestHandler->isAjax())
             {
                
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($Billing_data))
                {
                        $view->set('Billing_data',$Billing_data);
                        $html['html'] = $view->render("billing_data_page");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                        $html['billing_active'] =$billing_active;

                }
                else
                {
                        $html['message'] ='error';
                        $html['billing_active'] =$billing_active;
                }

                echo json_encode($html);die;

                }
            $this->set('Billing_data',$Billing_data);

	}

	function serch_billing_active()
	{
		$this->loadModel('BillingSetup');
		$this->loadModel('Contract');
		$data = $this->request->data;
         $li = '';
    /*    if($data !=''){

         	$search_entity_name = $this->Entitie->find("all",
         		array('recursive'=>'-1',
         			'fields'=>array('Entitie.entitiy_name','Entitie.id'),
         			'conditions'=>array('Entitie.status'=>'Active',
         				'OR' => array('Entitie.entitiy_name LIKE' => '%' . $data['search_key'] . '%',
									'Entitie.entity_pan LIKE' => '%' . $data['search_key'] . '%',
									'Entitie.entity_gst LIKE' => '%' . $data['search_key'] . '%',
									'Entitie.entity_id LIKE' => '%' . $data['search_key'] . '%')
         				
         			)));
         }
	*/	
		//pre($search_entity_name) ;die();
		foreach($search_entity_name as $k=>$entity_name)
		 {
		 	 $li.='<li ><a href="#" class="select_billing" id="'.$entity_name['Entitie']['id'].'">'.$entity_name['Entitie']['entitiy_name'].'</a></li>';
		 }
		 $msg['li']  =	$li;
		 echo json_encode($msg); die;

	
	}
	function billing_edit()
	{
		$this->loadModel('BillingSetup');
		$this->loadModel('Contract');
		$this->loadModel('CompanyAddress');
		$this->loadModel('EntityAddress');	  
		$this->loadModel('MasterDataDetail');
		$this->loadModel('Project');	
		$this->loadModel('RemarksApproval');
		$id   = $_GET['id'];
		$tab   = $_GET['tab'];
		$con_id   = $_GET['con_id'];
		$ent_id   = $_GET['ent_id'];
		$remarks = $_GET['remarks'];
		$bill_id=base64_decode($id );
		
        $remark_data =$this->RemarksApproval->find('all',array('recursive'=>'-1','fields'=>array('RemarksApproval.id','RemarksApproval.section','RemarksApproval.section_id','RemarksApproval.remarks_desc','RemarksApproval.created_date','RemarksApproval.remarks_by'),'conditions'=>array('RemarksApproval.section_id'=>$bill_id)));	
		$billing_data  = $this->BillingSetup->find('first',array('fields'=>array('BillingSetup.*'),'conditions'=>array('BillingSetup.id'=>$bill_id))); 
		$contract_data  = $this->Contract->find('first',array('recursive'=>'-1','fields'=>array('Contract.id','Contract.contract_number','Contract.contract_title','Contract.cust_entity_id','Contract.credit_limit','Contract.credit_period'),'conditions'=>array('Contract.id'=>$billing_data['BillingSetup']['contract_id']))); 
		$project_data = $this->Project->find('all',array('fields'=>array('Project.id','Project.customer_entity_id','Project.contract_id','Project.profit_center_id','Project.business_line','Project.subvertical','Project.project_type','Project.project_value','Project.award_date','Project.start_date','Project.project_title','Project.brief_description','Project.initial_end_date','Project.bill_from_address_id','Project.bill_to_address_id','Project.ship_to_address_id'),'conditions'=>array('Project.contract_id'=>$billing_data['BillingSetup']['contract_id'])));
		foreach($project_data as $prodata){
		    $from_addre = $this->CompanyAddress->find('first',array('fields'=>array('CompanyAddress.id','CompanyAddress.company_id','CompanyAddress.address_type','CompanyAddress.address_line_1','CompanyAddress.address_line_2','CompanyAddress.state','CompanyAddress.city','CompanyAddress.country','CompanyAddress.postal_code','CompanyAddress.zone'),'conditions'=>array('CompanyAddress.id'=>$prodata['Project']['bill_from_address_id'])));
			$from_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$from_addre['CompanyAddress']['address_type'])));
			$from_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$from_addre['CompanyAddress']['zone'])));
					 
			//Bill address
			$bill_addre = $this->EntityAddress->find('first',array('fields'=>array('EntityAddress.*'
					 	),'conditions'=>array('EntityAddress.id'=>$prodata['Project']['bill_to_address_id'])));
			$bill_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$bill_addre['EntityAddress']['address_type'])));
			$bill_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$bill_addre['EntityAddress']['zone'])));

			//Ship address
			$ship_addre = $this->EntityAddress->find('first',array('fields'=>array('EntityAddress.*'
					 	),'conditions'=>array('EntityAddress.id'=>$prodata['Project']['ship_to_address_id'])));
					  
			$ship_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ship_addre['EntityAddress']['address_type'])));
			$ship_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ship_addre['EntityAddress']['zone'])));
			$project_address.='<h5>'.$prodata['Project']['project_title'].'</h5><ul class="addressSlider"><li class="col-lg-4 col-md-3 col-sm-12 padding-l-0"><h4>Bill From:</h4><h5>'.$from_add_type['MasterDataDetail']['master_data_type'].'</h5><p>'.$from_addre['CompanyAddress']['address_line_1'].'</p><p>'.$from_addre['CompanyAddress']['city'].','.$from_addre['CompanyAddress']['state'].'-'.$from_addre['CompanyAddress']['postal_code'].'</p><p>Zone :'.$from_zone['MasterDataDetail']['master_data_desc'].'</p></li><li class="col-lg-4 col-md-3 col-sm-12"><h4>Bill To:</h4><input type="hidden" name="bill_add_id" value="'.$bill_addre['EntityAddress']['id'].'"><h5>'.$bill_add_type['MasterDataDetail']['master_data_type'].'</h5><p>'.$bill_addre['EntityAddress']['address_line_1'].'</p><p>'.$bill_addre['EntityAddress']['city'].','.$bill_addre['EntityAddress']['state'].'-'.$bill_addre['EntityAddress']['postal_code'].'</p><p>Zone :'.$bill_zone['MasterDataDetail']['master_data_desc'].'</p></li><li class="col-lg-4 col-md-3 col-sm-12"><h4>Ship To:</h4><h5>'.$ship_add_type['MasterDataDetail']['master_data_type'].'</h5><p>'.$ship_addre['EntityAddress']['address_line_1'].'</p><p>'.$ship_addre['EntityAddress']['city'].','.$ship_addre['EntityAddress']['state'].'-'.$ship_addre['EntityAddress']['postal_code'].'</p><p>Zone :'.$ship_zone['MasterDataDetail']['master_data_desc'].'</p></li></ul>';	
		}
		$frequenc = $this->MasterDataDetail->find('all',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.master_data_type'=>'Billing freq')));
					 	
		//pre($frequenc); die;
		$this->set('frequenc',$frequenc);
		$this->set('billing_data',$billing_data);
		$this->set('contract_data',$contract_data);
		$this->set('frequenc',$frequenc);
		$this->set('project_address',$project_address);
		$this->set('remarks',$remarks);
		$this->set('remark_data',$remark_data);

	}
	function billing_pending()
	{
		$sesn=$this->Session->read('admin');
		$userid = $sesn['Admin']['AppUser']['id'];
		$this->loadModel('BillingSetup');
		$this->loadModel('AppUser');
        $conditions = array();
        $data = $this->request->data;
        
		$short = array('BillingSetup.id'=>'DESC');
				
		if(isset($data['shortOredr']) && !empty($data['shortOredr'])){
			$shortVal = $data['shortOredr'];
			$short = array('BillingSetup.id'=>$shortVal);
		}
		/*
		if(isset($data['search_key']) && !empty($data['search_key'])){			
			$conditions = array_merge($conditions,array('OR'=>array('Entitie.entitiy_name LIKE'=>'%'.$data['search_key'].'%')));
		}
		*/
		
		if(isset($data['invoiveStart']) && !empty($data['invoiveStart'])) {
			$begin_data = date('Y-m-d',strtotime($data['invoiveStart']));
			$conditions = array_merge($conditions,array('BillingSetup.created_date >= '=>$begin_data));

		}
		if( isset($data['invoiceEnd']) && !empty($data['invoiceEnd']))
		 {
		 	$close_date = date('Y-m-d',strtotime($data['invoiceEnd']));
		 	$conditions = array_merge($conditions,array('BillingSetup.created_date <= '=>$close_date));
		 	
		 }
		 $reporting_id = $this->AppUser->find('all',array('fields'=>array('AppUser.id'),'conditions'=>array('AppUser.reporting_manager'=>$userid)));
				 
		 $reporting_ids=array();
		 foreach ($reporting_id as $key => $reporting_manager_id) {
		 	array_push($reporting_ids,$reporting_manager_id['AppUser']['id']);
		 	
		 }
		 array_push($reporting_ids,$userid);      
		$conditions = array_merge($conditions,array('BillingSetup.created_by'=>$reporting_ids,'BillingSetup.is_active'=>1));
				
		$this->paginate = array('limit'=>20,'order'=>$short,'fields'=>array('BillingSetup.id','BillingSetup.contract_id','BillingSetup.billing_start_dt','BillingSetup.billing_freq','BillingSetup.dt_of_mth','BillingSetup.day_of_week','BillingSetup.billing_address','BillingSetup.created_by','BillingSetup.created_date','BillingSetup.status'),'conditions'=>array('BillingSetup.status '=>array(0,2,3),$conditions));

		$Billing_data = $this->paginate('BillingSetup'); 
		//pre($Billing_data); die;

		$billing_reconsider = $this->BillingSetup->find('count',array('conditions'=>array('BillingSetup.status'=>3,$conditions)));
		$billing_pending_approval = $this->BillingSetup->find('count',array('conditions'=>array('BillingSetup.status'=>0,$conditions)));
		$billing_in_progress = $this->BillingSetup->find('count',array('conditions'=>array('BillingSetup.status'=>2,$conditions)));

		$billing_reconsider = $billing_reconsider != '' ? $billing_reconsider : 0;
		$billing_pending_approval = $billing_pending_approval != '' ? $billing_pending_approval : 0;
		$billing_in_progress = $billing_in_progress != '' ? $billing_in_progress : 0;

		//pre($Billing_data);die;
		
		$this->set('billing_reconsider',$billing_reconsider);
		$this->set('billing_pending_approval',$billing_pending_approval);
		$this->set('billing_in_progress',$billing_in_progress);
		$this->set('userid',$userid);

		if($this->RequestHandler->isAjax())
             {
                
                $this->autoRender = false;
                $this->layout =false;
                $this->viewPath = 'Elements'.DS.'home';
                $view = new View($this, false);
                $view1 = new View($this, false);

                if(!empty($Billing_data))
                {
                        $view->set('Billing_data',$Billing_data);
                        $view->set('userid',$userid);
                        $html['html'] = $view->render("billing_pending_data_page");
                        $view1->set('pageinfo',$pgdetl);
                        $html['pagination'] = $view1->render("pagination");
                        $html['message'] ='success';
                        $html['billing_reconsider'] =$billing_reconsider;
                        $html['billing_pending_approval'] =$billing_pending_approval;
                        $html['billing_in_progress'] =$billing_in_progress;

                }
                else
                {
                        $html['message'] ='error';
                        $html['billing_reconsider'] =$billing_reconsider;
                        $html['billing_pending_approval'] =$billing_pending_approval;
                        $html['billing_in_progress'] =$billing_in_progress;
                }

                echo json_encode($html);die;

                }
            $this->set('Billing_data',$Billing_data);
	}
	function create_projects()
	{
	}
	function view_billingdetail()
	{
		$id   = $_GET['id'];
		$bill_id=base64_decode($id );
		$this->loadModel('BillingSetup');
		$this->loadModel('Contract');
		$this->loadModel('AppUser');
		$this->loadModel('Entitie');
		$this->loadModel('Project');
		$this->loadModel('CompanyAddress');
		$this->loadModel('EntityAddress');	  
		$this->loadModel('CustomerInvoiceStage');	  
		$this->loadModel('MasterDataDetail');
		$aprroval_flag = $_GET['approval'];
		$this->loadModel('RemarksApproval');

        $remark_data =$this->RemarksApproval->find('all',array('conditions'=>array('RemarksApproval.section_id'=>$bill_id)));	
		//pre($aprroval_flag); die;
		$billing_data  = $this->BillingSetup->find('first',array('fields'=>array('BillingSetup.*'),'conditions'=>array('BillingSetup.id'=>$bill_id))); 
		$contracts_data  = $this->Contract->find('first',array('recursive'=>'-1','fields'=>array('Contract.*'),'conditions'=>array('Contract.id'=>$billing_data['BillingSetup']['contract_id']))); 
		$entitie_data  = $this->Entitie->find('first',array('recursive'=>'-1','fields'=>array('Entitie.*'),'conditions'=>array('Entitie.id'=>$contracts_data['Contract']['cust_entity_id']))); 
		$invoiceStages = $this->CustomerInvoiceStage ->find('all',array('fields'=>array('CustomerInvoiceStage.stage_desc','CustomerInvoiceStage.no_of_days'),'conditions'=>array('CustomerInvoiceStage.entity_id'=>$entitie_data['Entitie']['id'])));

		$getcollector = $this->AppUser->find('first',array('fields'=>array('AppUser.first_name','AppUser.last_name'),'conditions'=>array('AppUser.id'=>$entitie_data['Entitie']['collector_id'])));

		$getprojectdtl = $this->Project->find('first',array('fields'=>array('Project.*'),'conditions'=>array('Project.contract_id'=>$billing_data['BillingSetup']['contract_id'])));
		
		$project_data = $this->Project->find('all',array('fields'=>array('Project.id','Project.customer_entity_id','Project.contract_id','Project.profit_center_id','Project.business_line','Project.subvertical','Project.project_type','Project.project_value','Project.award_date','Project.start_date','Project.project_title','Project.brief_description','Project.initial_end_date','Project.bill_from_address_id','Project.bill_to_address_id','Project.ship_to_address_id'),'conditions'=>array('Project.contract_id'=>$billing_data['BillingSetup']['contract_id'])));
		//pre($project_data);die;
		foreach($project_data as $prodata){
                   //From address
					 $from_addre = $this->CompanyAddress->find('first',array('fields'=>array('CompanyAddress.id',
					 	'CompanyAddress.company_id','CompanyAddress.address_type','CompanyAddress.address_line_1',
					 	'CompanyAddress.address_line_2','CompanyAddress.state','CompanyAddress.city',
					 	'CompanyAddress.country','CompanyAddress.postal_code','CompanyAddress.zone'),'conditions'=>array('CompanyAddress.id'=>$prodata['Project']['bill_from_address_id'])));
					 $from_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$from_addre['CompanyAddress']['address_type'])));
					  $from_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$from_addre['CompanyAddress']['zone'])));
					 
					 //Bill address
					   $bill_addre = $this->EntityAddress->find('first',array('fields'=>array('EntityAddress.*'
					 	),'conditions'=>array('EntityAddress.id'=>$prodata['Project']['bill_to_address_id'])));
					  

					  $bill_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$bill_addre['EntityAddress']['address_type'])));
					  $bill_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$bill_addre['EntityAddress']['zone'])));

					 //Ship address
					  $ship_addre = $this->EntityAddress->find('first',array('fields'=>array('EntityAddress.*'
					 	),'conditions'=>array('EntityAddress.id'=>$prodata['Project']['ship_to_address_id'])));
					  
					  $ship_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ship_addre['EntityAddress']['address_type'])));
					  $ship_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ship_addre['EntityAddress']['zone'])));

					 $project_address.='<h5>'.$prodata['Project']['project_title'].'</h5><ul class="addressSlider">
										<li class="col-lg-4 col-md-3 col-sm-12 padding-l-0">
											<h4>Bill From:</h4>
											<h5>'.$from_add_type['MasterDataDetail']['master_data_type'].'</h5>
											<p>'.$from_addre['CompanyAddress']['address_line_1'].'</p>
											<p>'.$from_addre['CompanyAddress']['city'].','.$from_addre['CompanyAddress']['state'].'-'.$from_addre['CompanyAddress']['postal_code'].'</p>
											<p>Zone :'.$from_zone['MasterDataDetail']['master_data_desc'].'</p>
										</li>
										<li class="col-lg-4 col-md-3 col-sm-12">
											<h4>Bill To:</h4>
											<input type="hidden" name="bill_add_id" value="'.$bill_addre['EntityAddress']['id'].'">
											<h5>'.$bill_add_type['MasterDataDetail']['master_data_type'].'</h5>
											<p>'.$bill_addre['EntityAddress']['address_line_1'].'</p>
											<p>'.$bill_addre['EntityAddress']['city'].','.$bill_addre['EntityAddress']['state'].'-'.$bill_addre['EntityAddress']['postal_code'].'</p>
											<p>Zone :'.$bill_zone['MasterDataDetail']['master_data_desc'].'</p>
										</li>
										<li class="col-lg-4 col-md-3 col-sm-12">
											<h4>Ship To:</h4>
											<h5>'.$ship_add_type['MasterDataDetail']['master_data_type'].'</h5>
											<p>'.$ship_addre['EntityAddress']['address_line_1'].'</p>
											<p>'.$ship_addre['EntityAddress']['city'].','.$ship_addre['EntityAddress']['state'].'-'.$ship_addre['EntityAddress']['postal_code'].'</p>
											<p>Zone :'.$ship_zone['MasterDataDetail']['master_data_desc'].'</p>
										</li>
									</ul>';					
					}

		
		$this->set('billing_data',$billing_data);
		$this->set('contracts_data',$contracts_data);
		$this->set('entitie_data',$entitie_data);
		$this->set('project_data',$project_data);
		$this->set('project_address',$project_address);
		$this->set('aprroval_flag',$aprroval_flag);
		$this->set('remark_data',$remark_data);
		$this->set('invoiceStages',$invoiceStages);
		$this->set('getcollector',$getcollector);
		$this->set('getprojectdtl',$getprojectdtl);
	}
	function create_billing()
	{   
		$sesn = $this->Session->read('admin');
		$this->loadModel('BillingSetup');
		$this->loadModel('Contract');
        $data = $this->request->data;        
       if($this->request->isAjax()){
		  if(!empty($data)){     
       
         	$create_billing['BillingSetup']['contract_id']=$data['select_contract'];
			$create_billing['BillingSetup']['billing_start_dt']=$data['bilstartdate'];
			$create_billing['BillingSetup']['billing_freq']= $data['billing_frequency'];
			$create_billing['BillingSetup']['dt_of_mth']= $data['day_of_month']; 
			$create_billing['BillingSetup']['mth']= '';
			$create_billing['BillingSetup']['day_of_week']= $data['working_day'];
			$create_billing['BillingSetup']['billing_address']= $data['bill_add_id'];
			$create_billing['BillingSetup']['created_by']=$sesn['Admin']['AppUser']['id'];
			$create_billing['BillingSetup']['created_date']=date('Y-m-d H:i:s');
			$create_billing['BillingSetup']['status']=$data['billing_status'];
			$create_billing['BillingSetup']['is_active']='1';
			if($data['biiling_id']==''){
			    $this->BillingSetup->create();
			    $this->BillingSetup->save($create_billing);
			    $last_add_id = $this->BillingSetup->getInsertID();
			}else{
			    $create_billing['BillingSetup']['id']=$data['biiling_id'];
			    $this->BillingSetup->save($create_billing);
			     $last_add_id = $data['biiling_id'];
			}
			$update_contract['Contract']['id']=$data['select_contract'];
			$update_contract['Contract']['bill_status']='1';
			    $this->Contract->save($update_contract);
			    			 
			$msg['last_add_id']=$last_add_id;
			$msg['status']='success';
			}else{
				$msg['status']='error';
			}
			echo json_encode($msg);die;
	}
         
	}	
	function create_cmssetup()
	{
	}
	public function getCollectorPhone(){
	  $this->loadModel('AppUser');
	  $data = $this->request->data;
	  $dataval = array();
	  if($this->request->is('ajax')){
		   if(!empty($data)){
			    $collectorData = $this->AppUser->find('first',array('fields'=>array('AppUser.id','AppUser.user_mobile','AppUser.first_name',
				                    'AppUser.last_name'),'conditions'=>array('AppUser.id'=>$data['collectorId'])));
									 
				if(!empty($collectorData)){
					 $dataval['phone_no'] = $collectorData['AppUser']['user_mobile'];
					 $dataval['collector_name'] = $collectorData['AppUser']['first_name'].' '.$collectorData['AppUser']['last_name'];
					 $dataval['status'] = 'success';
					}
				else{
					 $dataval['phone_no'] = '';
					 $dataval['collector_name'] = '';
					 $dataval['status'] = 'error';
					}
					echo json_encode($dataval);die;
			   }
		  }
	 }
	 public function insert_cms_invoice(){
	 	$sesn=$this->Session->read('admin');
		$userid = $sesn['Admin']['AppUser']['id'];
      $this->loadModel('CustomerInvoiceStage');
	  $this->loadModel('Entitie');
	   $this->loadModel('BillingSetup');
	  $data = $this->request->data;
	  //pre($data); die;
	  if($this->request->isAjax()){
		  if(!empty($data)){
		  	$contract_id  = $this->BillingSetup->find('first',array('recursive'=>'-1','order'=>'BillingSetup.id DESC','fields'=>array('BillingSetup.id')));

		  	$BillingSetup['BillingSetup']['status'] = 0;
				 $this->BillingSetup->id = $contract_id['BillingSetup']['id'];
				 $this->BillingSetup->save($BillingSetup);
		  
		    if(!empty($data['collector_id'])){
				 $entityData['collector_id'] = $data['collector_id'];
				 $this->Entitie->id = $data['entitie_id'];
				 $this->Entitie->save($entityData);
				}				
				  for($i=0;$i<count($data['checkboxArr']);$i++){
					if($data['checkboxArr'][$i]=='Yes' && $data['invoiceTitleArr'][$i]!='on'){					   
					  $invoiceData['CustomerInvoiceStage']['entity_id'] = $data['entitie_id'];				  
					  $invoiceData['CustomerInvoiceStage']['stage_desc'] = $data['invoiceTitleArr'][$i];				  
					  $invoiceData['CustomerInvoiceStage']['seq_no'] = $data['sequenceArr'][$i];				 
					  $invoiceData['CustomerInvoiceStage']['inv_stage_id'] = $data['invoiceIdArr'][$i];				  
					  $invoiceData['CustomerInvoiceStage']['no_of_days'] = $data['daysArr'][$i];
					  $invoiceData['CustomerInvoiceStage']['created_date'] = date('Y-m-d');					 
					  $this->CustomerInvoiceStage->create();
					  $this->CustomerInvoiceStage->save($invoiceData);				  
					  }else{					  
					  $invoiceData['CustomerInvoiceStage']['entity_id'] = $data['entitie_id'];				  
					  $invoiceData['CustomerInvoiceStage']['stage_desc'] = $data['invoiceInputArr'][$i];				  
					  $invoiceData['CustomerInvoiceStage']['seq_no'] = '';				 
					  $invoiceData['CustomerInvoiceStage']['inv_stage_id'] = '';				  
					  $invoiceData['CustomerInvoiceStage']['no_of_days'] = $data['daysArr'][$i];
					  $invoiceData['CustomerInvoiceStage']['created_date'] = date('Y-m-d');					 
					  $this->CustomerInvoiceStage->create();
					  $this->CustomerInvoiceStage->save($invoiceData);
					  }
						   
				   } 
				   $reporting_email  = $this->AppUser->find('first',array('recursive'=>'-1','fields'=>array('AppUser.*'),'conditions'=>array('AppUser.id'=>$userid)));

				$link=HTTP_ROOT.'home/billing_pending';
				App::uses('CakeEmail', 'Network/Email');
				$Email = new CakeEmail();
				$Email->config('gmail'); 
				$Email->emailFormat('html'); 
				$Email->to($reporting_email['AppUser']['user_email']);
				//$Email->to('ramjee2443@gmail.com');
				$Email->subject('CMS Approval request');
				$Email->template('customers_approval');
				$Email->viewVars(array('from'=>$ses['Admin']['AppUser'],'link'=>$link));
				$Email->send();
				   echo 'success';die; 				   
			  }else{
			   echo 'error';die;
			  }		
			  
		  }
	 }
	function approve_invoice(){
		$this->loadModel('Invoice');
		$this->loadModel('AppUser');
		$data = $this->request->data; 
		if($data['type']=='approve')
		{
			$approve_inv['Invoice']['dun_pause_exclude_reason']='Approved';		 
			$this->Invoice->id=$data['id'];
			$this->Invoice->save($approve_inv);
		}
		else if($data['type']=='reject')
		{
			$reject_inv['Invoice']['dunning_status']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_start_dt']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_end_dt']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_reason']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_remarks']=NULL;
			$this->Invoice->id=$data['id'];
			$this->Invoice->save($reject_inv);
		}
		$invoie = $this->Invoice->find('first', array('recursive'=>'-1','fields'=>array('Entitie.id'),'conditions' => array('Invoice.id' =>$data['id']))); 
		$userDetail = $this->AppUser->find('first', array('recursive'=>'-1','fields'=>array('AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions' =>array('AppUser.entity_id' =>$invoie['Entitie']['id'],'AppUser.role_id'=>3)));
		if($userDetail)
		{
			App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();
			$Email->config('gmail'); 
			$Email->emailFormat('html'); 
			$Email->to($userDetail['AppUser']['user_email']);
			//$Email->to('neerajdv42@gmail.com');
			//$Email->to('ramjee2443@gmail.com');
			$Email->subject('Exclusion Response');
			$Email->template('exclusion_response');
			$Email->viewVars(array('to'=>$userDetail['AppUser'],'type'=>$data['type']));
			$Email->send();
		}
		 echo "success"; die();
	}
	function approve_invoice_included(){
		$this->loadModel('Invoice');
		$this->loadModel('AppUser');
		$data = $this->request->data; 
		if($data['type']=='reject')
		{
			$approve_inv['Invoice']['dun_pause_exclude_reason']='Approved';	
			$approve_inv['Invoice']['dunning_status']='Excluded';		 
			$this->Invoice->id=$data['id'];
			$this->Invoice->save($approve_inv);
		}
		else if($data['type']=='approve')
		{
			$reject_inv['Invoice']['dunning_status']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_start_dt']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_end_dt']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_reason']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_remarks']=NULL;
			$this->Invoice->id=$data['id'];
			$this->Invoice->save($reject_inv);
		}
		$invoie = $this->Invoice->find('first', array('recursive'=>'-1','fields'=>array('Entitie.id'),'conditions' => array('Invoice.id' =>$data['id']))); 
		$userDetail = $this->AppUser->find('first', array('recursive'=>'-1','fields'=>array('AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions' =>array('AppUser.entity_id' =>$invoie['Entitie']['id'],'AppUser.role_id'=>3)));
		if($userDetail)
		{
			App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();
			$Email->config('gmail'); 
			$Email->emailFormat('html'); 
			$Email->to($userDetail['AppUser']['user_email']);
			//$Email->to('neerajdv42@gmail.com');
			//$Email->to('ramjee2443@gmail.com');
			$Email->subject('Inclusion Response');
			$Email->template('inclusion_response');
			$Email->viewVars(array('to'=>$userDetail['AppUser'],'type'=>$data['type']));
			$Email->send();
		}
		 echo "success"; die();
	}
	function approve_customer(){
		$this->loadModel('Entitie');
		$this->loadModel('Contact');
		$this->loadModel('RemarksApproval');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
		$data = $this->request->data; 

		if($this->request->is('ajax')){
		    if(!empty($data)){
	        	
                $approve_entitie['Entitie']['status']=$data['customer_status'];		 
			    $this->Entitie->id=$data['customer_id'];
			    $this->Entitie->save($approve_entitie);
			    
			    if($data['customer_status'] == "Rejected"){
			    	$remarks_approve['RemarksApproval']['section']="customers";
			    	$remarks_approve['RemarksApproval']['section_id']=$data['customer_id'];
			    	$remarks_approve['RemarksApproval']['remarks_desc']=$data['remarks_customer'];
			    	$remarks_approve['RemarksApproval']['remarks_by']=$userid;		 
			    	$remarks_approve['RemarksApproval']['created_date']=date('Y-m-d');
			    	$remarks_approve['RemarksApproval']['tab']='info';
			    	$this->RemarksApproval->create();
			        $this->RemarksApproval->save($remarks_approve);
			    }
		   }
      		    $customer_email = $this->Contact->find('first', array('recursive'=>'-1','fields'=>
	    		array('Contact.contact_email'),'conditions' => array('Contact.entity_id' =>$data['customer_id'])));

		        $link=HTTP_ROOT.'home/customers_pending';
				App::uses('CakeEmail', 'Network/Email');
				$Email = new CakeEmail();
				$Email->config('gmail'); 
				$Email->emailFormat('html'); 
				$Email->to($customer_email['Contact']['contact_email']);
				//$Email->to('ramjee2443@gmail.com');
				$Email->subject('Customers Approval Response');
				$Email->template('customers_approval');
				$Email->viewVars(array('from'=>$ses['Admin']['AppUser'],'status'=>$data['customer_status'],'link'=>$link));
				$Email->send();
		   echo "success"; die;
		}else{
			 echo "error"; die;
		}				
		 
	}
	function approve_contract(){
		$this->loadModel('Contract');
		$this->loadModel('RemarksApproval');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
		$data = $this->request->data; 
		//pre($data); die;
		if($this->request->is('ajax')){
		    if(!empty($data)){

				$approve_contract['Contract']['status']=$data['contract_status'];		 
				$this->Contract->id=$data['contract_id'];
				$this->Contract->save($approve_contract);

				if($data['contract_status'] == 3){
			    	$remarks_approve['RemarksApproval']['section']="contracts";
			    	$remarks_approve['RemarksApproval']['section_id']=$data['contract_id'];
			    	$remarks_approve['RemarksApproval']['remarks_desc']=$data['remarks_contract'];
			    	$remarks_approve['RemarksApproval']['remarks_by']=$userid;		 
			    	$remarks_approve['RemarksApproval']['created_date']=date('Y-m-d');
			    	$remarks_approve['RemarksApproval']['tab']='con';
			    	$this->RemarksApproval->create();
			        $this->RemarksApproval->save($remarks_approve);
			    }
				
		    }
		        echo "success"; die;
		}else{
		        echo "error"; die;
		}
		
	}
	function approve_billing(){
		$this->loadModel('BillingSetup');
		$this->loadModel('RemarksApproval');
		$sesn = $this->Session->read('admin');
        $userid = $sesn['Admin']['AppUser']['id'];
		$data = $this->request->data; 
		//pre($data); die;
		if($this->request->is('ajax')){
		    if(!empty($data)){
				$approve_billing['BillingSetup']['status']=$data['billing_status'];		 
				$this->BillingSetup->id=$data['billing_id'];
				$this->BillingSetup->save($approve_billing);

				if($data['billing_status'] == 3){
			    	$remarks_approve['RemarksApproval']['section']="billing";
			    	$remarks_approve['RemarksApproval']['section_id']=$data['billing_id'];
			    	$remarks_approve['RemarksApproval']['remarks_desc']=$data['remarks_billing'];
			    	$remarks_approve['RemarksApproval']['remarks_by']=$userid;		 
			    	$remarks_approve['RemarksApproval']['created_date']=date('Y-m-d');
			    	$this->RemarksApproval->create();
			        $this->RemarksApproval->save($remarks_approve);
			    }				
		    }
		        echo "success"; die;
		}else{
		        echo "error"; die;
		}
		
	}

	function request_include_invoice(){
		$this->loadModel('Invoice');
		$this->loadModel('AppUser');
		$data = $this->request->data; 
		
			$request_inv['Invoice']['dunning_status']='Included';
			//$reject_inv['Invoice']['dun_pause_exclude_start_dt']=NULL;
			//$reject_inv['Invoice']['dun_pause_exclude_end_dt']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_reason']=NULL;
			$request_inv['Invoice']['dun_pause_exclude_remarks']='Included Request';
			$this->Invoice->id=$data['id'];
			$this->Invoice->save($request_inv);
		
		$invoie = $this->Invoice->find('first', array('recursive'=>'-1','fields'=>
			array('Entitie.id'),'conditions' => array('Invoice.id' =>$data['id']))); 
		$userDetail = $this->AppUser->find('first', array('recursive'=>'-1','fields'=>
			array('AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions'=>
			array('AppUser.entity_id' =>$invoie['Entitie']['id'],'AppUser.role_id'=>7)));
		
			$link=HTTP_ROOT.'home/cms_requests?email_req=true';
			App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();
			$Email->config('gmail'); 
			$Email->emailFormat('html'); 
			$Email->to($userDetail['AppUser']['user_email']);
			//$Email->to('ramjee2443@gmail.com');
			$Email->subject('Inclusion request');
			$Email->template('inclusion_approval');
			$Email->viewVars(array('to'=>$userDetail['AppUser'],'from'=>$ses['Admin']['AppUser'],'link'=>$link));
			$Email->send();
		 echo "success"; die;
	}
	public function include_invoice_request(){
		$this->loadModel('Invoice');
		$this->loadModel('AppUser');
		$data = $this->request->data; 
		
			$request_inv['Invoice']['dunning_status']='Included';
			//$reject_inv['Invoice']['dun_pause_exclude_start_dt']=NULL;
			//$reject_inv['Invoice']['dun_pause_exclude_end_dt']=NULL;
			$reject_inv['Invoice']['dun_pause_exclude_reason']=NULL;
			$request_inv['Invoice']['dun_pause_exclude_remarks']='Included Request';
			$this->Invoice->id=$data['id'];
			$this->Invoice->save($request_inv);
		
		$invoie = $this->Invoice->find('first', array('recursive'=>'-1','fields'=>
			array('Entitie.id'),'conditions' => array('Invoice.id' =>$data['id']))); 
		$userDetail = $this->AppUser->find('first', array('recursive'=>'-1','fields'=>
			array('AppUser.first_name','AppUser.last_name','AppUser.user_email'),'conditions'=>
			array('AppUser.entity_id' =>$invoie['Entitie']['id'],'AppUser.role_id'=>7)));
		
		$link=HTTP_ROOT.'home/cms_requests?email_req=true';
			App::uses('CakeEmail', 'Network/Email');
			$Email = new CakeEmail();
			$Email->config('gmail'); 
			$Email->emailFormat('html'); 
			$Email->to($userDetail['AppUser']['user_email']);
			//$Email->to('ramjee2443@gmail.com');
			$Email->subject('Inclusion request');
			$Email->template('inclusion_approval');
			$Email->viewVars(array('to'=>$userDetail['AppUser'],'from'=>$ses['Admin']['AppUser'],'link'=>$link));
			$Email->send();
		 echo "success"; die();

	}
    public function get_doc_cat()
	{
		$this->loadModel('ArSubCategory');
		$this->loadModel('ArCategory');
		$this->loadModel('OtherTransaction');
		$this->loadModel('Contact');
		$data = $this->request->data;
		$trsen = $this->OtherTransaction->find('first',array('fields'=>array('OtherTransaction.invoice_number','OtherTransaction.ar_cat_id','OtherTransaction.ar_sub_cat_id','OtherTransaction.entity_id'),
		 	'conditions'=>array('OtherTransaction.id'=>$data['id'])));
		
		$option='';
		 $arcategory = $this->ArCategory->find('list',array('fields'=>array('ArCategory.ar_cat')));
		 foreach($arcategory as $k=>$c)
		 {
			if($k==$trsen['OtherTransaction']['ar_cat_id'])
			$selc='selected="selected"';
			else
			$selc='';
		 	 $option.='<option '.$selc.' value="'.$k.'">'.$c.'</option>';
		 }
		
		 $msg['option']=	$option;
		 $optionsb='';
		 $ar_sub_category = $this->ArSubCategory->find('list',array('fields'=>array('ArSubCategory.ar_sub_cat'),
		 	'conditions'=>array('ArSubCategory.ar_cat_id'=>$trsen['OtherTransaction']['ar_cat_id'])));
		 foreach($ar_sub_category as $k=>$sub)
		 {
			if($k==$trsen['OtherTransaction']['ar_sub_cat_id'])
			$selc='selected="selected"';
			else
			$selc='';
		 	$optionsb.='<option '.$selc.' value="'.$k.'">'.$sub.'</option>';
		 }
		 $msg['optionsub']=	$optionsb;
		 $optionspo='';
		 $spoc = $this->Contact->find('list',array('fields'=>array('Contact.contact_designation'),
		 	'conditions'=>array('Contact.entity_id'=>$trsen['OtherTransaction']['entity_id'])));
		 foreach($spoc as $k=>$spo)
		 {
			$selc='';
		 	$optionspo.='<option '.$selc.' value="'.$k.'">'.$spo.'</option>';
		 }
		 $msg['optionspo']=	$optionspo;
		echo json_encode($msg); die;  
		
	}
	
	function update_arcates(){
		$ses=$this->Session->read('admin');
	    $this->loadModel('ArCategorie');
		$this->loadModel('ArSubCategory');
		$this->loadModel('NotesInvoiceArchangeHistory');
		$this->loadModel('OtherTransaction');
		$this->loadModel('InvoiceDunningComm');
		$this->loadModel('Invoice');
		$data = $this->request->data;
		//pre($data); die();
		$arr = $data['doctype'];
		$trsen = $this->OtherTransaction->find('first',array('fields'=>array('OtherTransaction.id','OtherTransaction.invoice_number','OtherTransaction.fiscal_year','OtherTransaction.ar_cat_id','OtherTransaction.ar_sub_cat_id',),'conditions'=>array('OtherTransaction.id'=>$data['id'])));
		
		$inv = $this->Invoice->find('first',array('fields'=>array('Invoice.id','Invoice.ar_cat_id',
			 'Invoice.ar_sub_cat_id'),'conditions'=>array('Invoice.invoice_number'=>
			 $trsen['OtherTransaction']['invoice_number'],'Invoice.fiscal_year'=>
			 $trsen['OtherTransaction']['fiscal_year'])));
		$docinvoice = array('AB','DR','OB','RV');
		 
		if(in_array($arr, $docinvoice))
		{
			$savinv['Invoice']['id']=$inv['Invoice']['id'];
			$savinv['Invoice']['ar_cat_id']=$data['ocat_id'];
			$savinv['Invoice']['ar_sub_cat_id']=$data['osubcat_id'];
			$savinv['Invoice']['ar_date']=date('Y-m-d H:i:s');
			$this->Invoice->save($savinv);

			$inh['NotesInvoiceArchangeHistory']['assign_to']=$data['assign_to'];
			$inh['NotesInvoiceArchangeHistory']['user_id']=$ses['Admin']['AppUser']['id'];
			$inh['NotesInvoiceArchangeHistory']['type']=1;
			$inh['NotesInvoiceArchangeHistory']['invoice_id']=$inv['Invoice']['id'];
			$inh['NotesInvoiceArchangeHistory']['ar_cat_id']=$inv['Invoice']['ar_cat_id'];
			$inh['NotesInvoiceArchangeHistory']['ar_sub_cat_id']=$inv['Invoice']['ar_sub_cat_id'];
			$inh['NotesInvoiceArchangeHistory']['notes']=$data['editnotes'];
			$inh['NotesInvoiceArchangeHistory']['date']=date('Y-m-d H:i:s');
			$this->NotesInvoiceArchangeHistory->create();
			$this->NotesInvoiceArchangeHistory->save($inh);
	
		} 
            $ar_update_desc = $this->ArCategorie->find('all',array('fields'=>array('ArCategorie.ar_cat'),
            	'conditions'=>array('ArCategorie.id'=>$inv['Invoice']['ar_cat_id'])));
            $ar_pri_desc = $this->ArCategorie->find('all',array('fields'=>array('ArCategorie.ar_cat'),
            	'conditions'=>array('ArCategorie.id'=>$data['ocat_id'])));

            $ar_sub_update_desc = $this->ArSubCategory->find('all',array('fields'=>array('ArSubCategory.ar_sub_cat'),
            	'conditions'=>array('ArSubCategory.id'=>$inv['Invoice']['ar_sub_cat_id'])));
           
            $ar_sub_pri_desc = $this->ArSubCategory->find('all',array('fields'=>array('ArSubCategory.ar_sub_cat'),
            	'conditions'=>array('ArSubCategory.id'=>$inv['Invoice']['ar_sub_cat_id'])));
            
              $note_history = 'AR category'.' ('.$ar_pri_desc[0]['ArCategorie']['ar_cat'].') '.'change into'.' ('.
             	$ar_update_desc[0]['ArCategorie']['ar_cat'].')'.'AR Subcategory'.' ('.
             	$ar_sub_pri_desc[0]['ArSubCategory']['ar_sub_cat'].') '.'change into'.' ('.
             	$ar_sub_update_desc[0]['ArSubCategory']['ar_sub_cat'].'=='.$data['editnotes'];
             	//echo $note_history; die();
                       
		    $dunning_inh['InvoiceDunningComm']['user_id']=$ses['Admin']['AppUser']['id'];
			$dunning_inh['InvoiceDunningComm']['invoice_id']=$inv['Invoice']['id'];
			$dunning_inh['InvoiceDunningComm']['document_id']=$data['document_id'];
			$dunning_inh['InvoiceDunningComm']['communication_text']= $note_history;
			$dunning_inh['InvoiceDunningComm']['history_of']= 'AR Category';
			$dunning_inh['InvoiceDunningComm']['comms_date']=date('Y-m-d H:i:s');
			$this->InvoiceDunningComm->create();
			$this->InvoiceDunningComm->save($dunning_inh);
					  
		$savothr['OtherTransaction']['id']=$inv['Invoice']['id'];
		$savothr['OtherTransaction']['ar_cat_id']=$data['ocat_id'];
		$savothr['OtherTransaction']['ar_sub_cat_id']=$data['osubcat_id'];
		$savothr['OtherTransaction']['ar_date']=date('Y-m-d H:i:s');
		$this->OtherTransaction->save($savothr);
		
		$inh1['NotesInvoiceArchangeHistory']['assign_to']=$data['assign_to'];
		$inh1['NotesInvoiceArchangeHistory']['user_id']=$ses['Admin']['AppUser']['id'];
		$inh1['NotesInvoiceArchangeHistory']['type']=2;
		$inh1['NotesInvoiceArchangeHistory']['other_transection_id']=$trsen['OtherTransaction']['id'];
		$inh1['NotesInvoiceArchangeHistory']['ar_cat_id']=$inv['Invoice']['ar_cat_id'];
		$inh1['NotesInvoiceArchangeHistory']['ar_sub_cat_id']=$inv['Invoice']['ar_sub_cat_id'];
		$inh1['NotesInvoiceArchangeHistory']['notes']=$data['editnotes'];
		$inh1['NotesInvoiceArchangeHistory']['date']=date('Y-m-d H:i:s');
		$this->NotesInvoiceArchangeHistory->create();
		$this->NotesInvoiceArchangeHistory->save($inh1);
		echo 'success';die;
	}
	function update_arcates_for_mul()
	{
		$ses=$this->Session->read('admin');
	    $this->loadModel('ArCategorie');
		$this->loadModel('ArSubCategory');
		$this->loadModel('NotesInvoiceArchangeHistory');
		$this->loadModel('OtherTransaction');
		$this->loadModel('InvoiceDunningComm');
		$this->loadModel('Invoice');
		$data = $this->request->data;
		//pre($data['id'][0]); die();		
		$invoices_id = $data['id'];

		// $trsen = $this->OtherTransaction->find('first',array('fields'=>array('OtherTransaction.id',
		// 	'OtherTransaction.invoice_number','OtherTransaction.fiscal_year','OtherTransaction.ar_cat_id',
		// 	'OtherTransaction.ar_sub_cat_id',),'conditions'=>array('OtherTransaction.id'=>$data['id'])));
		
		// $inv = $this->Invoice->find('first',array('fields'=>array('Invoice.id','Invoice.ar_cat_id',
		// 	 'Invoice.ar_sub_cat_id'),'conditions'=>array('Invoice.invoice_number'=>
		// 	 $trsen['OtherTransaction']['invoice_number'],'Invoice.fiscal_year'=>
		// 	 $trsen['OtherTransaction']['fiscal_year'])));
		// $docinvoice = array('AB','DR','OB','RV');		

	foreach ($invoices_id as $key => $invoice_id) {

		$invoice_number = $this->Invoice->find('first',array('fields'=>array('Invoice.*'),
			'conditions'=>array('Invoice.id'=>$data['id'])));
		$inv_num_all_tran = $this->OtherTransaction->find('first',array('fields'=>array('OtherTransaction.id'),
			'conditions'=>array('OtherTransaction.invoice_number'=>$invoice_number['Invoice']['invoice_number'])));

		$ar_update_desc = $this->ArCategorie->find('all',array('fields'=>array('ArCategorie.ar_cat'),
            	'conditions'=>array('ArCategorie.id'=>$invoice_number['Invoice']['ar_cat_id'])));
        $ar_pri_desc = $this->ArCategorie->find('all',array('fields'=>array('ArCategorie.ar_cat'),
            	'conditions'=>array('ArCategorie.id'=>$data['ocat_id'])));         

        $ar_sub_update_desc = $this->ArSubCategory->find('all',array('fields'=>array('ArSubCategory.ar_sub_cat'),
            	'conditions'=>array('ArSubCategory.id'=>$invoice_number['Invoice']['ar_sub_cat_id'])));
           
        $ar_sub_pri_desc = $this->ArSubCategory->find('all',array('fields'=>array('ArSubCategory.ar_sub_cat'),
            	'conditions'=>array('ArSubCategory.id'=>$invoice_number['Invoice']['ar_sub_cat_id'])));

	    $note_history = 'AR category'.' ('.$ar_pri_desc[0]['ArCategorie']['ar_cat'].') '.'change into'.' ('.
        $ar_update_desc[0]['ArCategorie']['ar_cat'].')'.'AR Subcategory'.' ('.
        $ar_sub_pri_desc[0]['ArSubCategory']['ar_sub_cat'].') '.'change into'.' ('.
        $ar_sub_update_desc[0]['ArSubCategory']['ar_sub_cat'].'=='.$data['editnotes'];	

			$savinv['Invoice']['id']=$invoice_id;
			$savinv['Invoice']['ar_cat_id']=$data['ocat_id'];
			$savinv['Invoice']['ar_sub_cat_id']=$data['osubcat_id'];
			$savinv['Invoice']['ar_date']=date('Y-m-d H:i:s');
			$this->Invoice->save($savinv);

			$savothr['OtherTransaction']['id']=$inv_num_all_tran['OtherTransaction']['id'];
			$savothr['OtherTransaction']['ar_cat_id']=$data['ocat_id'];
			$savothr['OtherTransaction']['ar_sub_cat_id']=$data['osubcat_id'];
			$savothr['OtherTransaction']['ar_date']=date('Y-m-d H:i:s');
			$this->OtherTransaction->save($savothr);

			$inh['NotesInvoiceArchangeHistory']['assign_to']=$data['assign_to'];
			$inh['NotesInvoiceArchangeHistory']['user_id']=$ses['Admin']['AppUser']['id'];
			$inh['NotesInvoiceArchangeHistory']['type']=1;
			$inh['NotesInvoiceArchangeHistory']['invoice_id']=$invoice_id;
			$inh['NotesInvoiceArchangeHistory']['ar_cat_id']=$data['ocat_id'];
			$inh['NotesInvoiceArchangeHistory']['ar_sub_cat_id']=$data['osubcat_id'];
			$inh['NotesInvoiceArchangeHistory']['notes']=$data['editnotes'];
			$inh['NotesInvoiceArchangeHistory']['date']=date('Y-m-d H:i:s');
			$this->NotesInvoiceArchangeHistory->create();
			$this->NotesInvoiceArchangeHistory->save($inh);

			$inh['NotesInvoiceArchangeHistory']['assign_to']=$data['assign_to'];
			$inh['NotesInvoiceArchangeHistory']['user_id']=$ses['Admin']['AppUser']['id'];
			$inh['NotesInvoiceArchangeHistory']['type']=2;
			$inh['NotesInvoiceArchangeHistory']['other_transection_id']=$inv_num_all_tran['OtherTransaction']['id'];
			$inh['NotesInvoiceArchangeHistory']['ar_cat_id']=$data['ocat_id'];
			$inh['NotesInvoiceArchangeHistory']['ar_sub_cat_id']=$data['osubcat_id'];
			$inh['NotesInvoiceArchangeHistory']['notes']=$data['editnotes'];
			$inh['NotesInvoiceArchangeHistory']['date']=date('Y-m-d H:i:s');
			$this->NotesInvoiceArchangeHistory->create();
			$this->NotesInvoiceArchangeHistory->save($inh);

			$dunning_inh['InvoiceDunningComm']['user_id']=$ses['Admin']['AppUser']['id'];
			$dunning_inh['InvoiceDunningComm']['invoice_id']=$invoice_id;
			//$dunning_inh['InvoiceDunningComm']['document_id']=$data['document_id'];
			$dunning_inh['InvoiceDunningComm']['communication_text']= $note_history;
			$dunning_inh['InvoiceDunningComm']['history_of']= 'AR Category';
			$dunning_inh['InvoiceDunningComm']['comms_date']=date('Y-m-d H:i:s');
			$dunning_inh['InvoiceDunningComm']['created_date']=date('Y-m-d H:i:s');
			$this->InvoiceDunningComm->create();
			$this->InvoiceDunningComm->save($dunning_inh);
			
		}
				
   //          $ar_update_desc = $this->ArCategorie->find('all',array('fields'=>array('ArCategorie.ar_cat'),
   //          	'conditions'=>array('ArCategorie.id'=>$inv['Invoice']['ar_cat_id'])));
   //          $ar_pri_desc = $this->ArCategorie->find('all',array('fields'=>array('ArCategorie.ar_cat'),
   //          	'conditions'=>array('ArCategorie.id'=>$data['ocat_id'])));
            
   //          $ar_sub_update_desc = $this->ArSubCategory->find('all',array('fields'=>array('ArSubCategory.ar_sub_cat'),
   //          	'conditions'=>array('ArSubCategory.id'=>$inv['Invoice']['ar_sub_cat_id'])));
           
   //          $ar_sub_pri_desc = $this->ArSubCategory->find('all',array('fields'=>array('ArSubCategory.ar_sub_cat'),
   //          	'conditions'=>array('ArSubCategory.id'=>$inv['Invoice']['ar_sub_cat_id'])));
                         
   //            $note_history = 'AR category'.' ('.$ar_pri_desc[0]['ArCategorie']['ar_cat'].') '.'change into'.' ('.
   //           	$ar_update_desc[0]['ArCategorie']['ar_cat'].')'.'AR Subcategory'.' ('.
   //           	$ar_sub_pri_desc[0]['ArSubCategory']['ar_sub_cat'].') '.'change into'.' ('.
   //           	$ar_sub_update_desc[0]['ArSubCategory']['ar_sub_cat'].'=='.$data['editnotes'];
   //           	//echo $note_history; die();
                        
		 //    $dunning_inh['InvoiceDunningComm']['user_id']=$ses['Admin']['AppUser']['id'];
			// $dunning_inh['InvoiceDunningComm']['invoice_id']=$inv['Invoice']['id'];
			// $dunning_inh['InvoiceDunningComm']['document_id']=$data['document_id'];
			// $dunning_inh['InvoiceDunningComm']['communication_text']= $note_history;
			// $dunning_inh['InvoiceDunningComm']['history_of']= 'AR Category';
			// $dunning_inh['InvoiceDunningComm']['comms_date']=date('Y-m-d H:i:s');
			// $this->InvoiceDunningComm->create();
			// $this->InvoiceDunningComm->save($dunning_inh);
					 
		// $savothr['OtherTransaction']['id']=$inv['Invoice']['id'];
		// $savothr['OtherTransaction']['ar_cat_id']=$data['ocat_id'];
		// $savothr['OtherTransaction']['ar_sub_cat_id']=$data['osubcat_id'];
		// $savothr['OtherTransaction']['ar_date']=date('Y-m-d H:i:s');
		// $this->OtherTransaction->save($savothr);
		
		// $inh1['NotesInvoiceArchangeHistory']['assign_to']=$data['assign_to'];
		// $inh1['NotesInvoiceArchangeHistory']['user_id']=$ses['Admin']['AppUser']['id'];
		// $inh1['NotesInvoiceArchangeHistory']['type']=2;
		// $inh1['NotesInvoiceArchangeHistory']['other_transection_id']=$trsen['OtherTransaction']['id'];
		// $inh1['NotesInvoiceArchangeHistory']['ar_cat_id']=$inv['Invoice']['ar_cat_id'];
		// $inh1['NotesInvoiceArchangeHistory']['ar_sub_cat_id']=$inv['Invoice']['ar_sub_cat_id'];
		// $inh1['NotesInvoiceArchangeHistory']['notes']=$data['editnotes'];
		// $inh1['NotesInvoiceArchangeHistory']['date']=date('Y-m-d H:i:s');
		// $this->NotesInvoiceArchangeHistory->create();
		// $this->NotesInvoiceArchangeHistory->save($inh1);
		echo 'success';die;

	}

	function save_spo_note()
	{
		$ses=$this->Session->read('admin');
		$this->loadModel('NotesInvoiceArchangeHistory');
		$this->loadModel('InvoiceDunningComm');
		$data = $this->request->data;
		//pre($data); die;
				
		 foreach($data['id'] as $key=>$datas){
		 			
			$inh1['NotesInvoiceArchangeHistory']['user_id']=$ses['Admin']['AppUser']['id'];
			$inh1['NotesInvoiceArchangeHistory']['invoice_id']=$datas;
			$inh1['NotesInvoiceArchangeHistory']['type']=2;
			$inh1['NotesInvoiceArchangeHistory']['contact_id']=$data['sposelect'];
			$inh1['NotesInvoiceArchangeHistory']['notes']=$data['notes'];
			$inh1['NotesInvoiceArchangeHistory']['date']=date('Y-m-d H:i:s');
			$this->NotesInvoiceArchangeHistory->save($inh1);

			$notes_inh['InvoiceDunningComm']['user_id']=$ses['Admin']['AppUser']['id'];
			$notes_inh['InvoiceDunningComm']['invoice_id']=$datas;
			$notes_inh['InvoiceDunningComm']['reason_id']=$data['resion'];
			$notes_inh['InvoiceDunningComm']['communication_text']= $data['notes'];
			$notes_inh['InvoiceDunningComm']['history_of']= 'Notes';
			$notes_inh['InvoiceDunningComm']['comms_date']=date('Y-m-d H:i:s');
			$notes_inh['InvoiceDunningComm']['email_sent']=$data['email'];
			$this->InvoiceDunningComm->create();
			$this->InvoiceDunningComm->save($notes_inh);
				
		 }

		echo 'success';die;
	}
	function get_spo_note()
	{
		$this->loadModel('NotesInvoiceArchangeHistory');
		$data = $this->request->data;;
		$trsen = $this->NotesInvoiceArchangeHistory->find('all',array('fields'=>array('NotesInvoiceArchangeHistory.date','NotesInvoiceArchangeHistory.notes','Contact.contact_designation'),'joins' => array(
		array('table' => 'contacts','alias' => 'Contact','type' => 'INNER','conditions' => array('Contact.id = NotesInvoiceArchangeHistory.contact_id'))),'conditions'=>array('NotesInvoiceArchangeHistory.other_transection_id'=>$data['id'])));
		if(empty($trsen))
		$msg['isdata']=0;
		else
		$msg['isdata']=1;
		$msg['data']=$trsen;
		echo json_encode($msg);die;
	}
	public function customer_export_excel(){
		$header = array('CustomerId','CustomerName','CustomerLocation','Address1Type',
			'Address1AddLine1','Address1AddLine2','Address1City','Address1State','Address1PostalCode',
			'Address1Zone','address1_contact1_phone','address1_contact1_name','address1_contact1_email',
			'address1_contact1_designation','address1_contact1_role','address1_contact2_name',
			'address1_contact2_email','address1_contact2_designation','address1_contact2_role',
			'address1_contact2_phone','Address1_gst','Address2AddLine1','Address2AddLine2',
			'Address2City','Address2State','Address2PostalCode','Address2Zone','address2_Contact1_name',
			'address2_Contact1_email','address2_Contact1_designation','address2_Contact1_role',
			'address2_contact1_phone','address2_Contact2_name','address2_Contact2_email',
			'address2_Contact2_designation','address2_Contact2_role','address2_contact2phone',
			'Address2_gst','RelationshipManagerName','RelationshipManagerContact',
			'RelationshipManagerEmail','CreditPeriod','CreditLimit','group_name','TotalTurnover','PanNo',
			'TanNo','GstNo');																																										
		// $header = array('CustomerId','CustomerName','CustomerLocation','Address1Type','Address1AddLine1','Address1AddLine2',
		//  	'Address1City','Address1State','Address1PostalCode','Address1Zone','Address2AddLine1',
		//  	'Address2AddLine2','Address2City','Address2State','Address2PostalCode','Address2Zone',
		//  	'CreditLimit','TotalTurnover','PanNo','TanNo','GstNo','address1_contact1_phone',
		//  	'address1_contact1_name','address1_contact1_email','address1_contact1_designation',
		//  	'address1_contact1_role','address1_contact2_name','address1_contact2_email','address1_contact2_designation',
		//  	'address1_contact2_role','address2_Contact1_name','address2_Contact1_email','address2_Contact1_designation',
		//  	'address2_Contact1_role','address2_Contact2_name','address2_Contact2_email','address2_Contact2_designation',
		//  	'address2_Contact2_role','Address1_gst','Address2_gst','RelationshipManagerName','RelationshipManagerContact',
		//  	'RelationshipManagerEmail','CreditPeriod','group_name','address1_contact2_phone','address2_contact1_phone',
		//  	'address2_contact2phone');
		//pre($header); die();
		$fileName = "customer_export_excel.xls";
	      header("Content-Disposition: attachment; filename=$fileName");
          header("Content-Type: application/vnd.ms-excel");
          $flag = false;
          echo implode("\t", array_values($header))."\n";
          // foreach ($header as $key => $value) {
          // if(!$flag){
          // 		echo implode("\t", array_values($value))."\n";
          // 		$flag = true;
          // }
          exit();
	}
	public function contacts_export_excel(){
		$header = array('CustomerId','ContractNumber','ContractType','Contract title','Curreny','TotalContractValue',
			'ParentContractNumber','ContractStartDate','ContractEndDate','NoticePeriod',
			'BillFromAddressLine1','BillFromAddressLine2','BillFromCity','BillFromState',
			'BillFromPostalCode','BillFromZone','BillFromGstNo','BillFromCountry','BillToAddressType',
			'ShipToAddressLType','BilltoAddressLine1','BilltoAddressLine2','BilltoCity','BilltoState',
			'BilltoPostalCode','BilltoZone','BilltoGstNo','BilltoCountry','PaymentTerm',
			'ContractSchedule1Increment','ContractSchedule1StartDate','ContractSchedule1EndDate',
			'ContractSchedule1Value','ContractSchedule1AdjustedValue','ContractSchedule2Increment',
			'ContractSchedule2StartDate','ContractSchedule2EndDate','ContractSchedule2Value',
			'ContractSchedule2AdjustedValue','ContractSchedule3Increment','ContractSchedule3StartDate',
			'ContractSchedule3EndDate','ContractSchedule3Value','ContractSchedule3AdjustedValue',
			'ContractSchedule4Increment','ContractSchedule4StartDate','ContractSchedule4EndDate',
			'ContractSchedule4Value','ContractSchedule4AdjustedValue','ContractSchedule5Increment',
			'ContractSchedule5StartDate','ContractSchedule5EndDate','ContractSchedule5Value',
			'ContractSchedule5AdjustedValue','ContractSchedule6Increment','ContractSchedule6StartDate',
			'ContractSchedule6EndDate','ContractSchedule6Value','ContractSchedule6AdjustedValue',
			'ContractSchedule7Increment','ContractSchedule7StartDate','ContractSchedule7EndDate',
			'ContractSchedule7Value','ContractSchedule7AdjustedValue');
		//pre($header); die();
		$fileName = "contact_export_excel.xls";
	      header("Content-Disposition: attachment; filename=$fileName");
          header("Content-Type: application/vnd.ms-excel");
          $flag = false;
          echo implode("\t", array_values($header))."\n";
          // foreach ($header as $key => $value) {
          // if(!$flag){
          // 		echo implode("\t", array_values($value))."\n";
          // 		$flag = true;
          // }
          exit();
	}
	public function project_export_excel(){
		$header = array('CustomerID','ContractNumber','ProjectID','BusinessLine','Subvertical','ProfitCenter',
			'ProjectType','ProjectValue','StartDate','EndDate','ProjectTitle','ProjectDescription',
			'ProjectBillingType','ProjectTaskTitle','ProjectTaskDescription','ProjectTaskStartDate',
			'ProjectTaskEndDate','RequiresSupportingDocuments','ProjectTaskUnit','ProjectTaskPerUnitRate',
			'ProjectFixedAmount','ProjectOneTimeCharge','ProjectMinimumCharge','ProjectTotalTaskAmount',
			'ProjectTaxInclusion','ProjectTaskRateStartRange1','ProjectTaskRateEndRange1','ProjectTaskRateRange1',
			'ProjectTaskRateStartRange2','ProjectTaskRateEndRange2','ProjectTaskRateRange2',
			'ProjectTaskRateStartRange3','ProjectTaskRateEndRange3','ProjectTaskRateRange3','ProjectTaskRateStartRange4',
			'ProjectTaskRateEndRange4','ProjectTaskRateRange4',	'ProjectTaskRateStartRange5','ProjectTaskRateEndRange5',
			'ProjectTaskRateRange5','ProjectTaskRateStartRange6','ProjectTaskRateEndRange6','ProjectTaskRateRange6',
			'ProjectTaskRateStartRange7','ProjectTaskRateEndRange7','ProjectTaskRateRange7'	);
		//pre($header); die();
		$fileName = "project_export_excel.xls";
	      header("Content-Disposition: attachment; filename=$fileName");
          header("Content-Type: application/vnd.ms-excel");
          $flag = false;
          echo implode("\t", array_values($header))."\n";
          // foreach ($header as $key => $value) {
          // if(!$flag){
          // 		echo implode("\t", array_values($value))."\n";
          // 		$flag = true;
          // }
          exit();
	}
	function billing_inputs_export_excel(){

		$this->loadModel('BillingInput');
		$ses=$this->Session->read('admin');
		$user = $ses['Admin']['AppUser']['id'];

		$billing_inputs=$this->BillingInput->find('all',array('fields'=>array('BillingInput.*'),'conditions'=>array()));
		//pre($billing_inputs); die;

		$this->layout = false;
		CakePlugin::load('PHPExcel');
		App::uses('PHPExcel', 'PHPExcel.Classes');
		
		$fileName = "billing_inputs_export_excel.xlsx";
		$filePath = '../webroot/files/'; 
		unlink($filePath.$fileName);
		$objPHPExcel = new PHPExcel();

		$border_style= array('borders' => array('right' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' =>
			array('rgb' => '000000')),'top' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' =>
			 '000000'))));

		$row=1;
		$objPHPExcel->getActiveSheet(0)
				->setCellValue('A'.$row,'Company Code')
				->setCellValue('B'.$row, 'Company Name')
				->setCellValue('C'.$row, 'Customer Code ')				
				->setCellValue('D'.$row, 'Customer Name')
				->setCellValue('E'.$row, 'Contract Type')
				->setCellValue('F'.$row, 'Contract ID')
				->setCellValue('G'.$row, 'Project ID')
				->setCellValue('H'.$row, 'Task_ID')
				->setCellValue('I'.$row, 'Task. Description')
				->setCellValue('J'.$row, 'Task_Qty')
				->setCellValue('K'.$row, 'Qty_Price')
				->setCellValue('L'.$row, 'Task_Actual_Start_Date')
				->setCellValue('M'.$row, 'Task_Original_End_Date')
				->setCellValue('N'.$row, 'Task_Actual_End_Date')
				->setCellValue('O'.$row, 'Service Name')
				->setCellValue('P'.$row, 'Billing_Type')
				->setCellValue('Q'.$row, 'Amount')
				->setCellValue('R'.$row, 'Doc. Currency')
				->setCellValue('S'.$row, 'Billing input date')
				->setCellValue('T'.$row, 'Billing Approval Date')
				->setCellValue('U'.$row, 'Billed date')
				->setCellValue('V'.$row, 'Project Manager')
				->setCellValue('W'.$row, 'Approved By')
				->setCellValue('X'.$row, 'Profit Center')
				->setCellValue('Y'.$row, 'Profit Center Name')
				->setCellValue('Z'.$row, 'Customer Creation Date')
				->setCellValue('AA'.$row, 'Payment Terms')
				->setCellValue('AB'.$row, 'Payment Terms Description ')
				->setCellValue('AC'.$row, 'Credit Limit Amount');

		$objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('B'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('C'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('D'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('E'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('F'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('G'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('H'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('I'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('J'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('K'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('L'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('M'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('N'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('O'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('P'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Q'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('R'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('S'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('T'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('U'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('V'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('W'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('X'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Y'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Z'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AA'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AB'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Ac'.$row)->applyFromArray($border_style);

		$row=2;
		foreach($billing_inputs as $billing_input){ 

		$objPHPExcel->getActiveSheet(1)
			->setCellValue('A'.$row, "".''."")
			->setCellValue('B'.$row, "".$billing_input['BillingInput']['company_name']."")
			->setCellValue('C'.$row, "".$billing_input['BillingInput']['customer_code']."")
			->setCellValue('D'.$row, "".$billing_input['BillingInput']['customer_name']."")
			->setCellValue('E'.$row, "".$billing_input['BillingInput']['contract_type']."")
			->setCellValue('F'.$row, "".$billing_input['BillingInput']['contract_id']."")
            ->setCellValue('G'.$row, "".$billing_input['BillingInput']['project_id']."")
		    ->setCellValue('H'.$row, "".$billing_input['BillingInput']['task_id']."")
			->setCellValue('I'.$row, "".$billing_input['BillingInput']['task_description']."")
			->setCellValue('J'.$row, "".$billing_input['BillingInput']['task_qty']."")
			->setCellValue('K'.$row, "".$billing_input['BillingInput']['qty_price']."")
			->setCellValue('L'.$row, "".$billing_input['BillingInput']['task_actual_start_date']."")
			->setCellValue('M'.$row, "".$billing_input['BillingInput']['task_original_end_date']."")
			->setCellValue('N'.$row, "".$billing_input['BillingInput']['task_actual_end_date']."")
			->setCellValue('O'.$row, "".$billing_input['BillingInput']['service_name']."")
			->setCellValue('P'.$row, "".$billing_input['BillingInput']['billing_type']."")
			->setCellValue('Q'.$row, "".$billing_input['BillingInput']['amount']."")
			->setCellValue('R'.$row, "".$billing_input['BillingInput']['doc_currency']."")
			->setCellValue('S'.$row, "".$billing_input['BillingInput']['billing_input_date']."")
			->setCellValue('T'.$row, "".$billing_input['BillingInput']['billing_approval_date']."")
			->setCellValue('U'.$row, "".$billing_input['BillingInput']['billed_date']."")
			->setCellValue('W'.$row, "".$billing_input['BillingInput']['project_manager']."")
			->setCellValue('V'.$row, "".$billing_input['BillingInput']['approved_by']."")
			->setCellValue('X'.$row, "".$billing_input['BillingInput']['profit_center']."")
			->setCellValue('Y'.$row, "".$billing_input['BillingInput']['profit_center_name']."")
			->setCellValue('Z'.$row, "".$billing_input['BillingInput']['customer_creation_date']."")
			->setCellValue('AA'.$row, "".$billing_input['BillingInput']['payment_terms']."")
			->setCellValue('AB'.$row, "".$billing_input['BillingInput']['payment_terms_description']."")
			->setCellValue('AC'.$row, "".$billing_input['BillingInput']['credit_limit_amount']."");
			$row++;
		}
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		$objWriter->save($filePath.$fileName);	
		echo 'success'; die;
				
				
	}

	function ar_ageing_export_excel()
	{
		$this->loadModel('OtherTransaction');
		$this->loadModel('MasterDataDetail');
		$this->loadModel('BusinessLine');
		$this->loadModel('ArCategorie');
		// $ar_ageing_data = $this->OtherTransaction->find('all', array('limit'=>20,'recursive'=>'-1','fields'=>
		// 	array('OtherTransaction.*','Entitie.*','ArCategorie.*','DocumentMaster.*'))); 

		$ar_ageing_data = $this->OtherTransaction->find('all',array('joins' => array(array('table' => 'entity_addresses','alias' => 'EntityAddress','type' => 'LEFT','conditions' => 
		array('EntityAddress.entity_id = OtherTransaction.entity_id')),array('table' => 'documents','alias' => 'Document','type' => 'LEFT','conditions' =>array('Document.contract_id = OtherTransaction.contract_id'))),'limit'=>1000,'recursive'=>'-1',
		'fields'=>array('EntityAddress.entity_id','EntityAddress.address_type','EntityAddress.state','EntityAddress.zone','OtherTransaction.entity_id','OtherTransaction.invoice_number','OtherTransaction.invoice_date','OtherTransaction.invoice_amount','OtherTransaction.billing_wi_id','OtherTransaction.ar_cat_id','OtherTransaction.ar_sub_cat_id','OtherTransaction.document_id','OtherTransaction.project_id','OtherTransaction.contract_id','OtherTransaction.document_type_id','OtherTransaction.dunning_attempt_no','OtherTransaction.gl_code','OtherTransaction.invoice_due_dt','OtherTransaction.fiscal_year','OtherTransaction.original_amount','OtherTransaction.debit_amount','OtherTransaction.credit_amount','OtherTransaction.netamountoutstanding','Entitie.entitiy_name','Entitie.credit_period',
			'Entitie.status','ArCategorie.id','ArCategorie.ar_cat','ArCategorie.ar_cat','DocumentMaster.id','DocumentMaster.doc','DocumentMaster.desc','DocumentMaster.created_date','Document.id','Document.document_type','Document.contract_id'),'conditions'=>array('EntityAddress.address_type'=>'Registered')));
        
		//pre($ar_ageing_data);die();
		$this->layout = false;
		CakePlugin::load('PHPExcel');
		App::uses('PHPExcel', 'PHPExcel.Classes');
		
		$fileName = "ar_ageing_export_excel.xlsx";
		$filePath = '../webroot/files/'; 
		unlink($filePath.$fileName);
		$objPHPExcel = new PHPExcel();
		//$this->response->download($fileName);

		$border_style= array('borders' => array('right' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' =>
			array('rgb' => '000000')),'top' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' =>
			 '000000'))));

		// $row=2;
  //       $objPHPExcel->getActiveSheet(0)
		// 	->setCellValue('A'.$row,'Check  Box')
		// 	->setCellValue('B'.$row, 'Due Date');
		
		// $row=3;	
		// $objPHPExcel->getActiveSheet(0)
		//    ->setCellValue('B'.$row,'Invoice Date');		

		// $row=4;					
		// $objPHPExcel->getActiveSheet(0)->mergeCells('A4:G4');	
		// $objPHPExcel->getActiveSheet(0)->getCell('A4')->setValue('Collector/Vertical/sub-vertical/customer etc - Filter option  required');
		// $objPHPExcel->getActiveSheet(0)->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		// $objPHPExcel->getActiveSheet(0)->getStyle("A4")->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet(0)->getStyle("A4:G4")->getFont()->setSize(10);	
		
		$row=7;	
		$objPHPExcel->getActiveSheet(0)
		   ->setCellValue('AB'.$row,'Debit Note Amount');		   

		$objPHPExcel->getActiveSheet(0)->mergeCells('AC7:AD7');	
		$objPHPExcel->getActiveSheet(0)->getCell('AC7')->setValue('Credit Note Amount');
		$objPHPExcel->getActiveSheet(0)->getStyle('AD7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet(0)
                    ->setCellValue('AL'.$row,'ERP Date');
		
        $objPHPExcel->getActiveSheet(0)->mergeCells('AQ7:AW7');	
		$objPHPExcel->getActiveSheet(0)->getCell('AQ7')->setValue('AR Categorization  Values ');
		$objPHPExcel->getActiveSheet(0)->getStyle('AW7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('AQ7:AW7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => 'e6b9b8'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('AY7:BG7');	
		$objPHPExcel->getActiveSheet(0)->getCell('AY7')->setValue('Customizable Aging Bucket (option for both invoice date or due da');
		$objPHPExcel->getActiveSheet(0)->getStyle('BG7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('AY7:BG7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => 'd7e4bd'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('BJ7:BL7');	
		$objPHPExcel->getActiveSheet(0)->getCell('BJ7')->setValue('Cummulative collectors notes ( With  Dates)');
		$objPHPExcel->getActiveSheet(0)->getStyle('BJ7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
        
        $row=8;
		$objPHPExcel->getActiveSheet(0)
				->setCellValue('A'.$row,'Zone')
				->setCellValue('B'.$row, 'State')
				->setCellValue('C'.$row, 'Parent/Group')				
				->setCellValue('D'.$row, 'Customer Name')
				->setCellValue('E'.$row, 'Customer Code')
				->setCellValue('F'.$row, 'Customer Status')
				->setCellValue('G'.$row, 'Customer Category')
				->setCellValue('H'.$row, 'Vertical')
				->setCellValue('I'.$row, 'Sub Vertical')
				->setCellValue('J'.$row, 'LOB')
				->setCellValue('K'.$row, 'Profit Center')
				->setCellValue('L'.$row, 'Profit Center Name')
				->setCellValue('M'.$row, 'Payment Terms')
				->setCellValue('N'.$row, 'Payment term description')
				->setCellValue('O'.$row, 'Credit Limit ')
				->setCellValue('P'.$row, 'Credit Period')
				->setCellValue('Q'.$row, 'Doc. Currency Key')
				->setCellValue('R'.$row, 'Fiscal Year')
				->setCellValue('S'.$row, 'Sales Order No.')
				->setCellValue('T'.$row, 'Batch  Number')
				->setCellValue('U'.$row, 'Batch  Date')
				->setCellValue('V'.$row, 'Document Date')
				->setCellValue('W'.$row, 'ERP Accounting Doc. No.')
				->setCellValue('X'.$row, 'Reference Key')
				->setCellValue('Y'.$row, 'Invoice No.')
				->setCellValue('Z'.$row, 'Invoice  Date')
				->setCellValue('AA'.$row, 'Original Invoice Amount')
				->setCellValue('AB'.$row, 'Debit Amount')
				->setCellValue('AC'.$row, 'Credit Amount')
				->setCellValue('AD'.$row, 'Received Amount')
				->setCellValue('AE'.$row, 'Net Amount Outstanding')
				->setCellValue('AF'.$row, 'Excise Inv. No.')
				->setCellValue('AG'.$row, 'Excise Inv. Date')
				->setCellValue('AH'.$row, 'Comm. Inv. No.')
				->setCellValue('AI'.$row, 'Comm. Inv. Date')
				->setCellValue('AJ'.$row, 'Exchange Rate ')
				->setCellValue('AK'.$row, 'Amount in Doc. Currency')
				->setCellValue('AL'.$row, 'Baseline Date')
				->setCellValue('AM'.$row, 'Advance/Securities')
				->setCellValue('AN'.$row, 'GL Code')
				->setCellValue('AO'.$row, 'Document Type')
				->setCellValue('AP'.$row, 'Document Description')
				->setCellValue('AQ'.$row, 'Not Due AR')
				->setCellValue('AR'.$row, 'Over Due AR')
				->setCellValue('AS'.$row, 'Collectable AR')
				->setCellValue('AT'.$row, 'Doubtful AR')
				->setCellValue('AU'.$row, 'Legal')
				->setCellValue('AV'.$row, 'Disputed')
				->setCellValue('AW'.$row, 'Unadjusted Credit')
				->setCellValue('AX'.$row, 'Due Date')
				->setCellValue('AY'.$row, '0-30 Days')
				->setCellValue('AZ'.$row, '31-60 Days')
				->setCellValue('BA'.$row, '61-90 Days ')
				->setCellValue('BB'.$row, '91-120 Days')
				->setCellValue('BC'.$row, '121-180 Days')
				->setCellValue('BD'.$row, '181-360 Days')
				->setCellValue('BE'.$row, '361-720 Days')
				->setCellValue('BF'.$row, '> 720 Days')
				->setCellValue('BG'.$row, 'AR Aging Days ( From Invoice Date)')
				->setCellValue('BH'.$row, 'AR Aging Days ( From Due Date)')
				->setCellValue('BI'.$row, 'AR Category Remark')
				->setCellValue('BJ'.$row, 'Remarks')
				->setCellValue('BK'.$row, 'Relationship manager')
				->setCellValue('BL'.$row, 'Project  Manager')
				->setCellValue('BM'.$row, 'Collector Manager')
				->setCellValue('BN'.$row, 'Collector');

	$objPHPExcel->getActiveSheet(0)->getStyle('A8:P8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));

	$objPHPExcel->getActiveSheet(0)->getStyle('Q8:AP8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '92d050'))));

	$objPHPExcel->getActiveSheet(0)->getStyle('AQ8:AW8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));

	$objPHPExcel->getActiveSheet(0)->getStyle('AX8:BH8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => 'ffc000'))));

	$objPHPExcel->getActiveSheet(0)->getStyle('BI8:BN8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));

        $objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('B'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('C'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('D'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('E'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('F'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('G'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('H'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('I'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('J'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('K'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('L'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('M'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('N'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('O'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('P'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Q'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('R'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('S'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('T'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('U'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('V'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('W'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('X'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Y'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Z'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AA'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AB'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AC'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AD'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AE'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AF'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AG'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AH'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AI'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AJ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AK'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AL'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AM'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AN'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AO'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AP'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AQ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AR'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AS'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AT'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AU'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AV'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AW'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AX'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AY'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AZ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BA'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BB'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BC'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BD'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BE'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BF'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BG'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BH'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BI'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BJ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BK'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BL'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BM'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BN'.$row)->applyFromArray($border_style);

        $row=9;

		foreach($ar_ageing_data as $ar_ageing_datas){ 

			 $collectable_ar = '';
			 $doubtful_ar = '';
			 $disputed_ar ='';
			 $unadjusted_credit='';
			 $Legal='';

		$entity_zones = $this->MasterDataDetail->find('all', array('fields'=>
		 	array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ar_ageing_datas['EntityAddress']['zone'])));

		$entity_document_type = $this->MasterDataDetail->find('all', array('fields'=>
		 	array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ar_ageing_datas['Document']['document_type'])));
		 
		$entity_vertical = $this->BusinessLine->find('all', array('conditions'=>array('BusinessLine.entity_id'=>$ar_ageing_datas['OtherTransaction']['entity_id']),'joins' => array(array('table' => 'subverticals','alias' => 'Subvertical','type' => 'LEFT','conditions' => array('Subvertical.business_line_id = BusinessLine.id')),array('table' => 'profit_centers','alias' => 'ProfitCenter','type' => 'LEFT','conditions' => array('ProfitCenter.subvertical_id = Subvertical.id'))),'fields'=>array('BusinessLine.bl_name','BusinessLine.id','Subvertical.id','Subvertical.sv_name','ProfitCenter.pc_name'))); 

		$entity_ar = $this->ArCategorie->find('all', array('fields'=>
		 	array('ArCategorie.ar_cat'),'conditions'=>array('ArCategorie.id'=>$ar_ageing_datas['OtherTransaction']['ar_cat_id'])));
		
		if($entity_ar[0]['ArCategorie']['ar_cat'] == 'Collectable AR'){

			 $collectable_ar= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];

		}elseif($entity_ar[0]['ArCategorie']['ar_cat'] == 'Doubtful AR'){

			$doubtful_ar= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'] ;

		}elseif ($entity_ar[0]['ArCategorie']['ar_cat'] == 'Disputed'){

			$disputed_ar= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'] ;

		}elseif($entity_ar[0]['ArCategorie']['ar_cat'] == 'Unadjusted Credit'){

			$unadjusted_credit= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'] ;
		

		}elseif($entity_ar[0]['ArCategorie']['ar_cat'] == 'Legal'){

			$Legal= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'] ;			
		};
	
		$invoice_date  = date('Y-m-d', strtotime($ar_ageing_datas['OtherTransaction']['invoice_date']));
        $due_dates     = date('Y-m-d', strtotime($ar_ageing_datas['OtherTransaction']['invoice_due_dt']));
        $current_dates = date('Y-m-d');
        $aging_days     = strtotime($current_dates) - strtotime($due_dates);
        $days         = floor($aging_days / (60*60*24));

        $aging_due_date='';
        $aging_30 ='';
		$aging_60 ='';
		$aging_90 ='';
		$aging_120 ='';
		$aging_180 ='';
		$aging_360 ='';
		$aging_720 ='';
		$aging_720_more ='';

        if($days <= 0){

			 $aging_due_date = $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];

		 }elseif($days <= 30 && $days >= 1){

		 	$aging_30 = $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];

	     }elseif($days <= 60 && $days >= 31){

		 	$aging_60 = $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];

		  }elseif ($days <= 90 && $days >= 61){

		 	$aging_90= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];

		 }elseif($days <= 120 && $days >= 91){

			$aging_120= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];
		
		 }elseif($days <= 180  && $days >= 121){

		   $aging_180= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];
			
		 }elseif($days <= 360  && $days >= 181){

		 	$aging_360= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];
			
		 }elseif($days <= 720  && $days >= 361){

		 	$aging_720= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'];
			
		 }elseif($days > 720){

		$aging_720_more= $ar_ageing_datas['OtherTransaction']['netamountoutstanding'] ;
			
		 };
		//pre($days); die;
			
		 	$objPHPExcel->getActiveSheet(1)
			->setCellValue('A'.$row, "".$entity_zones[0]['MasterDataDetail']['master_data_desc']."")
			->setCellValue('B'.$row, "".$ar_ageing_datas['EntityAddress']['state']."")
			->setCellValue('C'.$row, "".''."")
			->setCellValue('D'.$row, "".$ar_ageing_datas['Entitie']['entitiy_name']."")
			->setCellValue('E'.$row, "".''."")
			->setCellValue('F'.$row, "".$ar_ageing_datas['Entitie']['status']."")
            ->setCellValue('G'.$row, "".''."")
		    ->setCellValue('H'.$row, "".$entity_vertical[0]['BusinessLine']['bl_name']."")
			->setCellValue('I'.$row, "".$entity_vertical[0]['Subvertical']['sv_name']."")
			->setCellValue('J'.$row, "".''."")
			->setCellValue('K'.$row, "".''."")
			->setCellValue('L'.$row, "".$entity_vertical[0]['ProfitCenter']['pc_name']."")
			->setCellValue('M'.$row, "".''."")
			->setCellValue('N'.$row, "".''."")
			->setCellValue('O'.$row, "".''."")
			->setCellValue('P'.$row, "".$ar_ageing_datas['Entitie']['credit_period']."")
			->setCellValue('Q'.$row, "".''."")
			->setCellValue('R'.$row, "".$ar_ageing_datas['OtherTransaction']['fiscal_year']."")
			->setCellValue('S'.$row, "".''."")
			->setCellValue('T'.$row, "".''."")
			->setCellValue('U'.$row, "".''."")
			->setCellValue('W'.$row, "".''."")
			->setCellValue('V'.$row, "".''."")
			->setCellValue('X'.$row, "".''."")
			->setCellValue('Y'.$row, "".$ar_ageing_datas['OtherTransaction']['invoice_number']."")
			->setCellValue('Z'.$row, "".$ar_ageing_datas['OtherTransaction']['invoice_date']."")
			->setCellValue('AA'.$row, "".$ar_ageing_datas['OtherTransaction']['original_amount']."")
			->setCellValue('AB'.$row, "".$ar_ageing_datas['OtherTransaction']['debit_amount']."")
			->setCellValue('AC'.$row, "".$ar_ageing_datas['OtherTransaction']['credit_amount']."")
			->setCellValue('AD'.$row, "".''."")
			->setCellValue('AE'.$row, "".$ar_ageing_datas['OtherTransaction']['netamountoutstanding']."")
			->setCellValue('AF'.$row, "".''."")
			->setCellValue('AG'.$row, "".''."")
			->setCellValue('AH'.$row, "".''."")
			->setCellValue('AI'.$row, "".''."")
			->setCellValue('AJ'.$row, "".''."")
			->setCellValue('AK'.$row, "".''."")
			->setCellValue('AL'.$row, "".''."")
			->setCellValue('AM'.$row, "".''."")
			->setCellValue('AN'.$row, "".$ar_ageing_datas['OtherTransaction']['gl_code']."")
			->setCellValue('AO'.$row, "".$entity_document_type[0]['MasterDataDetail']['master_data_desc']."")
			->setCellValue('AP'.$row, "".$ar_ageing_datas['DocumentMaster']['desc']."")
			->setCellValue('AQ'.$row, "".''."")
			->setCellValue('AR'.$row, "".''."")
			->setCellValue('AS'.$row, "".$collectable_ar."")
			->setCellValue('AT'.$row, "".$doubtful_ar."")
			->setCellValue('AU'.$row, "".$Legal."")
			->setCellValue('AV'.$row, "".$disputed_ar."")
			->setCellValue('AW'.$row, "".$unadjusted_credit."")
			->setCellValue('AX'.$row, "".$aging_due_date."")
			->setCellValue('AY'.$row, "".$aging_30."")
			->setCellValue('AZ'.$row, "".$aging_60."")
			->setCellValue('BA'.$row, "".$aging_90."")
			->setCellValue('BB'.$row, "".$aging_120."")
			->setCellValue('BC'.$row, "".$aging_180."")
			->setCellValue('BD'.$row, "".$aging_360."")
			->setCellValue('BE'.$row, "".$aging_720."")
			->setCellValue('BF'.$row, "".$aging_720_more."")
			->setCellValue('BG'.$row, "".''."")
			->setCellValue('BH'.$row, "".''."")
			->setCellValue('BI'.$row, "".''."")
			->setCellValue('BJ'.$row, "".''."")
			->setCellValue('BK'.$row, "".''."")
			->setCellValue('BL'.$row, "".''."")
			->setCellValue('BM'.$row, "".''."")
			->setCellValue('BN'.$row, "".''."");
			
			$row++;
	    }

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		$objWriter->save($filePath.$fileName);	
		echo 'success'; die;
	}
function ar_ageing_customer_export_excel()
{
        $this->loadModel('Entitie');
		$this->loadModel('MasterDataDetail');
	    $this->loadModel('Subvertical');
		$this->loadModel('ArCategorie');

		//$ar_ageing_customer = $this->Entitie->find('all', array('limit'=>1000)); 

		$ar_ageing_customer = $this->Entitie->find('all',array('joins' => array(array('table' => 'entity_addresses','alias' => 'EntityAddress','type' => 'INNER','conditions' =>array('EntityAddress.entity_id = Entitie.id'))),
		'fields'=>array('EntityAddress.*','Entitie.*','BusinessLine.*'),'conditions'=>array('EntityAddress.address_type'=>'Registered')));

        //pre($ar_ageing_customer);die();		
        $this->layout = false;
		CakePlugin::load('PHPExcel');
		App::uses('PHPExcel', 'PHPExcel.Classes');
		
		
		$fileName = "ar_ageing_customer_export_excel.xlsx";
		$filePath = '../webroot/files/'; 
		unlink($filePath.$fileName);
		$objPHPExcel = new PHPExcel();
		//$this->response->download($fileName);
		
		$border_style= array('borders' => array('right' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000')),'top' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000'))));
   //      $row=2;
   //      $objPHPExcel->getActiveSheet(0)
			// ->setCellValue('A'.$row,'Check  Box')
			// ->setCellValue('B'.$row, 'Due Date')
			// ->setCellValue('T'.$row, '');

		$objPHPExcel->getActiveSheet(0)->getStyle('T2')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ff0000'))));
		$objPHPExcel->getActiveSheet(0)->mergeCells('U2:W2');	
		$objPHPExcel->getActiveSheet(0)->getCell('U2')->setValue('Above 10% of Credit limit');
		$objPHPExcel->getActiveSheet(0)->getStyle('U2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet(0)->getStyle("U2")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet(0)->getStyle("U2:W2")->getFont()->setSize(10);	

		$row=3;	
		// $objPHPExcel->getActiveSheet(0)
		//    ->setCellValue('B'.$row,'Invoice Date');
		$objPHPExcel->getActiveSheet(0)->getStyle('T3')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));
		$objPHPExcel->getActiveSheet(0)->mergeCells('U3:W3');	
		$objPHPExcel->getActiveSheet(0)->getCell('U3')->setValue('Upto 10% above credit limit');
		$objPHPExcel->getActiveSheet(0)->getStyle('U3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet(0)->getStyle("U3")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet(0)->getStyle("U3:W3")->getFont()->setSize(10);	

		$row=4;					
		// $objPHPExcel->getActiveSheet(0)->mergeCells('A4:E4');	
		// $objPHPExcel->getActiveSheet(0)->getCell('A4')->setValue('Collector/Vertical/sub-vertical/customer etc - Filter option  required');
		// $objPHPExcel->getActiveSheet(0)->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		// $objPHPExcel->getActiveSheet(0)->getStyle("A4")->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet(0)->getStyle("A4:E4")->getFont()->setSize(10);	

		$objPHPExcel->getActiveSheet(0)->getStyle('T4')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '92d050'))));
		$objPHPExcel->getActiveSheet(0)->mergeCells('U4:W4');	
		$objPHPExcel->getActiveSheet(0)->getCell('U4')->setValue('Within limit');
		$objPHPExcel->getActiveSheet(0)->getStyle('U4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet(0)->getStyle("U4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet(0)->getStyle("U4:W4")->getFont()->setSize(10);	

		$row=7;					
		$objPHPExcel->getActiveSheet(0)->mergeCells('M7:S7');	
		$objPHPExcel->getActiveSheet(0)->getCell('M7')->setValue('AR Categorization ');
		$objPHPExcel->getActiveSheet(0)->getStyle('S7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet(0)->getStyle("M7")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet(0)->getStyle("M7:S7")->getFont()->setSize(10);	
		//$objPHPExcel->getActiveSheet(0)->getStyle('M7:S7')->applyFromArray( array('font'=>array('color' => array('rgb' =>'FFFFFF'))));
		$objPHPExcel->getActiveSheet(0)->getStyle('M7:S7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'e6b9b8'))));

        $objPHPExcel->getActiveSheet(0)->mergeCells('U7:AC7');	
		$objPHPExcel->getActiveSheet(0)->getCell('U7')->setValue('Customizable Aging Bucket (option for both invoice date or due date) ');
		$objPHPExcel->getActiveSheet(0)->getStyle('AC7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$objPHPExcel->getActiveSheet(0)->getStyle("U7")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet(0)->getStyle("U7:AC7")->getFont()->setSize(10);	
		// $objPHPExcel->getActiveSheet(0)->getStyle('U7:AC7')->applyFromArray( array('font'=>array('color' => array('rgb' =>
		// 	'FFFFFF'))));
		$objPHPExcel->getActiveSheet(0)->getStyle('U7:AC7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => 'd7e4bd'))));
        
        $row=8;
		$objPHPExcel->getActiveSheet(0)
				->setCellValue('A'.$row,'Zone')
				->setCellValue('B'.$row, 'State')
				->setCellValue('C'.$row, 'Parent/Group')
				->setCellValue('D'.$row, 'Customer Code')
				->setCellValue('E'.$row, 'Customer Name')
				->setCellValue('F'.$row, 'Customer Category')
				->setCellValue('G'.$row, 'Vertical')
				->setCellValue('H'.$row, 'Sub Vertical')
				->setCellValue('I'.$row, 'LOB')
				->setCellValue('J'.$row, 'Credit Limit ')
				->setCellValue('K'.$row, 'Credit Terms')
				->setCellValue('L'.$row, 'Net AR')
				->setCellValue('M'.$row, 'Not Due AR')
				->setCellValue('N'.$row, 'Over Due AR')
				->setCellValue('O'.$row, 'Collectable AR')
				->setCellValue('P'.$row, 'Doubtful AR')
				->setCellValue('Q'.$row, 'Legal')
				->setCellValue('R'.$row, 'Disputed')
				->setCellValue('S'.$row, 'Unadjusted Credit')
				->setCellValue('T'.$row, 'AR > CL')
				->setCellValue('U'.$row, '0-30 Days')
				->setCellValue('V'.$row, '31-60 Days')
				->setCellValue('W'.$row, '61-90 Days')
				->setCellValue('X'.$row, '91-120 Days')
				->setCellValue('Y'.$row, '121-150 days')
				->setCellValue('Z'.$row, '151-180 Days')
				->setCellValue('AA'.$row, '181-365 Days')
				->setCellValue('AB'.$row, '366-730 Days')
				->setCellValue('AC'.$row, '>730 Days')
				->setCellValue('AD'.$row, 'Sales Manager')
				->setCellValue('AE'.$row, 'Collection Manager')
				->setCellValue('AF'.$row, 'Collector');
	$objPHPExcel->getActiveSheet(0)->getStyle('A8:F8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));
	$objPHPExcel->getActiveSheet(0)->getStyle('G8:I8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => 'ffff00'))));
	$objPHPExcel->getActiveSheet(0)->getStyle('J8:S8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));
	$objPHPExcel->getActiveSheet(0)->getStyle('T8:AC8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => 'ffff00'))));
	$objPHPExcel->getActiveSheet(0)->getStyle('AD8:AF8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));
	
	    $objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('B'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('C'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('D'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('E'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('F'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('G'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('H'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('I'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('J'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('K'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('L'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('M'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('N'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('O'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('P'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Q'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('R'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('S'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('T'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('U'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('V'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('W'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('X'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Y'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Z'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AA'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AB'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AC'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AD'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AE'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AF'.$row)->applyFromArray($border_style);

		$row=9;
		
		foreach($ar_ageing_customer as $key =>$ar_ageing_customers){ 

			$net_AR  ='' ;
			$ar_more_cl='';
			 $more_ar = '';

			 $collectable_ar_customer = '';
			 $doubtful_ar_customer = '';
			 $disputed_ar_customer ='';
			 $unadjusted_credit_customer ='';
			 $legal_customer='';

			 $aging_due_date='';
	         $aging_30 ='';
			 $aging_60 ='';
			 $aging_90 ='';
			 $aging_120 ='';
			 $aging_150 ='';
			 $aging_180 ='';
			 $aging_365 ='';
			 $aging_720 ='';
			 $aging_720_more ='';
			  
			$curtomer_zones = $this->MasterDataDetail->find('all', array('fields'=>
		 	array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ar_ageing_customers['EntityAddress']['zone'])));

		 	$entity_vertical = $this->Subvertical->find('all', array('conditions'=>array('Subvertical.business_line_id'=>$ar_ageing_customers['BusinessLine']['id']),'fields'=>array('Subvertical.id','Subvertical.sv_name'))); 


			foreach($ar_ageing_customers['OtherTransaction'] as $key =>$net_ar){

				$customer_ar = $this->ArCategorie->find('all', array('fields'=>
			 	array('ArCategorie.ar_cat'),'conditions'=>array('ArCategorie.id'=>$net_ar['ar_cat_id'])));

                $net_AR  +=  $net_ar['netamountoutstanding'];               

                if($customer_ar[0]['ArCategorie']['ar_cat'] == 'Collectable AR'){

			        $collectable_ar_customer += $net_ar['netamountoutstanding'];

		        }elseif($customer_ar[0]['ArCategorie']['ar_cat'] == 'Doubtful AR'){

			        $doubtful_ar_customer += $net_ar['netamountoutstanding'];

		        }elseif ($customer_ar[0]['ArCategorie']['ar_cat'] == 'Disputed'){

			        $disputed_ar_customer += $net_ar['netamountoutstanding'];

		        }elseif($customer_ar[0]['ArCategorie']['ar_cat'] == 'Unadjusted Credit'){

			        $unadjusted_credit_customer += $net_ar['netamountoutstanding'];		

		        }elseif($customer_ar[0]['ArCategorie']['ar_cat'] == 'Legal'){

			        $legal_customer += $net_ar['netamountoutstanding'];
			
		        };			

			$invoice_date  = date('Y-m-d', strtotime($net_ar['invoice_date']));
	        $due_dates     = date('Y-m-d', strtotime($net_ar['invoice_due_dt']));
	        $current_dates = date('Y-m-d');
	        $aging_days     = strtotime($current_dates) - strtotime($due_dates);
	        $days         = floor($aging_days / (60*60*24));	       

	        if($days <= 0){

				 $aging_due_date += $net_ar['netamountoutstanding'];

			 }elseif($days <= 30 && $days >= 1){

			 	$aging_30  += $net_ar['netamountoutstanding'];

		     }elseif($days <= 60 && $days >= 31){

			 	$aging_60  += $net_ar['netamountoutstanding'];

			  }elseif ($days <= 90 && $days >= 61){

			 	$aging_90  += $net_ar['netamountoutstanding'];

			 }elseif($days <= 120 && $days >= 91){

				$aging_120 += $net_ar['netamountoutstanding'];
			
			 }elseif($days <= 150  && $days >= 121){

			   $aging_150  += $net_ar['netamountoutstanding'];
				
			 }elseif($days <= 180  && $days >= 151){

			   $aging_180  += $net_ar['netamountoutstanding'];
				
			 }elseif($days <= 365  && $days >= 181){

			 	$aging_365  += $net_ar['netamountoutstanding'];
				
			 }elseif($days <= 720  && $days >= 366){

			 	$aging_720 += $net_ar['netamountoutstanding'];
				
			 }elseif($days > 720){

			$aging_720_more  += $net_ar['netamountoutstanding'] ;
				
			 };
		};

		 	  setlocale(LC_MONETARY, 'en_IN');
		 	  $net_ammount= money_format('%!i', $net_AR);

		 	  $credit_limit = $ar_ageing_customers['Entitie']['credit_limit'];
		 	  $AR_CL        = $net_ammount - $credit_limit;		 	  

		 	  	 $ar_more = $AR_CL*100/$credit_limit;
		 	  	 $ar_more_cl= money_format('%!i', $ar_more); 

		 	  	 if($ar_more_cl > 10 ){

                    $more_ar = $ar_more_cl;
		 	  	 	$objPHPExcel->getActiveSheet(0)->getStyle('T'. $row )->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ff0000'))));

		 	  	 }elseif($ar_more_cl <= 10 && $ar_more_cl >=1){

		 	  	 	$more_ar = $ar_more_cl;
		 	  	 	$objPHPExcel->getActiveSheet(0)->getStyle('T'. $row )->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ffc000'))));
		 	  	 }elseif($ar_more_cl < 0) {

		 	  	 	$objPHPExcel->getActiveSheet(0)->getStyle('T'. $row )->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '92d050'))));
		 	  	 	
		 	  	 }
     				
		 	$objPHPExcel->getActiveSheet(1)
			->setCellValue('A'.$row, "".$curtomer_zones[0]['MasterDataDetail']['master_data_desc']."")
			->setCellValue('B'.$row, "".$ar_ageing_customers['EntityAddress']['state']."")
			->setCellValue('C'.$row, "".''."")
			->setCellValue('D'.$row, "".''."")
			->setCellValue('E'.$row, "".$ar_ageing_customers['Entitie']['entitiy_name']."")
			->setCellValue('F'.$row, "".''."")
            ->setCellValue('G'.$row, "".$ar_ageing_customers['BusinessLine']['bl_name']."")
		    ->setCellValue('H'.$row, "".$entity_vertical[0]['Subvertical']['sv_name']."")
			->setCellValue('I'.$row, "".''."")
			->setCellValue('J'.$row, "".$ar_ageing_customers['Entitie']['credit_limit']."")
			->setCellValue('K'.$row, "".$ar_ageing_customers['Entitie']['credit_period']."")
			->setCellValue('L'.$row, "".$net_ammount."")
			->setCellValue('M'.$row, "".''."")
			->setCellValue('N'.$row, "".''."")
			->setCellValue('O'.$row, "".$collectable_ar_customer."")
			->setCellValue('P'.$row, "".$doubtful_ar_customer."")
			->setCellValue('Q'.$row, "".$legal_customer."")
			->setCellValue('R'.$row, "".$disputed_ar_customer."")
			->setCellValue('S'.$row, "".$unadjusted_credit_customer."")
			->setCellValue('T'.$row, "".$more_ar."")
			->setCellValue('U'.$row, "".$aging_30."")
			->setCellValue('W'.$row, "".$aging_60."")
			->setCellValue('V'.$row, "".$aging_90."")
			->setCellValue('X'.$row, "".$aging_120."")
			->setCellValue('Y'.$row, "".$aging_150."")
			->setCellValue('Z'.$row, "".$aging_180."")
			->setCellValue('AA'.$row, "".$aging_365."")
			->setCellValue('AB'.$row, "".$aging_720."")
			->setCellValue('AC'.$row, "".$aging_720_more."")
			->setCellValue('AD'.$row, "".''."")
			->setCellValue('AE'.$row, "".''."")
			->setCellValue('AF'.$row, "".''."");
			
			$row++;
	    }		
				
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		$objWriter->save($filePath.$fileName);	
		echo 'success'; die;
	}
	function collections_customer_export_excel()
	{
		$this->loadModel('Entitie');
		$this->loadModel('MasterDataDetail');
	    $this->loadModel('InvoiceDunning');
		$this->loadModel('ArCategorie');

		// $ar_ageing_customer = $this->Entitie->find('all', array('limit'=>100,'contain'=>array('Entitie'=>array('fields'=>array()))));
	
		$collections_customer = $this->Entitie->find('all',array('joins' => array(array('table' => 'entity_addresses','alias' => 'EntityAddress','type' => 'INNER','conditions' =>array('EntityAddress.entity_id = Entitie.id'))),'Limit'=>100,
		 'fields'=>array('EntityAddress.zone','EntityAddress.state','BusinessLine.id','BusinessLine.bl_name','Entitie.id','Entitie.entitiy_name','Entitie.credit_limit','Entitie.credit_period','Entitie.credit_period'),'conditions'=>array('EntityAddress.address_type'=>'Registered')));

		//pre($ar_ageing_customer); die;
		$this->layout = false;
		CakePlugin::load('PHPExcel');
		App::uses('PHPExcel', 'PHPExcel.Classes');
		
		$fileName = "collections_customer_export_excel.xlsx";
		$filePath = '../webroot/files/'; 
		unlink($filePath.$fileName);
		$objPHPExcel = new PHPExcel();
		//$this->response->download($fileName);
		$border_style= array('borders' => array('right' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000')),'top' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000'))));

		// $row=2;					
		// $objPHPExcel->getActiveSheet(0)->mergeCells('A2:G2');	
		// $objPHPExcel->getActiveSheet(0)->getCell('A2')->setValue('Collector/Vertical/sub-vertical/customer etc - Filter option  required');
		// $objPHPExcel->getActiveSheet(0)->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		// $objPHPExcel->getActiveSheet(0)->getStyle("A2")->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet(0)->getStyle("A2:G2")->getFont()->setSize(10);	

		$row=3;					
		$objPHPExcel->getActiveSheet(0)->mergeCells('A4:I4');	
		$objPHPExcel->getActiveSheet(0)->getCell('A4')->setValue('This report can be extracted every  Day,  data will be adding up day by day  to ge the actual status.');
		$objPHPExcel->getActiveSheet(0)->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet(0)->getStyle("A4")->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet(0)->getStyle("A4:I4")->getFont()->setSize(10);	
		
		$row=7;	
		$objPHPExcel->getActiveSheet(0)
		   ->setCellValue('G'.$row,'Debit Note Amount');
		   
		$objPHPExcel->getActiveSheet(0)->mergeCells('H7:I7');	
		$objPHPExcel->getActiveSheet(0)->getCell('H7')->setValue('Credit Note Amount');
		$objPHPExcel->getActiveSheet(0)->getStyle('I7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		

        $objPHPExcel->getActiveSheet(0)->mergeCells('J7:P7');	
		$objPHPExcel->getActiveSheet(0)->getCell('J7')->setValue('AR Categorization  Values ');
		$objPHPExcel->getActiveSheet(0)->getStyle('P7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('J7:P7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => 'e6b9b8'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('Q7:V7');	
		$objPHPExcel->getActiveSheet(0)->getCell('Q7')->setValue('Collections week Wise- Target');
		$objPHPExcel->getActiveSheet(0)->getStyle('V7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('Q7:V7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('W7:AB7');	
		$objPHPExcel->getActiveSheet(0)->getCell('W7')->setValue('Collections week Wise- Actual');
		$objPHPExcel->getActiveSheet(0)->getStyle('AB7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('W7:AB7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('AC7:AH7');	
		$objPHPExcel->getActiveSheet(0)->getCell('AC7')->setValue('Average Days to Pay');
		$objPHPExcel->getActiveSheet(0)->getStyle('AH7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('AC7:AH7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->setCellValue('AI7'.$row, 'Not Average of Averages');

		$objPHPExcel->getActiveSheet(0)->getStyle('AI7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('AJ7:AO7');	
		$objPHPExcel->getActiveSheet(0)->getCell('AJ7')->setValue(' Collection Forecast- Week Wise ( Avg. Days to Pay)');
		$objPHPExcel->getActiveSheet(0)->getStyle('AO7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('AJ7:AO7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('AP7:AU7');	
		$objPHPExcel->getActiveSheet(0)->getCell('AP7')->setValue('Actual Collections trend');
		$objPHPExcel->getActiveSheet(0)->getStyle('AP7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('AP7:AU7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('AV7:BA7');	
		$objPHPExcel->getActiveSheet(0)->getCell('AV7')->setValue(' Weekly Forecast- Invoice staging ');
		$objPHPExcel->getActiveSheet(0)->getStyle('BA7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('AV7:BA7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->mergeCells('BB7:BG7');	
		$objPHPExcel->getActiveSheet(0)->getCell('BB7')->setValue(' Weekly Forecast- Collector (PTP)');
		$objPHPExcel->getActiveSheet(0)->getStyle('BG7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet(0)->getStyle('BB7:BG7')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));
        
        $row=8;
		$objPHPExcel->getActiveSheet(0)
				->setCellValue('A'.$row,'Zone')
				->setCellValue('B'.$row, 'State')
				->setCellValue('C'.$row, 'Parent/Group')				
				->setCellValue('D'.$row, 'Customer Code')
				->setCellValue('E'.$row, 'Customer Name')
				->setCellValue('F'.$row, 'Customer Category')
				->setCellValue('G'.$row, 'Debit Amount')
				->setCellValue('H'.$row, 'Credit Amount')
				->setCellValue('I'.$row, 'Net AR')
				->setCellValue('J'.$row, 'Not Due AR')
				->setCellValue('K'.$row, 'Over Due AR')
				->setCellValue('L'.$row, 'Collectable AR')
				->setCellValue('M'.$row, 'Doubtful AR')
				->setCellValue('N'.$row, 'Legal')
				->setCellValue('O'.$row, 'Disputed')
				->setCellValue('P'.$row, 'Unadjusted Credit')
				->setCellValue('Q'.$row, 'Week 1')
				->setCellValue('R'.$row, 'Week 2')
				->setCellValue('S'.$row, 'Week 3')
				->setCellValue('T'.$row, 'Week 4')
				->setCellValue('U'.$row, 'Week 5')
				->setCellValue('V'.$row, 'Collection  Target')
				->setCellValue('W'.$row, 'Week 1')
				->setCellValue('X'.$row, 'Week 2')
				->setCellValue('Y'.$row, 'Week 3')
				->setCellValue('Z'.$row, 'Week 4')
				->setCellValue('AA'.$row, 'Week 5')
				->setCellValue('AB'.$row, 'MTD')
				->setCellValue('AC'.$row, 'Jan-18')
				->setCellValue('AD'.$row, 'Feb-18')
				->setCellValue('AE'.$row, 'Mar-18')
				->setCellValue('AF'.$row, 'Apr-18')
				->setCellValue('AG'.$row, 'May-18')
				->setCellValue('AH'.$row, 'Jun-18')
				->setCellValue('AI'.$row, 'Average 6 months')
				->setCellValue('AJ'.$row, 'Week 1')
				->setCellValue('AK'.$row, 'Week 2')
				->setCellValue('AL'.$row, 'Week 3')
				->setCellValue('AM'.$row, 'Week 4')
				->setCellValue('AN'.$row, 'Week 5')
			    ->setCellValue('AO'.$row, 'Total CF (DTP)')
				->setCellValue('AP'.$row, 'Jan-18')
				->setCellValue('AQ'.$row, 'Feb-18')
				->setCellValue('AR'.$row, 'Mar-18')
				->setCellValue('AS'.$row, 'Apr-18')
				->setCellValue('AT'.$row, 'May-18')
				->setCellValue('AU'.$row, 'Jun-18')
				->setCellValue('AV'.$row, 'Week 1')
				->setCellValue('AW'.$row, 'Week 2')
				->setCellValue('AX'.$row, 'Week 3')
				->setCellValue('AY'.$row, 'Week 4')
				->setCellValue('AZ'.$row, 'Week 5')
				->setCellValue('BA'.$row, 'Total CF (IS)')
				->setCellValue('BB'.$row, 'Week 1')
				->setCellValue('BC'.$row, 'Week 2')
				->setCellValue('BD'.$row, 'Week 3')
				->setCellValue('BE'.$row, 'Week 4')
				->setCellValue('BF'.$row, 'Week 5')
				->setCellValue('BG'.$row, 'Total CF (Coll)')
				->setCellValue('BH'.$row, 'Relationship manager')
				->setCellValue('BI'.$row, 'Sales Manager')
				->setCellValue('BJ'.$row, 'Collector Manager')
				->setCellValue('BK'.$row, 'Collector');
				
	$objPHPExcel->getActiveSheet(0)->getStyle('A8:F8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));

	$objPHPExcel->getActiveSheet(0)->getStyle('G8:H8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '92d050'))));

	$objPHPExcel->getActiveSheet(0)->getStyle('I8:BK8')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
		FILL_SOLID,'color' => array('rgb' => '558ed5'))));	

        $objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('B'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('C'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('D'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('E'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('F'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('G'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('H'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('I'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('J'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('K'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('L'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('M'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('N'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('O'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('P'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Q'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('R'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('S'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('T'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('U'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('V'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('W'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('X'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Y'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Z'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AA'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AB'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AC'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AD'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AE'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AF'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AG'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AH'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AI'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AJ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AK'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AL'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AM'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AN'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AO'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AP'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AQ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AR'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AS'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AT'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AU'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AV'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AW'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AX'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AY'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('AZ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BA'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BB'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BC'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BD'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BE'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BF'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BG'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BH'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BI'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BJ'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('BK'.$row)->applyFromArray($border_style);

		$row=9;		
		foreach($collections_customer as $key =>$collections_customers){ 
		    $net_AR = '';
		 	$debit_amount = '';
			$credit_amount = '';
		    $collectable_ar_customer = '';
			$doubtful_ar_customer = '';
			$disputed_ar_customer ='';
			$unadjusted_credit_customer ='';
			$legal_customer='';
			$week_1 ='';
			$week_2 ='';
			$week_3 ='';
			$week_4 ='';
			$week_5 ='';
			$monthly_target ='';

			$curtomer_zones = $this->MasterDataDetail->find('all', array('fields'=>
		 	array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$collections_customers['EntityAddress']['zone'])));

		 	foreach($collections_customers['Invoice'] as $key =>$invoice_data){
		 		
		 		$steps = $this->InvoiceDunning->find('count',array('recursive'=>'-1','conditions'=>array('InvoiceDunning.invoice_id'=>$invoice_data['id'],'InvoiceDunning.due_overdue_flg'=>0)));

		 		if($invoice_data['dunning_attempt_no'] == $steps ){

			 		$invoice_due_dt  = date('Y-m-d', strtotime($invoice_data['invoice_due_dt']));
			 		$current_date =  date('Y-m-d');
					$date = DateTime::createFromFormat("Y-m-d", $invoice_due_dt);
					$dates = DateTime::createFromFormat("Y-m-d", $current_date);
	                $year  = $date->format("Y");
	                $month =  $date->format("m");
	                $day =  $date->format("d");
	                $current_year  = $dates->format("Y");
	                $current_month =  $dates->format("m");
	                $current_day =  $dates->format("d");

                if($year ==  $current_year && $month == $current_month){

                	$monthly_target += $invoice_data['netamountoutstanding'];
                	if($day <= 7){
                		$week_1 += $invoice_data['netamountoutstanding'];

                	}elseif($day <= 14 || $day >= 7){
                		$week_2 += $invoice_data['netamountoutstanding'];

                	}elseif($day <= 21 || $day >= 15){
                		$week_3 += $invoice_data['netamountoutstanding'];

                	}elseif($day <= 28 || $day >= 22) {
                		$week_4 += $invoice_data['netamountoutstanding'];
                		
                	}elseif($day <= 31 || $day >= 29){
                		$week_5 += $invoice_data['netamountoutstanding'];

                	}

                }
                
		 	}
		 		//pre($steps); die;

		 		$debit_amount += $invoice_data['debit_amount'];
		 		$credit_amount += $invoice_data['credit_amount'];

		 		$customer_ar = $this->ArCategorie->find('all', array('fields'=>array('ArCategorie.ar_cat'),'conditions'=>array('ArCategorie.id'=>$invoice_data['ar_cat_id'])));

                $net_AR  +=  $invoice_data['netamountoutstanding'];               

                if($customer_ar[0]['ArCategorie']['ar_cat'] == 'Collectable AR'){

			        $collectable_ar_customer += $invoice_data['netamountoutstanding'];

		        }elseif($customer_ar[0]['ArCategorie']['ar_cat'] == 'Doubtful AR'){

			        $doubtful_ar_customer += $invoice_data['netamountoutstanding'];

		        }elseif ($customer_ar[0]['ArCategorie']['ar_cat'] == 'Disputed'){

			        $disputed_ar_customer += $invoice_data['netamountoutstanding'];

		        }elseif($customer_ar[0]['ArCategorie']['ar_cat'] == 'Unadjusted Credit'){

			        $unadjusted_credit_customer += $invoice_data['netamountoutstanding'];		

		        }elseif($customer_ar[0]['ArCategorie']['ar_cat'] == 'Legal'){

			        $legal_customer += $invoice_data['netamountoutstanding'];
			
		        };			              	        
		};
			setlocale(LC_MONETARY, 'en_IN');
		 	   $net_ammount= money_format('%!i', $net_AR);
     			//pre($collections_customers); die;		
		 	$objPHPExcel->getActiveSheet(1)
			    ->setCellValue('A'.$row,"".$curtomer_zones['MasterDataDetail']['master_data_desc']."")
				->setCellValue('B'.$row, "".$collections_customers['EntityAddress']['state']."")
				->setCellValue('C'.$row, "".''."")				
				->setCellValue('D'.$row, "".''."")
				->setCellValue('E'.$row, "".$collections_customers['EntityAddress']['entitiy_name']."")
				->setCellValue('F'.$row, "".''."")
				->setCellValue('G'.$row, "".$debit_amount."")
				->setCellValue('H'.$row, "".$credit_amount."")
				->setCellValue('I'.$row, "". $net_ammount."")
				->setCellValue('J'.$row, "".''."")
				->setCellValue('K'.$row, "".''."")
				->setCellValue('L'.$row, "".$collectable_ar_customer."")
				->setCellValue('M'.$row, "".$doubtful_ar_customer."")
				->setCellValue('N'.$row, "".$legal_customer."")
				->setCellValue('O'.$row, "".$disputed_ar_customer."")
				->setCellValue('P'.$row, "".$unadjusted_credit_customer."")
				->setCellValue('Q'.$row, "".$week_1."")
				->setCellValue('R'.$row, "".$week_2."")
				->setCellValue('S'.$row, "".$week_3."")
				->setCellValue('T'.$row, "".$week_4."")
				->setCellValue('U'.$row, "".$week_5."")
				->setCellValue('V'.$row, "".$monthly_target."")
				->setCellValue('W'.$row, "".''."")
				->setCellValue('X'.$row, "".''."")
				->setCellValue('Y'.$row, "".''."")
				->setCellValue('Z'.$row, "".''."")
				->setCellValue('AA'.$row, "".''."")
				->setCellValue('AB'.$row, "".''."")
				->setCellValue('AC'.$row, "".''."")
				->setCellValue('AD'.$row, "".''."")
				->setCellValue('AE'.$row, "".''."")
				->setCellValue('AF'.$row, "".''."")
				->setCellValue('AG'.$row, "".''."")
				->setCellValue('AH'.$row, "".''."")
				->setCellValue('AI'.$row, "".''."")
				->setCellValue('AJ'.$row, "".''."")
				->setCellValue('AK'.$row, "".''."")
				->setCellValue('AL'.$row, "".''."")
				->setCellValue('AM'.$row, "".''."")
				->setCellValue('AN'.$row, "".''."")
			    ->setCellValue('AO'.$row, "".''."")
				->setCellValue('AP'.$row, "".''."")
				->setCellValue('AQ'.$row, "".''."")
				->setCellValue('AR'.$row, "".''."")
				->setCellValue('AS'.$row, "".''."")
				->setCellValue('AT'.$row, "".''."")
				->setCellValue('AU'.$row, "".''."")
				->setCellValue('AV'.$row, "".''."")
				->setCellValue('AW'.$row, "".''."")
				->setCellValue('AX'.$row, "".''."")
				->setCellValue('AY'.$row, "".''."")
				->setCellValue('AZ'.$row, "".''."")
				->setCellValue('BA'.$row, "".''."")
				->setCellValue('BB'.$row, "".''."")
				->setCellValue('BC'.$row, "".''."")
				->setCellValue('BD'.$row, "".''."")
				->setCellValue('BE'.$row, "".''."")
				->setCellValue('BF'.$row, "".''."")
				->setCellValue('BG'.$row, "".''."")
				->setCellValue('BH'.$row, "".''."")
				->setCellValue('BI'.$row, "".''."")
				->setCellValue('BJ'.$row, "".''."")
				->setCellValue('BK'.$row, "".''."");
						
			$row++;
	    }		
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		$objWriter->save($filePath.$fileName);	
		echo 'success'; die;
	}
	function sales_register_customer_excel()
	{
		$this->loadModel('Entitie');
		$this->loadModel('MasterDataDetail');
	    $this->loadModel('Subvertical');
		$this->loadModel('ArCategorie');

		//$ar_ageing_customer = $this->Entitie->find('all', array('limit'=>1000)); 

		$sales_register_customer = $this->Entitie->find('all',array('joins' => array(array('table' => 'entity_addresses','alias' => 'EntityAddress','type' => 'INNER','conditions' =>array('EntityAddress.entity_id = Entitie.id'))),
		'fields'=>array('EntityAddress.*','Entitie.*','BusinessLine.*'),'conditions'=>array('EntityAddress.address_type'=>'Registered')));

		$this->layout = false;
		CakePlugin::load('PHPExcel');
		App::uses('PHPExcel', 'PHPExcel.Classes');
		
		$fileName = "sales_register_customer_excel.xlsx";
		$filePath = '../webroot/files/'; 
		unlink($filePath.$fileName);
		$objPHPExcel = new PHPExcel();
		//$this->response->download($fileName);
		$border_style= array('borders' => array('right' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000')),'top' => array('style' =>PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => '000000'))));
		// $row=2;					
		// $objPHPExcel->getActiveSheet(0)->mergeCells('A2:G2');	
		// $objPHPExcel->getActiveSheet(0)->getCell('A2')->setValue('Collector/Vertical/sub-vertical/customer etc - Filter option  required');
		// $objPHPExcel->getActiveSheet(0)->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		// $objPHPExcel->getActiveSheet(0)->getStyle("A2")->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet(0)->getStyle("A2:G2")->getFont()->setSize(10);	

		$row=3;					
		$objPHPExcel->getActiveSheet(0)->mergeCells('J3:O3');	
		$objPHPExcel->getActiveSheet(0)->getCell('J3')->setValue('Sales For The last 6 Months');
		$objPHPExcel->getActiveSheet(0)->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet(0)->getStyle('J3:O3')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => 'e6b9b8'))));			
				       
        $row=4;
		$objPHPExcel->getActiveSheet(0)
				->setCellValue('A'.$row,'Zone')
				->setCellValue('B'.$row, 'State')
				->setCellValue('C'.$row, 'Parent/Group')				
				->setCellValue('D'.$row, 'Customer Code')
				->setCellValue('E'.$row, 'Customer Name')
				->setCellValue('F'.$row, 'Customer Category')
				->setCellValue('G'.$row, 'Vertical')
				->setCellValue('H'.$row, 'Sub Vertical')
				->setCellValue('I'.$row, 'LOB')
				->setCellValue('J'.$row, 'Jan-18')
				->setCellValue('K'.$row, 'Feb-18')
				->setCellValue('L'.$row, 'Mar-18')
				->setCellValue('M'.$row, 'Apr-18')
				->setCellValue('N'.$row, 'May-18')
				->setCellValue('O'.$row, 'Jun-18')
				->setCellValue('P'.$row, 'MTD')
				->setCellValue('Q'.$row, 'Projected billing  for current month')
				->setCellValue('R'.$row, 'Relationship manager')
				->setCellValue('S'.$row, 'Sales Manager ')
				->setCellValue('T'.$row, 'Customer SPOC');				
				
		$objPHPExcel->getActiveSheet(0)->getStyle('A4:F4')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));

		$objPHPExcel->getActiveSheet(0)->getStyle('G4:I4')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => 'ffff00'))));

		$objPHPExcel->getActiveSheet(0)->getStyle('J4:T4')->applyFromArray( array('fill'=>array('type'=>PHPExcel_Style_Fill::
			FILL_SOLID,'color' => array('rgb' => '558ed5'))));	

        $objPHPExcel->getActiveSheet(0)->getStyle('A'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('B'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('C'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('D'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('E'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('F'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('G'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('H'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('I'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('J'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('K'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('L'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('M'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('N'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('O'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('P'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('Q'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('R'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('S'.$row)->applyFromArray($border_style);
		$objPHPExcel->getActiveSheet(0)->getStyle('T'.$row)->applyFromArray($border_style);

		$row=5;		
		foreach($sales_register_customer as $key =>$sales_register_customers){ 
			$monthly_sale_1 = '';
			$monthly_sale_2 = '';
			$monthly_sale_3 = '';
			$monthly_sale_4 = '';
			$monthly_sale_5 = '';
			$monthly_sale_6 = '';

			$curtomer_zones = $this->MasterDataDetail->find('all', array('fields'=>
		 	array('MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$sales_register_customers['EntityAddress']['zone'])));

		 	$entity_vertical = $this->Subvertical->find('all', array('conditions'=>array('Subvertical.business_line_id'=>$sales_register_customers['BusinessLine']['id']),'fields'=>array('Subvertical.id','Subvertical.sv_name')));

		 	foreach($sales_register_customers['OtherTransaction'] as $key =>$net_ar_monthly){
		 		
				$invoice_date  = date('Y-m-d', strtotime($net_ar_monthly['invoice_date']));
				$date = DateTime::createFromFormat("Y-m-d", $invoice_date);
                $year  = $date->format("Y");
                $month =  $date->format("m");

                if($year == 2018 && $month == 01){
                    $monthly_sale_1 += $net_ar_monthly['netamountoutstanding'];
                }elseif($year == 2018 && $month == 02){
                	$monthly_sale_2 += $net_ar_monthly['netamountoutstanding'];
                }elseif($year == 2018 && $month == 03 ){
                	$monthly_sale_3 += $net_ar_monthly['netamountoutstanding'];
                }elseif($year == 2018 && $month == 04){
                	$monthly_sale_4 += $net_ar_monthly['netamountoutstanding'];
                }elseif($year == 2018 && $month == 05){
                	$monthly_sale_5 += $net_ar_monthly['netamountoutstanding'];
                }elseif($year == 2018 && $month == 06){
                	$monthly_sale_6 += $net_ar_monthly['netamountoutstanding'];

                }
	        
		};
		
		 	  // setlocale(LC_MONETARY, 'en_IN');
		 	  // $net_ammount= money_format('%!i', $net_AR);
     				
		 	$objPHPExcel->getActiveSheet(1)
			->setCellValue('A'.$row, "".$curtomer_zones[0]['MasterDataDetail']['master_data_desc']."")
			->setCellValue('B'.$row, "".$sales_register_customers['EntityAddress']['state']."")
			->setCellValue('C'.$row, "".''."")
			->setCellValue('D'.$row, "".''."")
			->setCellValue('E'.$row, "".$sales_register_customers['Entitie']['entitiy_name']."")
			->setCellValue('F'.$row, "".''."")
            ->setCellValue('G'.$row, "".$sales_register_customers['BusinessLine']['bl_name']."")
		    ->setCellValue('H'.$row, "".$entity_vertical[0]['Subvertical']['sv_name']."")
			->setCellValue('I'.$row, "".''."")
			->setCellValue('J'.$row, "".$monthly_sale_1."")
			->setCellValue('K'.$row, "".$monthly_sale_2."")
			->setCellValue('L'.$row, "".$monthly_sale_3."")
			->setCellValue('M'.$row, "".$monthly_sale_4."")
			->setCellValue('N'.$row, "".$monthly_sale_5."")
			->setCellValue('O'.$row, "".$monthly_sale_6."")
			->setCellValue('P'.$row, "".''."")
			->setCellValue('Q'.$row, "".''."")
			->setCellValue('R'.$row, "".''."")
			->setCellValue('S'.$row, "".''."")
			->setCellValue('T'.$row, "".''."");
						
			$row++;
	    }		
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment;filename=\"$fileName\"");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		$objWriter->save($filePath.$fileName);	
		echo 'success'; die;
	}	
	//  function remove_minus(){

	// 	 $this->loadModel('Invoice');

	// 	 $netamountoutstanding = $this->Invoice->find('all',array('recursive'=>'-1','fields'=>array('Invoice.id','Invoice.netamountoutstanding')));


	// 	 foreach ($netamountoutstanding as $value) {
	// 	 	 $amt = $value['Invoice']['netamountoutstanding'];
	// 	 	 $id = $value['Invoice']['id'];
 //             $a = abs($amt); 

	// 	 	 $done_step_save['Invoice']['netamountoutstanding'] = $a;		     
	// 		 $this->Invoice->id = $id;
	// 		 $this->Invoice->save($done_step_save);


		
	// 	 }
	// 	  echo "success"; die;

	// 	 //pre($netamountoutstanding); die;
	// 	//echo "heeo"; die;

	// }
	public function fetchAllProject(){		
		$this->loadModel('Project');
		$this->loadModel('CompanyAddress');
		$this->loadModel('EntityAddress');	  
		$this->loadModel('MasterDataDetail');	
		$data = $this->request->data;
		
	    $dataval = array();
	    if($this->request->is('ajax')){
		   if(!empty($data)){

			    $projectData = $this->Project->find('all',array('group'=>'Project.id','fields'=>array('Project.id','Project.customer_entity_id','Project.contract_id',
				                    'Project.profit_center_id','Project.business_line','Project.subvertical','Project.project_type','Project.project_value','Project.award_date','Project.start_date','Project.project_title','Project.brief_description','Project.initial_end_date','Project.bill_from_address_id','Project.bill_to_address_id','Project.ship_to_address_id'),'conditions'=>array('Project.contract_id'=>$data['contractId'])));
				
				foreach($projectData as $prodata){
                   //From address
					 $from_addre = $this->CompanyAddress->find('first',array('fields'=>array('CompanyAddress.id',
					 	'CompanyAddress.company_id','CompanyAddress.address_type','CompanyAddress.address_line_1',
					 	'CompanyAddress.address_line_2','CompanyAddress.state','CompanyAddress.city',
					 	'CompanyAddress.country','CompanyAddress.postal_code','CompanyAddress.zone'),'conditions'=>array('CompanyAddress.id'=>$prodata['Project']['bill_from_address_id'])));
					 $from_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$from_addre['CompanyAddress']['address_type'])));
					  $from_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$from_addre['CompanyAddress']['zone'])));
					 //Bill address
					   $bill_addre = $this->EntityAddress->find('first',array('fields'=>array('EntityAddress.*'
					 	),'conditions'=>array('EntityAddress.id'=>$prodata['Project']['bill_to_address_id'])));
					  

					  $bill_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$bill_addre['EntityAddress']['address_type'])));
					  $bill_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$bill_addre['EntityAddress']['zone'])));

					 //Ship address
					  $ship_addre = $this->EntityAddress->find('first',array('fields'=>array('EntityAddress.*'
					 	),'conditions'=>array('EntityAddress.id'=>$prodata['Project']['ship_to_address_id'])));
					  
					  $ship_add_type = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ship_addre['EntityAddress']['address_type'])));
					  $ship_zone = $this->MasterDataDetail->find('first',array('fields'=>array('MasterDataDetail.id','MasterDataDetail.master_data_desc'),'conditions'=>array('MasterDataDetail.id'=>$ship_addre['EntityAddress']['zone'])));

					 $project_address.='<h2>'.$prodata['Project']['project_title'].'</h2><ul class="addressSlider">
										<li class="col-lg-4 col-md-3 col-sm-12 padding-l-0">
											<h4>Bill From:</h4>
											<h5>'.$from_add_type['MasterDataDetail']['master_data_type'].'</h5>
											<p>'.$from_addre['CompanyAddress']['address_line_1'].'</p>
											<p>'.$from_addre['CompanyAddress']['city'].','.$from_addre['CompanyAddress']['state'].'-'.$from_addre['CompanyAddress']['postal_code'].'</p>
											<p>Zone :'.$from_zone['MasterDataDetail']['master_data_desc'].'</p>
										</li>
										<li class="col-lg-4 col-md-3 col-sm-12">
											<h4>Bill To:</h4>
											<input type="hidden" name="bill_add_id" value="'.$bill_addre['EntityAddress']['id'].'">
											<h5>'.$bill_add_type['MasterDataDetail']['master_data_type'].'</h5>
											<p>'.$bill_addre['EntityAddress']['address_line_1'].'</p>
											<p>'.$bill_addre['EntityAddress']['city'].','.$bill_addre['EntityAddress']['state'].'-'.$bill_addre['EntityAddress']['postal_code'].'</p>
											<p>Zone :'.$bill_zone['MasterDataDetail']['master_data_desc'].'</p>
										</li>
										<li class="col-lg-4 col-md-3 col-sm-12">
											<h4>Ship To:</h4>
											<h5>'.$ship_add_type['MasterDataDetail']['master_data_type'].'</h5>
											<p>'.$ship_addre['EntityAddress']['address_line_1'].'</p>
											<p>'.$ship_addre['EntityAddress']['city'].','.$ship_addre['EntityAddress']['state'].'-'.$ship_addre['EntityAddress']['postal_code'].'</p>
											<p>Zone :'.$ship_zone['MasterDataDetail']['master_data_desc'].'</p>
										</li>
									</ul>';

					
					}
				if(!empty($projectData)){
					 $dataval['project_address'] = $project_address;					 
					 $dataval['status'] = 'success';
					}
				else{
					 $dataval['project_address'] = '';					 
					 $dataval['status'] = 'error';
					}
					echo json_encode($dataval);die;
			   }
		  }
	 }
}