# Barcode plugin for Craft CMS 3.x

Generate a barcode

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1.  Open your terminal and go to your Craft project:

        cd /path/to/project

2.  Then tell Composer to load the plugin:

        composer require kuriousagency/barcode

3.  In the Control Panel, go to Settings → Plugins and click the “Install” button for Barcode.

## Barcode Overview

This allows the generation of a barcode via a fieldtype, variable or twig filter.

## Configuring Barcode

-Insert text here-

## Using Barcode

    {{ craft.barcode.generate(123456789) }}

    {{ craft.barcode.generate(123456789, 'EAN13', 2, 30, '#000000') }}

    {{ 123456789|barcode }}

    {{ 123456789|barcode('EAN13', 2, 30, '#000000') }}

## Barcode Roadmap

Some things to do, and ideas for potential features:

-   Release it

Brought to you by [Kurious Agency](https://kurious.agency)
