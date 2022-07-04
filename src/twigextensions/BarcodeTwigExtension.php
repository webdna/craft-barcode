<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2019 webdna
 */

namespace webdna\barcode\twigextensions;

use webdna\barcode\Barcode;

use Craft;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author    webdna
 * @package   Barcode
 * @since     0.0.1
 */
class BarcodeTwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Barcode';
    }

    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('barcode', [$this, 'generate']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('barcode', [$this, 'generate']),
        ];
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function generate($number, $format="svg", $type='EAN13', $width=2, $height=30, $color='#000000'): string
    {
        if (!$number) {
            return '';
        }

        if ($format == 'svg') {
            return Barcode::$plugin->service->generateSVG($number, $type, $width, $height, $color);
        }
        if ($format == 'png') {
            return Barcode::$plugin->service->generatePNG($number, $type, $width, $height, $color);
        }
    }
}
