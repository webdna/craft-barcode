<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\barcode\fields;

use kuriousagency\barcode\Barcode;
// use kuriousagency\barcode\assetbundles\barcodefieldfield\BarcodeFieldFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    Kurious Agency
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
	public $property;
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

    // Public Methods
	// =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
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
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
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
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
		// Craft::$app->getView()->registerAssetBundle(BarcodeFieldFieldAsset::class);
		if (!$value) {
			$type = explode('\\',get_class($element));
			$type = strtolower(end($type));
			$number = Craft::$app->getView()->renderString($this->property, [$type=>$element]);
			$value = Barcode::$plugin->service->generateSVG($number);
		}

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
