<?php

class Mandagreen_Minifier_Core_Model_Design_Package extends Mage_Core_Model_Design_Package
{
    const KEY_ENABLE_JS                   = 'dev/mgminifier/active_js';
    const KEY_ENABLE_CSS                  = 'dev/mgminifier/active_css';
    const KEY_MERGE_CSS_BY_HANDLE         = 'dev/mgminifier/merge_css_by_handle';
    const KEY_MERGE_JS_BY_HANDLE          = 'dev/mgminifier/merge_js_by_handle';
    const KEY_CLEAN_HANDLES               = 'dev/mgminifier/clean_handles';
    const KEY_ENABLE_CONVERT_RGB_COLORS   = 'dev/mgminifier/active_convert_colors';
    const KEY_ENABLE_CONVERT_HSL_COLORS   = 'dev/mgminifier/active_compress_hsl_colors';
    const KEY_ENABLE_COMPRESS_COLORS      = 'dev/mgminifier/active_compress_colors';
    const KEY_ENABLE_COMPRES_UNIT_VALUES  = 'dev/mgminifier/active_compress_unit_values';
    const KEY_ENABLE_CONVERT_FONT_WEIGHT  = 'dev/mgminifier/active_convert_fontweight';
    const KEY_ENABLE_CONVERT_NAMED_COLORS = 'dev/mgminifier/active_convert_namedcolors';
    const KEY_ENABLE_REPLACE_VARIABLES    = 'dev/mgminifier/active_replace_variables';
    
    
    public function getMergedCssUrl($files)
    {
        #Varien_Profiler::start('Mandagreen_Minifier::getMergedCssUrl');
        // secure or unsecure
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'css_secure' : 'css';
        $targetDir = $this->_initMergerDir($mergerDir);
        if (!$targetDir) {
            return '';
        }

        // base hostname & port
        $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
        $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
        $port = parse_url($baseMediaUrl, PHP_URL_PORT);
        if (false === $port) {
            $port = $isSecure ? 443 : 80;
        }

        /* add more variables to the hashed name so that merged file name depend on the content */
        $entropy = '';
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $entropy .= md5_file($file);
        }
        /* end */

        $targetFilename = md5(implode(',', $files) . "|$entropy|{$hostname}|{$port}") . '.css';
        if (is_file($targetDir . DS . $targetFilename)) {
            #Varien_Profiler::stop('Mandagreen_Minifier::getMergedCssUrl');
            return Mage::getBaseUrl('media') . $mergerDir . '/' . $targetFilename;
        }

        // merge into target file
        if ($this->_mergeFiles($files, $targetDir.DS.$targetFilename, false, array($this, 'beforeMergeCss'), 'css')) {
            #Varien_Profiler::stop('Mandagreen_Minifier::getMergedCssUrl');
            return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
        }

        #Varien_Profiler::stop('Mandagreen_Minifier::getMergedCssUrl');
        return '';
    }
    
    public function beforeMergeCss($file, $contents)
    {
        $contents = parent::beforeMergeCss($file, $contents);
        if (!Mage::getStoreConfig(self::KEY_ENABLE_CSS)) {
            return $contents;
        }

        $profilerKey = "Mandagreen_Minifier::beforeMergeCss ($file)";
        #Varien_Profiler::start($profilerKey);
        
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
        
        $ret = Mandagreen_Minifier_Model_CssMin::minify($contents, $filters, $plugins, $parserPlugins);
        #Varien_Profiler::stop($profilerKey);
        return $ret;
    }
    
    public function getMergedJsUrl($files)
    {
        #Varien_Profiler::start('Mandagreen_Minifier::getMergedJsUrl');
        /* add more variables to the hashed name so that merged file name depend on the content */
        $entropy = '';
        foreach( $files as $file) {
            if (!is_file($file)) { continue; }
            
            $entropy .= md5_file($file);
        }
        /* end */
        
        $hash = md5(implode(',', $files) . "|$entropy");
        $targetFilename = $hash . '.js';
        $targetFilenameMerged = $hash . '.min.js';
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'js_secure' : 'js';
        $targetDir = $this->_initMergerDir($mergerDir);

        if (!$targetDir) {
            return '';
        }

        if (is_file($targetDir . DS . $targetFilenameMerged)) {
            #Varien_Profiler::stop('Mandagreen_Minifier::getMergedJsUrl');
            return Mage::getBaseUrl('media') . $mergerDir . '/' . $targetFilenameMerged;
        }

        if (Mage::helper('core')->mergeFiles($files, $targetDir . DS . $targetFilename, false, null, 'js')) {
            if (Mage::getStoreConfig(self::KEY_ENABLE_JS) && !is_file($targetDir . DS . $targetFilenameMerged)) {
                file_put_contents(
                    $targetDir . DS . $targetFilenameMerged,
                    Mandagreen_Minifier_Model_JSMin::minify(file_get_contents($targetDir . DS . $targetFilename)),
                    LOCK_EX
                );

                $targetFilename = $targetFilenameMerged;
            }

            #Varien_Profiler::stop('Mandagreen_Minifier::getMergedJsUrl');
            return Mage::getBaseUrl('media') . $mergerDir . '/' . $targetFilename;
        }

        #Varien_Profiler::stop('Mandagreen_Minifier::getMergedJsUrl');
        return '';
    }
}