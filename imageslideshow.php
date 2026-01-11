<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowRepository;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class ImageSlideshow extends Module implements WidgetInterface
{
    protected string $templateFile = 'module:imageslideshow/views/templates/hook/imageslideshow.tpl';

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

    public function hookDisplayHeader(): void
    {
        $this->context->controller->registerStylesheet(
            $this->name, "modules/$this->name/public/front/imageslideshow.css", ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->registerJavascript(
            $this->name, "modules/$this->name/public/front/imageslideshow.js", ['position' => 'bottom', 'priority' => 150]
        );
    }

    public function renderWidget($hookName, array $configuration): string
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId());
    }

    public function getWidgetVariables($hookName, array $configuration): array
    {
        $slideshow = ['slides' => []];
        /** @var ImageSlideshowRepository $repo */
        $repo = $this->get(ImageSlideshowRepository::class);
        if ($slideshowEntity = $repo->findActive($configuration['slug'] ?? null)) {
            $slideshow += ['id' => $slideshowEntity->getId(), 'slug' => $slideshowEntity->getSlug()];
            foreach ($slideshowEntity->getActiveSlides() as $slideEntity) {
                $slide = [
                    'id' => $slideEntity->getId(),
                    'title' => $slideEntity->getTitle(),
                    'description' => '',
                    'legend' => $slideEntity->getLang()->getLegend(),
                    // 'sizes' => @getimagesize($slideEntity->getImagePath()),
                    'url' => $slideEntity->getLang()->getUrl(),
                    'target_blank' => $slideEntity->isTargetBlank(),
                    'image_url' => $slideEntity->getImagePath(),
                ];
                // if (isset($slide['sizes'][3]) && $slide['sizes'][3]) {
                //     $slide['size'] = $slide['sizes'][3];
                // }
                if ($description = $slideEntity->getLang()->getDescription()) {
                    $slide['description'] = html_entity_decode($description);
                }
                $slideshow['slides'][] = $slide;
            }
        }

        return ['slideshow' => $slideshow];
    }

    private function installer(): ImageSlideshowInstaller
    {
        require_once _PS_MODULE_DIR_ . 'imageslideshow/install/ImageSlideshowInstaller.php';
        return new ImageSlideshowInstaller($this);
    }
}
