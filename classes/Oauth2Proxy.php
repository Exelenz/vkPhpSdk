<?php
/**
 * IOauth2Proxy class file.
 *
 * This source file is subject to the New BSD License
 * that is bundled with this package in the file license.txt.
 * 
 * @author Andrey Geonya <manufacturer.software@gmail.com>
 * @link https://github.com/AndreyGeonya/vkPhpSdk
 * @copyright Copyright &copy; 2011-2012 Andrey Geonya
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace Exelenz\vkPhpSdk\classes;

use Exelenz\vkPhpSdk\interfaces\IOauth2Proxy;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Oauth2Proxy is the OAuth 2.0 proxy class.
 * Redirects requests to the external web resource by OAuth 2.0 protocol.
 *
 * @see http://oauth.net/2/
 * @author Andrey Geonya
 * @contributor Alexander Volochnev
 */
class Oauth2Proxy implements IOauth2Proxy
{
    protected $_clientId;
    protected $_clientSecret;
    protected $_dialogUrl;
    protected $_redirectUri;
    protected $_scope;
    protected $_responseType;
    protected $_accessTokenUrl;
    protected $_accessParams;
    protected $_authJson;
    protected $_sessionPrefix;
    protected $_session;


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
     * @param string $sessionPrefix Prefix session name
     * @param SessionInterface $session Session manager
	 */
	public function __construct(
        $clientId,
        $clientSecret,
        $accessTokenUrl,
        $dialogUrl,
        $responseType,
        $redirectUri = null,
        $scope = null,
        $sessionPrefix = 'vkPhpSdk',
        SessionInterface $session
    )
	{
		$this->_clientId       = $clientId;
		$this->_clientSecret   = $clientSecret;
		$this->_accessTokenUrl = $accessTokenUrl;		
		$this->_dialogUrl      = $dialogUrl;
		$this->_responseType   = $responseType;
		$this->_redirectUri    = $redirectUri;
		$this->_scope          = $scope;
        $this->_sessionPrefix  = $sessionPrefix;
        $this->_session        = $session;
	}

	/**
	 * Authorize client.
	 */
	public function authorize()
	{
		if(!$this->_session->is_started()){
			$this->_session->start();
        }

		$result = false;
		
		if($this->_session->has($this->_sessionPrefix . 'authJson' . $this->_clientId)) {
			$this->_authJson = $this->_session->get($this->_sessionPrefix . 'authJson' . $this->_clientId);
			$result = true;
		} else {
			if(!(isset($_REQUEST['code']) && $_REQUEST['code'])) {
                $this->_session->set($this->_sessionPrefix . 'state', md5(uniqid(rand(), true))); // CSRF protection

				$this->_dialogUrl .= '?client_id=' . $this->_clientId;
				$this->_dialogUrl .= '&redirect_uri=' . $this->_redirectUri;
				$this->_dialogUrl .= '&scope=' . $this->_scope;
				$this->_dialogUrl .= '&response_type=' . $this->_responseType;
				$this->_dialogUrl .= '&state=' . $this->_session->get($this->_sessionPrefix . 'state');

				echo("<script>top.location.href='" . $this->_dialogUrl . "'</script>");

			} elseif ($_REQUEST['state'] === $this->_session->get($this->_sessionPrefix . 'state')) {
				$this->_authJson = file_get_contents("$this->_accessTokenUrl?client_id=$this->_clientId&client_secret=$this->_clientSecret&code={$_REQUEST['code']}");

				if($this->_authJson !== false) {
					$this->_session->set($this->_sessionPrefix . 'authJson' . $this->_clientId, $this->_authJson);
					$result = true;
				} else {
					$result = false;
                }
			}
		}

		return $result;
	}
	
	/**
	 * Get access token.
	 * 
	 * @return string
	 */
	public function getAccessToken()
	{		
		if ($this->_accessParams === null) {
            $this->_accessParams = json_decode($this->getAuthJson(), true);
        }

		return $this->_accessParams['access_token'];
	}

	/**
	 * Get expires time.
	 * 
	 * @return string
	 */
	public function getExpiresIn()
	{
		if ($this->_accessParams === null) {
			$this->_accessParams = json_decode($this->getAuthJson(), true);
        }

		return $this->_accessParams['expires_in'];
	}
	
	/**
	 * Get user id.
	 * 
	 * @return string
	 */
	public function getUserId()
	{
		if ($this->_accessParams === null) {
			$this->_accessParams = json_decode($this->getAuthJson(), true);
        }

		return $this->_accessParams['user_id'];		
	}
	
	/**
	 * Get authorization JSON string.
	 * 
	 * @return string
	 */
	protected function getAuthJson()
	{
		return $this->_authJson;
	}
}