# Barcode plugin for Craft CMS 4.x

Generate a barcode

## Requirements

This plugin requires Craft CMS 4.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1.  Open your terminal and go to your Craft project:

```
    cd /path/to/project
```

2.  Then tell Composer to load the plugin:

```
    composer require webdna/barcode
```

3.  In the Control Panel, go to Settings → Plugins and click the “Install” button for Barcode.

## Barcode Overview

This allows the generation of a barcode via a fieldtype, variable or twig filter.

## Barcode Options

All instances of barcode accept the following parameters:

1. **Code**: number or alphanumeric depending on the barcode type.
2. **Format**: svg or png (default: svg)
3. **Type**: see below for all accepted types (default: EAN13)
4. **Width** factor: this set with width factor of the bars (default: 2)
5. **Height**: the in pixels of the bars (default: 30)
6. **Color**: the hex value of the bars (default: '#000000')

## Using Barcode

Twig variables:

```
    {{ craft.barcode.generate(123456789) }}

    {{ craft.barcode.generate(123456789, 'png', 'EAN13', 2, 30, '#000000') }}
```

Twig Filters:

```
    {{ 123456789|barcode }}

    {{ 123456789|barcode('png', 'EAN13', 2, 30, '#000000') }}
```

## Supported Barcode Types

| Code     | Name                     |
| -------- | ------------------------ |
| C39      | CODE_39                  |
| C39+     | CODE_39_CHECKSUM         |
| C39E     | CODE_39E                 |
| C39E+    | CODE_39E_CHECKSUM        |
| C93      | CODE_93                  |
| S25      | STANDARD_2_5             |
| S25+     | STANDARD_2_5_CHECKSUM    |
| I25      | INTERLEAVED_2_5          |
| I25+     | INTERLEAVED_2_5_CHECKSUM |
| C128     | CODE_128                 |
| C128A    | CODE_128_A               |
| C128B    | CODE_128_B               |
| C128C    | CODE_128_C               |
| EAN2     | EAN_2                    |
| EAN5     | EAN_5                    |
| EAN8     | EAN_8                    |
| EAN13    | EAN_13                   |
| UPCA     | UPC_A                    |
| UPCE     | UPC_E                    |
| MSI      | MSI                      |
| MSI+     | MSI_CHECKSUM             |
| POSTNET  | POSTNET                  |
| PLANET   | PLANET                   |
| RMS4CC   | RMS4CC                   |
| KIX      | KIX                      |
| IMB      | IMB                      |
| CODABAR  | CODABAR                  |
| CODE11   | CODE_11                  |
| PHARMA   | PHARMA_CODE              |
| PHARMA2T | PHARMA_CODE_TWO_TRACKS   |

## Barcode Roadmap

Some things to do, and ideas for potential features:

-   Release it

Brought to you by [webdna](https://webdna.co.uk)
