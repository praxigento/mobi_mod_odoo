<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Odoo;

use Flancer32\Lib\DataObject;

class Contact extends DataObject
{


    public function getCity()
    {
        $result = parent::getCity();
        return $result;
    }

    public function getCountry()
    {
        $result = parent::getCountry();
        return $result;
    }

    public function getEmail()
    {
        $result = parent::getEmail();
        return $result;
    }

    public function getName()
    {
        $result = parent::getName();
        return $result;
    }

    public function getPhone()
    {
        $result = parent::getPhone();
        return $result;
    }

    public function getState()
    {
        $result = parent::getState();
        return $result;
    }

    public function getStreet()
    {
        $result = parent::getStreet();
        return $result;
    }

    public function getZip()
    {
        $result = parent::getZip();
        return $result;
    }

    public function setCity($data = null)
    {
        parent::setCity($data);
    }

    public function setCountry($data = null)
    {
        parent::setCountry($data);
    }

    public function setEmail($data = null)
    {
        parent::setEmail($data);
    }

    public function setName($data = null)
    {
        parent::setName($data);
    }

    public function setPhone($data = null)
    {
        parent::setPhone($data);
    }

    public function setState($data = null)
    {
        parent::setState($data);
    }

    public function setStreet($data = null)
    {
        parent::setStreet($data);
    }

    public function setZip($data = null)
    {
        parent::setZip($data);
    }

}