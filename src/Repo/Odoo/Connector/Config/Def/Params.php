<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Config\Def;

/**
 * @method void setBaseUri(string $data)
 * @method void setDbName(string $data)
 * @method void setUserName(string $data)
 * @method void setUserPassword(string $data)
 */
class Params
    extends \Flancer32\Lib\DataObject
    implements \Praxigento\Odoo\Repo\Odoo\Connector\Config\IAuthentication
{

    public function getBaseUri()
    {
        $result = parent::getBaseUri();
        return $result;
    }

    public function getDbName()
    {
        $result = parent::getDbName();
        return $result;
    }

    public function getUserName()
    {
        $result = parent::getUserName();
        return $result;
    }

    public function getUserPassword()
    {
        $result = parent::getUserPassword();
        return $result;
    }
}