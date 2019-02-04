<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\barcode\twigextensions;

use kuriousagency\barcode\Barcode;

use Craft;

/**
 * @author    Kurious Agency
 * @package   Barcode
 * @since     0.0.1
 */
class BarcodeTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Barcode';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('barcode', [$this, 'generate']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('barcode', [$this, 'generate']),
        ];
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function generate($number, $type='EAN13', $width=2, $height=30, $color='#000000')
    {
        if (!$number) {
			return '';
		}

		return Barcode::$plugin->service->generate($number, $type, $width, $height, $color);
    }
}
