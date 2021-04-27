<?php

namespace App\Libraries;

class Encryption
{

    public static function encrypt($plaintext) {
		$password = "sidikapasswordkey";
		$iv = "385e33f741h38kn9";
		$ciphertext = openssl_encrypt($plaintext, "AES-256-CBC", hash('sha256', $password, true), OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext . $iv, hash('sha256', $password, true), true);
		return base64_encode($iv.$hmac.$ciphertext);		
	}

	public static function decrypt($ciphertext) {
		$password = "sidikapasswordkey";
		$ciphertext = base64_decode($ciphertext);
		if (!hash_equals(hash_hmac('sha256', substr($ciphertext, 48).substr($ciphertext, 0, 16), hash('sha256', $password, true), true), substr($ciphertext, 16, 32))) return null;
		return openssl_decrypt(substr($ciphertext, 48), "AES-256-CBC", hash('sha256', $password, true), OPENSSL_RAW_DATA, substr($ciphertext, 0, 16));
	}
}
?>