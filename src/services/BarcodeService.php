<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\barcode\services;

use kuriousagency\barcode\Barcode;

use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;

use Craft;
use craft\base\Component;
use craft\helpers\Template;

/**
 * @author    Kurious Agency
 * @package   Barcode
 * @since     0.0.1
 */
class BarcodeService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function generate($number, $type='EAN13', $width=2, $height=30, $color='#000000')
    {	
		return $this->generateSVG($number, $type, $width, $height, $color);
	}
	
	public function generateSVG($number, $type='EAN13', $width=2, $height=30, $color='#000000')
    {
		$generator = new BarcodeGeneratorSVG();
		
		return Template::raw($generator->getBarcode($number, $type, $width, $height, $color));
	}

	public function generatePNG($number, $type='EAN13', $width=2, $height=30, $color='#000000')
    {
		$color = sscanf($color, "#%02x%02x%02x");
		$generator = new BarcodeGeneratorPNG();
		
		return Template::raw("data:image/png;base64," . base64_encode($generator->getBarcode($number, $type, $width, $height, $color)));
	}

	public function getTypes()
	{
		return [
			'C39' => 'CODE_39',
			'C39+' => 'CODE_39_CHECKSUM',
			'C39E' => 'CODE_39E',
			'C39E+' => 'CODE_39E_CHECKSUM',
			'C93' => 'CODE_93',
			'S25' => 'STANDARD_2_5',
			'S25+' => 'STANDARD_2_5_CHECKSUM',
			'I25' => 'INTERLEAVED_2_5',
			'I25+' => 'INTERLEAVED_2_5_CHECKSUM',
			'C128' => 'CODE_128',
			'C128A' => 'CODE_128_A',
			'C128B' => 'CODE_128_B',
			'C128C' => 'CODE_128_C',
			'EAN2' => 'EAN_2',
			'EAN5' => 'EAN_5',
			'EAN8' => 'EAN_8',
			'EAN13' => 'EAN_13',
			'UPCA' => 'UPC_A',
			'UPCE' => 'UPC_E',
			'MSI' => 'MSI',
			'MSI+' => 'MSI_CHECKSUM',
			'POSTNET' => 'POSTNET',
			'PLANET' => 'PLANET',
			'RMS4CC' => 'RMS4CC',
			'KIX' => 'KIX',
			'IMB' => 'IMB',
			'CODABAR' => 'CODABAR',
			'CODE11' => 'CODE_11',
			'PHARMA' => 'PHARMA_CODE',
			'PHARMA2T' => 'PHARMA_CODE_TWO_TRACKS',
		];
	}
}
