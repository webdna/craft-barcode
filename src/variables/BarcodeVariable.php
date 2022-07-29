<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2019 webdna
 */

namespace webdna\barcode\variables;

use webdna\barcode\Barcode;

use Craft;

/**
 * @author    webdna
 * @package   Barcode
 * @since     0.0.1
 */
class BarcodeVariable
{

    // Public Methods
	// =========================================================================

	public function generate(string $number, ?string $format = 'svg', ?string $type = 'EAN13', ?int $width = 2, ?int $height = 30, ?string $color = '#000000'): string
	{
		if ($format == 'svg') {
			return Barcode::$plugin->service->generateSVG($number, $type, $width, $height, $color);
		}
		if ($format == 'png') {
			return Barcode::$plugin->service->generatePNG($number, $type, $width, $height, $color);
		}
	}

	public function generateSVG(string $number, ?string $type = 'EAN13', ?int $width = 2, ?int $height = 30, ?string $color = '#000000'): string
	{
		return Barcode::$plugin->service->generateSVG($number, $type, $width, $height, $color);
	}

	public function generatePNG(string $number, ?string $type = 'EAN13', ?int $width = 2, ?int $height = 30, ?string $color = '#000000'): string
	{
		return Barcode::$plugin->service->generatePNG($number, $type, $width, $height, $color);
	}

}
