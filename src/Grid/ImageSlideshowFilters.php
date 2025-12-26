<?php

namespace PrestaShop\Module\ImageSlideshow\Grid;

use PrestaShop\PrestaShop\Core\Search\Filters;

class ImageSlideshowFilters extends Filters
{
    protected $filterId = ImageSlideshowGridDefinitionFactory::GRID_ID;

    public function __construct(array $filters = [], $filterId = '')
    {
        parent::__construct($filters, $filterId);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_image_slideshow',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
