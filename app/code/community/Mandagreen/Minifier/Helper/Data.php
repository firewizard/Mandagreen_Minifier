<?php

class Mandagreen_Minifier_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DATA_HANDLE = 'data-handle';

    public function getDataHandleRegExp()
    {
        return '/\s?' . static::DATA_HANDLE . '="(.*?)"/';
    }

    public function getDataHandleParam($paramValue)
    {
        return static::DATA_HANDLE . '="' . $paramValue . '"';
    }
}