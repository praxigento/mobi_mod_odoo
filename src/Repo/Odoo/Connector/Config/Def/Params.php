<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Config\Def;

use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\IAuthentication;

/**
 * @method void setBaseUri(string $data)
 * @method void setDbName(string $data)
 * @method void setUserName(string $data)
 * @method void setUserPassword(string $data)
 */
class Params extends DataObject implements IAuthentication
{
    /**
     * @inheritdoc
     */
    public function getBaseUri()
    {
        $result = parent::getBaseUri();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getDbName()
    {
        $result = parent::getDbName();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getUserName()
    {
        $result = parent::getUserName();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getUserPassword()
    {
        $result = parent::getUserPassword();
        return $result;
    }
}