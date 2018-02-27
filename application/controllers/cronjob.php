<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cronjob extends MY_Controller
{
	function index()
	{
			$this->theme_view = 'blank';
			$this->load->helper(array('dompdf', 'file'));
			$timestamp = time(); 
			$core_settings = Setting::first();
			$date = date("Y-m-d"); 
			$this->load->library('parser');
			
			
		if($core_settings->cronjob == "1" && time() > ($core_settings->last_cronjob + 300))
		{
			$core_settings->last_cronjob = time();
			$core_settings->save();
   			$this->load->database();
			
   			//Check Subscriptions
			$sql = 'SELECT * FROM subscriptions WHERE status != "Inactive" AND end_date > "'.$date.'" AND "'.$date.'" >= next_payment ORDER BY next_payment';
					$res = $this->db->query($sql);
					$res = $res->result();
					foreach ($res as $key2 => $value2) { 
						$eventline = 'New invoice created for subscription <a href="'.base_url().'subscriptions/view/'.$value2->id.'">#'.$value2->reference.'</a>';
						
							$subscription = Subscription::find($value2->id);
								$invoice = Invoice::last();
								$invoice_reference = Setting::first();
								if($subscription){
									$_POST['subscription_id'] = $subscription->id;
									$_POST['company_id'] = $subscription->company_id;
									if($subscription->subscribed != 0){$_POST['status'] = "Paid";}else{$_POST['status'] = "Open";}
									$_POST['currency'] = $subscription->currency;
									$_POST['issue_date'] = $subscription->next_payment;
									$_POST['due_date'] = date('Y-m-d', strtotime('+14 day', strtotime ($subscription->next_payment)));
									$_POST['currency'] = $subscription->currency;
									$_POST['terms'] = $subscription->terms;
									$_POST['discount'] = $subscription->discount;
									$_POST['tax'] = $subscription->tax;
									$_POST['second_tax'] = $subscription->second_tax;
									$_POST['reference'] = $invoice_reference->invoice_reference;
									$invoice = Invoice::create($_POST);
									$invoiceid = Invoice::last();
									$items = SubscriptionHasItem::find('all',array('conditions' => array('subscription_id=?',$value2->id)));
									foreach ($items as $value):
										$itemvalues = array(
											'invoice_id' => $invoiceid->id,
											'item_id' => $value->item_id,
											'amount' =>  $value->amount,
											'description' => $value->description,
											'value' => $value->value,
											'name' => $value->name,
											'type' => $value->type,
											);
										InvoiceHasItem::create($itemvalues);
									endforeach;
									$invoice_reference->update_attributes(array('invoice_reference' => $invoice_reference->invoice_reference+1));
						       		

						       		if($invoice){	
						       			$subscription->next_payment = date('Y-m-d', strtotime($subscription->frequency, strtotime ($subscription->next_payment)));
						       			$subscription->save();
						       			

						       			//Send Invoice to Client via email
						       			$this->email->clear(TRUE);
										

										$data["invoice"] = Invoice::find($invoiceid->id); 
										$data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$invoiceid->id)));
							     		$data["core_settings"] = Setting::first();
							     		//Calculate invoice sum
							     		$invoice_sum = $data["invoice"];
										

										$i = 0; $sum = 0;
										foreach ($data['items'] as $value){
											$sum = $sum+$invoice_sum->invoice_has_items[$i]->amount*$invoice_sum->invoice_has_items[$i]->value; $i++;
										}
										if(substr($invoice_sum->discount, -1) == "%"){ 
											$discount = sprintf("%01.2f", round(($sum/100)*substr($invoice_sum->discount, 0, -1), 2)); 
										}
										else{
											$discount = $invoice_sum->discount;
										}
										$sum = $sum-$discount;

										if($invoice_sum->tax != ""){
											$tax_value = $invoice_sum->tax;
										}else{
											$tax_value = $data["core_settings"]->tax;
										}

										if($invoice_sum->second_tax != ""){
									      $second_tax_value = $invoice_sum->second_tax;
									    }else{
									      $second_tax_value = $data["core_settings"]->second_tax;
									    }

										$tax = sprintf("%01.2f", round(($sum/100)*$tax_value, 2));
										$second_tax = sprintf("%01.2f", round(($sum/100)*$second_tax_value, 2));

								    	$sum = sprintf("%01.2f", round($sum+$tax+$second_tax, 2));

										$invoice_sum->sum = $sum;
										$invoice_sum->save();


							    		// Generate PDF     
							    		$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->invoice_pdf_template, $data, true);
							    		$filename = $this->lang->line('application_invoice').'_'.$data["invoice"]->reference;
							     		pdf_create($html, $filename, FALSE);
							     		//email
										$this->email->from($data["core_settings"]->email, $data["core_settings"]->company);
										$this->email->to($data["invoice"]->company->client->email); 
										$this->email->subject($data["core_settings"]->invoice_mail_subject); 
							  			$this->email->attach("files/temp/".$filename.".pdf");
							  			$due_date = date($data["core_settings"]->date_format, human_to_unix($data["invoice"]->due_date.' 00:00:00')); 
							  			//Set parse values
							  			$parse_data = array(
							            					'client_contact' => $data["invoice"]->company->client->firstname.' '.$data["invoice"]->company->client->lastname,
							            					'due_date' => $due_date,
							            					'invoice_id' => $data["invoice"]->reference,
							            					'client_link' => $data["core_settings"]->domain,
							            					'company' => $data["core_settings"]->company,
							            					'logo' => '<img src="'.base_url().''.$data["core_settings"]->logo.'" alt="'.$data["core_settings"]->company.'"/>',
							            					'invoice_logo' => '<img src="'.base_url().''.$data["core_settings"]->invoice_logo.'" alt="'.$data["core_settings"]->company.'"/>'
							            					);
							  			$email_invoice = read_file('./application/views/'.$data["core_settings"]->template.'/templates/email_invoice.html');
							  			$message = $this->parser->parse_string($email_invoice, $parse_data);
										$this->email->message($message);			
										if($this->email->send()){
										$data["invoice"]->update_attributes(array('status' => 'Sent', 'sent_date' => date("Y-m-d")));
										}
							       		log_message('error', $eventline); 
										unlink("files/temp/".$filename.".pdf");
										
						       			}

									} 
							} //Check Subscriptions end

						// Auto Backup every 7 days
						if($core_settings->autobackup == "1" && time() > ($core_settings->last_autobackup + 7 * 24 * 60 * 60)){
								$this->load->dbutil();
								$prefs = array('format' => 'zip', 'filename' => 'Database-auto-full-backup_'.date('Y-m-d_H-i'));
								$backup =& $this->dbutil->backup($prefs); 

								if ( ! write_file('./files/backup/Database-auto-full-backup_'.date('Y-m-d_H-i').'.zip', $backup))
									{
									    log_message('error', "Error while creating auto database backup!");
									}
									else
									{ 
										$core_settings->last_autobackup = time();
										$core_settings->save();
										log_message('error', "Auto backup has been created.");

									}
						}


						echo "Success";

					



			
		}
		
	}

	
}
