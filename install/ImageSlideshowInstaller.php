<?php

class ImageSlideshowInstaller
{
    private array $adminControllers = [
        [
            'class_name' => 'ImageSlideshowParentController',
            'visible' => false,
            'parent_class_name' => 'AdminParentModulesSf',
            'name' => 'Image Slideshow',
        ],
        [
            'route_name' => 'imageslideshow_index',
            'class_name' => 'ImageSlideshowController',
            'visible' => false, // true shows useless nav-pill
            'parent_class_name' => 'ImageSlideshowParentController',
            'name' => 'Slideshows',
        ]
    ];

    public function __construct(
        private readonly ImageSlideshow $module,
    ){}

    public function install(): bool
    {
        return $this->createTables() && $this->createTabs() && $this->installSamples();
    }

    public function uninstall(): bool
    {
        return $this->dropTables() && $this->deleteTabs();
    }

    private function createTables(): bool
    {
        $ok = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'image_slideshow` (
            `id_image_slideshow` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_shop`      INT DEFAULT 1    NOT NULL,
            `name`         VARCHAR(128)     NOT NULL,
            `slug`         VARCHAR(132)     NOT NULL,
            `active`       TINYINT(1) UNSIGNED NOT NULL DEFAULT 1
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;');

        $ok = $ok ? Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'image_slideshow_slide` (
            `id_image_slideshow_slide` INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `id_image_slideshow`       INT(10) UNSIGNED    NOT NULL,
            `position`           SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
            `active`             TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            `target_blank`       TINYINT(1) UNSIGNED not null DEFAULT 0,
            CONSTRAINT ' . _DB_PREFIX_ . 'image_slideshow_slide_ibfk_1
                FOREIGN KEY (id_image_slideshow) REFERENCES ' . _DB_PREFIX_ . 'image_slideshow (id_image_slideshow)
                    ON DELETE CASCADE
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;') : $ok;

        return $ok ? Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'image_slideshow_slide_lang` (
            `id_image_slideshow_slide` INT(10) UNSIGNED NOT NULL,
            `id_lang`            INT DEFAULT 1    NOT NULL,
            `title`              VARCHAR(255)     NULL,
            `description`        TEXT             NULL,
            `legend`             VARCHAR(255)     NULL,
            `url`                VARCHAR(255)     NULL,
            `image`              VARCHAR(255)     NOT NULL,
            `image_mobile`       VARCHAR(255)     NULL,
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

    /**
     * based on \PrestaShop\Module\BlockWishList\Database\Install::installTabs
     */
    private function createTabs(): bool
    {
        $translator = $this->module->getTranslator();
        $installTabCompleted = true;
        foreach ($this->adminControllers as $controller) {
            if (Tab::getIdFromClassName($controller['class_name']) || !$installTabCompleted) {
                continue;
            }

            $tab = new Tab();
            $tab->class_name = $controller['class_name'];
            $tab->active = $controller['visible'];
            foreach (Language::getLanguages() as $lang) {
                $tab->name[$lang['id_lang']] = $translator->trans($controller['name'], [], 'Modules.Imageslideshow.Imageslideshow', $lang['locale']);
            }
            $tab->id_parent = Tab::getIdFromClassName($controller['parent_class_name']);
            $tab->module = 'imageslideshow';
            $tab->route_name = $controller['route_name'] ?? '';
            $installTabCompleted = $tab->add();
        }

        return $installTabCompleted;
    }

    /**
     * based on \PrestaShop\Module\BlockWishList\Database\Uninstall::uninstallTabs
     */
    private function deleteTabs(): bool
    {
        $uninstallTabCompleted = true;

        foreach ($this->adminControllers as $controller) {
            $id_tab = (int) Tab::getIdFromClassName($controller['class_name']);
            $tab = new Tab($id_tab);
            if (Validate::isLoadedObject($tab)) {
                $uninstallTabCompleted = $uninstallTabCompleted && $tab->delete();
            }
        }

        return $uninstallTabCompleted;
    }

    /**
     * Adds samples
     */
    private function installSamples(): bool
    {
        /** @var PrestaShopBundle\Doctrine\DatabaseConnection $conn */
        $conn = $this->module->get('doctrine.dbal.default_connection');

        $conn->insert(_DB_PREFIX_ . 'image_slideshow', ['name' => 'Home', 'slug' => 'home']);
        $slideshowId = $conn->lastInsertId();

        $languages = Language::getLanguages(false);
        for ($i = 1; $i <= 3; ++$i) {

            $conn->insert(_DB_PREFIX_ . 'image_slideshow_slide', [
                'id_image_slideshow' => $slideshowId,
                'position' => $i - 1
            ]);
            $sildeId = $conn->lastInsertId();

            $image = "sample-$i.jpg";
            @copy(__DIR__ . "/$image", __DIR__ . "/../images/$image");

            foreach ($languages as $language) {
                $conn->insert(_DB_PREFIX_ . 'image_slideshow_slide_lang', [
                    'id_image_slideshow_slide' => $sildeId,
                    'id_lang' => $language['id_lang'],
                    'title' => "Sample $i",
                    'image' => $image,
                    'url' => '/',
                    'description' => '<h3>EXCEPTEUR OCCAECAT</h3>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tristique in tortor et dignissim. Quisque non tempor leo. Maecenas egestas sem elit</p>'
                ]);
            }
        }

        return true;
    }
}
