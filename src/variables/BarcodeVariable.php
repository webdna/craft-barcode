<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\barcode\variables;

use kuriousagency\barcode\Barcode;

use Craft;

/**
 * @author    Kurious Agency
 * @package   Barcode
 * @since     0.0.1
 */
class BarcodeVariable
{

    // Public Methods
	// =========================================================================
	
	public function generate($number, $format='svg', $type='EAN13', $width=2, $height=30, $color='#000000')
	{
		if ($format == 'svg') {
			return Barcode::$plugin->service->generateSVG($number, $type, $width, $height, $color);
		}
		if ($format == 'png') {
			return Barcode::$plugin->service->generatePNG($number, $type, $width, $height, $color);
		}
	}

	public function generateSVG($number, $type='EAN13', $width=2, $height=30, $color='#000000')
	{
		return Barcode::$plugin->service->generateSVG($number, $type, $width, $height, $color);
	}

	public function generatePNG($number, $type='EAN13', $width=2, $height=30, $color='#000000')
	{
		return Barcode::$plugin->service->generatePNG($number, $type, $width, $height, $color);
	}

}
