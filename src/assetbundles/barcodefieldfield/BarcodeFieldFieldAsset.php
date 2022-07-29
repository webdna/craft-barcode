<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2019 webdna
 */

namespace webdna\barcode\assetbundles\barcodefieldfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    webdna
 * @package   Barcode
 * @since     0.0.1
 */
class BarcodeFieldFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = "@webdna/barcode/assetbundles/barcodefieldfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/BarcodeField.js',
        ];

        $this->css = [
            'css/BarcodeField.css',
        ];

        parent::init();
    }
}
