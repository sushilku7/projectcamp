<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Projectapproval extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('client_project_model');
        $this->load->helper('url');
		$this->load->model('login_model');
        $this->load->helper('site_functions_helper');
        $this->load->library('form_validation');
      
    }
    
	public function sendrequestforapproval()
	{
			$this->load->helper('url');
			$this->load->model('client_project_model');
			$this->load->model('login_model');
			$docsId = $this->input->post('documentId1');
			$projectId = $this->input->post('projectId1');
			$emailId = $this->input->post('emailId');
			$emailAddress = base64_encode($this->input->post('emailId'));
			$filesNotes = $this->input->post('filesNotes1');
			$url = $projectId."_".$docsId."_".$emailAddress;
			$name = $this->input->post('clientName');
			$config['charset'] = 'utf-8';
			$config['mailtype'] = 'html';
			$this->email->initialize($config);
			$this->load->library('upload', $config);
			 $projectName = $this->input->post('projectName');

             //echo "helloooo";die();
			        $exitEmailId = $this->login_model->is_exist_emailId($this->input->post('emailId'));
                    //echo $exitEmailId;die();
                    if($exitEmailId=='0')
                    {
                        $singupArray=array(
                       'memberUsername'=> $this->input->post('clientName'),
                       'memberName'=> $this->input->post('clientName'),
					   'memberPost'=> $this->input->post('companyName'),
                       'memberEmail'=> $this->input->post('emailId'),
                       'memberPass'=> md5($this->input->post('emailId')),
                       'isActive'=>'1',
                       'createdOn'=> date('Y-m-d h:i:s')
                        );
                        $result = $this->login_model->clientSignupforApproval($singupArray);    
					}
			 
            //$data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            //$email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.i-guru.net/project-approval/$url";
				//echo $path;die();
                $logoPath = base_url()."asset/images/i-guru_projectcamp11.png";
                $message = "<img src='$logoPath' alt='$projectName'>
                            <h2>Here's the latest activity across everything</h2>
                            <p> $fromName has uploaded the new document for your approval project $projectName.</p>
                            <br>
                            <p>$filesNotes</p>
                            <a href='$path' style='width: auto; text-decoration: none;'>View Project,Click me.</a>
                            <br /><br />
                            If you have any questions, just reply to this email <br /> <br />
                            Thanks & Regards <br />
                            Project Team";
                 //echo $message;die();                         
                          
                send_email($subject, $message, $emailId, $emailFrom, $fromName);
	}
	public function approval_docs()
	{
	    //echo "hello";die();
			$this->load->helper('url');
			$this->load->model('client_project_model');
			$url = $this->uri->segment(2);
			$encriptUrl = $url;
			$urlData = explode("_",$url);
			$projectId=base64_decode($urlData[0]);
			$documentId=base64_decode($urlData[1]);
			$email= base64_decode($urlData[2]);
			$currentDate= date('Y-m-d H:i:s');
			if($this->input->post('filesNotes'))
			{
			    $courl= $this->input->post('encurl');
			    $urlData = explode("_",$courl);
    			$projectId=base64_decode($urlData[0]);
    			$documentId=base64_decode($urlData[1]);
    			$projectIdformail=($urlData[0]);
    			$documentIdformail=($urlData[1]);
    			$email= base64_decode($urlData[2]);
			    //echo "hiii";die();
			    $memberId = $this->session->userdata('memberId');  
		        $arrayDate= array(
				'projectId'=>$this->input->post('projectId'),
				'documentId'=>$documentId,
				'memberId'=>$memberId,
				'comment'=>$this->input->post('filesNotes'),
				'createdOn'=>$currentDate
			    );
            $commentId = $this->client_project_model->insert_comments($arrayDate);
            //$fileTitle = $this->input->post('fileTitle1');
            $filesNotes = $this->input->post('filesNotes');
            
                
             $memberId = $this->session->userdata('memberId');
             $this->load->helper('url');
             $projectName = $this->input->post('projectName');
             
             $this->load->model('client_project_model');
         
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
            $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            $email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.i-guru.net/docs-details/$projectIdformail/$documentIdformail";
                $logoPath = base_url()."asset/images/i-guru_projectcamp11.png";
                $message = "<img src='$logoPath' alt='$projectName'>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> $fromName has commented on document project $projectName.</p>
                                          
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project,Click me.</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                        Project Team";
                                          
                          
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
				
				$total = count($_FILES['fileDocument']['name']);
             //echo $total;die();
             for($i = 0; $i<$total; $i++)
             {
                 //echo $_FILES['fileDocument']['name'][$i];die();
            if(!empty($_FILES['fileDocument']['name'][$i])){
                    $_FILES['fileDocuments']['name'] = $_FILES['fileDocument']['name'][$i];
                    $_FILES['fileDocuments']['type'] = $_FILES['fileDocument']['type'][$i];
                    $_FILES['fileDocuments']['tmp_name'] = $_FILES['fileDocument']['tmp_name'][$i];
                    $_FILES['fileDocuments']['error'] = $_FILES['fileDocument']['error'][$i];
                    $_FILES['fileDocuments']['size'] = $_FILES['fileDocument']['size'][$i];
                    
                    $config['upload_path'] = './assets/uploads/client-project-document/'; 
                    $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP|txt|csv';
                    $config['max_size'] = '5000'; // max_size in kb
                    $config['file_name'] = $_FILES['fileDocument']['name'][$i];
                    
					$this->load->library('upload', $config);
					//echo "hii";die();
					$this->upload->initialize($config);
				   if($this->upload->do_upload('fileDocuments')){

						//echo "upload fiels";die();
					 //$data = $this->upload->data();
					 $uploadData = $this->upload->data();
					 $filename = $uploadData['file_name'];
					// Initialize array
					 $data['filenames'][] = $filename;
					 //echo print_r($data);die();
					 $projectDocumentArr = array(
						 'projectId'=>$projectId,
						 'commentId'=>$commentId,
						 'commentType'=>$documentId,
						 'memberId'=>$memberId,
						 'docsName'=>$filename
					 );
					$docsId = $this->client_project_model->insert_commentdocsFiles($projectDocumentArr);
					
					}
					}
				}
					
			}
			
			$data['memberDetails']=$this->login_model->getMemberId($email);
		//	print_r($data);die();
		    $data['encriptUrl']=$encriptUrl;
		    $data['documentId']=$documentId;
		    $memberId = $this->session->userdata('memberId');
			$data['clientProjectDocDetaisl'] = $this->client_project_model->get_client_Approval_document_details($projectId,$documentId);
			//print_r($data['clientProjectDocDetaisl']);die();
			$data['docsfiles'] = $this->client_project_model->get_docsFiles($projectId,$documentId);
			//print_r($data['docsfiles']);die();
			$data['commentDetails'] = $this->client_project_model->getCommentDetails($memberId,$projectId,$documentId);
			$data['commentdocsfiles'] = $this->client_project_model->get_commentdocsFiles($projectId,$commentId,$documentId);
           
		    $this->load->view('manage-project/docs_approval', $data);

	}
    public function dashboard() {
        $this->load->helper('url');
        $this->load->model('client_project_model');
        $memberId = $this->session->userdata('memberId');
        $memberEmail = $this->session->userdata('admin_email');
        $data['client_project_list'] = $this->client_project_model->get_client_project_list($memberEmail);
        $data['loginMemberId'] = $memberEmail;
       // print_r($data);die();
         $this->load->view('header');
        $this->load->view('manage-project/home', $data);
        $this->load->view('footer');
       }
     public function index() {
        $this->load->helper('url');
        $this->load->model('client_project_model');
        $memberId = $this->session->userdata('memberId');
        $memberEmail = $this->session->userdata('admin_email');
        $memberType= $this->session->userdata('member_type');
        $data['memberType']=$memberType;
        //$this->load->view('header');
        $memberResult = $this->client_project_model->update_invite_id($memberId,$memberEmail);
        $data['client_project_list'] = $this->client_project_model->get_client_project_list($memberId);
        $data['other_project_list'] = $this->client_project_model->get_other_project_list($memberId);
        //$data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
        $data['tasklist'] = $this->client_project_model->get_project_list_task_status($memberId);
        $data['tasklistReport'] = $this->client_project_model->get_project_list_work_report($memberId);

		$data['loginMemberId'] = $memberEmail;
        //print_r($data['client_project_list']);die();
        $this->load->view('manage-project/project_list', $data);
        //$this->load->view('footer');
    }
    public function show_archive_client_list() {
        $this->load->helper('url');
        $memberId = $this->session->userdata('memberId');
        $this->load->view('header');
        $data['client_project_list'] = $this->client_project_model->get_archive_client_project_list($memberId);
        $data['loginMemberId'] = $memberId;
        $this->load->view('manage-project/archive_project_list', $data);
        $this->load->view('footer');
    }
    public function add_client_project() {
        if($this->input->post('action')) 
        {
            
           $this->load->helper('url');
           $this->load->helper('site_functions_helper');
            
            $fileName1 = "";
            $fileName2 = "";
            $fileName3 = "";
            $fileName4 = "";
            $fileName5 = "";
            $fileName6 = "";
            $fileName7 = "";
            $fileName8 = "";    
             $memberId = $this->session->userdata('memberId');
             $emailFrom = $this->session->userdata('admin_email');
             $fromName = $this->session->userdata('admin_username');
             
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
           
            $this->form_validation->set_rules('companyName','company name','required');
            if($this->form_validation->run())
            {
            
            $projectDataArr = array(
                'companyName' => $this->input->post('companyName'),
                'projectNotes' => $this->input->post('projectNotes'),
				'projectType' => $this->input->post('projectCate'),
                'memberId' => $memberId,
                'projectStatus' => '1',
                'createdBy' => $memberId,
                'createdOn' => date("Y-m-d")
            );
            //echo "hiioo";die();
            $insert_projectId = $this->client_project_model->insert_project_details($projectDataArr);
           
            $YourDataArr = array(
                'projectId' => $insert_projectId,
                'memberTitle' => '',
                'memberName' => $fromName,
                'memberEmail' => $emailFrom,
				'status' => 1,
                'companyName' => $this->input->post('companyName')
            );
            //print_r($YourDataArr);die();
            
            $projectName = $this->input->post('companyName');
            $insert_message = $this->client_project_model->insert_invite_people($YourDataArr);
            //die();
            if($this->input->post('projectCate')=='1')
			{
				//echo "heiii";die();
			$task= 	$this->client_project_model->get_task_and_assign($insert_projectId,$memberId);
			}
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "Latest activity on Project Camp";
                $path = "http://projectcamp.i-guru.net/project-details/$insert_projectId";
                $message = "<h2>I-Guru Projectcamp</h2>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> New Project has been upload the new document on Uthara Print (Projectcamp)</p>
                                          <br />
                                          Project Name - $projectName
                                          <br /><br />
                                          
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                          Project Team <br /><br />";
                                          
                //$this->load->library('email');
                // echo $message;die();          
                send_email($subject, $message, $emailFrom, $emailFrom, $fromName);
            
            redirect(base_url() . 'project-list/');
            }
            else {
                  echo "error";die();
            }
            
        } 
        else 
        {
            //Add CK Editor                    
            $path = '../js/ckfinder';
            $width = '800px';
            $this->editor($path, $width);
            //End Code               
            $data['clientId'] = $this->uri->segment(3);
            //$data['memberId'] = $this->uri->segment(4);
            $invoiceDetails = $this->client_project_model->get_invoice_number_by_client_id($data['clientId']);
            $clientDetails = $this->client_project_model->get_client_details_by_clientId($data['clientId']);
            $data['invoiceNum'] = "UTP-" . $invoiceDetails['invoiceId'] . "-";
            $data['clientCompanyName'] = $clientDetails['clientCompanyName'];
            $data['memberName'] = $clientDetails['memberName'];
            $data['memberEmail'] = $clientDetails['memberEmail'];
            $data['memberId'] = $clientDetails['memberId'];
            $this->load->helper('url');
            $this->load->view('header');
            $this->load->view('manage-project/add_project', $data);
            $this->load->view('footer');
        }
    }
     public function update_project() {
		
         $projectName =  $this->input->post('companyName');
         $projectNote =  $this->input->post('projectNotes');
         $projectId =  $this->input->post('projectId');
             
		$this->client_project_model->update_project_details($projectId,$projectName,$projectNote);	
		
		 $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
        //print_r($data['projectDetails']);die();
        $data['clientProjectDocList'] = $this->client_project_model->get_client_project_document($projectId);
		$documentType="1";
        $data['shortMessageCenter'] = $this->client_project_model->get_short_docs_list($projectId,$documentId,$documentType);
		$documentType="2";
		$data['shortTodos'] = $this->client_project_model->get_short_Todos_list($projectId,$documentId,$documentType);
        $documentType="3";
		//$data['shortDocs'] = $this->client_project_model->get_short_docs_list($projectId,$documentId,$documentType);

        //print_r($data);die();
        $data['shortdocsfiles'] = $this->client_project_model->get_short_docsfiles_list($projectId,$documentId,$documentType);

       //echo count($data['shortdocsfiles']);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/view_project', $data);
	 }
    public function message_dashboard()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        //echo $projectId;die();
        
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
        $data['shortMessageDocs'] = $this->client_project_model->get_short_docs_list($projectId);
       
    }
    
    public function message_list()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        //echo $projectId;die();
        if($this->input->post('action'))
        {
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
		$documentType = "1";
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocList'] = $this->client_project_model->get_document_meessage_list($projectId,$documentType);
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/message_list',$data)   ;
        //$this->load->view('footer');
        }
        else
        {
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
	    $documentType = "1";
        $data['clientProjectDocList'] = $this->client_project_model->get_document_meessage_list($projectId,$documentType);
        
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/message_list',$data)   ;
        }
    }
    
    public function message_details()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        $documentId = $this->uri->segment(4);
        $currentDate=date('Y-m-d H:i:s');
        if($this->input->post('filesNotes'))
        {
        $arrayDate= array(
            'projectId'=>$projectId,
            'documentId'=>$documentId,
            'memberId'=>$memberId,
            'comment'=>$this->input->post('filesNotes'),
            'createdOn'=>$currentDate
				);
        $commentId = $this->client_project_model->insert_comments($arrayDate);
        
           
          
            //$fileTitle = $this->input->post('fileTitle1');
            $filesNotes = $this->input->post('filesNotes');
            
                
             $memberId = $this->session->userdata('memberId');
             $this->load->helper('url');
            // $projectId = $this->input->post('projectId');
             
             $this->load->model('client_project_model');
         
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
            $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            $email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.i-guru.net/Manageproject/message_details/$projectId/$documentId";
                $message = "<h2>I-Guru Projectcamp</h2>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> $fromName has been commented on document.</p>
                                          <br />
                                          $filesNotes
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                        Project Team";
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
				
				
				  $total = count($_FILES['fileDocument']['name']);
             //echo $total;die();
             for($i = 0; $i<$total; $i++)
             {
                 //echo $_FILES['fileDocument']['name'][$i];die();
            if(!empty($_FILES['fileDocument']['name'][$i])){
                    $_FILES['fileDocuments']['name'] = $_FILES['fileDocument']['name'][$i];
                    $_FILES['fileDocuments']['type'] = $_FILES['fileDocument']['type'][$i];
                    $_FILES['fileDocuments']['tmp_name'] = $_FILES['fileDocument']['tmp_name'][$i];
                    $_FILES['fileDocuments']['error'] = $_FILES['fileDocument']['error'][$i];
                    $_FILES['fileDocuments']['size'] = $_FILES['fileDocument']['size'][$i];
                    
                    $config['upload_path'] = './assets/uploads/client-project-document/'; 
                    $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP|txt|csv';
                    $config['max_size'] = '5000'; // max_size in kb
                    $config['file_name'] = $_FILES['fileDocument']['name'][$i];
                    
					$this->load->library('upload', $config);
					//echo "hii";die();
					$this->upload->initialize($config);
				   if($this->upload->do_upload('fileDocuments')){

						//echo "upload fiels";die();
					 //$data = $this->upload->data();
					 $uploadData = $this->upload->data();
					 $filename = $uploadData['file_name'];
					// Initialize array
					 $data['filenames'][] = $filename;
					 //echo print_r($data);die();
					 $projectDocumentArr = array(
						 'projectId'=>$projectId,
						 'commentId'=>$commentId,
						 'commentType'=>$documentId,
						 'memberId'=>$memberId,
						 'docsName'=>$filename
					 );
					$docsId = $this->client_project_model->insert_commentdocsFiles($projectDocumentArr);
					//$data['commentDetails'] = $this->client_project_model->getCommentDetails($memberId,$projectId,$documentId);

				    //$data['docsfiles'] = $this->client_project_model->get_commentdocsFiles($projectId,$commentId,$documentId);
                 //$this->load->helper('url');
				   //$this->load->view('manage-project/message_details',$data)   ;
					}
            }
        }
		}
        
        //echo $projectId;die();
        $data['projectId']=$projectId;
        $data['documentId']=$documentId;
        $data['memberId']=$memberId;
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        //$data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocDetaisl'] = $this->client_project_model->get_client_project_document_details($projectId,$documentId);
        
        $data['docsfiles'] = $this->client_project_model->get_docsFiles($projectId,$documentId);
        //print_r($data);die();
        $data['commentDetails'] = $this->client_project_model->getCommentDetails($memberId,$projectId,$documentId);
		$data['commentdocsfiles'] = $this->client_project_model->get_commentdocsFiles($projectId,$commentId,$documentId);
        //print_r($data['commentdocsfiles']);die();
		$this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/message_details',$data)   ;
        //$this->load->view('footer');
		
    }
	public function todos_list()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        //echo $projectId;die();
        if($this->input->post('action'))
        {
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
		$documentType="2";
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocList'] = $this->client_project_model->get_document_meessage_list($projectId,$documentType);
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/todos_list',$data)   ;
        //$this->load->view('footer');
        }
        else
        {
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
		$documentType="2";
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocList'] = $this->client_project_model->get_document_meessage_list($projectId,$documentType);
        
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/todos_list',$data)   ;
        }
    }
    
    public function todos_details()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        $documentId = $this->uri->segment(4);
        $currentDate=date('Y-m-d H:i:s');
        if($this->input->post('filesNotes'))
        {
            $arrayDate= array(
            'projectId'=>$projectId,
            'documentId'=>$documentId,
            'memberId'=>$memberId,
            'comment'=>$this->input->post('filesNotes'),
            'createdOn'=>$currentDate
				);
				
        $commentId = $this->client_project_model->insert_comments($arrayDate);
        
          
            //$fileTitle = $this->input->post('fileTitle1');
            $filesNotes = $this->input->post('filesNotes');
            
                
             $memberId = $this->session->userdata('memberId');
             $this->load->helper('url');
            // $projectId = $this->input->post('projectId');
             
             $this->load->model('client_project_model');
         
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
            $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            $email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.i-guru.net/Manageproject/message_details/$projectId/$documentId";
                $message = "<h2>I-Guru Projectcamp</h2>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> $fromName has been commented on document.</p>
                                          <br />
                                          $filesNotes
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                        Project Team";
                                          
                          
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
				
				$total = count($_FILES['fileDocument']['name']);
             //echo $total;die();
             for($i = 0; $i<$total; $i++)
             {
                 //echo $_FILES['fileDocument']['name'][$i];die();
            if(!empty($_FILES['fileDocument']['name'][$i])){
                    $_FILES['fileDocuments']['name'] = $_FILES['fileDocument']['name'][$i];
                    $_FILES['fileDocuments']['type'] = $_FILES['fileDocument']['type'][$i];
                    $_FILES['fileDocuments']['tmp_name'] = $_FILES['fileDocument']['tmp_name'][$i];
                    $_FILES['fileDocuments']['error'] = $_FILES['fileDocument']['error'][$i];
                    $_FILES['fileDocuments']['size'] = $_FILES['fileDocument']['size'][$i];
                    
                    $config['upload_path'] = './assets/uploads/client-project-document/'; 
                    $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP|txt|csv';
                    $config['max_size'] = '5000'; // max_size in kb
                    $config['file_name'] = $_FILES['fileDocument']['name'][$i];
                    
					$this->load->library('upload', $config);
					//echo "hii";die();
					$this->upload->initialize($config);
				   if($this->upload->do_upload('fileDocuments')){

						//echo "upload fiels";die();
					 //$data = $this->upload->data();
					 $uploadData = $this->upload->data();
					 $filename = $uploadData['file_name'];
					// Initialize array
					 $data['filenames'][] = $filename;
					 //echo print_r($data);die();
					 $projectDocumentArr = array(
						 'projectId'=>$projectId,
						 'commentId'=>$commentId,
						 'commentType'=>$documentId,
						 'memberId'=>$memberId,
						 'docsName'=>$filename
					 );
					$docsId = $this->client_project_model->insert_commentdocsFiles($projectDocumentArr);
					//$data['commentDetails'] = $this->client_project_model->getCommentDetails($memberId,$projectId,$documentId);

				    //$data['docsfiles'] = $this->client_project_model->get_commentdocsFiles($projectId,$commentId,$documentId);
                 //$this->load->helper('url');
				   //$this->load->view('manage-project/message_details',$data)   ;
					}
            }
        }
        }
        
        //echo $projectId;die();
        $data['projectId']=$projectId;
        $data['documentId']=$documentId;
        $data['memberId']=$memberId;
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        //$data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocDetaisl'] = $this->client_project_model->get_client_project_document_details($projectId,$documentId);
        
        $data['docsfiles'] = $this->client_project_model->get_docsFiles($projectId,$documentId);
        //print_r($data);die();
        $data['commentDetails'] = $this->client_project_model->getCommentDetails($memberId,$projectId,$documentId);
		$data['commentdocsfiles'] = $this->client_project_model->get_commentdocsFiles($projectId,$commentId,$documentId);

        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/todos_details',$data)   ;
        //$this->load->view('footer');
    }
	public function docs_list()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        //echo $projectId;die();
        if($this->input->post('action'))
        {
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
		$documentType="3";
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocList'] = $this->client_project_model->get_project_document_list($projectId,$documentType);
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        //$this->load->view('manage-project/docs_list',$data)   ;
        //$this->load->view('footer');
        }
        else
        {
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
		$documentType="3";
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocList'] = $this->client_project_model->get_project_document_list($projectId,$documentType);
        
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/docs_list',$data)   ;
        }
    }
    
    public function docs_details()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        $documentId = $this->uri->segment(4);
        $currentDate=date('Y-m-d H:i:s');
        if($this->input->post('filesNotes'))
        {
        $arrayDate= array(
            'projectId'=>$projectId,
            'documentId'=>$documentId,
            'memberId'=>$memberId,
            'comment'=>$this->input->post('filesNotes'),
            'createdOn'=>$currentDate
        );
        $commentId = $this->client_project_model->insert_comments($arrayDate);
        
         
          
            //$fileTitle = $this->input->post('fileTitle1');
            $filesNotes = $this->input->post('filesNotes');
            
                
             $memberId = $this->session->userdata('memberId');
             $this->load->helper('url');
            // $projectId = $this->input->post('projectId');
             
             $this->load->model('client_project_model');
         
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
            $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            $email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.i-guru.net/Manageproject/message_details/$projectId/$documentId";
                $message = "<h2>I-Guru Projectcamp</h2>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> $fromName has been commented on document.</p>
                                          <br />
                                          $filesNotes
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                        Project Team";
                                          
                          
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
				
				$total = count($_FILES['fileDocument']['name']);
             //echo $total;die();
             for($i = 0; $i<$total; $i++)
             {
                 //echo $_FILES['fileDocument']['name'][$i];die();
            if(!empty($_FILES['fileDocument']['name'][$i])){
                    $_FILES['fileDocuments']['name'] = $_FILES['fileDocument']['name'][$i];
                    $_FILES['fileDocuments']['type'] = $_FILES['fileDocument']['type'][$i];
                    $_FILES['fileDocuments']['tmp_name'] = $_FILES['fileDocument']['tmp_name'][$i];
                    $_FILES['fileDocuments']['error'] = $_FILES['fileDocument']['error'][$i];
                    $_FILES['fileDocuments']['size'] = $_FILES['fileDocument']['size'][$i];
                    
                    $config['upload_path'] = './assets/uploads/client-project-document/'; 
                    $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP|txt|csv';
                    $config['max_size'] = '5000'; // max_size in kb
                    $config['file_name'] = $_FILES['fileDocument']['name'][$i];
                    
					$this->load->library('upload', $config);
					//echo "hii";die();
					$this->upload->initialize($config);
				   if($this->upload->do_upload('fileDocuments')){

						//echo "upload fiels";die();
					 //$data = $this->upload->data();
					 $uploadData = $this->upload->data();
					 $filename = $uploadData['file_name'];
					// Initialize array
					 $data['filenames'][] = $filename;
					 //echo print_r($data);die();
					 $projectDocumentArr = array(
						 'projectId'=>$projectId,
						 'commentId'=>$commentId,
						 'commentType'=>$documentId,
						 'memberId'=>$memberId,
						 'docsName'=>$filename
					 );
					$docsId = $this->client_project_model->insert_commentdocsFiles($projectDocumentArr);
					
					}
				}
			}
                
        }
        
        //echo $projectId;die();
        $data['projectId']=$projectId;
        $data['documentId']=$documentId;
        $data['memberId']=$memberId;
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        //$data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocDetaisl'] = $this->client_project_model->get_client_project_document_details($projectId,$documentId);
        //print_r($data['clientProjectDocDetaisl']);die();
        $data['docsfiles'] = $this->client_project_model->get_docsFiles($projectId,$documentId);
        //print_r($data['docsfiles']);die();
        $data['commentDetails'] = $this->client_project_model->getCommentDetails($memberId,$projectId,$documentId);
		$data['commentdocsfiles'] = $this->client_project_model->get_commentdocsFiles($projectId,$commentId,$documentId);

        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/docs_details',$data)   ;
        //$this->load->view('footer');
    }
	
	/*  start task */
	public function add_project_task()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        //echo $projectId;die();
		$currentDate=date('Y-m-d H:i:s');
        if($this->input->post('action'))
        {
		$data['projectId']=$projectId;
        $data['memberId']=$memberId;
		$documentType = "1";
		$staus = $this->input->post('projectTaskStatus');
		$projectId = $this->input->post('projectId');
        $projectTaskId =  $this->input->post('projectTaskId');
		$taskDataArr = array(
                'projectId' => $this->input->post('projectId'),
				'taskType' => $this->input->post('projectType'),
                'status'=> 1,
                'taskDesc' => $this->input->post('filesNotes'),
				'createdBy' => $memberId,
				'createdOn' => $currentDate,
            );
        $insertId = $this->client_project_model->insert_project_task($taskDataArr);
		
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        
        $data['projectTaskList'] = $this->client_project_model->get_project_task_list($projectId);
        
			$config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
            $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            $email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.i-guru.net/Manageproject/task_list/$projectId";
                $message = "<h2>I-Guru Projectcamp</h2>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> $fromName has been added new task.</p>
                                          <br />
                                          You can check your task.click on link and browse task.
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                        Project Team";
                                          
                          
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
		//print_r($data);die();
        //$this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/task_list',$data);
        //$this->load->view('footer');
        }
	}
    public function task_list()
    {
		
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        //echo $projectId;die();
        if($this->input->post('action'))
        {$data['shorttasklist'] = $this->client_project_model->get_short_task_list($projectId,$documentId,$documentType);
        
		$data['projecttotaltask'] = $this->client_project_model->get_project_task_status($projectId);
		$data['projecttotalreport'] = $this->client_project_model->get_project_work_report($projectId);

		$data['projectId']=$projectId;
        $data['memberId']=$memberId;
		$staus = $this->input->post('projectTaskStatus');
		$projectId = $this->input->post('projectId');
        $projectTaskId =  $this->input->post('projectTaskId');
        $data['nabd'] = $this->client_project_model->update_project_task($projectId,$projectTaskId,$staus);
		
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['projectTaskList'] = $this->client_project_model->get_project_task_list($projectId);
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/task_list',$data);
        //$this->load->view('footer');
        }
        else
        {
        $data['projectId']=$projectId;
        $data['memberId']=$memberId;
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
	    $documentType = "1";
        $data['projectTaskList'] = $this->client_project_model->get_project_task_list($projectId);
        
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/task_list',$data);
        }
    }
	
	 public function task_details()
    {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        $projectTaskId = $this->uri->segment(4);
		$data['projectId']=$projectId;
        $data['memberId']=$memberId;
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['projectTaskDetails'] = $this->client_project_model->get_project_task_details($projectId,$projectTaskId);
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/task_details',$data);
    }
	public function remove_task()
    {
		//echo "abc";die();
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        $projectTaskId = $this->uri->segment(4);
		$data['projectId']=$projectId;
        $data['memberId']=$memberId;
		
        //$data['projectTaskList'] = $this->client_project_model->update_project_task($projectId,$projectTaskId,$staus);
		$data['projectDetails'] = $this->client_project_model->delete_task($projectId,$projectTaskId);
       
        $data['projectTaskList'] = $this->client_project_model->get_project_task_list($projectId);
        //print_r($data);die();
        $this->load->helper('url');
        //$this->load->view('header');
		redirect(base_url("Manageproject/task_list/$projectId"));
        //$this->load->view('manage-project/task_list',$data);
    }
	/* end task */
     public function add_comments() {
        //Add CK Editor                    
        $path = '../js/ckfinder';
        $width = '800px';
        $this->editor($path, $width);
        //End Code    
        $data['projectId'] = $this->uri->segment(3);
        $data['documnetId'] = $this->uri->segment(4);
        $this->load->helper('url');
        $this->load->view('header');
        $this->load->view('manage-project/message_details', $data);
        $this->load->view('footer');
    }
    public function add_people_on_project() {
        
        if ($this->input->post('save')) {
            
            $total= count($_POST['name']);
           // print_r($_POST['email']);die();
            $projectId= $_POST['projectId'];
            $memberId= $this->session->userdata('memberId');
            $this->load->model('client_project_model');
           
            for($i=0;$i<$total;$i++)
            {
               $name = $_POST['name'][$i];
               $email = $_POST['email'][$i];
                $insert_message = $this->client_project_model->insert_invite_Multiple_people($projectId,$name,$memberId,$email);
            //$memberDetails = $this->client_project_model->get_member_details_by_memberId($this->input->post('memberId'));
           
            }
            
            $email_list = $_POST['email'];
            
         $this->load->model('client_project_model');
         
         $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
         $memberId = $this->session->userdata('memberId');
         $this->load->helper('url');
        // $projectId = $this->uri->segment(2);
        //print_r($data['projectDetails']);
        
         $projectName =  $data['projectDetails']['clientCompanyName'];
        
         
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
           // $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            //$email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            
            $this->load->helper('site_functions_helper');
            
            $emailFrom = $this->session->userdata('admin_email');
            $fromName = $this->session->userdata('admin_username');
            $subject = "Welcome to I-Guru projectcamp";
            $path = "http://projectcamp.i-guru.net/project-details/$projectId";
            $message = "<h2>I-Guru Projectcamp</h2>
                  <h2>Here's the latest activity across everything</h2>
                    <p> New Project has been upload and invite you on project.</p>
                    <br />
                    Project Name - $projectName 
                    <br /><br />
                     <br /><br /> 
                    <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                    <br /><br /> 
                    If you have any questions, just reply to this email <br /> <br />
                    Thanks & Regards <br />
                   I-Guru Team<br /><br />";
                                          
                          
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
            
           
            
            die();
          
            //redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId/");
            //}
        } else {
            $projectId = $this->uri->segment(2);
            $clientId = $this->uri->segment(2);
            $memberId = $this->uri->segment(4);
            $data['projectId'] = $projectId;
            $data['clientId'] = $clientId;
            $data['loginId'] = $memberId;
            $data['peoples'] = $this->client_project_model->get_invite_people_list($projectId);
            $data['members'] = $this->client_project_model->get_active_member_list();
            $this->load->helper('url');
            $this->load->view('header');
            $this->load->view('manage-project/invite_people_to_add_project', $data);
            $this->load->view('footer');
        }
    }
    
    public function add_client_on_project_proceed() {
        if ($this->input->post('save')) {
            $this->load->helper('url');
            
            $fileName1 = "";
            $fileName2 = "";
            $fileName3 = "";
            $fileName4 = "";
            $fileName5 = "";
            $fileName6 = "";
            $fileName7 = "";
            $fileName8 = "";
            
            $fileName1 = @$this->input->post('fileName1');
            $fileName2 = @$this->input->post('fileName2');
            $fileName3 = @$this->input->post('fileName3');
            $fileName4 = @$this->input->post('fileName4');
            $fileName5 = @$this->input->post('fileName5');
            $fileName6 = @$this->input->post('fileName6');
            $fileName7 = @$this->input->post('fileName7');
            $fileName8 = @$this->input->post('fileName8');
            
            
            $projectDataArr = array(
                'clientId' => $this->input->post('clientId'),
                'projectId' => $this->input->post('projectId'),
                'clientTitle' => $this->input->post('clientTitle'),
                'clientName' => $this->input->post('clientName'),
                'companyName' => $this->input->post('companyName'),
                'clientEmail' => $this->input->post('clientEmail'),
                'documentId' => $this->input->post('documentId'),                
                'fileName'   => $fileName1,
                'fileName2'  => $fileName2,
                'fileName3'  => $fileName3,
                'fileName4'  => $fileName4,
                'fileName5'  => $fileName5,
                'fileName6'  => $fileName6,
                'fileName7'  => $fileName7,
                'fileName8'  => $fileName8,
                'securityCode' => $this->input->post('securityCode')
            );
            $inviteClientId = $this->client_project_model->insert_invite_client($projectDataArr);
            $projectId = $this->input->post('projectId');
            $this->load->helper('site_functions_helper');
            $sendTo = $this->input->post('clientEmail');
            $emailFrom = $_SESSION['admin_email'];
            $fromName = $_SESSION['admin_username'];
            $subject = "Utharadesigns - Design Proof";
            $message = $this->input->post('clientDraft');
            send_email($subject, $message, $sendTo, $emailFrom, $fromName);
            redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId/");
        } else {
            //Add CK Editor                    
            $path = '../js/ckfinder';
            $width = '800px';
            $height = '300px';
            $this->editor($path, $width, $height);
            //End Code
            $projectId = $this->uri->segment(3);
            $clientId = $this->uri->segment(4);
            $documentId2 = $this->uri->segment(5);
            $data['fileName'] = $this->uri->segment(6);
            $data['projectId'] = $projectId;
            $data['clientId'] = $clientId;
            $detais = $this->client_project_model->get_security_code($projectId, $clientId, $documentId);
            $clientDetails = $this->client_project_model->get_client_details_by_clientId($clientId);
            
            $data['docList'] = $this->client_project_model->get_document_list($documentId);
            
            
            
            $data['name'] = $clientDetails['clientName'];
            $data['email'] = $clientDetails['clientEmail'];
            $data['code'] = md5($detais['peopleId']);
            $data['documentId'] = $documentId2;
            $this->load->helper('url');
            $this->load->view('header');
            $this->load->view('manage-project/invite_people_to_add_orderproccess', $data);
            $this->load->view('footer');
        }
    }
    public function add_client_on_project() {
        if ($this->input->post('save')) {
            $this->load->helper('url');
          
            $fileName1 = "";
            $fileName2 = "";
            $fileName3 = "";
            $fileName4 = "";
            $fileName5 = "";
            $fileName6 = "";
            $fileName7 = "";
            $fileName8 = "";
            
            $fileName1 = @$this->input->post('fileName1');
            $fileName2 = @$this->input->post('fileName2');
            $fileName3 = @$this->input->post('fileName3');
            $fileName4 = @$this->input->post('fileName4');
            $fileName5 = @$this->input->post('fileName5');
            $fileName6 = @$this->input->post('fileName6');
            $fileName7 = @$this->input->post('fileName7');
            $fileName8 = @$this->input->post('fileName8');
            
            
            $projectDataArr = array(
                'clientId' => $this->input->post('clientId'),
                'projectId' => $this->input->post('projectId'),
                'clientTitle' => $this->input->post('clientTitle'),
                'clientName' => $this->input->post('clientName'),
                'companyName' => $this->input->post('companyName'),
                'clientEmail' => $this->input->post('clientEmail'),
                'documentId' => $this->input->post('documentId'),
                'fileName'   => $fileName1,
                'fileName2'  => $fileName2,
                'fileName3'  => $fileName3,
                'fileName4'  => $fileName4,
                'fileName5'  => $fileName5,
                'fileName6'  => $fileName6,
                'fileName7'  => $fileName7,
                'fileName8'  => $fileName8,
                'securityCode' => $this->input->post('securityCode')
            );
            $inviteClientId = $this->client_project_model->insert_invite_client($projectDataArr);
            $projectId = $this->input->post('projectId');
            $this->load->helper('site_functions_helper');
            $sendTo = $this->input->post('clientEmail');
            $emailFrom = $_SESSION['admin_email'];
            $fromName = $_SESSION['admin_username'];
            $subject = "Utharadesigns - Design Proof";
            $message = $this->input->post('clientDraft');
            send_email($subject, $message, $sendTo, $emailFrom, $fromName);
            redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId/");
        } else {
            //Add CK Editor   
           
            $path = '../js/ckfinder';
            $width = '800px';
            $height = '300px';
            $this->editor($path, $width, $height);
            //End Code
            $projectId = $this->uri->segment(3);
            $clientId = $this->uri->segment(4);
            $documentId = $this->uri->segment(5);
            $data['fileName'] = $this->uri->segment(6);
            $data['projectId'] = $projectId;
            $data['clientId'] = $clientId;
            $detais = $this->client_project_model->get_security_code($projectId, $clientId, $documentId);
            $clientDetails = $this->client_project_model->get_client_details_by_clientId($clientId);
            
            $data['docList'] = $this->client_project_model->get_document_list($documentId);
            
            $data['name'] = $clientDetails['clientName'];
            $data['email'] = $clientDetails['clientEmail'];
            $data['userEmail'] = $_SESSION['admin_email'];
            $data['code'] = md5($detais['peopleId']);
            $data['documentId'] = $documentId;
            $this->load->helper('url');
            $this->load->view('header');
            $this->load->view('manage-project/invite_people_to_add_client', $data);
            $this->load->view('footer');
        }
    }
	public function remove_project() {
        
            //if ($this->student_model->validate_form_data() == TRUE)
            //{     
            //echo "invite";die();
			$this->load->helper('url');
            $this->load->helper('site_functions_helper');
           
            $projectId= $_POST['projectId'];
            $memberId= $this->session->userdata('memberId');
            $this->load->model('client_project_model');
            $projectId = $this->uri->segment(3);
            $peopleId = $this->uri->segment(4);
			
            $removeId = $this->client_project_model->remove_project_status($projectId);
            
           die();
            redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId/");
            //}
        }
	 public function remove_people_from_project() {
        
            //if ($this->student_model->validate_form_data() == TRUE)
            //{     
            //echo "invite";die();
			$this->load->helper('url');
            $this->load->helper('site_functions_helper');
           
            $projectId= $_POST['projectId'];
            $memberId= $this->session->userdata('memberId');
            $this->load->model('client_project_model');
            $projectId = $this->uri->segment(3);
            $peopleId = $this->uri->segment(4);
			
            $removeId = $this->client_project_model->remove_people_from_project($projectId,$peopleId);
            
           die();
            redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId/");
            //}
        }
		
    public function view_client_project_details() {
        
        $memberId = $this->session->userdata('memberId');
         $this->load->helper('url');
         $projectId = $this->uri->segment(2);
        //print_r($data['invite_list']);die();
        //echo $projectId;die();
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
        //print_r($data['invite_list']);die();
        $data['clientProjectDocList'] = $this->client_project_model->get_client_project_document($projectId);
		$documentType="1";
        $data['shortMessageCenter'] = $this->client_project_model->get_short_docs_list($projectId,$documentId,$documentType);
		$documentType="2";
		$data['shortTodos'] = $this->client_project_model->get_short_Todos_list($projectId,$documentId,$documentType);
        $documentType="3";
		//$data['shortDocs'] = $this->client_project_model->get_short_docs_list($projectId,$documentId,$documentType);

        //print_r($data);die();
        $data['shortdocsfiles'] = $this->client_project_model->get_short_docsfiles_list($projectId,$documentId,$documentType);
        $data['shorttasklist'] = $this->client_project_model->get_short_task_list($projectId,$documentId,$documentType);
        
		$data['projecttotaltask'] = $this->client_project_model->get_project_task_status($projectId);
		$data['projecttotalreport'] = $this->client_project_model->get_project_work_report($projectId);

       //echo count($data['shortdocsfiles']);die();
        $this->load->helper('url');
        //$this->load->view('header');
        $this->load->view('manage-project/view_project', $data);
        //$this->load->view('footer');
    }
    public function share_document_details() {
        $memberId = $this->session->userdata('memberId');
        $this->load->helper('url');
        if (!isset($memberId) || $memberId == "") {
            redirect(base_url() . "index.php/login");
        }
        $projectId = $this->uri->segment(3);
        $documentId = $this->uri->segment(4);
        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);
        $data['invite_list'] = $this->client_project_model->get_invite_people_list($projectId);
       // print_r($data);die();
        $data['clientProjectDocDeatils'] = $this->client_project_model->get_client_project_document_details($projectId,$documentId);
        
        $data1['chat_discussion_list'] = $this->client_project_model->get_chat_discussion_list($documentId);

       // print_r($data1);die();
            $data['documentId'] = $documentId;
            $data['projectId'] = $projectId;
            //Add CK Editor                    
            $path = '../js/ckfinder';
            $width = '800px';
            $this->editor($path, $width);
            //End Code 
            $this->load->helper('url');
            $this->load->view('header');
            $this->load->view('manage-project/start_chat_discussion', $data);
            $this->load->view('footer');
        
        
    }
    public function share_document_files() {
        //Add CK Editor                    
        $path = '../js/ckfinder';
        $width = '800px';
        $this->editor($path, $width);
        //End Code 
        $data['memberId'] = $this->session->userdata('memberId');
        $data['projectId'] = $this->uri->segment(3);
        $this->load->helper('url');
        $this->load->view('header');
        $this->load->view('manage-project/share_doucment_files', $data);
        $this->load->view('footer');
    }
    
	public function share_document_do_upload_forgoogledrive() 
    {
        
            $projectId = $this->input->post('projectId');
            $memberId = $this->input->post('memberId');
            $createdOn = date('Y-m-d h:i:s');
            $projectDocumentArr = array(
                'projectId' => $this->input->post('projectId'),
                'fileTitle' => $this->input->post('fileTitle1'),
                'filesNotes' => $this->input->post('filesNotes1'),
				'fileName' => $this->input->post('filelink'),
				'documentType' => $this->input->post('documentType'),
                'memberId' => $memberId,
                'createdOn' => $createdOn    
				);
            $this->load->model('client_project_model');
            $documentId = $this->client_project_model->insert_client_project_document($projectDocumentArr);            
            
            
            $total = count($_FILES['fileDocument']['name']);
             
             for($i = 0; $i<$total; $i++)
             {
                 //echo $_FILES['fileDocument']['name'][$i];die();
            if(!empty($_FILES['fileDocument']['name'][$i])){
                    $_FILES['fileDocuments']['name'] = $_FILES['fileDocument']['name'][$i];
                    $_FILES['fileDocuments']['type'] = $_FILES['fileDocument']['type'][$i];
                    $_FILES['fileDocuments']['tmp_name'] = $_FILES['fileDocument']['tmp_name'][$i];
                    $_FILES['fileDocuments']['error'] = $_FILES['fileDocument']['error'][$i];
                    $_FILES['fileDocuments']['size'] = $_FILES['fileDocument']['size'][$i];                   
                    $config['upload_path'] = './assets/uploads/client-project-document/'; 
                    $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP|txt|csv';
                    $config['max_size'] = '5000'; // max_size in kb
                    $config['file_name'] = $_FILES['fileDocument']['name'][$i];
                    
            $this->load->library('upload', $config);
            //echo "hii";die();
            $this->upload->initialize($config);
           if($this->upload->do_upload('fileDocuments'))
           {

             $uploadData = $this->upload->data();
             $filename = $uploadData['file_name'];

            // Initialize array
             $data['filenames'][] = $filename;
             //echo print_r($data);die();
             $projectDocumentArr = array(
                 'projectId'=>$projectId,
                 'documentId'=>$documentId,
                 'memberId'=>$memberId,
                 'docsName'=>$filename
             );
            $docsId = $this->client_project_model->insert_docsFiles($projectDocumentArr);

             
            }
            }
          }
           
          
            $fileTitle = $this->input->post('fileTitle1');
            $filesNotes = $this->input->post('filesNotes1');
            
                
             $memberId = $this->session->userdata('memberId');
             $this->load->helper('url');
             $projectId = $this->input->post('projectId');
             
             $this->load->model('client_project_model');
         
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
            $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            $email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.utharaprint.co.uk/index.php/Login";
                $message = "<h2>I-Guru Projectcamp</h2>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> $fromName has been upload the new document.</p>
                                          <br />
                                          Document Name - $fileTitle
                                          <br /><br />
                                          $filesNotes
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                        Project Team";
                                          
                          
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
                
        exit;
    }
    
    public function share_document_do_upload() 
    {
        
        
        
            $projectId = $this->input->post('projectId');
            $memberId = $this->input->post('memberId');
            $createdOn = date('Y-m-d h:i:s');
			//echo $this->input->post('filesNotes');die();
            $projectDocumentArr = array(
                'projectId' => $this->input->post('projectId'),
                'fileTitle' => $this->input->post('fileTitle'),
                'filesNotes' => $this->input->post('filesNotes'),
				'documentType' => $this->input->post('documentType'),
                'memberId' => $memberId,
                'createdOn' => $createdOn    
            );
            $this->load->model('client_project_model');
            $documentId = $this->client_project_model->insert_client_project_document($projectDocumentArr);
            
         //print_r($_FILES['fileDocument']['name']);die();
             $total = count($_FILES['fileDocument']['name']);
             //echo $total;die();
             for($i = 0; $i<$total; $i++)
             {
                 //echo $_FILES['fileDocument']['name'][$i];die();
            if(!empty($_FILES['fileDocument']['name'][$i])){
                    $_FILES['fileDocuments']['name'] = $_FILES['fileDocument']['name'][$i];
                    $_FILES['fileDocuments']['type'] = $_FILES['fileDocument']['type'][$i];
                    $_FILES['fileDocuments']['tmp_name'] = $_FILES['fileDocument']['tmp_name'][$i];
                    $_FILES['fileDocuments']['error'] = $_FILES['fileDocument']['error'][$i];
                    $_FILES['fileDocuments']['size'] = $_FILES['fileDocument']['size'][$i];
                    
                    $config['upload_path'] = './assets/uploads/client-project-document/'; 
                    $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP|txt|csv';
                    $config['max_size'] = '5000'; // max_size in kb
                    $config['file_name'] = $_FILES['fileDocument']['name'][$i];
                    
            $this->load->library('upload', $config);
            //echo "hii";die();
            $this->upload->initialize($config);
           if($this->upload->do_upload('fileDocuments')){

                //echo "upload fiels";die();
             //$data = $this->upload->data();
             $uploadData = $this->upload->data();
             $filename = $uploadData['file_name'];

            // Initialize array
             $data['filenames'][] = $filename;
             //echo print_r($data);die();
             $projectDocumentArr = array(
                 'projectId'=>$projectId,
                 'documentId'=>$documentId,
                 'memberId'=>$memberId,
                 'docsName'=>$filename
             );
            $docsId = $this->client_project_model->insert_docsFiles($projectDocumentArr);

             
            }
            }
          }
            
           
            
            $fileTitle = $this->input->post('fileTitle');
            $filesNotes = $this->input->post('filesNotes');
            
                
             $memberId = $this->session->userdata('memberId');
             $this->load->helper('url');
             $projectId = $this->input->post('projectId');
             
             $this->load->model('client_project_model');
         
            $config['charset'] = 'utf-8';
            $config['mailtype'] = 'html';
            $this->email->initialize($config);
            $this->load->library('upload', $config);
             
            $data['list_of_emails'] = $this->client_project_model->get_invite_people_Email_list($projectId);
            $email_list = implode(',',array_column($data['list_of_emails'],'memberEmail'));//Implode array elements with comma as separator
            //print_r($email_list);die();
            //$this->email->to($email_list);
            
            $this->load->helper('site_functions_helper');
            
                $emailFrom = $this->session->userdata('admin_email');
                $fromName = $this->session->userdata('admin_username');
                $subject = "I-Guru projectcamp :Here's the latest activity.";
                $path = "http://projectcamp.i-guru.net/project-details/$projectId";
                $message = "<h2>I-Guru Projectcamp</h2>
                                          <h2>Here's the latest activity across everything</h2>
                                          <p> $fromName has been upload the new document.</p>
                                          <br />
                                          Document Name - $fileTitle
                                          <br /><br />
                                          $filesNotes
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email <br /> <br />
                                          Thanks & Regards <br />
                                        Project Team";
                                          
                          
                send_email($subject, $message, $email_list, $emailFrom, $fromName);
               
               //echo "document sent";die(); 
               
                // redirect(base_url());
        //exit;
    }
   
    public function download_client_project_document($fileName = NULL) {
        $this->load->helper('download');
        if ($fileName) {
            //$file = realpath( "./assets/uploads/client-project-document/")."\\".$fileName;
            $file = realpath("./assets/uploads/client-project-document/");
            $filePath = $file . "/$fileName";
            if (file_exists($filePath)) {
                $data = file_get_contents($filePath);
                force_download($fileName, $data);
            } else {
                redirect(base_url());
            }
        }
    }
    
     public function download_project_document($fileName = NULL) {
        $this->load->helper('download');
        if ($fileName) {
            //$file = realpath( "./assets/uploads/client-project-document/")."\\".$fileName;
            $file = realpath("./assets/uploads/client-project-document/");
            $filePath = $file . "/$fileName";
            if (file_exists($filePath)) {
                $data = file_get_contents($filePath);
                force_download($fileName, $data);
            } else {
                redirect(base_url());
            }
        }
    }
    
    public function start_chat_discussion() {
        if ($this->input->post('save')) {
            //if ($this->student_model->validate_form_data() == TRUE)
            //{
            $memberId=$this->session->userdata('member');
            $projectDataArr = array(
                'documentId' => $this->input->post('documentId'),
                'projectId' => $this->input->post('projectId'),
                'memberName' => $memberId,
                'chatTitle' => $this->input->post('chatTitle'),
                'chatMessage' => $this->input->post('chatMessage'),
                'createdOn' => date("Y-m-d")
            );
            $projectId = $this->input->post('projectId');
            $insert_message = $this->client_project_model->insert_chat_discussion_on_document($projectDataArr);
            $this->load->helper('url');
            $projectId = $this->input->post('projectId');
            $chatTitle = $this->input->post('chatTitle');
            $chatMessage = $this->input->post('chatMessage');
            $getProjectMemberList = $this->client_project_model->get_invite_people_list($projectId);
            for ($i = 0; $i < count($getProjectMemberList); $i++) {
                $this->load->helper('site_functions_helper');
                $sendTo = trim($getProjectMemberList[$i]['memberEmail']);
                $emailFrom = $_SESSION['admin_email'];
                $fromName = $_SESSION['admin_username'];
                $subject = "Latest activity on Project Camp)";
                $message = "<h2>Project Camp</h2>
                                       <h2>Here's the latest activity across everything</h2>
                                       <p> $fromName has been upload the new document on Uthara Print (Projectcamp)</p>
                                       <br /><br />
                                       If you have any questions, just reply to this email
                                       Thanks and Regards <br />
                                       Project Team";
                send_email($subject, $message, $sendTo, $emailFrom, $fromName);
            }
            redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId");
      
        } else {
            $data['documentId'] = $this->uri->segment(4);
            $data['projectId'] = $this->uri->segment(3);
            //Add CK Editor                    
            $path = '../js/ckfinder';
            $width = '800px';
            $this->editor($path, $width);
            //End Code 
            $this->load->helper('url');
            $this->load->view('header');
            $this->load->view('manage-project/start_chat_discussion', $data);
            $this->load->view('footer');
        }
    }
    public function chat_discussion_list() {
        $this->load->helper('url');
        $projectId = $this->uri->segment(3);
        $documentId = $this->uri->segment(4);
        $this->load->view('header');
        $data['chat_discussion_list'] = $this->client_project_model->get_chat_discussion_list($documentId);
        print_r($data);die();
        $data['documentId'] = $documentId;
        $data['docId'] = $documentId;
        $data['projectId'] = $projectId;
        $this->load->view('manage-project/chat_disscussion_list', $data);
        $this->load->view('footer');
    }
    public function archive_projects() {
        $this->load->helper('url');
        $projectsIds = $this->input->post('record');
        $this->client_project_model->archive_projects($projectsIds);
        redirect(base_url() . "index.php/Manageproject//show_archive_client_list");
    }
    public function activate_projects() {
        $this->load->helper('url');
        $projectsIds = $this->input->post('record');
        $this->client_project_model->activate_projects($projectsIds);
        redirect(base_url() . "index.php/Manageproject/index");
    }
    public function editor($path, $width, $height = '') {
        //Loading Library For Ckeditor
        $this->load->helper('url');
        $this->load->library('ckeditor');
        $this->load->library('ckFinder');
        //configure base path of ckeditor folder 
        $this->ckeditor->basePath = base_url() . 'assets/js/ckeditor/';
        $this->ckeditor->config['toolbar'] = 'Full';
        $this->ckeditor->config['language'] = 'en';
        $this->ckeditor->config['width'] = $width;
        if ($height != "")
            $this->ckeditor->config['height'] = $height;
        //configure ckfinder with ckeditor config 
        $this->ckfinder->SetupCKEditor($this->ckeditor, $path);
    }
    public function start_chat_message() {
        if ($this->input->post('save')) {
            $this->load->helper('url');
            $chatMessage = $this->input->post('chatTitle');
            $memberId=$this->session->userdata('memberId');
            $projectDataArr = array(
                'documentId' => $this->input->post('documentId'),
                'projectId' => $this->input->post('projectId'),
                'memberId' => $memberId,
                'chatTitle' => $chatMessage,
                'chatMessage' => $this->input->post('chatMessage'),
                'createdOn' => date("Y-m-d")
            );
            $projectId = $this->input->post('projectId');
            $insert_message = $this->client_project_model->insert_chat_discussion_on_document($projectDataArr);
            $this->load->helper('url');
            $projectId = $this->input->post('projectId');
            $chatTitle = $this->input->post('chatTitle');
            $chatMessage = $this->input->post('chatMessage');
            $getProjectMemberList = $this->client_project_model->get_invite_people_list($projectId);
            for ($i = 0; $i < count($getProjectMemberList); $i++) {
                $this->load->helper('site_functions_helper');
                $sendTo = trim($getProjectMemberList[$i]['memberEmail']);
                $emailFrom = $_SESSION['admin_email'];
                $fromName = $_SESSION['admin_username'];
                $subject = "Latest activity on Projectcamp";
                $message = "<h2>Projectcamp</h2>
                                       <h2>Here's the latest activity across everything</h2>
                                       <p> $fromName has been upload the new document on Uthara Print (Projectcamp)</p>
                                       <br /><br />
                                       If you have any questions, just reply to this email
                                       Thanks and Regards <br />
                                       Project Team <br /><br />";
                send_email($subject, $message, $sendTo, $emailFrom, $fromName);
            }
            redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId");
        }
    }
    public function download_project_client_task_document($fileName = NULL) {
        $this->load->helper('download');
        if ($fileName) {
            //$file = realpath( "./assets/uploads/client-project-document/")."\\".$fileName;
            $file = realpath("./assets/uploads/client-project-task-files/");
            $filePath = $file . "/$fileName";
            if (file_exists($filePath)) {
                $data = file_get_contents($filePath);
                force_download($fileName, $data);
            } else {
                redirect(base_url());
            }
        }
    }
    public function change_project_work_status() {
        $this->load->helper('url');
        $projectId = $this->input->post('projectId');
        $this->client_project_model->update_project_work_status($projectId);
        redirect(base_url() . "index.php/Manageproject/view_client_project_details/$projectId");
    }
    public function download_proof() 
    {
        $data['status'] = "";
        if ($this->input->post('save')) 
        {
            $this->load->helper('url');
            $projectId = $this->input->post('projectId');
            $proofStatus = $this->input->post('proofStatus');
            $proofNotes  = $this->input->post('proofNotes');
            $email = $this->input->post('email');
            $userEmail  = $this->input->post('userEmail');
            $projectDataArr = array(
                'proofStatus' => $this->input->post('proofStatus'),
                'proofNotes' => $this->input->post('proofNotes')
            );
            $inviteClientId = $this->client_project_model->update_client_document_proof_status($projectDataArr);
            $data['status'] = "Proof Status Updated Successfully";
            
            $getProjectMemberList = $this->client_project_model->get_invite_people_list($projectId);
            for ($i = 0; $i < count($getProjectMemberList); $i++) 
            {
                $this->load->helper('site_functions_helper');
                $sendTo = trim($getProjectMemberList[$i]['memberEmail']);
                $emailFrom = $_SESSION['admin_email'];
                $fromName = $_SESSION['admin_username'];
                $emailFrom = $userEmail;
                $fromName =$email;
                $subject = "Proof Status on Utharaprint Ltd (Projectcamp)";
                $path = "http://projectcamp.utharaprint.co.uk/index.php/Manageproject/view_client_project_details/$projectId/";
                $message = "<h2>Project Camp</h2>
                                          <h2>Here's the Proof Status on Projectcamp</h2>
                                          <p>Client has been Updated the Proof Status on Uthara Print (Projectcamp)</p>
                                          <br />
                                          Proof Status - $proofStatus
                                          <br />
                                          Proof Notes -  $proofNotes
                                          <br /><br /> 
                                          <a href='$path' style='width: auto; text-decoration: none;'>View Project</a>
                                          <br /><br /> 
                                          If you have any questions, just reply to this email
                                          Thanks and Regards <br />
                                          Project Team <br /><br />";
                send_email($subject, $message, $sendTo, $emailFrom, $fromName);
            }
         
            
        }        
        
        
        $path = '../js/ckfinder';
        $width = '800px';
        $this->editor($path, $width);
        //End Code    
        $data['projectId'] = $this->uri->segment(3);
        $data['documentId'] = $this->uri->segment(4);
        $data['code'] = $this->uri->segment(5);
        $detail = $this->client_project_model->get_client_invitation_details($data['projectId'], $data['documentId'], $data['code']);
        $data['advertLists'] = $this->client_project_model->get_advert_list();
        //echo "<pre>";
        //print_r($detail);

        $data['peopleId'] = $detail['peopleId'];
        $data['fileName1']  = $detail['fileName'];
        $data['fileName2'] = $detail['fileName2'];
        $data['fileName3'] = $detail['fileName3'];
        $data['fileName4'] = $detail['fileName4'];
        $data['fileName5'] = $detail['fileName5'];
        $data['fileName6'] = $detail['fileName6'];
        $data['fileName7'] = $detail['fileName7'];
        $data['fileName8'] = $detail['fileName8'];    
       
        $data['proofStatus'] = $detail['proofStatus'];
        $data['proofNotes'] = $detail['proofNotes'];
        $this->load->helper('url');
        $this->load->view('header_client');
        $this->load->view('manage-project/download_document_proof', $data);
        $this->load->view('footer');
    }
     public function we_transfer_doucment_files() 
     { 
         
        $this->load->helper('url');
        $this->load->view('header');
        $this->load->view('manage-project/we_transfer_doucment_files');
        $this->load->view('footer');
    }
    
    public function do_upload_multiple() 
    {
        $this->load->model('client_project_model');
        $this->load->helper('url');
                
        $config['upload_path']   = './assets/uploads/client-transfer-document/';
        $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP';
        $this->load->library('upload', $config);
               
        
        $_FILES['file_Name_first']['name']     = $_FILES['file_Name']['name'][0];
        $_FILES['file_Name_first']['tmp_name'] = $_FILES['file_Name']['tmp_name'][0];
        $_FILES['file_Name_first']['type']     = $_FILES['file_Name']['type'][0];
        $_FILES['file_Name_first']['error']    = $_FILES['file_Name']['error'][0];
        $_FILES['file_Name_first']['size']    = $_FILES['file_Name']['size'][0];
        
        if($this->upload->do_upload('file_Name_first'))
        {            
            $data = $this->upload->data();
            $fileName = $data['file_name'];
            $tmpFileName = $data['file_name']."<br />";
            $firstPostDataArr   = array(                                
                                            'fileTitle'     => $this->input->post('fileTitle'), 
                                            'fileName'      => $fileName,
                                            'senderEmail'   => $this->input->post('senderEmail'),
                                            'receiverEmail' => $this->input->post('receiverEmail'),
                                            'fileMessage'   => $this->input->post('fileMessage')
                                );        
            $this->client_project_model->insert_file_transfer_records($firstPostDataArr);   
        }
        $no_of_files = "";
        
        if(count(@$_FILES['file']['name'])>0)
        {
            $no_of_files = count($_FILES['file']['name'])+1;
            
            for($i=0;$i<count($_FILES['file']['name']);$i++)
            {
                $fileName = "";
                $_FILES['file_tmp_name']['name']       = $_FILES['file']['name'][$i];
                $_FILES['file_tmp_name']['tmp_name']   = $_FILES['file']['tmp_name'][$i];
                $_FILES['file_tmp_name']['type']       = $_FILES['file']['type'][$i];
                $_FILES['file_tmp_name']['error']      = $_FILES['file']['error'][$i];
                $_FILES['file_tmp_name']['size']       = $_FILES['file']['size'][$i];

                if (!$this->upload->do_upload('file_tmp_name'))
                {
                    $error = array('error' => $this->upload->display_errors());       
                }
                else
                {
                    $data         = $this->upload->data();
                    $fileName     = $data['file_name'];
                    $tmpFileName  .= $data['file_name']."<br />";

                    $secondPostDataArr      = array(                                
                                        'fileTitle'     => $this->input->post('fileTitle'),
                                        'fileName'      => $fileName,
                                        'senderEmail'   => $this->input->post('senderEmail'),
                                        'receiverEmail' => $this->input->post('receiverEmail'),
                                        'fileMessage'   => $this->input->post('fileMessage')
                                    );        
                   $this->client_project_model->insert_file_transfer_records($secondPostDataArr);   
                }
            }
        }
        //Send email to client
            $this->load->helper('site_functions_helper');
            $sendTo        = $this->input->post('receiverEmail');
            $emailFrom     = $this->input->post('senderEmail');
            $fromName      = $this->input->post('senderEmail');
            $fileMessage   = $this->input->post('fileMessage');
            
            $path = "http://www.utharaprint.co.uk/uthara-projectcamp/index.php/Manageproject/downloadFiles/";
            $subject = "$emailFrom sent you files via Uthara Transfer";
            $message = "<table class='m_129748183163604849table_full_width' style='border-collapse: collapse; border-spacing: 0; margin: 0; outline: none; padding: 0; table-layout: fixed; width: 100%;' border='0' cellspacing='0' cellpadding='0'>
                        <tbody>
                        <tr>
                        <td class='m_129748183163604849main_heading_td m_129748183163604849unpadded_mobile m_129748183163604849main_heading_td_wider' style='color: #17181a; font-family: 'FreightSans Pro','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-size: 26px; font-style: normal; font-weight: normal; line-height: 30px; margin: 0; outline: none; padding: 60px 80px 0; width: 100%; word-spacing: 0;' align='center' valign='top'><a class='m_129748183163604849main_heading_email_link' style='color: #17181a; font-weight: normal; text-decoration: none;' href='mailto:$emailFrom' target='_blank'><span class='m_129748183163604849main_heading_email_link' style='color: #409fff; font-weight: normal; text-decoration: none;'>$emailFrom</span></a> <br /> sent you some files</td>
                        </tr>
                        <tr>
                        <td class='m_129748183163604849button_outer_wrapper_td m_129748183163604849unpadded_mobile' style='margin: 0; outline: none; padding: 40px 160px 0; width: 100%;' align='left' valign='top'>
                        <table class='m_129748183163604849table_full_width m_129748183163604849button_table' style='border-collapse: collapse; border-spacing: 0; margin: 0; outline: none; padding: 0; table-layout: fixed; width: 100%;' border='0' cellspacing='0' cellpadding='0'>
                        <tbody>
                        <tr>
                        <td style='height: 35px;margin: 0; outline: none; padding: 0;  width: 100%; padding: 15px 20px; text-align: center; text-decoration: none;' align='left' valign='top'><a class='m_129748183163604849button_anchor m_129748183163604849button_2_anchor' style='background: #409fff; border-radius: 25px; color: #ffffff; display: block; font-family: 'Fakt Pro Medium','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-size: 14px; font-style: normal; padding: 15px 20px; text-align: center; text-decoration: none; word-spacing: 0;' href='$path' target='_blank' data-saferedirecturl='$path'>Get your files </a></td>
                        </tr>
                        </tbody>
                        </table>
                        </td>
                        </tr>
                        <tr>
                        <td class='m_129748183163604849body_content_td m_129748183163604849unpadded_mobile' style='color: #797c7f; font-family: 'Fakt Pro','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-size: 14px; font-style: normal; font-weight: normal; line-height: 24px; margin: 0; outline: none; padding: 50px 80px 0; width: 100%; word-spacing: 0;' align='left' valign='top'>$fileMessage</td>
                        </tr>
                        <tr>
                        <td class='m_129748183163604849separator_20_outer_wrapper_td m_129748183163604849unpadded_mobile' style='margin: 0; outline: none; padding: 20px 80px 0; width: 100%;' align='left' valign='top'>
                        <table class='m_129748183163604849table_full_width' style='border-collapse: collapse; border-spacing: 0; margin: 0; outline: none; padding: 0; table-layout: fixed; width: 100%;' border='0' cellspacing='0' cellpadding='0'>
                        <tbody>
                        <tr>
                        <td class='m_129748183163604849separator_td' style='border-bottom-color: #f4f4f4; border-bottom-style: solid; border-bottom-width: 2px; font-size: 1px; line-height: 0; margin: 0; outline: none; padding: 0; width: 100%;' align='left' valign='top'>&nbsp;</td>
                        </tr>
                        </tbody>
                        </table>
                        </td>
                        </tr>
                        <tr>
                        <td class='m_129748183163604849body_content_td m_129748183163604849unpadded_mobile m_129748183163604849download_link_container' style='color: #797c7f; font-family: 'Fakt Pro','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-size: 14px; font-style: normal; font-weight: normal; line-height: 24px; margin: 0; outline: none; padding: 50px 80px 0; width: 100%; word-break: break-all; word-spacing: 0;' align='left' valign='top'><span class='m_129748183163604849body_content_subheading_span' style='color: #17181a; font-family: 'FreightSans Pro','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-size: 18px; font-weight: 500;'> Download link </span> <br /> <a class='m_129748183163604849download_link_link' style='color: #17181a; font-family: 'Fakt Pro Medium','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-weight: normal; text-decoration: underline; word-wrap: break-word;' href='$path' target='_blank' data-saferedirecturl='$path'><span class='m_129748183163604849download_link_link' style='color: #409fff; font-weight: normal; text-decoration: underline; word-wrap: break-word;'>$path</span> </a></td>
                        </tr>
                        <tr>
                        <td class='m_129748183163604849body_content_td m_129748183163604849body_content_padding_bottom_td m_129748183163604849files_list m_129748183163604849unpadded_mobile' style='color: #797c7f; font-family: 'Fakt Pro','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-size: 14px; font-style: normal; font-weight: normal; line-height: 24px; margin: 0; outline: none; padding: 50px 80px; width: 100%; word-spacing: 0;' align='left' valign='top'><span class='m_129748183163604849body_content_subheading_span' style='color: #17181a; font-family: 'FreightSans Pro','Segoe UI','SanFrancisco Display',Arial,sans-serif; font-size: 18px; font-weight: 500;'> $no_of_files files </span> <br />$tmpFileName</td>
                        </tr>
                        </tbody>
                        </table>
                        <p>&nbsp;</p>";
            
            send_email($subject, $message, $sendTo, $emailFrom, $fromName);
            $data['successMsg'] = "Files Sent Successfully";
            $this->load->view('header');
            $this->load->view('manage-project/we_transfer_doucment_files',$data);
            $this->load->view('footer');
            //redirect(base_url() . "index.php/Manageproject/");
    }
    
    public function SendEmail($subject, $message, $sendTo, $emailFrom, $fromName){

					$config = Array('protocol' => 'smtp',

						'smtp_host' => 'mail.utharaprint.co.uk',

						'smtp_port' => 587,

						'smtp_user' => 'sushil@utharaprint.co.uk',

						'smtp_pass' => 'lakshya@2772016',

						'smtp_timeout' => '4',

						'mailtype'  => 'html', 

						'charset'   => 'iso-8859-1');

					$this->load->library('email', $config);

					$this->email->set_newline("\r\n");

					$this->email->from('sushil@utharaprint.co.uk', 'Free file Transfer');

					$data = array('userName'=> 'Admin');

					$this->email->to($sendTo);  // replace it with receiver mail id				

					$this->email->bcc('sush@i-guru.net');

					$this->email->subject("Thank you for using fft"); // replace it with relevant subject

					//$body = $this->load->view('pages/registration_email',$emaildata,TRUE);

					$this->email->message($message);

					$this->email->send();

    }
    public function getallRecord()
    {
       $client_url= "http://localhost/newfreefiletransfer/api";
       //echo $client_url;die();
       $curl= curl_init($client_url);
       //curl_setopt($curl, CURLOPT_URL,"http://localhost/newfreefiletransfer/api");
       curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
       $result = curl_exec($curl);
       curl_close($curl);
       $data= json_decode($result);
       foreach($data as $row)
       {
          echo $row->advertTitle;
       }
    }
}
