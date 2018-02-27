<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	function index()
	{	
			$core_settings = Setting::first();
			if($core_settings->registration != 1){ redirect('login');}
		
		if($_POST)
		{
			$this->load->library('parser');
			$this->load->helper('file');
			$this->load->helper('notification');
			$client = Client::find_by_email($_POST['email']);
			if($client->inactive == 1){$client = FALSE;}
			$check_company = Company::find_by_name($_POST['name']);

			

			

			if(!$client && !$check_company && $_POST['name'] != "" && $_POST['email'] != "" && $_POST['password'] != "" && $_POST['firstname'] != "" && $_POST['lastname'] != "" && $_POST['confirmcaptcha'] != ""){ 
				 
				$client_attr = array();
				$company_attr['name'] = $_POST['name'];
				$company_attr['website'] = $_POST['website'];
				$company_attr['phone'] = $_POST['phone'];
				$company_attr['mobile'] = $_POST['mobile'];
				$company_attr['address'] = $_POST['address'];
				$company_attr['zipcode'] = $_POST['zipcode'];
				$company_attr['city'] = $_POST['city'];
				$company_attr['country'] = $_POST['country'];
				$company_attr['province'] = $_POST['province'];
				$company_attr['vat'] = $_POST['vat'];
				$company_attr['reference'] = $core_settings->company_reference;

				$core_settings->company_reference = $core_settings->company_reference+1;
				$core_settings->save();

				$company = Company::create($company_attr);

				if(!$company){
					$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_registration_error'));
					redirect('register');
				}

				$lastclient = Client::last();
				$client_attr = array();
				$client_attr['email'] = $_POST['email'];
				$client_attr['firstname'] = $_POST['firstname'];
				$client_attr['lastname'] = $_POST['lastname'];
				$client_attr['phone'] = $_POST['phone'];
				$client_attr['mobile'] = $_POST['mobile'];
				$client_attr['address'] = $_POST['address'];
				$client_attr['zipcode'] = $_POST['zipcode'];
				$client_attr['city'] = $_POST['city'];
				$modules = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type = ?', 'client')));
				$client_attr['access'] = "";
				foreach ($modules as $value) {
					if($value->name == "Projects" || $value->name == "Messages" || $value->name == "Tickets" || $value->name == "Invoices"){
						$client_attr['access'] .= $value->id.",";
					}
				}
				
				$client_attr['company_id'] = $company->id;
				

				$client = Client::create($client_attr);
				if($client){
					$client->password = $client->set_password($_POST['password']);
					$client->save();
					$company->client_id = $client->id;
					$company->save();

					$this->email->from($core_settings->email, $core_settings->company);
					$this->email->to($client_attr['email']); 

					$this->email->subject($this->lang->line('application_your_account_has_been_created'));
					$parse_data = array(
	            					'link' => base_url().'login/',
	            					'company' => $core_settings->company,
	            					'company_reference' => $company->reference,
	            					'logo' => '<img src="'.base_url().''.$core_settings->logo.'" alt="'.$core_settings->company.'"/>',
	            					'invoice_logo' => '<img src="'.base_url().''.$core_settings->invoice_logo.'" alt="'.$core_settings->company.'"/>'
	            					);
		  			$email = read_file('./application/views/'.$core_settings->template.'/templates/email_create_account.html');
		  			$message = $this->parser->parse_string($email, $parse_data);
					$this->email->message($message);
					$this->email->send();
					send_notification($core_settings->email, $this->lang->line('application_new_client_has_registered'), $this->lang->line('application_new_client_has_registered').': <br><strong>'.$company_attr['name'].'</strong><br>'.$client_attr['firstname'].' '.$client_attr['lastname'].'<br>'.$client_attr['email']);
            		

					$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_registration_success'));
					redirect('login');
				}else{
					$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_registration_error'));
					redirect('login');
				}

			}else{

				$this->view_data['error'] = $this->lang->line('messages_email_already_taken');
				$this->theme_view = 'login';
				$this->content_view = 'auth/register';
				$this->view_data['form_action'] = 'register';
				$this->view_data['registerdata'] = $_POST;

			}
			
			
			
				
		}else{
			$this->view_data['error'] = 'false';
			$this->theme_view = 'login';
			$this->content_view = 'auth/register';
			$this->view_data['form_action'] = 'register';
		}


		
	}

	
}
