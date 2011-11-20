<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
 
//TODO: Fix trust_render in defaultCall(); 
define('_OPENID_SERVER_CLASS_', 'server');
		
class ServerController extends Object
{
	private $server_url = "http://izportal.com/@openid/server/";

	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function defaultCall()
	{
		$xrds_path = config('root.url')."@xrds.openid/server/idpXrds";
		//header('X-XRDS-Location: '.$xrds_path);
		config('.html.header', array('X-XRDS-Location'=>$xrds_path));
		
		$store = $this->getOpenIDStore();
		$op_endpoint = config('root.url')."@openid/server/";//$this->buildUrl();
		
		$server = & $this->getManager(_OPENID_SERVER_CLASS_)->getServer($store, $op_endpoint);
		
		$method = $_SERVER['REQUEST_METHOD'];
		$request = null;
		
		if ($method == 'GET') {
        	$request = $_GET;
		} else {
			$request = $_POST;
		}
		
		$request = $server->decodeRequest();
		
		
		if (!$request){	
			return $this->about();
		}
		
		$this->getManager(_OPENID_SERVER_CLASS_)->setRequestInfo($request);
		
		if (in_array($request->mode, array('checkid_immediate', 'checkid_setup'))){
			
			if ($request->idSelect()) {
				//perform IDP-driven indentifier selection
				if($request->mode == 'checkid_immediate'){
					$response = & $request->answer(false);
				} else {
					return $this->trust($request);
				}
			} else if ((!$request->identity) && (!$request->idSelect())){
				//no identifier used or desired, display a page saying so
				return $this->noidentifier();
			} else if ($request->immediate) {
				$response =& $request->answer(false, $this->buildUrl());
			} else {
				if (!$this->getManager(_OPENID_SERVER_CLASS_)->getLoggedInUser()){
					return $this->login();					
				}
				
				return $this->trust($request);
			}
		} else {
			$response  = & $server->handleRequest($request);
		}
		
		$webresponse = & $server->encodeResponse($response);
		
		if ($webresponse->code != AUTH_OPENID_HTTP_OK) {
			header(sprintf("HTTP/1.1 %d ", $webresponse->code), true, $webresponse->code);
		}

		foreach ($webresponse->headers as $k => $v) {
			header("$k: $v");
		}
		
		header('Connection: close');
		print $webresponse->body;
		exit(0);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function about()
	{
		$render = $this->getTemplate('server_about');
		$render->setServerUrl($this->buildUrl());
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function me($indentity = null)
	{
		$xrds_path = config('root.url')."@xrds.openid/server/userXrds";
		//header('X-XRDS-Location: '.$xrds_path);	
		config('.html.tag', array('<link rel="openid2.provider openid.server" href="'.$this->buildUrl().'"/>'));
		config('.html.header', array('X-XRDS-Location'=>$xrds_path, 'Connection'=>'close'));
		config('.html.meta', array(array('http-equiv'=>'X-XRDS-Location', 'content'=>$xrds_path)));
		
		$render = $this->getTemplate('server_idpage');
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function login($error = null)
	{
		$render = $this->getTemplate('server_login');
		$render->setError($error);
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function logout()
	{
		$this->getManager(_OPENID_SERVER_CLASS_)->setLoggedInUser(null);
		$this->getManager(_OPENID_SERVER_CLASS_)->setRequestInfo(null);
		return $this->cancelAuth(null);
	}
	/**
	 * Login parameters check
	 *
	 * @param string $input 
	 * @return Array
	 * @author Thanh H. Nguyen
	 */
	private function loginCheckInput($input)
	{
		$openid_url = false;
		$errors = array();

		if (!isset($input['openid_url'])) {
			$errors[] = 'Enter an OpenID URL to continue';
		}
		if (count($errors) == 0) {
			$openid_url = $input['openid_url'];
		}
		return array($errors, $openid_url);
	}

	/**
	 * Process POST value from login page
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function loginId()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		
		switch ($method) {
			case 'GET':
				# if the function is call thru GET, redirect back to login
				return $this->login();
			case 'POST':
				# get the request info
				$info = $this->getManager(_OPENID_SERVER_CLASS_)->getRequestInfo();
				# get the summitted POST value
				$fields = $_POST;
				# if Cancel button is pressed, cancel authorization process
				if (isset($fields['cancel'])) {
					return $this->cancelAuth($info);
				}
				# check the input POST value
				list ($errors, $openid_url) = $this->loginCheckInput($fields);
				
				if (count($errors) || !$openid_url) {
					# if there are errors or no openid_url
					$needed = $info ? $info->identity : false;
					$error = object('error');
					$error->setMessage('There is error, check again!');
					//return login_render($errors, @$fields['openid_url'], $needed);
					return $this->login($error);
				} else {
					# if there is no error && there is openid_url, continue to do authorization
					$this->getManager(_OPENID_SERVER_CLASS_)->setLoggedInUser($openid_url);
					return $this->doAuth($info);
				}
			default:
				return $this->login();
		}
	}
	/**
	 * Display dialog to confirm & trust ID with consumer
	 *
	 * @param string $info 
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	public function trust($info = null)
	{
		if (@$_POST['postback']){
			# if the form is submitted
			$info = $this->getManager(_OPENID_SERVER_CLASS_)->getRequestInfo();
			# whether site should be trusted;
			$trusted = isset($_POST['trust']);
			# 3rd param (fail_cancels) set to true so that if not trusted it will be cancelled
			return $this->doAuth($info, $trusted, true, @$_POST['idSelect']);
			# public function doAuth($info, $trusted=null, $fail_cancels=false, $idpSelect=null)
		}
		//present the trust form
		$render = $this->getTemplate('server_trust');
		$render->setUsername($this->getManager(_OPENID_SERVER_CLASS_)->getLoggedInUser());
		$render->setIdentity($this->idUrl($this->getManager(_OPENID_SERVER_CLASS_)->getLoggedInUser()));
		$render->setTrustRoot(htmlspecialchars($info->trust_root));
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function idpXrds()
	{
		//header('Content-type: application/xrds+xml');
		//config('.layout.template','nolayout');
		//header( "Content-type: text/plain; charset=utf-8" );
		$xrd = object('xrds');
		$service = object('service');
		$service->setType(Auth_OpenID_TYPE_2_0_IDP);
		$service->setUri(config('root.url').'@openid/server/');
		$xrd->setIzparamsservice(array('priority'=>'0'));
		$xrd->setService($service);
		
		
		$render = $this->getTemplate('server_idpxrds');
		
		$render->setXrd($xrd);
		//$render->setType(Auth_OpenID_TYPE_2_0_IDP);
		//$render->setUri($this->buildUrl());
		//print_r($render);
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function userXrds($identity = null)
	{
		$xrd = object('xrds');
		$service = object('service');
		$service->setType(Auth_OpenID_TYPE_2_0);
		$service->setUri(config('root.url').'@openid/server/');
		$xrd->setIzparamsservice(array('priority'=>'0'));
		$xrd->setService($service);
		
		$render = $this->getTemplate('server_usrxrds');
		$render->setXrd($xrd);
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function cancelAuth($info)
	{
		if($info){
			$this->getManager(_OPENID_SERVER_CLASS_)->setRequestInfo($info);
			$url = $info->getCancelUrl();
		}else{
			$url = $this->getServerUrl();
		}
		header('HTTP/1.1 302 Found');
		header('Content-Type: text/plain; charset=utf-8');
		header('Connection: close');
		header('Location: '.$url);
		//Event::fire('redirect', $url);
	}
	
	/**
	 * undocumented function
	 *
	 * @return Render
	 * @author user
	 **/
	public function doAuth($info, $trusted=null, $fail_cancels=false, $idpSelect=null)
	{
		if (!$info){
			// there is no authentication information, so bail
			$this->cancelAuth(null);
		}
		//Is the identifier to be selected by the IDP?
		
		if ($info->idSelect()){
			if($idpSelect){
				$req_url = $this->idUrl($idpSelect);
			}else{
				$trusted = false;
			}
		}else{
			$req_url = $info->identity;
		}
		
		$user = $this->getManager(_OPENID_SERVER_CLASS_)->getLoggedInUser();					//return user session or false
		$this->getManager(_OPENID_SERVER_CLASS_)->setRequestInfo($info);
		
		if ((!$info->idSelect()) && ($req_url != $this->idUrl($user))){
			//return login_render
			//echo 'hi';
			return $this->login();
		}
		
		$trust_root = $info->trust_root;
		
		if ($trusted){
			
			$server = & $this->getManager(_OPENID_SERVER_CLASS_)->getServer($this->getOpenIDStore(), $this->buildUrl());
			$response = & $info->answer(true, null, $req_url);
			
			// Answer with some sample Simple Registration data.
			$sreg_data = array(
							'fullname' => 'Example User',
							'nickname' => 'example',
							'dob' => '1970-01-01',
							'email' => 'invalid@example.com',
							'gender' => 'F',
							'postcode' => '12345',
							'country' => 'ES',
							'language' => 'eu',
							'timezone' => 'America/New_York'); 
			// Add the simple registration response values to the OpenID
			// response message.
			$sreg_request = Auth_OpenID_SRegRequest::fromOpenIDRequest($info);
			$sreg_response = Auth_OpenID_SRegResponse::extractResponse($sreg_request, $sreg_data);
			$sreg_response->toMessage($response->fields);
			// Generate a response to send to the user agent.
			$webresponse =& $server->encodeResponse($response);
			$new_headers = array();
			foreach ($webresponse->headers as $k => $v) {
				$new_headers[] = $k.": ".$v;
				header($k.": ".$v);
			}
			//config('.html.header', $webresponse->headers);
			print $webresponse->body; exit(0);
			return array($new_headers, $webresponse->body);
			
		} else if ($fail_cancels){
			return $this->cancelAuth($info);
		} else {
			// return trust_render
			return $this->trust($info); 
		}
		
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	private function getServerUrl()
	{
		return config('root.uri');
	}
	
	/**
	 * Form the url for an identity
	 *
	 * @param string $identity 
	 * @return string
	 * @author Thanh H. Nguyen
	 */
	private function idUrl($identity)
	{
		return $this->buildUrl("me/{$identity}");
	}
	
	/**
	 * Get OpenID Mysql Store
	 *
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	private function getOpenIDStore()
	{
	    //import('core.lib.openid.auth.openid.MySQLStore');
    	require_once 'DB.php';

		$dsn = array(
				'phptype'  => 'mysql',
				'username' => 'root',
				'password' => 'root',
				'hostspec' => 'localhost'
				);

		$db =& DB::connect($dsn);

		if (PEAR::isError($db)) {
			return null;
		}
		
		$db->query("USE izopenid");
		$s =& new Auth_OpenID_MySQLStore($db);
		$s->createTables();
		return $s;
	}
	/**
	 * Build the url for an action
	 *
	 * @param string $action 
	 * @param string $escaped 
	 * @return void
	 * @author Thanh H. Nguyen
	 */
	private function buildUrl($action = null, $escaped = true)
	{
		# form base url
		$url = $this->getServerUrl().'server/';
		# if action is specified, form the action url
		if ($action) $url .= $action;
		# return with escaped characters?
		return $escaped ? htmlspecialchars($url, ENT_QUOTES) : $url;
	}	
} // END class 
?>