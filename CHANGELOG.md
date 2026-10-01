# Barcode Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 3.0.0 - Unreleased
### Changed
- Craft CMS 5 compatibility (requires Craft 5 and PHP 8.2). Allows `picqer/php-barcode-generator` 3.x.

### Fixed
- The field no longer errors when Craft normalizes its value without an element.
- A template error, an empty number, or a number the barcode type cannot encode now gives the field no value (and is logged) instead of an error page.
- `generate()` with a format other than `png` returns SVG instead of nothing.

## 2.0.1 - 2022-07-20
### Changed
- Updated icon

## 2.0.0 - 2022-06-02

### Added

- Added Craft 4 support

## 1.0.4 - 2022-06-10
### Changed
- Change of ownership

## 1.0.3 - 2020-06-25
### Added
- added EANUPC type doesn't force a 0 prefix on numbers

## 1.0.2 - 2020-05-27
### Changed
- Updated composer dependencies

## 0.0.1 - 2019-02-04
### Added
- Initial release
