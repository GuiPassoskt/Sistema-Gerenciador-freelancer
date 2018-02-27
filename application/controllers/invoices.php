<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoices extends MY_Controller {
               
	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if($this->client){	
			redirect('cprojects');
		}elseif($this->user){
			foreach ($this->view_data['menu'] as $key => $value) { 
				if($value->link == "invoices"){ $access = TRUE;}
			}
			if(!$access){redirect('login');}
		}else{
			redirect('login');
		}
		$this->view_data['submenu'] = array(
				 		$this->lang->line('application_all') => 'invoices',
				 		$this->lang->line('application_open') => 'invoices/filter/open',
				 		$this->lang->line('application_Sent') => 'invoices/filter/sent',
				 		$this->lang->line('application_Paid') => 'invoices/filter/paid',
				 		);	
		
	}	
	function index()
	{
		$this->view_data['invoices'] = Invoice::find('all', array('conditions' => array('estimate != ?', 1)));
		$days_in_this_month = days_in_month(date('m'), date('Y'));
		$lastday_in_month =  strtotime(date('Y')."-".date('m')."-".$days_in_this_month);
		$firstday_in_month =  strtotime(date('Y')."-".date('m')."-01");

		$this->view_data['invoices_paid_this_month'] = Invoice::count(array('conditions' => 'UNIX_TIMESTAMP(`paid_date`) <= '.$lastday_in_month.' and UNIX_TIMESTAMP(`paid_date`) >= '.$firstday_in_month.' AND estimate != 1 AND status = "paid"'));
		$this->view_data['invoices_due_this_month'] = Invoice::count(array('conditions' => 'UNIX_TIMESTAMP(`due_date`) <= '.$lastday_in_month.' and UNIX_TIMESTAMP(`due_date`) >= '.$firstday_in_month.' AND estimate != 1 AND status != "paid"'));
		
		//statistic
		$now = time();
		$beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
		$end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
		$this->view_data['invoices_due_this_month_graph'] = Invoice::find_by_sql('select count(id) AS "amount", DATE_FORMAT(`due_date`, "%w") AS "date_day", DATE_FORMAT(`due_date`, "%Y-%m-%d") AS "date_formatted" from invoices where UNIX_TIMESTAMP(`due_date`) >= "'.$beginning_of_week.'" AND UNIX_TIMESTAMP(`due_date`) <= "'.$end_of_week.'" AND estimate != 1');
		$this->view_data['invoices_paid_this_month_graph'] = Invoice::find_by_sql('select count(id) AS "amount", DATE_FORMAT(`paid_date`, "%w") AS "date_day", DATE_FORMAT(`paid_date`, "%Y-%m-%d") AS "date_formatted" from invoices where UNIX_TIMESTAMP(`paid_date`) >= "'.$beginning_of_week.'" AND UNIX_TIMESTAMP(`paid_date`) <= "'.$end_of_week.'" AND estimate != 1');


		$this->content_view = 'invoices/all';
	}
	function calc()
	{
		$invoices = Invoice::find('all', array('conditions' => array('estimate != ?', 1)));
		foreach ($invoices as $invoice) {
		
		$settings = Setting::first();

		$items = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$invoice->id)));

		//calculate sum
		$i = 0; $sum = 0;
		foreach ($items as $value){
			$sum = $sum+$invoice->invoice_has_items[$i]->amount*$invoice->invoice_has_items[$i]->value; $i++;
		}
		if(substr($invoice->discount, -1) == "%"){ 
			$discount = sprintf("%01.2f", round(($sum/100)*substr($invoice->discount, 0, -1), 2)); 
		}
		else{
			$discount = $invoice->discount;
		}
		$sum = $sum-$discount;

		if($invoice->tax != ""){
			$tax_value = $invoice->tax;
		}else{
			$tax_value = $settings->tax;
		}

		if($invoice->second_tax != ""){
	      $second_tax_value = $invoice->second_tax;
	    }else{
	      $second_tax_value = $core_settings->second_tax;
	    }

		$tax = sprintf("%01.2f", round(($sum/100)*$tax_value, 2));
		$second_tax = sprintf("%01.2f", round(($sum/100)*$second_tax_value, 2));

    $sum = sprintf("%01.2f", round($sum+$tax+$second_tax, 2));


		$invoice->sum = $sum;
		$invoice->save();

		}
		redirect('invoices');

	}
	function filter($condition = FALSE)
	{
		$days_in_this_month = days_in_month(date('m'), date('Y'));
		$lastday_in_month =  date('Y')."-".date('m')."-".$days_in_this_month;
		$firstday_in_month =  date('Y')."-".date('m')."-01";
		$this->view_data['invoices_paid_this_month'] = Invoice::count(array('conditions' => 'paid_date <= '.$lastday_in_month.' and paid_date >= '.$firstday_in_month.' AND estimate != 1'));
		$this->view_data['invoices_due_this_month'] = Invoice::count(array('conditions' => 'due_date <= '.$lastday_in_month.' and due_date >= '.$firstday_in_month.' AND estimate != 1'));

		switch ($condition) {
			case 'open':
				$this->view_data['invoices'] = Invoice::find('all', array('conditions' => array('status = ? and estimate != ?', 'Open', 1)));
				break;
			case 'sent':
				$this->view_data['invoices'] = Invoice::find('all', array('conditions' => array('status = ? and estimate != ?', 'Sent', 1)));
				break;
			case 'paid':
				$this->view_data['invoices'] = Invoice::find('all', array('conditions' => array('status = ? and estimate != ?', 'Paid', 1)));
				break;
			default:
				$this->view_data['invoices'] = Invoice::find('all', array('conditions' => array('estimate != ?', 1)));
				break;
		}
		
		$this->content_view = 'invoices/all';
	}
	function create()
	{	
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$invoice = Invoice::create($_POST);
			$new_invoice_reference = $_POST['reference']+1;
			
			$invoice_reference = Setting::first();
			$invoice_reference->update_attributes(array('invoice_reference' => $new_invoice_reference));
       		if(!$invoice){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_create_invoice_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_create_invoice_success'));}
			redirect('invoices');
		}else
		{
			$this->view_data['invoices'] = Invoice::all();
			$this->view_data['next_reference'] = Invoice::last();
			//$this->view_data['projects'] = Project::all();
			$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=?','0')));
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_create_invoice');
			$this->view_data['form_action'] = 'invoices/create';
			$this->content_view = 'invoices/_invoice';
		}	
	}	
	function update($id = FALSE, $getview = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			unset($_POST['_wysihtml5_mode']);
			unset($_POST['files']);
			$id = $_POST['id'];
			$view = FALSE;
			if(isset($_POST['view'])){$view = $_POST['view']; }
			unset($_POST['view']);
			if($_POST['status'] == "Paid" && !isset($_POST['paid_date'])){ $_POST['paid_date'] = date('Y-m-d', time());}
			$invoice = Invoice::find($id);
			$invoice->update_attributes($_POST);
			
       		if(!$invoice){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_invoice_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_invoice_success'));}
			if($view == 'true'){redirect('invoices/view/'.$id);}else{redirect('invoices');}
			
		}else
		{
			$this->view_data['invoice'] = Invoice::find($id);
			$this->view_data['projects'] = Project::all();
			$this->view_data['companies'] = Company::find('all',array('conditions' => array('inactive=?','0')));
			if($getview == "view"){$this->view_data['view'] = "true";}
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_invoice');
			$this->view_data['form_action'] = 'invoices/update';
			$this->content_view = 'invoices/_invoice';
		}	
	}	
	
	function view($id = FALSE)
	{

		
		$this->view_data['submenu'] = array(
						$this->lang->line('application_back') => 'invoices',
				 		);	
		
		$this->view_data['invoice'] = Invoice::find($id);
		$data["core_settings"] = Setting::first();
		$invoice = $this->view_data['invoice'];
		$this->view_data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));

		//calculate sum
		$i = 0; $sum = 0;
		foreach ($this->view_data['items'] as $value){
			$sum = $sum+$invoice->invoice_has_items[$i]->amount*$invoice->invoice_has_items[$i]->value; $i++;
		}
		if(substr($invoice->discount, -1) == "%"){ 
			$discount = sprintf("%01.2f", round(($sum/100)*substr($invoice->discount, 0, -1), 2)); 
		}
		else{
			$discount = $invoice->discount;
		}
		$sum = $sum-$discount;

		if($invoice->tax != ""){
			$tax_value = $invoice->tax;
		}else{
			$tax_value = $data["core_settings"]->tax;
		}

		if($invoice->second_tax != ""){
	      $second_tax_value = $invoice->second_tax;
	    }else{
	      $second_tax_value = $data["core_settings"]->second_tax;
	    }

		$tax = sprintf("%01.2f", round(($sum/100)*$tax_value, 2));
		$second_tax = sprintf("%01.2f", round(($sum/100)*$second_tax_value, 2));

    	$sum = sprintf("%01.2f", round($sum+$tax+$second_tax, 2));

		$invoice->sum = $sum;
			$invoice->save();
		$this->content_view = 'invoices/view';
	}
	function banktransfer($id = FALSE, $sum = FALSE){

		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_bank_transfer');
	
		$data["core_settings"] = Setting::first();
		$this->view_data['invoice'] = Invoice::find($id);
		$this->content_view = 'invoices/_banktransfer';
	}
	function stripepay($id = FALSE, $sum = FALSE){
		$data["core_settings"] = Setting::first();

		$stripe_keys = array(
		  "secret_key"      => $data["core_settings"]->stripe_p_key, 
		  "publishable_key" => $data["core_settings"]->stripe_key 
		);


		if($_POST){
			unset($_POST['send']);
			$invoice = Invoice::find($_POST['id']);
			
			// Stores errors:
	$errors = array();
	
	// Need a payment token:
	if (isset($_POST['stripeToken'])) {
		
		$token = $_POST['stripeToken'];
		
		// Check for a duplicate submission, just in case:
		// Uses sessions, you could use a cookie instead.
		if (isset($_SESSION['token']) && ($_SESSION['token'] == $token)) {
			$errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
			$this->session->set_flashdata('message', 'error: You have apparently resubmitted the form. Please do not do that.');
		
		} else { // New submission.
			$_SESSION['token'] = $token;
		}		
		
	} else {
		$this->session->set_flashdata('message', 'error: The order cannot be processed. Please make sure you have JavaScript enabled and try again.');
		$errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
		log_message('error', 'Stripe: ERROR - Payment canceled for invoice #'.$invoice->reference.'.');
			
	}
	
	// Set the order amount somehow:
	$sum_exp = explode('.', $_POST['sum']);
	$amount = $sum_exp[0]*100+$sum_exp[1]; // in cents

	//Get currency
	# PHP ISO currency => name list
	$currency = $invoice->currency;
    $currency_codes = array("AFA"=>"Afghani","AFN"=>"Afghani","ALK"=>"Albanian old lek","ALL"=>"Lek","DZD"=>"Algerian Dinar","USD"=>"US Dollar","ADF"=>"Andorran Franc","ADP"=>"Andorran Peseta","EUR"=>"Euro","AOR"=>"Angolan Kwanza Readjustado","AON"=>"Angolan New Kwanza","AOA"=>"Kwanza","XCD"=>"East Caribbean Dollar","ARA"=>"Argentine austral","ARS"=>"Argentine Peso","ARL"=>"Argentine peso ley","ARM"=>"Argentine peso moneda nacional","ARP"=>"Peso argentino","AMD"=>"Armenian Dram","AWG"=>"Aruban Guilder","AUD"=>"Australian Dollar","ATS"=>"Austrian Schilling","AZM"=>"Azerbaijani manat","AZN"=>"Azerbaijanian Manat","BSD"=>"Bahamian Dollar","BHD"=>"Bahraini Dinar","BDT"=>"Taka","BBD"=>"Barbados Dollar","BYR"=>"Belarussian Ruble","BEC"=>"Belgian Franc (convertible)","BEF"=>"Belgian Franc (currency union with LUF)","BEL"=>"Belgian Franc (financial)","BZD"=>"Belize Dollar","XOF"=>"CFA Franc BCEAO","BMD"=>"Bermudian Dollar","INR"=>"Indian Rupee","BTN"=>"Ngultrum","BOP"=>"Bolivian peso","BOB"=>"Boliviano","BOV"=>"Mvdol","BAM"=>"Convertible Marks","BWP"=>"Pula","NOK"=>"Norwegian Krone","BRC"=>"Brazilian cruzado","BRB"=>"Brazilian cruzeiro","BRL"=>"Brazilian Real","BND"=>"Brunei Dollar","BGN"=>"Bulgarian Lev","BGJ"=>"Bulgarian lev A/52","BGK"=>"Bulgarian lev A/62","BGL"=>"Bulgarian lev A/99","BIF"=>"Burundi Franc","KHR"=>"Riel","XAF"=>"CFA Franc BEAC","CAD"=>"Canadian Dollar","CVE"=>"Cape Verde Escudo","KYD"=>"Cayman Islands Dollar","CLP"=>"Chilean Peso","CLF"=>"Unidades de fomento","CNX"=>"Chinese People's Bank dollar","CNY"=>"Yuan Renminbi","COP"=>"Colombian Peso","COU"=>"Unidad de Valor real","KMF"=>"Comoro Franc","CDF"=>"Franc Congolais","NZD"=>"New Zealand Dollar","CRC"=>"Costa Rican Colon","HRK"=>"Croatian Kuna","CUP"=>"Cuban Peso","CYP"=>"Cyprus Pound","CZK"=>"Czech Koruna","CSK"=>"Czechoslovak koruna","CSJ"=>"Czechoslovak koruna A/53","DKK"=>"Danish Krone","DJF"=>"Djibouti Franc","DOP"=>"Dominican Peso","ECS"=>"Ecuador sucre","EGP"=>"Egyptian Pound","SVC"=>"Salvadoran colón","EQE"=>"Equatorial Guinean ekwele","ERN"=>"Nakfa","EEK"=>"Kroon","ETB"=>"Ethiopian Birr","FKP"=>"Falkland Island Pound","FJD"=>"Fiji Dollar","FIM"=>"Finnish Markka","FRF"=>"French Franc","XFO"=>"Gold-Franc","XPF"=>"CFP Franc","GMD"=>"Dalasi","GEL"=>"Lari","DDM"=>"East German Mark of the GDR (East Germany)","DEM"=>"Deutsche Mark","GHS"=>"Ghana Cedi","GHC"=>"Ghanaian cedi","GIP"=>"Gibraltar Pound","GRD"=>"Greek Drachma","GTQ"=>"Quetzal","GNF"=>"Guinea Franc","GNE"=>"Guinean syli","GWP"=>"Guinea-Bissau Peso","GYD"=>"Guyana Dollar","HTG"=>"Gourde","HNL"=>"Lempira","HKD"=>"Hong Kong Dollar","HUF"=>"Forint","ISK"=>"Iceland Krona","ISJ"=>"Icelandic old krona","IDR"=>"Rupiah","IRR"=>"Iranian Rial","IQD"=>"Iraqi Dinar","IEP"=>"Irish Pound (Punt in Irish language)","ILP"=>"Israeli lira","ILR"=>"Israeli old sheqel","ILS"=>"New Israeli Sheqel","ITL"=>"Italian Lira","JMD"=>"Jamaican Dollar","JPY"=>"Yen","JOD"=>"Jordanian Dinar","KZT"=>"Tenge","KES"=>"Kenyan Shilling","KPW"=>"North Korean Won","KRW"=>"Won","KWD"=>"Kuwaiti Dinar","KGS"=>"Som","LAK"=>"Kip","LAJ"=>"Lao kip","LVL"=>"Latvian Lats","LBP"=>"Lebanese Pound","LSL"=>"Loti","ZAR"=>"Rand","LRD"=>"Liberian Dollar","LYD"=>"Libyan Dinar","CHF"=>"Swiss Franc","LTL"=>"Lithuanian Litas","LUF"=>"Luxembourg Franc (currency union with BEF)","MOP"=>"Pataca","MKD"=>"Denar","MKN"=>"Former Yugoslav Republic of Macedonia denar A/93","MGA"=>"Malagasy Ariary","MGF"=>"Malagasy franc","MWK"=>"Kwacha","MYR"=>"Malaysian Ringgit","MVQ"=>"Maldive rupee","MVR"=>"Rufiyaa","MAF"=>"Mali franc","MTL"=>"Maltese Lira","MRO"=>"Ouguiya","MUR"=>"Mauritius Rupee","MXN"=>"Mexican Peso","MXP"=>"Mexican peso","MXV"=>"Mexican Unidad de Inversion (UDI)","MDL"=>"Moldovan Leu","MCF"=>"Monegasque franc (currency union with FRF)","MNT"=>"Tugrik","MAD"=>"Moroccan Dirham","MZN"=>"Metical","MZM"=>"Mozambican metical","MMK"=>"Kyat","NAD"=>"Namibia Dollar","NPR"=>"Nepalese Rupee","NLG"=>"Netherlands Guilder","ANG"=>"Netherlands Antillian Guilder","NIO"=>"Cordoba Oro","NGN"=>"Naira","OMR"=>"Rial Omani","PKR"=>"Pakistan Rupee","PAB"=>"Balboa","PGK"=>"Kina","PYG"=>"Guarani","YDD"=>"South Yemeni dinar","PEN"=>"Nuevo Sol","PEI"=>"Peruvian inti","PEH"=>"Peruvian sol","PHP"=>"Philippine Peso","PLZ"=>"Polish zloty A/94","PLN"=>"Zloty","PTE"=>"Portuguese Escudo","TPE"=>"Portuguese Timorese escudo","QAR"=>"Qatari Rial","RON"=>"New Leu","ROL"=>"Romanian leu A/05","ROK"=>"Romanian leu A/52","RUB"=>"Russian Ruble","RWF"=>"Rwanda Franc","SHP"=>"Saint Helena Pound","WST"=>"Tala","STD"=>"Dobra","SAR"=>"Saudi Riyal","RSD"=>"Serbian Dinar","CSD"=>"Serbian Dinar","SCR"=>"Seychelles Rupee","SLL"=>"Leone","SGD"=>"Singapore Dollar","SKK"=>"Slovak Koruna","SIT"=>"Slovenian Tolar","SBD"=>"Solomon Islands Dollar","SOS"=>"Somali Shilling","ZAL"=>"South African financial rand (Funds code) (discont","ESP"=>"Spanish Peseta","ESA"=>"Spanish peseta (account A)","ESB"=>"Spanish peseta (account B)","LKR"=>"Sri Lanka Rupee","SDD"=>"Sudanese Dinar","SDP"=>"Sudanese Pound","SDG"=>"Sudanese Pound","SRD"=>"Surinam Dollar","SRG"=>"Suriname guilder","SZL"=>"Lilangeni","SEK"=>"Swedish Krona","CHE"=>"WIR Euro","CHW"=>"WIR Franc","SYP"=>"Syrian Pound","TWD"=>"New Taiwan Dollar","TJS"=>"Somoni","TJR"=>"Tajikistan ruble","TZS"=>"Tanzanian Shilling","THB"=>"Baht","TOP"=>"Pa'anga","TTD"=>"Trinidata and Tobago Dollar","TND"=>"Tunisian Dinar","TRY"=>"New Turkish Lira","TRL"=>"Turkish lira A/05","TMM"=>"Manat","RUR"=>"Russian rubleA/97","SUR"=>"Soviet Union ruble","UGX"=>"Uganda Shilling","UGS"=>"Ugandan shilling A/87","UAH"=>"Hryvnia","UAK"=>"Ukrainian karbovanets","AED"=>"UAE Dirham","GBP"=>"Pound Sterling","USN"=>"US Dollar (Next Day)","USS"=>"US Dollar (Same Day)","UYU"=>"Peso Uruguayo","UYN"=>"Uruguay old peso","UYI"=>"Uruguay Peso en Unidades Indexadas","UZS"=>"Uzbekistan Sum","VUV"=>"Vatu","VEF"=>"Bolivar Fuerte","VEB"=>"Venezuelan Bolivar","VND"=>"Dong","VNC"=>"Vietnamese old dong","YER"=>"Yemeni Rial","YUD"=>"Yugoslav Dinar","YUM"=>"Yugoslav dinar (new)","ZRN"=>"Zairean New Zaire","ZRZ"=>"Zairean Zaire","ZMK"=>"Kwacha","ZWD"=>"Zimbabwe Dollar","ZWC"=>"Zimbabwe Rhodesian dollar");
	if(!array_key_exists($currency, $currency_codes)){
		$currency = $data["core_settings"]->stripe_currency;
	}
	


	// Validate other form data!

	// If no errors, process the order:
	if (empty($errors)) {
		
		// create the charge on Stripe's servers - this will charge the user's card
		try {
			
			// Include the Stripe library:
			$this->load->file(APPPATH.'helpers/stripe/lib/Stripe.php', true);

			// set your secret key: remember to change this to your live secret key in production
			// see your keys here https://manage.stripe.com/account
			Stripe::setApiKey($stripe_keys["secret_key"]);

			// Charge the order:
			$charge = Stripe_Charge::create(array(
				"amount" => $amount, // amount in cents, again
				"currency" => $currency,
				"card" => $token,
				"receipt_email" => $invoice->company->client->email,
				"description" => $invoice->reference,

				
				)
			);

			// Check that it was paid:
			if ($charge->paid == true) {
				$attr= array();
				$paid_date = date('Y-m-d', time());
				
				$invoice->update_attributes(array('paid_date' => $paid_date, 'status' => 'Paid'));
				$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_payment_complete'));
				log_message('error', 'Stripe: Payment for Invoice #'.$invoice->reference.' successfully made');
			} else { // Charge was not paid!	
				$this->session->set_flashdata('message', 'error: Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction.');
				log_message('error', 'Stripe: ERROR - Payment for Invoice #'.$invoice->reference.' was not successful!');
				}
			
		} catch (Stripe_CardError $e) {
		    // Card was declined.
			$e_json = $e->getJsonBody();
			$err = $e_json['error'];
			$errors['stripe'] = $err['message'];
			$this->session->set_flashdata('message', 'error: Card was declined!');
			log_message('error', 'Stripe: ERROR - Credit Card was declined by Stripe! Payment process canceled for invoice #'.$invoice->reference.'.');
		
		} catch (Stripe_ApiConnectionError $e) {
		    // Network problem, perhaps try again.
		} catch (Stripe_InvalidRequestError $e) {

		} catch (Stripe_ApiError $e) {
		    // Stripe's servers are down!
		} catch (Stripe_CardError $e) {
		    // Something else that's not the customer's fault.
		}

	}else{
		
		$this->session->set_flashdata('message', 'error: '.$errors["token"]);
		log_message('error', 'Stripe: '.$errors["token"]);
		
	} 



			redirect('invoices/view/'.$_POST['id']);
		}else
		{
			$this->view_data['invoices'] = Invoice::find_by_id($id);
			
			$this->view_data['public_key'] = $data["core_settings"]->stripe_key;
			$this->view_data['sum'] = $sum;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_pay_with_credit_card');
			$this->view_data['form_action'] = 'invoices/stripepay';
			$this->content_view = 'invoices/_stripe';
		}

	}
	function delete($id = FALSE)
	{	
		$invoice = Invoice::find($id);
		$invoice->delete();
		$this->content_view = 'invoices/all';
		if(!$invoice){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_invoice_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_invoice_success'));}
			redirect('invoices');
	}
	function preview($id = FALSE){
     $this->load->helper(array('dompdf', 'file')); 
     $this->load->library('parser');
     $data["invoice"] = Invoice::find($id); 
     $data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));
     $data["core_settings"] = Setting::first();
   
     $due_date = date($data["core_settings"]->date_format, human_to_unix($data["invoice"]->due_date.' 00:00:00'));  
     $parse_data = array(
            					'due_date' => $due_date,
            					'invoice_id' => $data["invoice"]->reference,
            					'client_link' => $data["core_settings"]->domain,
            					'company' => $data["core_settings"]->company,
            					);
  	$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->invoice_pdf_template, $data, true); 
  	$html = $this->parser->parse_string($html, $parse_data);
     
     $filename = $this->lang->line('application_invoice').'_'.$data["invoice"]->reference;
     pdf_create($html, $filename, TRUE);
	}
	function previewHTML($id = FALSE){
     $this->load->helper(array('file')); 
     $this->load->library('parser');
     $data["htmlPreview"] = true;
     $data["invoice"] = Invoice::find($id); 
     $data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));
     $data["core_settings"] = Setting::first();
   
     $due_date = date($data["core_settings"]->date_format, human_to_unix($data["invoice"]->due_date.' 00:00:00'));  
     $parse_data = array(
            					'due_date' => $due_date,
            					'invoice_id' => $data["invoice"]->reference,
            					'client_link' => $data["core_settings"]->domain,
            					'company' => $data["core_settings"]->company,
            					);
  	$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->invoice_pdf_template, $data, true); 
  	$html = $this->parser->parse_string($html, $parse_data);
     $this->theme_view = 'blank';
	$this->content_view = 'invoices/_preview';
	}
	function sendinvoice($id = FALSE){
			$this->load->helper(array('dompdf', 'file'));
			$this->load->library('parser');

			$data["invoice"] = Invoice::find($id); 
			$data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));
     		$data["core_settings"] = Setting::first();
    		$due_date = date($data["core_settings"]->date_format, human_to_unix($data["invoice"]->due_date.' 00:00:00')); 
  			//Set parse values
  			$parse_data = array(
            					'client_contact' => $data["invoice"]->company->client->firstname.' '.$data["invoice"]->company->client->lastname,
            					'client_company' => $data["invoice"]->company->name,
            					'due_date' => $due_date,
            					'invoice_id' => $data["invoice"]->reference,
            					'client_link' => $data["core_settings"]->domain,
            					'company' => $data["core_settings"]->company,
            					'logo' => '<img src="'.base_url().''.$data["core_settings"]->logo.'" alt="'.$data["core_settings"]->company.'"/>',
            					'invoice_logo' => '<img src="'.base_url().''.$data["core_settings"]->invoice_logo.'" alt="'.$data["core_settings"]->company.'"/>'
            					);
            // Generate PDF     
  			$html = $this->load->view($data["core_settings"]->template. '/' .$data["core_settings"]->invoice_pdf_template, $data, true); 
    		$html = $this->parser->parse_string($html, $parse_data);
    		$filename = $this->lang->line('application_invoice').'_'.$data["invoice"]->reference;
     		pdf_create($html, $filename, FALSE);
     		//email
     		$subject = $this->parser->parse_string($data["core_settings"]->invoice_mail_subject, $parse_data);
			$this->email->from($data["core_settings"]->email, $data["core_settings"]->company);
			if(!isset($data["invoice"]->company->client->email)){
				$this->session->set_flashdata('message', 'error:This client company has no primary contact! Just add a primary contact.');
				redirect('invoices/view/'.$id);
			}
			$this->email->to($data["invoice"]->company->client->email); 
			$this->email->subject($subject); 
  			$this->email->attach("files/temp/".$filename.".pdf");
  			


  			$email_invoice = read_file('./application/views/'.$data["core_settings"]->template.'/templates/email_invoice.html');
  			$message = $this->parser->parse_string($email_invoice, $parse_data);
			$this->email->message($message);			
			if($this->email->send()){$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_send_invoice_success'));
			$data["invoice"]->update_attributes(array('status' => 'Sent', 'sent_date' => date("Y-m-d")));
			log_message('error', 'Invoice #'.$data["invoice"]->reference.' has been send to '.$data["invoice"]->company->client->email);
			}
       		else{$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_send_invoice_error'));
       		log_message('error', 'ERROR: Invoice #'.$data["invoice"]->reference.' has not been send to '.$data["invoice"]->company->client->email.'. Please check your servers email settings.');
       		}
			unlink("files/temp/".$filename.".pdf");
			redirect('invoices/view/'.$id);
	}
	function item($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			if($_POST['name'] != ""){
				$_POST['name'] = $_POST['name'];
				$_POST['value'] = $_POST['value'];
				$_POST['type'] = $_POST['type'];
			}else{
				if($_POST['item_id'] == "-"){
					$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_add_item_error'));
					redirect('invoices/view/'.$_POST['invoice_id']);

				}else{
					$rebill = explode("_", $_POST['item_id']);
					if($rebill[0] == "rebill"){
						$itemvalue = Expense::find_by_id($rebill[1]);
						$_POST['name'] = $itemvalue->description;
						$_POST['type'] = $_POST['item_id'];
						$_POST['value'] = $itemvalue->value;
						$itemvalue->rebill = 2;
						$itemvalue->invoice_id = $_POST['invoice_id'];
						$itemvalue->save();
					}else{
						$itemvalue = Item::find_by_id($_POST['item_id']);
						$_POST['name'] = $itemvalue->name;
						$_POST['type'] = $itemvalue->type;
						$_POST['value'] = $itemvalue->value;
					}
				
				}
			}

			$item = InvoiceHasItem::create($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_add_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_add_item_success'));}
			redirect('invoices/view/'.$_POST['invoice_id']);
			
		}else
		{
			$this->view_data['invoice'] = Invoice::find($id);
			$this->view_data['items'] = Item::find('all',array('conditions' => array('inactive=?','0')));
			$this->view_data['rebill'] = Expense::find('all',array('conditions' => array('project_id=? and (rebill=? or invoice_id=?)',$this->view_data['invoice']->project_id, 1, $id)));
	

			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_add_item');
			$this->view_data['form_action'] = 'invoices/item';
			$this->content_view = 'invoices/_item';
		}	
	}	
	function item_update($id = FALSE)
	{	
		if($_POST){
			unset($_POST['send']);
			$_POST = array_map('htmlspecialchars', $_POST);
			$item = InvoiceHasItem::find($_POST['id']);
			$item = $item->update_attributes($_POST);
       		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_save_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_save_item_success'));}
			redirect('invoices/view/'.$_POST['invoice_id']);
			
		}else
		{
			$this->view_data['invoice_has_items'] = InvoiceHasItem::find($id);
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_edit_item');
			$this->view_data['form_action'] = 'invoices/item_update';
			$this->content_view = 'invoices/_item';
		}	
	}	
	function item_delete($id = FALSE, $invoice_id = FALSE)
	{	
		$item = InvoiceHasItem::find($id);
		$item->delete();
		$this->content_view = 'invoices/view';
		if(!$item){$this->session->set_flashdata('message', 'error:'.$this->lang->line('messages_delete_item_error'));}
       		else{$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_delete_item_success'));}
			redirect('invoices/view/'.$invoice_id);
	}	
	
}