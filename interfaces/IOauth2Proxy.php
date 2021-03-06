<?php
/**
 * IOauth2Proxy interface file.
 *
 * This source file is subject to the New BSD License
 * that is bundled with this package in the file license.txt.
 * 
 * @author Andrey Geonya <manufacturer.software@gmail.com>
 * @link https://github.com/AndreyGeonya/vkPhpSdk
 * @copyright Copyright &copy; 2011-2012 Andrey Geonya
 */

namespace Exelenz\vkPhpSdk\interfaces;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Oauth2Proxy is the OAuth 2.0 proxy interface.
 * Redirects requests to the external web resource by OAuth 2.0 protocol.
 *
 * @see http://oauth.net/2/
 * @author Andrey Geonya
 */
interface IOauth2Proxy
{
	/**
	 * Constructor.
	 * 
	 * @param string $clientId id of the client application
	 * @param string $clientSecret application secret key
 	 * @param string $accessTokenUrl access token url
	 * @param string $dialogUrl dialog url
	 * @param string $responseType response type (for example: code)
	 * @param string $redirectUri redirect uri
	 * @param string $scope access scope (for example: friends,video,offline)
     * @param string $sessionPrefix session prefix
     * @param SessionInterface $session Session manager
	 */
	public function __construct($clientId, $clientSecret, $accessTokenUrl, $dialogUrl, $responseType, $redirectUri = null, $scope = null, $sessionPrefix = 'vkPhpSdk',SessionInterface $session);
	
	/**
	 * Authorize client.
	 */	
	public function authorize();
	
	/**
	 * Get access token.
	 * 
	 * @return string
	 */
	public function getAccessToken();
	
	/**
	 * Get expires time.
	 * 
	 * @return string
	 */
	public function getExpiresIn();
	
	/**
	 * Get user id.
	 * 
	 * @return string
	 */
	public function getUserId();
}