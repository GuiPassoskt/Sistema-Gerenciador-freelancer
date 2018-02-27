<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cProjects extends MY_Controller {
               
	function __construct()
	{
		parent::__construct();
		
		$access = FALSE;
		if($this->client){	
			$this->view_data['invoice_access'] = FALSE;
			foreach ($this->view_data['menu'] as $key => $value) { 
				if($value->link == "cinvoices"){ $this->view_data['invoice_access'] = TRUE;}
				if($value->link == "cprojects"){ $access = TRUE;}
			}
			if(!$access && !empty($this->view_data['menu'][0])){
				redirect($this->view_data['menu'][0]->link);
			}elseif(empty($this->view_data['menu'][0])){
				$this->view_data['error'] = "true";
				$this->session->set_flashdata('message', 'error: You have no access to any modules!');
				redirect('login');
			}
		}elseif($this->user){
				redirect('projects');
		}else{
			redirect('login');
		}


		

		$this->view_data['submenu'] = array(
				 		$this->lang->line('application_my_projects') => 'cprojects'
				 		);	
		function submenu($id){ return array(
								$this->lang->line('application_back') => 'cprojects',
								$this->lang->line('application_overview') => 'cprojects/view/'.$id,
						 		$this->lang->line('application_media') => 'cprojects/media/'.$id,
						 		);
						}
	}	
	function index()
	{
		$this->view_data['project'] = Project::find('all',array('conditions' => array('company_id=?',$this->client->company->id)));
		$this->content_view = 'projects/client_views/all';
	}
	function view($id = FALSE)
	{
		$this->view_data['submenu'] = array(
								$this->lang->line('application_back') => 'cprojects',
								$this->lang->line('application_overview') => 'cprojects/view/'.$id,
						 		$this->lang->line('application_media') => 'cprojects/media/'.$id,
						 		);
		$this->view_data['project'] = Project::find($id);
		$this->view_data['project_has_invoices'] = Invoice::find('all',array('conditions' => array('project_id = ? AND company_id=? AND estimate != ? AND issue_date<=?',$id,$this->client->company->id,1,date('Y-m-d', time()))));
		$tasks = ProjectHasTask::count(array('conditions' => 'project_id = '.$id));
		$tasks_done = ProjectHasTask::count(array('conditions' => array('status = ? AND project_id = ?', 'done', $id)));
		@$this->view_data['opentaskspercent'] = $tasks_done/$tasks*100;
		
		$this->view_data['time_days'] = round((human_to_unix($this->view_data['project']->end.' 00:00') - human_to_unix($this->view_data['project']->start.' 00:00')) / 3600 / 24);
		$this->view_data['time_left'] = $this->view_data['time_days'];
		$this->view_data['timeleftpercent'] = 100;

		if(human_to_unix($this->view_data['project']->start.' 00:00') < time() && human_to_unix($this->view_data['project']->end.' 00:00') > time()){
			$this->view_data['time_left'] = round((human_to_unix($this->view_data['project']->end.' 00:00') - time()) / 3600 / 24);
			$this->view_data['timeleftpercent'] = $this->view_data['time_left']/$this->view_data['time_days']*100;
		}
		if(human_to_unix($this->view_data['project']->end.' 00:00') < time()){
			$this->view_data['time_left'] = 0;
			$this->view_data['timeleftpercent'] = 0;
		}
		@$this->view_data['opentaskspercent'] = $tasks_done/$tasks*100;
		$tracking = $this->view_data['project']->time_spent;
		if(!empty($this->view_data['project']->tracking)){ $tracking=(time()-$this->view_data['project']->tracking)+$this->view_data['project']->time_spent; }
		$this->view_data['timertime'] = $tracking;
		$this->view_data['time_spent_from_today'] = time() - $this->view_data['project']->time_spent;	
		$tracking = floor($tracking/60);
		$tracking_hours = floor($tracking/60);
		$tracking_minutes = $tracking-($tracking_hours*60);

		

		$this->view_data['time_spent'] = $tracking_hours." ".$this->lang->line('application_hours')." ".$tracking_minutes." ".$this->lang->line('application_minutes');
		$this->view_data['time_spent_counter'] = sprintf("%02s", $tracking_hours).":".sprintf("%02s", $tracking_minutes);

		if(!isset($this->view_data['project_has_invoices'])){$this->view_data['project_has_invoices'] = array();}
		if($this->view_data['project']->company_id != $this->client->company->id){ redirect('cprojects');}
		$this->content_view = 'projects/client_views/view';

	}
	function media($id = FALSE, $condition = FALSE, $media_id = FALSE)
	{
		$this->load->helper('notification');
			$this->view_data['submenu'] = array(
								$this->lang->line('application_back') => 'cprojects',
								$this->lang->line('application_overview') => 'cprojects/view/'.$id,
						 		$this->lang->line('application_media') => 'cprojects/media/'.$id,
						 		);
		switch ($condition) {
			case 'view':

				if($_POST){
					unset($_POST['send']);
					unset($_POST['_wysihtml5_mode']);
					unset($_POST['files']);
					//$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['text'] = $_POST['message'];
					unset($_POST['message']);
					$_POST['project_id'] = $id;
					$_POST['media_id'] = $media_id; 
					$_POST['from'] = $this->client->firstname.' '.$this->client->lastname;
					$this->view_data['project'] = Project::find_by_id($id);
					$this->view_data['media'] = ProjectHasFile::find($media_id);
					$message = Message::create($_POST);
       				if(!$message){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_message_error'));}
       				else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_message_success'));
       					foreach ($this->view_data['project']->project_has_workers as $workers){
            			    send_notification($workers->user->email, "[".$this->view_data['project']->name."] New comment", 'New comment on meida file: '.$this->view_data['media']->name.'<br><strong>'.$this->view_data['project']->name.'</strong>');
            			}

       				}
       				redirect('cprojects/media/'.$id.'/view/'.$media_id);
				}
				$this->content_view = 'projects/client_views/view_media';
				$this->view_data['media'] = ProjectHasFile::find($media_id);
				$project = Project::find_by_id($id);
				if($project->company_id != $this->client->company->id){ redirect('cprojects');}
				$this->view_data['form_action'] = 'cprojects/media/'.$id.'/view/'.$media_id;
				$this->view_data['filetype'] = explode('.', $this->view_data['media']->filename);
				$this->view_data['filetype'] = $this->view_data['filetype'][1];
				$this->view_data['backlink'] = 'cprojects/view/'.$id;
				break;
			case 'add':
				$this->content_view = 'projects/_media';
				$this->view_data['project'] = Project::find($id);
				if($_POST){
					$config['upload_path'] = './files/media/';
					$config['encrypt_name'] = TRUE;
					$config['allowed_types'] = '*';

					$this->load->library('upload', $config);
					if ( ! $this->upload->do_upload())
						{
							$error = $this->upload->display_errors('', ' ');
							$this->session->set_flashdata('message', 'error:'.$error);
							redirect('cprojects/view/'.$id);
						}
						else
						{
							$data = array('upload_data' => $this->upload->data());

							$_POST['filename'] = $data['upload_data']['orig_name'];
							$_POST['savename'] = $data['upload_data']['file_name'];
							$_POST['type'] = $data['upload_data']['file_type'];
						}

					unset($_POST['send']);
					unset($_POST['userfile']);
					unset($_POST['file-name']);
					unset($_POST['files']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$_POST['project_id'] = $id;
					$_POST['client_id'] = $this->client->id;
					$media = ProjectHasFile::create($_POST);
		       		if(!$media){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_media_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_media_success'));
		       			$attributes = array('subject' => $this->lang->line('application_new_media_subject'), 'message' => '<b>'.$this->client->firstname.' '.$this->client->lastname.'</b> '.$this->lang->line('application_uploaded'). ' '.$_POST['name'], 'datetime' => time(), 'project_id' => $id, 'type' => 'media', 'client_id' => $this->client->id);
					    $activity = ProjectHasActivity::create($attributes);
    		       		
    		       		foreach ($this->view_data['project']->project_has_workers as $workers){
            			    send_notification($workers->user->email, "[".$this->view_data['project']->name."] ".$this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added').' <strong>'.$this->view_data['project']->name.'</strong>');
            			}
            			if(isset($this->view_data['project']->company->client->email)){
            			send_notification($this->view_data['project']->company->client->email, "[".$this->view_data['project']->name."] ".$this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added').' <strong>'.$this->view_data['project']->name.'</strong>');
            			}
		       		}
					redirect('cprojects/view/'.$id);
				}else
				{
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_add_media');
					$this->view_data['form_action'] = 'cprojects/media/'.$id.'/add';
					$this->content_view = 'projects/_media';
				}	
				break;
			case 'update':
				$this->content_view = 'projects/_media';
				$this->view_data['media'] = ProjectHasFile::find($media_id);
				$this->view_data['project'] = Project::find($id);
				if($_POST){
					unset($_POST['send']);
					unset($_POST['_wysihtml5_mode']);
					unset($_POST['files']);
					$_POST = array_map('htmlspecialchars', $_POST);
					$media_id = $_POST['id'];
					$media = ProjectHasFile::find($media_id);
					if ($this->view_data['media']->client_id != "0") {
						$media->update_attributes($_POST);
					}
		       		if(!$media){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_media_error'));}
		       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_media_success'));}
					redirect('cprojects/view/'.$id);
				}else
				{
					$this->theme_view = 'modal';
					$this->view_data['title'] = $this->lang->line('application_edit_media');
					$this->view_data['form_action'] = 'cprojects/media/'.$id.'/update/'.$media_id;
					$this->content_view = 'projects/_media';
				}	
				break;
			case 'delete':
					$media = ProjectHasFile::find($media_id);
					if ($media->client_id != "0") {
						$media->delete();
						$this->load->database();
						$sql = "DELETE FROM messages WHERE media_id = $media_id";
						$this->db->query($sql);
					}
		       		if(!$media){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_media_error'));}
		       		else{	unlink('./files/media/'.$media->savename);
		       				$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_media_success'));
		       			}
					redirect('cprojects/view/'.$id);
				break;
			default:
				$this->view_data['project'] = Project::find($id);
				$this->content_view = 'projects/client_views/media';
				break;
		}

	}
	function deletemessage($project_id, $media_id, $id){
					$from = $this->client->firstname.' '.$this->client->lastname;
					$message = Message::find($id);
					if($message->from == $this->client->firstname." ".$this->client->lastname){
					$message->delete();
					}
		       		if(!$message){
		       			$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_message_error'));
		       		}
		       		else{ 
		       			$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_message_success'));
		       		}
					redirect('cprojects/media/'.$project_id.'/view/'.$media_id);
	}
	function download($media_id = FALSE){

		$this->load->helper('download');
		$media = ProjectHasFile::find($media_id);
		$project = Project::find_by_id($media->project_id);
		if($project->company_id != $this->client->company->id){ redirect('cprojects');}
		$media->download_counter = $media->download_counter+1;
		$media->save();

		$data = file_get_contents('./files/media/'.$media->savename); 
		$name = $media->filename;
		force_download($name, $data);
	}

	function activity($id = FALSE, $condition = FALSE, $activityID = FALSE)
	{
	    $this->load->helper('notification');
		$project = Project::find_by_id($id);
		//$activity = ProjectHasAktivity::find_by_id($activityID);
		switch ($condition) {
			case 'add':
				if($_POST){
					unset($_POST['send']);
					$_POST['subject'] = htmlspecialchars($_POST['subject']);
					$_POST['message'] = strip_tags($_POST['message'], '<br><br/><p></p><a></a><b></b><i></i><u></u><span></span>');
					$_POST['project_id'] = $id;
					$_POST['client_id'] = $this->client->id;
					$_POST['type'] = "comment";
					unset($_POST['files']);
					$_POST['datetime'] = time();
					$activity = ProjectHasActivity::create($_POST);
		       		if(!$activity){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_error'));}
		       		else{
		       		    $this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_success'));
		       		    foreach ($project->project_has_workers as $workers){
            			    send_notification($workers->user->email, "[".$project->name."] ".$_POST['subject'], $_POST['message'].'<br><strong>'.$project->name.'</strong>');
            			}
            			if(isset($project->company->client->email)){
            			send_notification($project->company->client->email, "[".$project->name."] ".$_POST['subject'], $_POST['message'].'<br><strong>'.$project->name.'</strong>');
            			}
		       		}
					//redirect('projects/view/'.$id);
					
				}
				break;
			case 'update':
				
				break;
			case 'delete':
				
				break;
		}

	}

}