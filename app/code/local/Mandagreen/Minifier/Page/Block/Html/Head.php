<?php

class Mandagreen_Minifier_Page_Block_Html_Head extends Mage_Page_Block_Html_Head
{
    public function getCssJsHtml()
    {
        $html = parent::getCssJsHtml();
        if (Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_CLEAN_HANDLES)) {
            $html = preg_replace(Mage::helper('mgminifier')->getDataHandleRegExp(), '', $html);
        }

        return $html;
    }
}