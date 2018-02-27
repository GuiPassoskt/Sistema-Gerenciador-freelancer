<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cInvoices extends MY_Controller {
               
	function __construct()
	{
		parent::__construct();
		$access = FALSE;
		if($this->client){	
			foreach ($this->view_data['menu'] as $key => $value) { 
				if($value->link == "cinvoices"){ $access = TRUE;}
			}
			if(!$access){redirect('login');}
		}elseif($this->user){
				redirect('invoices');
		}else{

			redirect('login');
		}
		$this->view_data['submenu'] = array(
				 		$this->lang->line('application_all_invoices') => 'cinvoices',
				 		);	
		
	}	
	function index()
	{
		$this->view_data['invoices'] = Invoice::find('all',array('conditions' => array('company_id=? AND estimate != ? AND issue_date<=?',$this->client->company->id,1,date('Y-m-d', time()))));
		$this->content_view = 'invoices/client_views/all';
	}

	function view($id = FALSE)
	{
		$this->view_data['submenu'] = array(
						$this->lang->line('application_back') => 'invoices',
				 		);	
		$this->view_data['invoice'] = Invoice::find($id);
		$this->view_data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));
		if($this->view_data['invoice']->company_id != $this->client->company->id){ redirect('cinvoices');}
		$this->content_view = 'invoices/client_views/view';
	}
	function download($id = FALSE){
     $this->load->helper(array('dompdf', 'file')); 
     $this->load->library('parser');
     $data["invoice"] = Invoice::find($id); 
     $data['items'] = InvoiceHasItem::find('all',array('conditions' => array('invoice_id=?',$id)));
     if($data['invoice']->company_id != $this->client->company->id){ redirect('cinvoices');}
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
     $filename = 'Invoice_'.$data["invoice"]->reference;
     pdf_create($html, $filename, TRUE);
       
	}
	function banktransfer($id = FALSE, $sum = FALSE){

		$this->theme_view = 'modal';
		$this->view_data['title'] = $this->lang->line('application_bank_transfer');
	
		$data["core_settings"] = Setting::first();
		$this->view_data['invoice'] = Invoice::find($id);
		$this->content_view = 'invoices/client_views/_banktransfer';
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

			// set your secret key
			// see your keys here https://manage.stripe.com/account
			Stripe::setApiKey($stripe_keys["secret_key"]);

			// Charge the order:
			$charge = Stripe_Charge::create(array(
				"amount" => $amount, // amount in cents, again
				"currency" => $currency,
				"card" => $token,
				"description" => $invoice->reference
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



			redirect('cinvoices/view/'.$_POST['id']);
		}else
		{
			$this->view_data['invoices'] = Invoice::find_by_id($id);
			
			$this->view_data['public_key'] = $data["core_settings"]->stripe_key;
			$this->view_data['sum'] = $sum;
			$this->theme_view = 'modal';
			$this->view_data['title'] = $this->lang->line('application_pay_with_credit_card');
			$this->view_data['form_action'] = 'cinvoices/stripepay';
			$this->content_view = 'invoices/_stripe';
		}

	}
		function success($id = FALSE){
		$this->session->set_flashdata('message', 'success:'.$this->lang->line('messages_payment_success'));
		redirect('cinvoices/view/'.$id);
	}

	
	
}