<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2019 webdna
 */

namespace webdna\barcode\fields;

use webdna\barcode\Barcode;
// use webdna\barcode\assetbundles\barcodefieldfield\BarcodeFieldFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    webdna
 * @package   Barcode
 * @since     0.0.1
 */
class BarcodeField extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $property = '';
    public $type = 'EAN13';
    public $width = 2;
    public $height = 30;
    public $color = "#000000";

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('barcode', 'Barcode');
    }

    /**
     * @inheritdoc
     */
    public static function icon(): string
    {
        return __DIR__ . '/../assetbundles/barcodefieldfield/dist/img/BarcodeField-icon.svg';
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['property', 'string'],
            ['property', 'required'],
        ]);
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        // Craft 5 normalizes some values with no element (previews, condition rules), so only pass one when there is one.
        $variables = [];
        if ($element !== null) {
            $typeParts = explode('\\', get_class($element));
            $variables[strtolower(end($typeParts))] = $element;
        }

        try {
            $number = trim(Craft::$app->getView()->renderString((string)$this->property, $variables));
            if ($number === '') {
                return null;
            }
            return Barcode::$plugin->service->generateSVG($number);
        } catch (\Throwable $e) {
            // A template error, or a number the barcode type can't encode, must not take the element's page down with it.
            Craft::$app->getErrorHandler()->logException($e);
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'barcode/_components/fields/BarcodeField_settings',
            [
                'field' => $this,
                'types' => Barcode::$plugin->service->getTypes(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        // Register our asset bundle
        // Craft::$app->getView()->registerAssetBundle(BarcodeFieldFieldAsset::class);
        /*if (!$value) {

        }*/

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            ];
        $jsonVars = Json::encode($jsonVars);
        // Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').BarcodeBarcodeField(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'barcode/_components/fields/BarcodeField_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }

}
