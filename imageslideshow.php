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
        return $this->installer()->install() &&
            parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->installer()->uninstall();
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

    private function installer(): ImageSlideshowInstaller
    {
        require_once _PS_MODULE_DIR_ . 'imageslideshow/install/ImageSlideshowInstaller.php';
        return new ImageSlideshowInstaller($this);
    }
}
