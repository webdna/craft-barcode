<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2019 webdna
 */

namespace webdna\barcode\services;

use webdna\barcode\Barcode;

use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\Exceptions\BarcodeException;
use Picqer\Barcode\Exceptions\InvalidCharacterException;
use Picqer\Barcode\Exceptions\InvalidCheckDigitException;

use Twig\Markup;

use Craft;
use craft\base\Component;
use craft\helpers\Template;

/**
 * @author    webdna
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
    public function generate(string $number, ?string $type = 'EAN13', ?int $width = 2, ?int $height = 30, ?string $color = '#000000'): Markup
    {
        return $this->generateSVG($number, $type, $width, $height, $color);
    }

    public function generateSVG(string $number, ?string $type = 'EAN13', ?int $width = 2, ?int $height = 30, ?string $color = '#000000'): Markup
    {
        if ($type == 'EANUPC') {
            $image = $this->getBarcodeSVG($number, $type, $width, $height, $color);
        } else {
            $generator = new BarcodeGeneratorSVG();
            $image = $generator->getBarcode($number, $type, $width, $height, $color);
        }

        return Template::raw($image);
    }

    public function generatePNG(string $number, ?string $type = 'EAN13', ?int $width = 2, ?int $height = 30, ?string $color = '#000000'): Markup
    {
        $color = sscanf($color, "#%02x%02x%02x");

        if ($type == 'EANUPC') {
            $image = $this->getBarcodePNG($number, $type, $width, $height, $color);
        } else {
            $generator = new BarcodeGeneratorPNG();
            $image = $generator->getBarcode($number, $type, $width, $height, $color);
        }

        return Template::raw("data:image/png;base64," . base64_encode($image));
    }

    public function getTypes(): array
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
            'EANUPC' => 'EANUPC',
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

    private function getBarcodePNG(string $code, string $type, ?int $widthFactor = 2, ?int $totalHeight = 30, ?array $color = array(0, 0, 0)): ?string
    {
        $barcodeData = $this->getBarcodeData($code, strlen($code));

        // calculate image size
        $width = ($barcodeData['maxWidth'] * $widthFactor);
        $height = $totalHeight;

        if (function_exists('imagecreate')) {
            // GD library
            $imagick = false;
            $png = imagecreate($width, $height);
            $colorBackground = imagecolorallocate($png, 255, 255, 255);
            imagecolortransparent($png, $colorBackground);
            $colorForeground = imagecolorallocate($png, $color[0], $color[1], $color[2]);
        } elseif (extension_loaded('imagick')) {
            $imagick = true;
            $colorForeground = new \imagickpixel('rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')');
            $png = new \Imagick();
            $png->newImage($width, $height, 'none', 'png');
            $imageMagickObject = new \imagickdraw();
            $imageMagickObject->setFillColor($colorForeground);
        } else {
            throw new BarcodeException('Neither gd-lib or imagick are installed!');
        }

        // print bars
        $positionHorizontal = 0;
        foreach ($barcodeData['bars'] as $bar) {
            $bw = round(($bar['width'] * $widthFactor), 3);
            $bh = round(($bar['height'] * $totalHeight / $barcodeData['maxHeight']), 3);
            if ($bar['drawBar']) {
                $y = round(($bar['positionVertical'] * $totalHeight / $barcodeData['maxHeight']), 3);
                // draw a vertical bar
                if ($imagick && isset($imageMagickObject)) {
                    $imageMagickObject->rectangle($positionHorizontal, $y, ($positionHorizontal + $bw), ($y + $bh));
                } else {
                    imagefilledrectangle($png, $positionHorizontal, $y, ($positionHorizontal + $bw) - 1, ($y + $bh),
                        $colorForeground);
                }
            }
            $positionHorizontal += $bw;
        }
        ob_start();
        if ($imagick && isset($imageMagickObject)) {
            $png->drawImage($imageMagickObject);
            echo $png;
        } else {
            imagepng($png);
            imagedestroy($png);
        }
        $image = ob_get_clean();

        return $image;
    }

    private function getBarcodeSVG(string $code, string $type, ?int $widthFactor = 2, ?int $totalHeight = 30, ?string $color = 'black'): string
    {
        $barcodeData = $this->getBarcodeData($code, strlen($code));

        // replace table for special characters
        $repstr = array("\0" => '', '&' => '&amp;', '<' => '&lt;', '>' => '&gt;');

        $width = round(($barcodeData['maxWidth'] * $widthFactor), 3);

        $svg = '<?xml version="1.0" standalone="no" ?>' . "\n";
        $svg .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . "\n";
        $svg .= '<svg width="' . $width . '" height="' . $totalHeight . '" viewBox="0 0 ' . $width . ' ' . $totalHeight . '" version="1.1" xmlns="http://www.w3.org/2000/svg">' . "\n";
        $svg .= "\t" . '<desc>' . strtr($barcodeData['code'], $repstr) . '</desc>' . "\n";
        $svg .= "\t" . '<g id="bars" fill="' . $color . '" stroke="none">' . "\n";
        // print bars
        $positionHorizontal = 0;
        foreach ($barcodeData['bars'] as $bar) {
            $barWidth = round(($bar['width'] * $widthFactor), 3);
            $barHeight = round(($bar['height'] * $totalHeight / $barcodeData['maxHeight']), 3);
            if ($bar['drawBar']) {
                $positionVertical = round(($bar['positionVertical'] * $totalHeight / $barcodeData['maxHeight']), 3);
                // draw a vertical bar
                $svg .= "\t\t" . '<rect x="' . $positionHorizontal . '" y="' . $positionVertical . '" width="' . $barWidth . '" height="' . $barHeight . '" />' . "\n";
            }
            $positionHorizontal += $barWidth;
        }
        $svg .= "\t" . '</g>' . "\n";
        $svg .= '</svg>' . "\n";

        return $svg;
    }

    private function getBarcodeData(string $code, ?int $len = 13): array
    {
        $upce = false;
        if ($len == 6) {
            $len = 12; // UPC-A
            $upce = true; // UPC-E mode
        }
        $data_len = $len;// - 1;
        //Padding
        $code = str_pad($code, $data_len, '0', STR_PAD_LEFT);
        $code_len = strlen($code);
        // calculate check digit
        $sum_a = 0;
        for ($i = 1; $i < $data_len; $i += 2) {
            $sum_a += $code[$i];
        }
        if ($len > 12) {
            $sum_a *= 3;
        }
        $sum_b = 0;
        for ($i = 0; $i < $data_len; $i += 2) {
            $sum_b += ($code[$i]);
        }
        if ($len < 13) {
            $sum_b *= 3;
        }
        $r = ($sum_a + $sum_b) % 10;
        if ($r > 0) {
            $r = (10 - $r);
        }
        if ($code_len == $data_len) {
            // add check digit
            $code .= $r;
        } elseif ($r !== intval($code[$data_len])) {
            throw new InvalidCheckDigitException();
        }
        if ($len == 12) {
            // UPC-A
            $code = '' . $code;
            ++$len;
        }
        if ($upce) {
            // convert UPC-A to UPC-E
            $tmp = substr($code, 4, 3);
            if (($tmp == '000') OR ($tmp == '100') OR ($tmp == '200')) {
                // manufacturer code ends in 000, 100, or 200
                $upce_code = substr($code, 2, 2) . substr($code, 9, 3) . substr($code, 4, 1);
            } else {
                $tmp = substr($code, 5, 2);
                if ($tmp == '00') {
                    // manufacturer code ends in 00
                    $upce_code = substr($code, 2, 3) . substr($code, 10, 2) . '3';
                } else {
                    $tmp = substr($code, 6, 1);
                    if ($tmp == '0') {
                        // manufacturer code ends in 0
                        $upce_code = substr($code, 2, 4) . substr($code, 11, 1) . '4';
                    } else {
                        // manufacturer code does not end in zero
                        $upce_code = substr($code, 2, 5) . substr($code, 11, 1);
                    }
                }
            }
        }
        //Convert digits to bars
        $codes = array(
            'A' => array( // left odd parity
                '0' => '0001101',
                '1' => '0011001',
                '2' => '0010011',
                '3' => '0111101',
                '4' => '0100011',
                '5' => '0110001',
                '6' => '0101111',
                '7' => '0111011',
                '8' => '0110111',
                '9' => '0001011'
            ),
            'B' => array( // left even parity
                '0' => '0100111',
                '1' => '0110011',
                '2' => '0011011',
                '3' => '0100001',
                '4' => '0011101',
                '5' => '0111001',
                '6' => '0000101',
                '7' => '0010001',
                '8' => '0001001',
                '9' => '0010111'
            ),
            'C' => array( // right
                '0' => '1110010',
                '1' => '1100110',
                '2' => '1101100',
                '3' => '1000010',
                '4' => '1011100',
                '5' => '1001110',
                '6' => '1010000',
                '7' => '1000100',
                '8' => '1001000',
                '9' => '1110100'
            )
        );
        $parities = array(
            '0' => array('A', 'A', 'A', 'A', 'A', 'A'),
            '1' => array('A', 'A', 'B', 'A', 'B', 'B'),
            '2' => array('A', 'A', 'B', 'B', 'A', 'B'),
            '3' => array('A', 'A', 'B', 'B', 'B', 'A'),
            '4' => array('A', 'B', 'A', 'A', 'B', 'B'),
            '5' => array('A', 'B', 'B', 'A', 'A', 'B'),
            '6' => array('A', 'B', 'B', 'B', 'A', 'A'),
            '7' => array('A', 'B', 'A', 'B', 'A', 'B'),
            '8' => array('A', 'B', 'A', 'B', 'B', 'A'),
            '9' => array('A', 'B', 'B', 'A', 'B', 'A')
        );
        $upce_parities = array();
        $upce_parities[0] = array(
            '0' => array('B', 'B', 'B', 'A', 'A', 'A'),
            '1' => array('B', 'B', 'A', 'B', 'A', 'A'),
            '2' => array('B', 'B', 'A', 'A', 'B', 'A'),
            '3' => array('B', 'B', 'A', 'A', 'A', 'B'),
            '4' => array('B', 'A', 'B', 'B', 'A', 'A'),
            '5' => array('B', 'A', 'A', 'B', 'B', 'A'),
            '6' => array('B', 'A', 'A', 'A', 'B', 'B'),
            '7' => array('B', 'A', 'B', 'A', 'B', 'A'),
            '8' => array('B', 'A', 'B', 'A', 'A', 'B'),
            '9' => array('B', 'A', 'A', 'B', 'A', 'B')
        );
        $upce_parities[1] = array(
            '0' => array('A', 'A', 'A', 'B', 'B', 'B'),
            '1' => array('A', 'A', 'B', 'A', 'B', 'B'),
            '2' => array('A', 'A', 'B', 'B', 'A', 'B'),
            '3' => array('A', 'A', 'B', 'B', 'B', 'A'),
            '4' => array('A', 'B', 'A', 'A', 'B', 'B'),
            '5' => array('A', 'B', 'B', 'A', 'A', 'B'),
            '6' => array('A', 'B', 'B', 'B', 'A', 'A'),
            '7' => array('A', 'B', 'A', 'B', 'A', 'B'),
            '8' => array('A', 'B', 'A', 'B', 'B', 'A'),
            '9' => array('A', 'B', 'B', 'A', 'B', 'A')
        );
        $k = 0;
        $seq = '101'; // left guard bar
        if ($upce) {
            $bararray = array('code' => $upce_code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
            $p = $upce_parities[$code[1]][$r];
            for ($i = 0; $i < 6; ++$i) {
                $seq .= $codes[$p[$i]][$upce_code[$i]];
            }
            $seq .= '010101'; // right guard bar
        } else {
            $bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
            $half_len = intval(ceil($len / 2));
            if ($len == 8) {
                for ($i = 0; $i < $half_len; ++$i) {
                    $seq .= $codes['A'][$code[$i]];
                }
            } else {
                $p = $parities[$code[0]];
                for ($i = 1; $i < $half_len; ++$i) {
                    $seq .= $codes[$p[$i - 1]][$code[$i]];
                }
            }
            $seq .= '01010'; // center guard bar
            for ($i = $half_len; $i < $len; ++$i) {
                if ( ! isset($codes['C'][$code[$i]])) {
                    throw new InvalidCharacterException('Char ' . $code[$i] . ' not allowed');
                }
                $seq .= $codes['C'][$code[$i]];
            }
            $seq .= '101'; // right guard bar
        }
        $clen = strlen($seq);
        $w = 0;
        for ($i = 0; $i < $clen; ++$i) {
            $w += 1;
            if (($i == ($clen - 1)) OR (($i < ($clen - 1)) AND ($seq[$i] != $seq[($i + 1)]))) {
                if ($seq[$i] == '1') {
                    $t = true; // bar
                } else {
                    $t = false; // space
                }
                $bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
                $bararray['maxw'] += $w;
                ++$k;
                $w = 0;
            }
        }

        //return $bararray;

        if ( ! isset($bararray['maxWidth'])) {
            $bararray = $this->convertBarcodeArrayToNewStyle($bararray);
        }

        return $bararray;
    }

    private function convertBarcodeArrayToNewStyle(array $oldBarcodeArray): array
    {
        $newBarcodeArray = [];
        $newBarcodeArray['code'] = $oldBarcodeArray['code'];
        $newBarcodeArray['maxWidth'] = $oldBarcodeArray['maxw'];
        $newBarcodeArray['maxHeight'] = $oldBarcodeArray['maxh'];
        $newBarcodeArray['bars'] = [];
        foreach ($oldBarcodeArray['bcode'] as $oldbar) {
            $newBar = [];
            $newBar['width'] = $oldbar['w'];
            $newBar['height'] = $oldbar['h'];
            $newBar['positionVertical'] = $oldbar['p'];
            $newBar['drawBar'] = $oldbar['t'];
            $newBar['drawSpacing'] = ! $oldbar['t'];

            $newBarcodeArray['bars'][] = $newBar;
        }

        return $newBarcodeArray;
    }
}
