<?php

use Symfony\Contracts\Translation\TranslatorInterface;

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

    public function install(TranslatorInterface $translator): bool
    {
        return $this->createTables() && $this->createTabs($translator);
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

    /**
     * based on \PrestaShop\Module\BlockWishList\Database\Install::installTabs
     */
    private function createTabs(TranslatorInterface $translator): bool
    {
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
}
