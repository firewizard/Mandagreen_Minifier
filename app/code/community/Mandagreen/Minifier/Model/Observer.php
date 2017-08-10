<?php

class Mandagreen_Minifier_Model_Observer
{
    /**
     * removes data-handles from css & js html tags
     * observes http_response_send_before
     * @param Varien_Event_Observer $observer
     */
    public function cleanDataHandles(Varien_Event_Observer $observer)
    {
        if (!$this->_shouldClean()) {
            return;
        }

        /** @var Mage_Core_Controller_Response_Http $response */
        $response = $observer->getEvent()->getResponse();

        /** @var Mandagreen_Minifier_Helper_Data $helper */
        $helper = Mage::helper('mgminifier');

        $body = $response->getBody();
        $body = preg_replace($helper->getDataHandleRegExp(), '', $body);
        $response->setBody($body);
        Mage::register('_mgm_cleaned_up', true, true);
    }

    /**
     * @return bool
     */
    protected function _shouldClean()
    {
        // ensure the replace doesn't happen twice
        if (Mage::registry('_mgm_cleaned_up')) {
            return false;
        }

        // clean up should not run for the api
        if ('api' == Mage::app()->getRequest()->getModuleName()) {
            return false;
        }

        // and it should not run if it's not enabled
        if (!Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_CLEAN_HANDLES)) {
            return false;
        }

        $isAdmin = Mage::app()->getStore()->isAdmin();

        $canUse = !$isAdmin || ($isAdmin && Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_USE_IN_ADMIN));

        $shouldMergeJs = $canUse
            && Mage::getStoreConfigFlag('dev/js/merge_files')
//            && Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_ENABLE_JS)
            && Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_MERGE_JS_BY_HANDLE);

        $shouldMergeCss = $canUse
            && Mage::getStoreConfigFlag('dev/css/merge_css_files')
//            && Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_ENABLE_CSS)
            && Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_MERGE_CSS_BY_HANDLE);

        // it should only run when at least one of the js or css minify by handle are active
        return $shouldMergeCss || $shouldMergeJs;
    }
}