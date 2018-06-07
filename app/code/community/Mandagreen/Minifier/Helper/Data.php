<?php

class Mandagreen_Minifier_Helper_Data extends Mage_Core_Helper_Abstract
{
    const DATA_HANDLE = 'data-mgm-handle';

    public function getDataHandleRegExp()
    {
        return '/\s?' . static::DATA_HANDLE . '="(.*?)"/';
    }

    public function getDataHandleParam($paramValue)
    {
        return static::DATA_HANDLE . '="' . $paramValue . '"';
    }

    public function getMergeHandleEquivalence($handle)
    {
        $eq = trim(Mage::getStoreConfig('dev/mgminifier/handle_equivalences'));
        if (!empty($eq)) {
            $eq = array_map('trim', explode("\n", str_replace("\r", "\n", $eq)));
            $eq = array_map(function ($el) {
                return array_map('trim', explode(':', $el));
            }, $eq);
        }

        $data = array();
        foreach ($eq as $line) {
            if (empty($line[0]) || empty($line[1])) {
                continue;
            }

            $data[$line[0]] = $line[1];
        }

        if (isset($data[$handle])) {
            return $data[$handle];
        }

        return $handle;
    }
}