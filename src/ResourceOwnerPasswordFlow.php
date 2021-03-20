<?php
/** Copyright © 2019-2021 Dragos Gaftoneanu
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Okta\OIDC;
use Exception;

class ResourceOwnerPasswordFlow extends Exception
{
	protected $client_id, $client_secret, $issuer, $scopes, $username, $password;
	
	public function setClientId($client_id)
	{
		$this->client_id = $client_id;
	}
	
	public function setClientSecret($client_secret)
	{
		$this->client_secret = $client_secret;
	}
	
	public function setIssuer($issuer)
	{
		$this->issuer = $issuer;
	}
	
	public function setScopes($scopes)
	{
		$this->scopes = $scopes;
	}
	
	public function setUsername($username)
	{
		$this->username = $username;
	}
	
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	public function getTokens($full=true)
	{
		if(!isset($this->client_id) || !isset($this->issuer) || !isset($this->scopes) || !isset($this->username) || !isset($this->password))
			$this->error('One or more required parameters have not been pre-initialized.');
		
		$response = $this->doRequest($this->getMetadata()['token_endpoint'], array(
			'grant_type' => 'password',
			'username' => $this->username,
			'password' => $this->password,
			'scope' => $this->scopes
		),base64_encode($this->client_id . (isset($this->client_secret) ? ":" . $this->client_secret : "")));
		
		if(!isset($response['access_token']))
			$this->error('Access token could not be fetched.');

		if($full == true)
			return array(
				"access_token" => array(
					"value" => $response['access_token'],
					"introspect" => $this->introspectToken($response['access_token'])
				),
				"id_token" => array(
					"value" => $response['id_token'],
					"introspect" => $this->introspectToken($response['id_token']),
					"userinfo" => $this->userinfoToken($response['access_token'])
				),
				"refresh_token" => $response['refresh_token']
			);
		else
			return array(
				"access_token" => array(
					"value" => $response['access_token']
				),
				"id_token" => array(
					"value" => $response['id_token']
				),
				"refresh_token" => $response['refresh_token']
			);
	}

	private function introspectToken($token)
	{
		return $this->doRequest($this->getMetadata()['introspection_endpoint'], [
			'token' => $token,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret
		]);
	}

	private function userinfoToken($access_token)
	{
		return $this->doRequest($this->getMetadata()['userinfo_endpoint'], false, false, $access_token);
	}
	
	private function getMetadata()
	{
		return $this->doRequest($this->issuer . '/.well-known/openid-configuration');
	}
	
	private function doRequest($url, $params=false, $basic=false, $bearer=false)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);			
		
		if($params)
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		
		if($basic)
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $basic));
		
		if($bearer)
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $bearer));

		$result = curl_exec($ch);
	
		curl_close($ch);
		
		return json_decode($result, 1);
	}
	
	private function error($message)
	{
		throw new \Exception(
			json_encode(array(
				"error" => array(
					"errorSummary" => $message
				)
			),JSON_UNESCAPED_SLASHES)
		);
	}
}