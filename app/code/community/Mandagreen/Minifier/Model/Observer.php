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
        if (!Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_CLEAN_HANDLES)) {
            return;
        }

        /** @var Mage_Core_Controller_Response_Http $response */
        $response = $observer->getEvent()->getResponse();

        /** @var Mandagreen_Minifier_Helper_Data $helper */
        $helper = Mage::helper('mgminifier');

        $body = $response->getBody();
        $body = preg_replace($helper->getDataHandleRegExp(), '', $body);
        $response->setBody($body);
    }
}