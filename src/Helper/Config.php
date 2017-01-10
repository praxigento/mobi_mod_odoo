<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Helper;

/**
 * Helper to get configuration parameters related to the module.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Config
{

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getConnectDb()
    {
        $result = $this->scopeConfig->getValue('praxigento_odoo/connect/database');
        return $result;
    }

    /**
     * @return string
     */
    public function getConnectPassword()
    {
        $result = $this->scopeConfig->getValue('praxigento_odoo/connect/password');
        return $result;
    }

    /**
     * Base URL to connect to Odoo.
     *
     * @return string
     */
    public function getConnectUri()
    {
        $result = $this->scopeConfig->getValue('praxigento_odoo/connect/uri');
        return $result;
    }

    /**
     * @return string
     */
    public function getConnectUser()
    {
        $result = $this->scopeConfig->getValue('praxigento_odoo/connect/user');
        return $result;
    }


}