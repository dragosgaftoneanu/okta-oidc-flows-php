# Okta OIDC Flows PHP
This repository contains the source for the Okta OIDC PHP library that can be used to login your users using [Authorization Code Flow](https://developer.okta.com/docs/guides/implement-auth-code/overview/), [Authorization Code Flow with PKCE](https://developer.okta.com/docs/guides/implement-auth-code-pkce/overview/) or [Resource Owner Password Flow](https://developer.okta.com/docs/guides/implement-password/overview/) inside your PHP application.

:warning: **Disclaimer:** This is not an official product and does not qualify for Okta Support.

## Installation
You can install this library by running the following command through Composer

```
composer require dragosgaftoneanu/okta-oidc-flows-php
```

## Requirements
* An Okta account, called an _organization_ (you can sign up for a free [developer organization](https://developer.okta.com/signup/))
* A local web server that runs PHP 5.0+
* [cURL library](https://www.php.net/manual/en/book.curl.php) available for usage

## Authorization Code Flow methods
### setClientId($client_id)
This method sets the client ID used for authorization code flow.

### setClientSecret($client_secret)
This method sets the client secret used for authorization code flow.

### setRedirectUri($redirect_uri)
This method sets the redirect uri used for authorization code flow.

### setIssuer($issuer)
This method sets the issuer used for authorization code flow.

### setScopes($scopes)
This method sets the scopes used for authorization code flow.

### setState($state)
This method sets the state used for authorization code flow.

### setNonce($nonce)
This method sets the nonce used for authorization code flow.

### parseAuthCode($code, $state, $error, $full=true)
This method takes the authorization code, state, error parameters from either `GET` or `POST` in order to further process the flow.
If `$full` is set to true, then the result will contain also the details from /introspect and /userinfo endpoint, otherwise it will return only the JWT tokens received after exchanging the code.

## Authorization Code Flow with PKCE methods
### setClientId($client_id)
This method sets the client ID used for authorization code flow with PKCE.

### setRedirectUri($redirect_uri)
This method sets the redirect uri used for authorization code flow with PKCE.

### setIssuer($issuer)
This method sets the issuer used for authorization code flow with PKCE.

### setScopes($scopes)
This method sets the scopes used for authorization code flow with PKCE.

### setState($state)
This method sets the state used for authorization code flow with PKCE.

### setNonce($nonce)
This method sets the nonce used for authorization code flow with PKCE.

### setCodeVerifier($code_verifier)
This method sets the code verifier used for authorization code flow with PKCE.

### parseAuthCode($code, $state, $error, $full=true)
This method takes the authorization code, state, error parameters from either `GET` or `POST` in order to further process the flow.
If `$full` is set to true, then the result will contain also the details from /introspect and /userinfo endpoint, otherwise it will return only the JWT tokens received after exchanging the code.

## Resource Owner PAssword Flow methods
### setClientId($client_id)
This method sets the client ID used for resource owner password flow.

### setClientSecret($client_secret)
This method sets the client secret used for resource owner password flow.

### setIssuer($issuer)
This method sets the issuer used for resource owner password flow.

### setScopes($scopes)
This method sets the scopes used for resource owner password flow.

### setUsername($username)
This method sets the username used for resource owner password flow.

### setPassword($password)
This method sets the password used for resource owner password flow.

### getTokens($full=true)
This method sends the request to the /token endpoint and retrieves the JWT tokens.
If `$full` is set to true, then the result will contain also the details from /introspect and /userinfo endpoint, otherwise it will return only the JWT tokens received after exchanging the code.

## Bugs?
If you find a bug or encounter an issue when using the library, please open an issue on GitHub [here](https://github.com/dragosgaftoneanu/okta-oidc-flows-php/issues) and it will be further investigated.