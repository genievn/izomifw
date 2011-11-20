= Consumer receive the openid url identifier
	+ Consumer try to obtain the openid server
	+ Consumer setup interaction session, connecting to openid server
	+ Consumer redirect to login page of openid server
	+ Server get login infor, set to the session variables and perform check
	+ On authenticated, Server render trust page to confirm the identity
	+ if confirmed, redirect back to RP
	
 ================================================
 = FORM HTML AUTOSUBMIT - SENT BACK FROM SERVER =
 ================================================
 <html><head><title>OpenId transaction in progress</title></head><body onload='document.forms[0].submit();'><form accept-charset="UTF-8" enctype="application/x-www-form-urlencoded" id="openid_message" action="http://izportal.com/izomi/test/openid/examples/server/server.php" method="post">
<input type="hidden" name="openid.ns" value="http://specs.openid.net/auth/2.0" />
<input type="hidden" name="openid.ns.sreg" value="http://openid.net/extensions/sreg/1.1" />
<input type="hidden" name="openid.ns.pape" value="http://specs.openid.net/extensions/pape/1.0" />
<input type="hidden" name="openid.sreg.required" value="nickname" />
<input type="hidden" name="openid.sreg.optional" value="fullname,email" />
<input type="hidden" name="openid.pape.preferred_auth_policies" value="" />
<input type="hidden" name="openid.realm" value="http://izportal.com/izomi/@openid/consumer/" />
<input type="hidden" name="openid.mode" value="checkid_setup" />
<input type="hidden" name="openid.return_to" value="http://izportal.com/izomi/@openid/consumer/finishAuth/?janrain_nonce=2008-08-22T03%3A17%3A22ZYbtgYd" />
<input type="hidden" name="openid.identity" value="http://izportal.com/izomi/test/openid/examples/server/server.php/idpage?user=genie" />
<input type="hidden" name="openid.claimed_id" value="http://izportal.com/izomi/test/openid/examples/server/server.php/idpage?user=genie" />
<input type="hidden" name="openid.assoc_handle" value="{HMAC-SHA1}{48ad23fb}{rUQCcw==}" />
<input type="submit" value="Continue" />
</form>
<script>var elements = document.forms[0].elements;for (var i = 0; i < elements.length; i++) {  elements[i].style.display = "none";}</script></body></html>
 ============================
 = END FORM HTML AUTOSUBMIT =
 ============================