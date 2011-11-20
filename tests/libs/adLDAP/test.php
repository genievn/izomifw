<?php
require_once(dirname(__FILE__) . '../../../../libs/adLDAP/adLDAP.php');
$adldap = new adLDAP(array(
    "base_dn"=>"DC=pvtech,DC=pvn,DC=vn"
    , "account_suffix"=>"@pvtech.pvn.vn"
    , "domain_controllers"=>array("pvtech.pvn.vn") 
));
$adldap->close();
$adldap->set_ad_username('hieunt');
$adldap->set_ad_password('pvtech@123');
$adldap->connect();
$result=$adldap->user_info("thanhnh2");
print_r($result);

$authUser = $adldap->authenticate('hieunt', 'daukhi@2010');
if ($authUser === true) {
  echo "User authenticated successfully";
}
else {
  echo "User authentication unsuccessful";
}
?>