<?php
/**
 * undocumented class
 *
 * @package default
 * @author user
 **/
define('_OPENID_CONSUMER_CLASS_', 'consumer');
  
class ConsumerController extends Object
{
	private $pape_policy_uris = array(
				PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
				PAPE_AUTH_MULTI_FACTOR,
				PAPE_AUTH_PHISHING_RESISTANT
			  );
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function defaultCall()
	{
		return $this->login();
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function login($error = null)
	{
		$render = $this->getTemplate('consumer_login');
		$render->setPapePolicyUri($this->pape_policy_uris);
		$render->setError($error);
		return $render;
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function finishAuth()
	{
		$consumer = $this->getManager(_OPENID_CONSUMER_CLASS_)->getConsumer();
		$return_to = $this->getReturnTo();
		$response = $consumer->complete($return_to);
		
		//check the response status
		if ($response->status == Auth_OpenID_CANCEL) {
			// This means the authentication was cancelled.
			$msg = 'Verification cancelled.';
		} else if ($response->status == Auth_OpenID_FAILURE) {
			// Authentication failed; display the error message.
			$msg = "OpenID authentication failed: " . $response->message;
		} else if ($response->status == Auth_OpenID_SUCCESS) {
			// This means the authentication succeeded; extract the
			// identity URL and Simple Registration data (if it was
			// returned).
			$openid = $response->getDisplayIdentifier();
			$esc_identity = htmlentities($openid);

			$success = sprintf('You have successfully verified ' .
                           '<a href="%s">%s</a> as your identity.',
                           $esc_identity, $esc_identity);

			if ($response->endpoint->canonicalID) {
				$escaped_canonicalID = htmlentities($response->endpoint->canonicalID);
				$success .= '  (XRI CanonicalID: '.$escaped_canonicalID.') ';
			}

			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);

			$sreg = $sreg_resp->contents();

			if (@$sreg['email']) {
				$success .= "  You also returned '".htmlentities($sreg['email']).
							"' as your email.";
			}

			if (@$sreg['nickname']) {
				$success .= "  Your nickname is '".htmlentities($sreg['nickname']).
							"'.";
			}

			if (@$sreg['fullname']) {
				$success .= "  Your fullname is '".htmlentities($sreg['fullname']).
							"'.";
			}

			$pape_resp = Auth_OpenID_PAPE_Response::fromSuccessResponse($response);

			if ($pape_resp) {
				if ($pape_resp->auth_policies) {
					$success .= "<p>The following PAPE policies affected the authentication:</p><ul>";

					foreach ($pape_resp->auth_policies as $uri) {
						$escaped_uri = htmlentities($uri);
						$success .= "<li><tt>$escaped_uri</tt></li>";
					}

					$success .= "</ul>";
				} else {
					$success .= "<p>No PAPE policies affected the authentication.</p>";
            	}

				if ($pape_resp->auth_age) {
					$age = htmlentities($pape_resp->auth_age);
					$success .= "<p>The authentication age returned by the " .
								"server is: <tt>".$age."</tt></p>";
				}

				if ($pape_resp->nist_auth_level) {
					$auth_level = htmlentities($pape_resp->nist_auth_level);
					$success .= "<p>The NIST auth level returned by the " .
								"server is: <tt>".$auth_level."</tt></p>";
				}

			} else {
				$success .= "<p>No PAPE response was sent by the provider.</p>";
			}
		}
		
		$error = object('error');
		$error->setMessage($msg);
		$error->setSuccess($success);
		return $this->login($error);
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	public function tryAuth()
	{
		$validate = true;
		$error = object('error');
		# get the url of the openid identifier
		$openid = @$_GET['openid_identifier'];
		
		if(empty($openid)){
			# if there is no openid input from user, redirect back & include error message
			$error->setMessage('Enter OpenID Identifier');
			return $this->login($error);
		}
		# get the consumer
		$consumer = $this->getManager(_OPENID_CONSUMER_CLASS_)->getConsumer();
		
		// Begin the OpenID authentication process.
    	$auth_request = $consumer->begin($openid);
    	
    	// No auth request means we can't begin OpenID.
		if (!$auth_request) {
			$error->setMessage('Authentication error; not a valid OpenID.');
			return $this->login($error);
		}
		# Simple Registration Data
		$sreg_request = Auth_OpenID_SRegRequest::build(
							// Required
							array('nickname'),
							// Optional
							array('fullname', 'email'));
							
		if ($sreg_request) {
			$auth_request->addExtension($sreg_request);
		}
		
		$policy_uris = $_GET['policies'];

		$pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
		if ($pape_request) {
			$auth_request->addExtension($pape_request);
		}
		
		// Redirect the user to the OpenID server for authentication.
		// Store the token for this authentication so we can verify the
		// response.
		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		
		if ($auth_request->shouldSendRedirect()) {
			
			$redirect_url = $auth_request->redirectURL($this->getTrustRoot(), $this->getReturnTo());			
						
			// If the redirect URL can't be built, display an error
			// message.
			if (Auth_OpenID::isFailure($redirect_url)) {
				$error->setMessage("Could not redirect to server: " . $redirect_url->message);
				return $this->login($error);
        	} else {
				// Send redirect.
				header("Location: ".$redirect_url);				
				//Event::fire('redirect', $redirect_url);
			}
    	} else {
    		# OpenID 2 - Generate form - structure below
    		/*
    		"<html>
    			<head><title>OpenId transaction in progress</title></head>
    			<body onload='document.forms[0].submit();'>
    				<form accept-charset=\"UTF-8\" enctype=\"application/x-www-form-urlencoded\" id=\"openid_message\" action=\"http://izportal.com/@openid/server/\" method=\"post\">
    				<input type=\"hidden\" name=\"openid.ns\" value=\"http://specs.openid.net/auth/2.0\" />
    				<input type=\"hidden\" name=\"openid.ns.sreg\" value=\"http://openid.net/extensions/sreg/1.1\" />
    				<input type=\"hidden\" name=\"openid.ns.pape\" value=\"http://specs.openid.net/extensions/pape/1.0\" />
    				<input type=\"hidden\" name=\"openid.sreg.required\" value=\"nickname\" />
    				<input type=\"hidden\" name=\"openid.sreg.optional\" value=\"fullname,email\" />
    				<input type=\"hidden\" name=\"openid.pape.preferred_auth_policies\" value=\"\" />
    				<input type=\"hidden\" name=\"openid.realm\" value=\"http://izportal.com/@openid/consumer/\" />
    				<input type=\"hidden\" name=\"openid.mode\" value=\"checkid_setup\" />
    				<input type=\"hidden\" name=\"openid.return_to\" value=\"http://izportal.com/@openid/consumer/finishAuth/?janrain_nonce=2009-04-04T03%3A55%3A58ZqQRBUu\" />
    				<input type=\"hidden\" name=\"openid.identity\" value=\"http://izportal.com/@openid/server/me/thanhnh\" />
    				<input type=\"hidden\" name=\"openid.claimed_id\" value=\"http://izportal.com/@openid/server/me/thanhnh\" />
    				<input type=\"hidden\" name=\"openid.assoc_handle\" value=\"{HMAC-SHA1}{49d5d602}{9QWOcw==}\" />
    				<input type=\"submit\" value=\"Continue\" />\n</form>
    				<script>
    					var elements = document.forms[0].elements;
    					for (var i = 0; i < elements.length; i++) {  elements[i].style.display = \"none\";}
    				</script>
    			</body>
    		</html>"
	    	*/
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $auth_request->htmlMarkup($this->getTrustRoot(), $this->getReturnTo(), false, array('id' => $form_id));
			//$fs = $this->getManager('files');
			//$fs->write('c:\form_submit.txt',$form_html);
			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html)) {
				$error->setMessage("Could not redirect to server: " . $form_html->message);
				//displayError("Could not redirect to server: " . $form_html->message);
				return $this->login($error);
			} else {
				print $form_html;
				exit(0);
			}
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	private function getTrustRoot()
	{
		return config('root.uri')."consumer/";
	}
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author user
	 **/
	private function getReturnTo()
	{
		return config('root.uri')."consumer/finishAuth/";
	}
} // END class 
?>