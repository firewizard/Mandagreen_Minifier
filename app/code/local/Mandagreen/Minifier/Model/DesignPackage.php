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
	
	
	public function getMergedCssUrl($files) {
		// secure or unsecure
		$isSecure = Mage::app()->getRequest()->isSecure();
		$mergerDir = $isSecure ? 'css_secure' : 'css';
		$targetDir = $this->_initMergerDir($mergerDir);
		if( !$targetDir ) {
			return '';
		}

		// base hostname & port
		$baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
		$hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
		$port = parse_url($baseMediaUrl, PHP_URL_PORT);
		if( false === $port ) {
			$port = $isSecure ? 443 : 80;
		}

		/* add more variables to the hashed name so that merged file name depend on the content */
		$entropy = '';
		foreach( $files as $file ) {
			$entropy .= md5_file($file);
		}
		/* end */
		
		// merge into target file
		$targetFilename = md5(implode(',', $files) . "|$entropy|{$hostname}|{$port}") . '.css';
		if( $this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, array($this, 'beforeMergeCss'), 'css') ) {
			return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
		}
		
		return '';
	}
	
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
				"CompressExpressionValues"      => false,
		);
		
		$parserPlugins = array(
				'Comment'     => true,
				'String'      => true,
				'Url'         => true,
				'Expression'  => true,
				'Ruleset'     => true,
				'AtCharset'   => true,
				'AtFontFace'  => true,
				'AtImport'    => true,
				'AtKeyframes' => false,
				'AtMedia'     => true,
				'AtPage'      => true,
				'AtVariables' => true,
		);
		
		return Mandagreen_Minifier_Model_CssMin::minify($contents, $filters, $plugins, $parserPlugins);
	}
	
	public function getMergedJsUrl($files) {
		/* add more variables to the hashed name so that merged file name depend on the content */
		$entropy = '';
		foreach( $files as $file ) {
			$entropy .= md5_file($file);
		}
		/* end */
		
		$hash = md5(implode(',', $files) . "|$entropy");
		$targetFilename = $hash . '.js';
		$targetFilenameMerged = $hash . '.min.js';
		$targetDir = $this->_initMergerDir('js');
		
		if( !$targetDir ) {
			return '';
		}
		
		if( is_file($targetDir . DS . $targetFilenameMerged) ) {
			return Mage::getBaseUrl('media') . 'js/' . $targetFilenameMerged;
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