<?php
namespace App\Libraries;

use GuzzleHttp\Client;

class HttpClient{
	
	public static function httpPostOnXform($url, $data)
	{
		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS , http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		curl_close($curl);
		
		return $response;
	}
	
	public static function httpPostOnRaw($url , $data)
	{
		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS , $data);
		$response = curl_exec($curl);
		curl_close($curl);
		
		return $response;
	}

	public static function httpPost($url ,  $token , $body) {
		$headers = [
            'Content-Type' => 'multipart/form-data',
            'Accept' => '*/json',
            'Authorization' => 'Bearer '.$token
        ];
        
        $client = new Client(
            [
                'headers' => $headers
			]);
			
		$response = $client->post(
			$url ,  
			array(
				'form_params' => $body,
				'verify' => false
			));
	
		return $response->getBody();
	}
}
?>