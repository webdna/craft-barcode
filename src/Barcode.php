<?php
/**
 * Barcode plugin for Craft CMS 3.x
 *
 * Generate a barcode
 *
 * @link      https://webdna.co.uk
 * @copyright Copyright (c) 2019 webdna
 */

namespace webdna\barcode;

use webdna\barcode\services\BarcodeService;
use webdna\barcode\variables\BarcodeVariable;
use webdna\barcode\twigextensions\BarcodeTwigExtension;
use webdna\barcode\fields\BarcodeField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Class Barcode
 *
 * @author    webdna
 * @package   Barcode
 * @since     0.0.1
 *
 * @property  Service $barcodeService
 */
class Barcode extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Barcode
     */
    public static Plugin $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '0.0.1';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'service' => BarcodeService::class,
        ]);

        Craft::$app->view->registerTwigExtension(new BarcodeTwigExtension());

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = BarcodeField::class;
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('barcode', BarcodeVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'barcode',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
