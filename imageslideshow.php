<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class ImageSlideshow extends Module implements WidgetInterface
{

    public function __construct()
    {
        $this->name = 'imageslideshow';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'diezsiete';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Image Slideshow', [], 'Modules.Imageslideshow.Imageslideshow');
        $this->description = $this->trans('Add sliding images to your store.', [], 'Modules.Imageslideshow.Imageslideshow');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayHome') &&
            $this->createTables();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->dropTables();
    }

    public function getContent()
    {
        $route = $this->get('router')->generate('imageslideshow_index');
        Tools::redirectAdmin($route);
    }

    public function hookdisplayHeader(): void
    {
        // $this->context->controller->registerStylesheet('modules-homeslider', 'modules/' . $this->name . '/css/homeslider.css', ['media' => 'all', 'priority' => 150]);
        // $this->context->controller->registerJavascript('modules-responsiveslides', 'modules/' . $this->name . '/js/responsiveslides.min.js', ['position' => 'bottom', 'priority' => 150]);
        // $this->context->controller->registerJavascript('modules-homeslider', 'modules/' . $this->name . '/js/homeslider.js', ['position' => 'bottom', 'priority' => 150]);
    }

    public function renderWidget($hookName, array $configuration)
    {

    }

    public function getWidgetVariables($hookName, array $configuration)
    {

    }

    private function createTables(): bool
    {
        $ok = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'image_slideshow` (
            `id_image_slideshow` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_shop`      INT DEFAULT 1    NOT NULL,
            `name`         VARCHAR(128)     NOT NULL,
            `slug`         VARCHAR(132)     NOT NULL,
            `active`       tinyint(1) unsigned NOT NULL DEFAULT 1
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;');

        $ok = $ok ? Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'image_slideshow_slide` (
            `id_image_slideshow_slide` INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_image_slideshow`       INT(10) UNSIGNED    NOT NULL,
            `position`           smallint unsigned   NOT NULL DEFAULT 0,
            `active`             tinyint(1) unsigned NOT NULL DEFAULT 1,
            `target_blank`       tinyint(1) unsigned not null DEFAULT 0,
            CONSTRAINT ' . _DB_PREFIX_ . 'image_slideshow_slide_ibfk_1
                FOREIGN KEY (id_image_slideshow) REFERENCES ' . _DB_PREFIX_ . 'image_slideshow (id_image_slideshow)
                    ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;') : $ok;

        return $ok ? Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'image_slideshow_slide_lang` (
            `id_image_slideshow_slide` INT(10) UNSIGNED NOT NULL,
            `id_lang`            INT DEFAULT 1    NOT NULL,
            `title`              varchar(255)     NOT NULL,
            `description`        text             NULL,
            `legend`             varchar(255)     NULL,
            `url`                varchar(255)     NOT NULL,
            `image`              varchar(255)     NOT NULL,
            `image_mobile`       varchar(255)     NULL,
            PRIMARY KEY (id_image_slideshow_slide, id_lang),
            CONSTRAINT ' . _DB_PREFIX_ . 'image_slideshow_slide_lang_ibfk_1
                FOREIGN KEY (id_image_slideshow_slide) REFERENCES ' . _DB_PREFIX_ . 'image_slideshow_slide (id_image_slideshow_slide)
                    ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;') : $ok;
    }

    private function dropTables(): bool
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'image_slideshow_slide_lang`, `' . _DB_PREFIX_ . 'image_slideshow_slide`, `' . _DB_PREFIX_ . 'image_slideshow`;
        ');
    }
}
