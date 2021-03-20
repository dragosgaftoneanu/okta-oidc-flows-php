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

class AuthCodeFlow extends Exception
{
	protected $client_id, $client_secret, $redirect_uri, $issuer, $scopes, $state, $nonce;
	
	public function setClientId($client_id)
	{
		$this->client_id = $client_id;
	}
	
	public function setClientSecret($client_secret)
	{
		$this->client_secret = $client_secret;
	}
	
	public function setRedirectUri($redirect_uri)
	{
		$this->redirect_uri = $redirect_uri;
	}
	
	public function setIssuer($issuer)
	{
		$this->issuer = $issuer;
	}
	
	public function setScopes($scopes)
	{
		$this->scopes = $scopes;
	}
	
	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function setNonce($nonce)
	{
		$this->nonce = $nonce;
	}
	
	public function parseAuthCode($code, $state, $error, $full=true)
	{
		if(!isset($this->client_id) || !isset($this->client_secret) || !isset($this->redirect_uri) || !isset($this->issuer) || !isset($this->scopes) || !isset($this->state) || !isset($this->nonce))
			$this->error('One or more required parameters have not been pre-initialized.');
		
		if($this->state != $state)
			$this->error('Authorization server returned a different state parameter than the one defined.');

		if(isset($error))
			$this->error('Authorization server returned an error: ' . htmlentities($error));
		

		$response = $this->doRequest($this->getMetadata()['token_endpoint'], array(
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $this->redirect_uri,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret
		));
		
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
	
	public function getAuthorizationUrl()
	{		
		return $this->getMetadata()['authorization_endpoint'] . '?' . http_build_query(array(
			'response_type' => 'code',
			'client_id' => $this->client_id,
			'redirect_uri' => $this->redirect_uri,
			'state' => $this->state,
			'scope' => $this->scopes,
			'nonce' => $this->nonce
		));
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
		return $this->doRequest($this->getMetadata()['userinfo_endpoint'], false, $access_token);
	}
	
	private function getMetadata()
	{
		return $this->doRequest($this->issuer . '/.well-known/openid-configuration');
	}
	
	private function doRequest($url, $params=false, $bearer=false)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		
		if($params)
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		
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