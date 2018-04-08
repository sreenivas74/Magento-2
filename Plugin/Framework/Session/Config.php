<?php

namespace Gigya\GigyaIM\Plugin\Framework\Session;

use Gigya\GigyaIM\Model\Config as GigyaConfig;
use \Magento\Framework\App\State;
use \Magento\Framework\App\Request\Http as RequestHttp;

class Config
{
    /**
     * @var GigyaConfig
     */
    protected $config;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var RequestHttp
     */
    protected $request;

    /**
     * Config constructor.
     * @param GigyaConfig $config
     * @param State $state
     * @param RequestHttp $request
     */
    public function __construct(
        GigyaConfig $config,
        State $state,
        RequestHttp $request
    )
    {
        $this->config = $config;
        $this->state = $state;
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Session\Config $subject
     * @param $cookieLifetime
     * @param null $default
     * @return array
     */
    public function beforeSetCookieLifetime(\Magento\Framework\Session\Config $subject, $cookieLifetime, $default = null)
    {
        $areaCode = $this->state->getAreaCode();
        $sessionMode = $this->config->getSessionMode();

        if ($areaCode == 'frontend' && $sessionMode == GigyaConfig::SESSION_MODE_FIXED) {
            $loginData = $this->getRequest()->getParam('login_data');
            if (!empty($loginData)) {
                $loginData = json_decode($loginData);

                if (is_object($loginData) && isset($loginData->expiresIn)) {
                    $cookieLifetime = intval($loginData->expiresIn);
                }
            }
        }

        return array($cookieLifetime, $default);
    }

    /**
     * @return RequestHttp
     */
    public function getRequest()
    {
        return $this->request;
    }
}