<?php


function generate_salt($email) {
    // Create a SHA1 hash
    $salt = sha1('~'.$email.'~'.microtime(TRUE).'~');

    // Limit to random 10 characters in the hash
    $salt = substr($salt, rand(0,30), 10);

    return $salt;
}

function hash_password($password, $salt) {
    return sha1('~'.$password.'~'.$salt.'~');
}

function valid_password($password, $hash, $salt) {
    return hash_password($password, $salt) == $hash;
}
?>