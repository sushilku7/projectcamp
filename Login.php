<?phpdefined('BASEPATH') OR exit('No direct script access allowed');class Login extends CI_Controller{    var $data;	    function  __construct()     {        parent::__construct();               //$this->load->library('admin_init_elements');        //$this->admin_init_elements->init_elements();         //echo "heoo";die();        $this->load->model('login_model');        $this->load->library('form_validation');        $this->load->model('client_project_model');        $this->load->helper('site_functions_helper');        $this->load->helper('url');            }   function index()   {              //echo "heooo";die();        $this->load->helper('url');        $data = array();        $data['error_message']   = "";        $data['success_message'] = "";                $is_admin_logged_in = $this->session->userdata('is_admin_logged_in');        if($is_admin_logged_in == TRUE)        {                        redirect(base_url().'index.php/Manageproject/index');        }                if($this->session->userdata('error_message')!="")        {                $data['error_message'] = $this->session->userdata('error_message');                $this->session->unset_userdata('error_message');        }        if($this->session->userdata('success_message')!="")        {                $data['success_message']=$this->session->userdata('success_message');                $this->session->unset_userdata('success_message');                $this->session->sess_destroy();        }                        $this->load->view('index', $data);    }    function userlogin()    {        $this->load->helper('url');        $this->load->view('userlogin', $data);    }	     // Admin authentication ----------    function validate_admin_login()    {        $this->load->helper('url');        $this->load->model('login_model');        $query = $this->login_model->validate_admin_login();        if($query)        {                // prepare session variables                $data = Array (                                    'is_admin_logged_in' => true,                                    'admin_user_name' => $this->input->post('username'),                                    'success_message'=>'Logged in Successfully'                           );                $this->session->set_userdata($data);                  $redirectUrl = "";                $redirectUrl = $this->input->post('url');                if($this->session->userdata('member_type')=='1')                {                 //redirect(base_url('dashboard'));                       redirect(base_url().'project-list/');                }                else                {                    redirect(base_url().'project-list/');                }                                    }    else    {                $data = Array ('error_message'=>'Invalid login Details, Please Try Again');                $this->session->set_userdata($data);                redirect(base_url().'Login/userlogin');        }    }	    function signup()    {                //echo "heiill";die();                $this->load->helper('url');                $this->load->model('login_model');                $data = array();                $data['error_message']   = "";                if ($this->input->post('signup'))                {                    $exitEmailId = $this->login_model->is_exist_emailId($this->input->post('memberEmail'));                    //echo $exitEmailId;die();                                        if($exitEmailId=='0')                    {                        $singupArray=array(                       'memberUsername'=> $this->input->post('username'),                       'memberName'=> $this->input->post('username'),					   'memberPost'=> $this->input->post('companyName'),                       'memberEmail'=> $this->input->post('memberEmail'),                       'memberPass'=> md5($this->input->post('password')),                       'isActive'=>'1',                       'createdOn'=> date('Y-m-d h:i:s')                        );                                                                           $this->load->helper('site_functions_helper');            			   $config['charset'] = 'utf-8';                           $config['mailtype'] = 'html';                                                     $this->email->initialize($config);                            //$this->load->library('upload', $config);                                                    $memberEmail = $this->input->post('memberEmail');                            $emailFrom = $this->input->post('memberEmail');                            $fromName = "I-Guru projectcamp";                            $subject   = "Welcome to I-Guru Projectcamp!";                            $path       = "http://projectcamp.i-guru.net/signin";                            $logoPath = base_url()."asset/images/i-guru_projectcamp11.png";                                                        $message = "<img src='$logoPath' alt='$projectName'><br><br>            				<h2>Thanks for Signup,</h2></b><br><br>            				You have got all project activity on your mail.<br><br>                            I-Guru Projectcamp <a href='$path' style='width: auto; text-decoration: none;'>Click here to Login.</a>            				<br><br>";                                                                                                                   $this->load->library('email');                                                        send_email($subject, $message, $emailFrom, $emailFrom, $fromName);                                                                                                                          $result = $this->login_model->userSignup();                                           if($result)                        {							$memberResult = $this->login_model->update_invite_id($result,$memberEmail);                            $this->validate_admin_login();                            redirect(base_url().'index.php/Manageproject/index');                        }                                           //$this->load->view('usersignup');                             //exit();                    }              else {                $data['checkEmail'] = Array ('email_message_error'=>'Email Address allready exit,Please enter email address');                //$this->session->set_userdata($data);                                    $this->load->view('usersignup',$data);                        }             }             else             {                              $this->load->helper('url');             $this->load->view('usersignup');                 }    }	function ForgotPassword()	{	   $this->load->model('login_model');				if ($this->input->post('forgetPassword'))        {			$exitEmailId = $this->login_model->is_exist_emailId($this->input->post('memberEmail'));			if($exitEmailId=='0')               {			   $data['checkEmail'] = Array ('email_message_error'=>'Email Address does not Match ,Please enter email address');                //$this->session->set_userdata($data);                                    $this->load->view('forget_password',$data); 							}				else				{				    $eml = $this->input->post('memberEmail');            		$new_pass = substr(md5(date('Y-m-d h:i:s')),0,7);            		$encrypt_pass = md5($new_pass);            		            		$postdata = array(            						'memberPass' => $encrypt_pass            						 );		    	$this->login_model->SendNewPass($postdata);	 		    			    				   $data['checkEmail'] = Array ('email_message_error'=>"New Password has been send to your email address.");			   			   $this->load->helper('site_functions_helper');			   $config['charset'] = 'utf-8';               $config['mailtype'] = 'html';                             $this->email->initialize($config);                $this->load->library('upload', $config);                                        $emailFrom = $this->input->post('memberEmail');                $fromName = "I-Guru projectcamp";                $subject   = "I-Guru projectcamp :Password reset successfully";                $path       = "http://projectcamp.i-guru.net/signin";                $logoPath = base_url()."asset/images/i-guru_projectcamp11.png";                                $message = "<img src='$logoPath' alt='$projectName'><br><br>				<b>Your New Login details</b><br><br>				<b>New Password: </b>$new_pass<br><br>				<b>Email Address: </b>$emailFrom<br><br />                 I-Guru Projectcamp <a href='$path' style='width: auto; text-decoration: none;'>Click here to Login.</a>				<br><br>                 If you have any questions, just reply to this email <br /> <br />                 Thanks & Regards <br />                 Support Team";                                                                   $this->load->library('email');                                send_email($subject, $message, $emailFrom, $emailFrom, $fromName);			   			   				$this->load->view('forget_password',$data);				}					}				//$this->data['maincontent'] = $this->load->view('admin/maincontents/forgotpass',$data,TRUE);        //render full layout, specific to this function        $this->load->view('forget_password');	}		function is_exist($str) //callback function for validation rule (called from model) for checking uniqueness of key data       {        if ($this->login_model->is_exist($str) == TRUE)        {            $this->form_validation->set_message('is_exist', 'This Email Does not exist');            return FALSE;        }        else        {            return TRUE;        }      }	     public function user_details()    {		        $memberId = $this->session->userdata('memberId');		$memberEmail = $this->session->userdata('admin_email');        $this->load->helper('url');		$projectId=base64_decode($this->uri->segment(2));		$this->load->model('client_project_model');        $filename="";		if($this->input->post('Updatedetails'))		{						$fileSize = $_FILES['profileImg']['size'];			$fileSize = $fileSize/1024;					//print_r($abc);die();		   $data['checkEmail'] = Array ('email_message_error'=>"Update Successfully.");		   if(isset($_FILES['profileImg']['name']) && $_FILES['profileImg']['name']!='')			{					$_FILES['profileImgs']['name'] = $_FILES['profileImg']['name'];                    $_FILES['profileImgs']['type'] = $_FILES['profileImg']['type'];                    $_FILES['profileImgs']['tmp_name'] = $_FILES['profileImg']['tmp_name'];                    $_FILES['profileImgs']['error'] = $_FILES['profileImg']['error'];                    $_FILES['profileImgs']['size'] = $_FILES['profileImg']['size'];                                        $config['upload_path'] = './assets/uploads/project-image/';                     $config['allowed_types'] = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLSX|gif|GIF|jpg|JPG|png|PNG|tiff|TIFF|zip|ZIP|txt|csv';                    $config['max_size'] = '5000'; // max_size in kb                    $config['file_name'] = $_FILES['profileImg']['name'];                    					 $config['width']    = 150;					 $config['height']   = 150;					 					$this->load->library('upload', $config);					//echo "hii";die();					$this->upload->initialize($config);				   if($this->upload->do_upload('profileImgs')){						//echo "upload fiels";die();					 //$data = $this->upload->data();					 $uploadData = $this->upload->data();					 $filename = $uploadData['file_name'];										}			}			$this->login_model->update_userdetails($memberId,$filename);						if($this->input->post('password')!='')			{				$this->login_model->update_user_password($memberId);			}		$data['userDetails'] = $this->login_model->getUserDetails($memberId);        $data['projectDetails'] = $this->client_project_model->get_project_details($projectId);        $documentType = "1";		$data['unreadymmsgnotification'] = $this->client_project_model->get_count_meessage_notification_list($projectId,$documentType,$memberEmail);        $documentType = "2";		$data['unreadytodosnotification'] = $this->client_project_model->get_count_meessage_notification_list($projectId,$documentType,$memberEmail);        $documentType = "3";		$data['unreadydocsnotification'] = $this->client_project_model->get_count_meessage_notification_list($projectId,$documentType,$memberEmail);		$data['unreadtasknotification'] = $this->client_project_model->get_count_task_notification_list($projectId,$documentType,$memberEmail);        $this->load->view('manage-project/user_details',$data);		}		else		{			//echo "hello";die();		$data['projectId']=$projectId;        $data['memberId']=$memberId;		$data['projectDetails'] = $this->client_project_model->get_project_details($projectId);        $data['userDetails'] = $this->login_model->getUserDetails($memberId);        $documentType = "1";		$data['unreadymmsgnotification'] = $this->client_project_model->get_count_meessage_notification_list($projectId,$documentType,$memberEmail);        $documentType = "2";		$data['unreadytodosnotification'] = $this->client_project_model->get_count_meessage_notification_list($projectId,$documentType,$memberEmail);        $documentType = "3";		$data['unreadydocsnotification'] = $this->client_project_model->get_count_meessage_notification_list($projectId,$documentType,$memberEmail);		$data['unreadtasknotification'] = $this->client_project_model->get_count_task_notification_list($projectId,$documentType,$memberEmail);        $this->load->helper('url');        //$this->load->view('header');        $this->load->view('manage-project/user_details',$data);		}    }        function logout()    {                $this->load->helper('url');        //Code added        $data = Array ('success_message'=>'Logged out Successfully');        $this->session->set_userdata($data);        $this->session->unset_userdata('is_admin_logged_in');        redirect(base_url());            }    }?>