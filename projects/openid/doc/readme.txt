***
## OPENID STEPS ####
***
1. User enters their OpenID.
2. Server checks to see if the OpenID is a delegate, if so, it finds the source OpenID server and (red)irects the user as appropriate (i.e. to login and to allow access).
3. The OpenID will (red)irect the user back to our server††
4. Our server will now run a callback to the OpenID server which authenticates the whole process.
5. If the OpenID responds with 'ok', we'll proceed, otherwise, there was some problem with the log in process.

***
## IZFRAMEWORK OPENID PROCESSING FLOW ##
***

*	**Goto <http://izportal.com/@openid/consumer/>**
	>	This is the testing place for IZPORTAL OPENID Consumer

*	**Enter OpenID url: <http://izportal.com/@openid/server/me/thanhnh>**
	>	Specify OpenID Server Identifier

*	**Form is submitted to Consumber/tryAuth**
	*	Initialize consumer
	*	If OpenID2: generate form and attach submit to document.onLoad
		>	A form is created and loaded with information to submit to the OpenID server thru Javascript Redirection
		
		*	submit to <http://izportal.com/@openid/server/>
			*	call Server/defaultCall()
				*	xrds\_path = <http://izportal.com/@xrds.openid/server/idpXrds>
				*	config('.html.header', array('X-XRDS-Location'=>$xrds\_path));
				*	get OpenID store (here using file system)
				*	get openid endpoint $op\_endpoint = <http://izportal.com/@openid/server>
				*	get $server
				*	$request = $server->decodeRequest()
					>	if ($request), set $\_SESSION["request"] = $request
				
					$request is Auth\_OpenID\_CheckIDRequest\_
				
					>	*	verifyReturnTo = "Auth\_OpenID\_verifyReturnTo"
					>	*	mode = "checkid\_setup"
					>	*	immediate = false
					>	*	trust\_root = "<http://izportal.com/@openid/consumer/>"
					>	*	namespace = "http://specs.openid.com/auth/2.0/"
					>	*	assoc\_handle = "\{HMAC-SHA1\}..."
					>	*	identity = "<http://izportal.com/@openid/server/me/thanhnh>"
					>	*	claimed\_id = "<http://izportal.com/@openid/server/me/thanhnh>"
					>	*	return\_to = "<http://izportal.com/@openid/consumer/finishAuth/?janrain\_nonce=...>"
					>	*	server (Auth\_OpenID\_Server)
					>	*	message (Auth\_OpenID\_Message)
				
				*	Check if mode == checkid\_setup (true)
				
					*	If $request->idSelect():
						>	Doing what???
					
					*	else if ((!$request->identity) && (!$request->idSelect()))
						>	No identifier used or desired, display page saying so
					
					*	else if ($request->immediate) {
						>	Doing what???
					
					*	else
						>	[This will be executed!!!]
						
						*	Get Loggined User, if failed, return ServerController->login()
						*	Otherwise, **display form for trusting consumer** to use loggedin information

*	**A log-in form is displayed**
	>	Form will be submitted to <http://izportal.com/@openid/server/loginId>
	
*	**User enter username/password**
	>	If form is not POST, redirect back to login
	
	*	Get the request **info** (from the session **$\_SESSION["request"]**)
	*	Check if Cancel is pressed => call **server/cancelAuth**
	*	Authorizae POST value server/loginCheckInput($fields)
		>	TODO: currently no check on username, just authorize everything user input
			This should be changed to validate with the account database
		
		*	If no username or there is errors, redirect to login()
		*	Otherwise, if logged-in successfully, we **setLoggedInUser($username)**, and call **doAuth($info)** method
*	**doAuth($info) method**
	*	If no $info, then **cancelAuth()**
	*	Check
		
		if ($info->idSelect()){			
			if($idpSelect){				
				$req_url = $this->idUrl($idpSelect);				
			}else{				
				$trusted = false;				
			}			
		}else{			
			$req_url = $info->identity;			
		}

	*	get the user session **getLoggedInUser()**
	*	set the request info to session **$\_SESSION["request"]**
	*	Check if the openid url the user request is actually correct with the supplied username $req\_url == $this->idUrl($user)
	*	If OK, then display Trust form to trust the Consumer (call **$this->trust()**)
*	**Trusting Consumer**
	*	Form is submitted to <http://izportal.com/@openid/server/trust/>
	*	If trust, **doAuth($info, $trusted = true, $fail\_cancels = true, $idpSelect = null)**
		>	Server send authorization along with Simple Registration data SREG
	
	
	