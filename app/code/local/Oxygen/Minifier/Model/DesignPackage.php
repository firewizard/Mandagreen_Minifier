<?php

class Oxygen_Minifier_Model_DesignPackage extends Mage_Core_Model_Design_Package {
	const KEY_ENABLE_JS                  = 'dev/oxy_minifier/active_js';
	const KEY_ENABLE_CSS                 = 'dev/oxy_minifier/active_css';
	const KEY_ENABLE_CONVERT_COLORS      = 'dev/oxy_minifier/active_convert_colors';
	const KEY_ENABLE_COMPRESS_COLORS     = 'dev/oxy_minifier/active_compress_colors';
	const KEY_ENABLE_COMPRES_UNIT_VALUES = 'dev/oxy_minifier/active_compress_unit_values';
	
	
	public function beforeMergeCss($file, $contents) {
		$contents = parent::beforeMergeCss($file, $contents);
		if( !Mage::getStoreConfig(self::KEY_ENABLE_CSS) ) { return $contents; }
		
		return Oxygen_Minifier_Model_CssMin::minify($contents, array(
			'convert-color-values'			=> (bool)Mage::getStoreConfig(self::KEY_ENABLE_CONVERT_COLORS),
			'compress-color-values'			=> (bool)Mage::getStoreConfig(self::KEY_ENABLE_COMPRESS_COLORS),
			'compress-unit-values'			=> (bool)Mage::getStoreConfig(self::KEY_ENABLE_COMPRES_UNIT_VALUES),
			'emulate-css3-variables'		=> true
		));
	}
	
	public function getMergedJsUrl($files) {
		$targetFilename = md5(implode(',', $files)) . '.js';
		$targetDir = $this->_initMergerDir('js');
		if( !$targetDir ) {
			return '';
		}
		if( Mage::helper('core')->mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeJs'), 'js') ) {
			return Mage::getBaseUrl('media') . 'js/' . $targetFilename;
		}
		return '';
	}

	public function beforeMergeJs($file, $contents) {
		return Mage::getStoreConfig(self::KEY_ENABLE_JS) ? Oxygen_Minifier_Model_JSMin::minify($contents) : $contents;
	}
}