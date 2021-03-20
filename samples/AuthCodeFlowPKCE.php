<?php
include "../src/AuthCodeFlowPKCE.php";
use Okta\OIDC\AuthCodeFlowPKCE;

error_reporting(0);

try{	
	$flow = new AuthCodeFlowPKCE();
	$flow->setClientId("0oa3pkx35h6fRyG3K2p7");
	$flow->setRedirectUri("http://localhost/okta-oidc-flows-php/samples/AuthCodeFlowPKCE.php");
	$flow->setIssuer("https://dragos.okta.com");
	$flow->setScopes("openid");
	$flow->setState("abc");
	$flow->setNonce("abc");
	$flow->setCodeVerifier("ac0639dbecf900ddfb3aabd84ab0ff07ac0639dbecf900ddfb3aabd84ab0ff07");

	if(!isset($_GET['code']))
		echo '<a href="' . $flow->getAuthorizationUrl() . '">Log in with Okta</a>';
	else
	{
		$vars = $flow->parseAuthCode($_GET['code'], $_GET['state'], $_GET['error'], true);
	?>
	<table border=1 cellspacing=0 style='width:100%;font-size:14px;' cellpadding=2>
	<tr style='font-weight:bold;'><td style='width:20%'>Key</td><td style='width:80%'>Value</td></tr>
	<tr><td>Access token<td><textarea style='width:100%;height:125px;'><?php echo $vars['access_token']['value']; ?></textarea></td></tr>
	<tr><td>ID token<td><textarea style='width:100%;height:125px;'><?php echo $vars['id_token']['value']; ?></textarea></td></tr>
	<tr><td>Refresh token<td><textarea style='width:100%;height:125px;'><?php echo $vars['refresh_token']; ?></textarea></td></tr>
	</table><br /><br />
	
	<table style='width:100%;' cellspacing='0' border='1' cellpadding='2'>
	<tr><td style='width:50%;'><b>Access token content</b></td><td style='width:50%'><b>ID token content</b></td></tr>
	<tr>
	<td><pre><?php 
		echo json_encode(json_decode(base64_decode(explode(".",$vars['access_token']['value'])[0])),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
	?>
	</pre></td>
	<td><pre><?php 
		echo json_encode(json_decode(base64_decode(explode(".",$vars['id_token']['value'])[0])),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
	?>
	</pre></td>
	</tr>
	<tr>
	<td><pre><?php 
		echo json_encode(json_decode(base64_decode(explode(".",$vars['access_token']['value'])[1])),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
	?>
	</pre></td>
	<td><pre><?php 
		echo json_encode(json_decode(base64_decode(explode(".",$vars['id_token']['value'])[1])),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); 
	?>
	</pre></td>
	</tr></table><br /><br />
	
	<table style='width:100%;' cellspacing='0' border='1' cellpadding='2'>
	<tr><td style='width:50%;'><b>Access token sent to /introspect endpoint</b></td><td style='width:50%'><b>ID token sent to /introspect endpoint</b></td></tr>
	<tr>
	<td><pre><?php 
		echo json_encode($vars['access_token']['introspect'],JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	?>
	</pre></td>
	<td><pre><?php 
		echo json_encode($vars['id_token']['introspect'],JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	?>
	</pre></td>
	</tr></table><br /><br />
	
	<table style='width:50%;' cellspacing='0' border='1' cellpadding='2'>
	<tr><td><b>Access token sent to /userinfo endpoint</b></td></tr>
	<tr>
	<td><pre><?php 
		echo json_encode($vars['id_token']['userinfo'],JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	?>
	</td></tr>
	</table>
	<?php
	}
}catch(Exception $e){
	echo $e;
}