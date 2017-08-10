<?php

class Mandagreen_Minifier_Core_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
    /**
     * Collect and merge layout updates from file
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer|null $storeId
     * @return Mage_Core_Model_Layout_Element
     */
    public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        $xml = parent::getFileLayoutUpdatesXml($area, $package, $theme, $storeId);
        if (Mage::getDesign()->getArea() != 'adminhtml') {
            $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files')
                && Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_MERGE_JS_BY_HANDLE);

            $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files')
                && Mage::getStoreConfigFlag(Mandagreen_Minifier_Core_Model_Design_Package::KEY_MERGE_CSS_BY_HANDLE);

            $methods = array();
            if ($shouldMergeJs) {
                $methods[] = 'addJs';
            }
            if ($shouldMergeCss) {
                $methods[] = 'addCss';
            }
            if ($shouldMergeJs || $shouldMergeCss) {
                $methods[] = 'addItem';
            }

            $helper = Mage::helper('mgminifier');
            foreach ($methods as $method) {
                foreach ($xml->children() as $handle => $child) {
                    $items = $child->xpath(".//action[@method='" . $method . "']");
                    foreach ($items as $item) {
                        if ($method == 'addItem' && (
                                (!$shouldMergeCss && (string)$item->{'type'} == 'skin_css') ||
                                (!$shouldMergeJs && (string)$item->{'type'} == 'skin_js'))) {
                            continue;
                        }

                        $params = $item->xpath("params");
                        if (count($params)) {
                            foreach ($params as $param) {
                                if (trim($param)) {
                                    $param->{0} = (string)$param . ' ' . $helper->getDataHandleParam($handle);
                                } else {
                                    $param->{0} = $helper->getDataHandleParam($handle);
                                }
                            }
                        } else {
                            $item->addChild('params', $helper->getDataHandleParam($handle));
                        }
                    }
                }
            }
        }

        return $xml;
    }

}
