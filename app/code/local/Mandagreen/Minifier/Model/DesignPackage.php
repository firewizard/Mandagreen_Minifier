<?php

class Mandagreen_Minifier_Model_DesignPackage extends Mage_Core_Model_Design_Package {
	const KEY_ENABLE_JS                   = 'dev/mgminifier/active_js';
	const KEY_ENABLE_CSS                  = 'dev/mgminifier/active_css';
	const KEY_ENABLE_CONVERT_RGB_COLORS   = 'dev/mgminifier/active_convert_colors';
	const KEY_ENABLE_CONVERT_HSL_COLORS   = 'dev/mgminifier/active_compress_hsl_colors';
	const KEY_ENABLE_COMPRESS_COLORS      = 'dev/mgminifier/active_compress_colors';
	const KEY_ENABLE_COMPRES_UNIT_VALUES  = 'dev/mgminifier/active_compress_unit_values';
	const KEY_ENABLE_CONVERT_FONT_WEIGHT  = 'dev/mgminifier/active_convert_fontweight';
	const KEY_ENABLE_CONVERT_NAMED_COLORS = 'dev/mgminifier/active_convert_namedcolors';
	const KEY_ENABLE_REPLACE_VARIABLES    = 'dev/mgminifier/active_replace_variables';
	
	
	public function beforeMergeCss($file, $contents) {
		$contents = parent::beforeMergeCss($file, $contents);
		if( !Mage::getStoreConfig(self::KEY_ENABLE_CSS) ) { return $contents; }
		
		$filters = array(
				"ImportImports"                 => false,
				"RemoveComments"                => true, 
				"RemoveEmptyRulesets"           => true,
				"RemoveEmptyAtBlocks"           => true,
				"ConvertLevel3AtKeyframes"      => false,
				"ConvertLevel3Properties"       => false,
				"Variables"                     => true,
				"RemoveLastDelarationSemiColon" => true
		);
		
		$plugins = array(
				"Variables"                     => (bool)Mage::getStoreConfig(self::KEY_ENABLE_REPLACE_VARIABLES),
				"ConvertFontWeight"             => (bool)Mage::getStoreConfig(self::KEY_ENABLE_CONVERT_FONT_WEIGHT),
				"ConvertHslColors"              => (bool)Mage::getStoreConfig(self::KEY_ENABLE_CONVERT_HSL_COLORS),
				"ConvertRgbColors"              => (bool)Mage::getStoreConfig(self::KEY_ENABLE_CONVERT_RGB_COLORS),
				"ConvertNamedColors"            => (bool)Mage::getStoreConfig(self::KEY_ENABLE_CONVERT_NAMED_COLORS),
				"CompressColorValues"           => (bool)Mage::getStoreConfig(self::KEY_ENABLE_COMPRESS_COLORS),
				"CompressUnitValues"            => (bool)Mage::getStoreConfig(self::KEY_ENABLE_COMPRES_UNIT_VALUES),
				"CompressExpressionValues"      => false
		);
		
		return Mandagreen_Minifier_Model_CssMin::minify($contents, $filters, $plugins);
	}
	
	public function getMergedJsUrl($files) {
		$hash = md5(implode(',', $files));
		$targetFilename = $hash . '.js';
		$targetFilenameMerged = $hash . '.min.js';
		$targetDir = $this->_initMergerDir('js');
		
		if( !$targetDir ) {
			return '';
		}
		
		if( Mage::helper('core')->mergeFiles($files, $targetDir . DS . $targetFilename, false, null, 'js') ) {
			if( Mage::getStoreConfig(self::KEY_ENABLE_JS) && !is_file($targetDir . DS . $targetFilenameMerged) ) {
				file_put_contents($targetDir . DS . $targetFilenameMerged, Mandagreen_Minifier_Model_JSMin::minify(file_get_contents($targetDir . DS . $targetFilename)), LOCK_EX);
				$targetFilename = $targetFilenameMerged;
			}
			
			return Mage::getBaseUrl('media') . 'js/' . $targetFilename;
		}
		
		return '';
	}
}