<?php

namespace PrestaShop\Module\ImageSlideshow\Grid;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;


class ImageSlideshowGridDefinitionFactory implements GridDefinitionFactoryInterface
{
    const GRID_ID = 'imageslideshow';

    public function getId(): string
    {
        return static::GRID_ID;
    }

    public function getName(): string
    {
        return 'Image Slideshow';
    }

    public function getDefinition()
    {
        return new GridDefinition(
            $this->getId(),
            $this->getName(),
            $this->getColumns(),
            new FilterCollection(),
            $this->getGridActions(),
            new BulkActionCollection(),
            new ViewOptionsCollection()
        );
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            // ->add((new BulkActionColumn('bulk'))->setOptions([
            //     'bulk_field' => 'id_cms',
            // ]))
            ->add((new DataColumn('id_image_slideshow'))->setName('ID')->setOptions([
                'field' => 'id_image_slideshow',
            ]))
            ->add((new DataColumn('name'))->setName('Título')->setOptions([
                'field' => 'name',
            ]))
            ->add((new DataColumn('slug'))->setName('Llave')->setOptions([
                'field' => 'slug',
            ]))
            ->add((new ToggleColumn('active'))
                ->setName('Activo')
                ->setOptions([
                    'field' => 'active',
                    'route' => 'imageslideshow_toggle',
                    'primary_field' => 'id_image_slideshow',
                    'route_param_name' => 'idImageSlideshow',
                ])
            )
            ->add((new ActionColumn('actions'))->setName('Acciones')->setOptions([
                'actions' => (new RowActionCollection())
                    ->add((new LinkRowAction('edit_slides'))
                        ->setName('Editar')
                        ->setIcon('slideshow')
                        ->setOptions([
                            'route' => 'imageslideshow_slides',
                            'route_param_name' => 'idImageSlideshow',
                            'route_param_field' => 'id_image_slideshow',
                        ])
                    )
                    ->add((new LinkRowAction('edit'))
                        ->setName('Modificar')
                        ->setIcon('edit')
                        ->setOptions([
                            'route' => 'imageslideshow_edit',
                            'route_param_name' => 'idImageSlideshow',
                            'route_param_field' => 'id_image_slideshow',
                        ])
                    )
                    ->add((new SubmitRowAction('delete'))
                        ->setName('Eliminar')
                        ->setIcon('delete')
                        ->setOptions([
                            'method' => 'DELETE',
                            'route' => 'imageslideshow_delete',
                            'route_param_name' => 'idImageSlideshow',
                            'route_param_field' => 'id_image_slideshow',
                            'confirm_message' => '¿Eliminar el elemento seleccionado?',
                        ])
                    )
            ]));
    }


    /**
     * basedon \PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ManufacturerGridDefinitionFactory::getGridActions
     */
    private function getGridActions(): GridActionCollectionInterface
    {
        return (new GridActionCollection())
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName('Actualizar lista')
                ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
                ->setName('Mostrar consulta SQL')
                ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
                ->setName('Exportar al gestor SQL')
                ->setIcon('storage')
            );
    }
}
