<?php

namespace Backend\Modules\Carousel\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the Carousel module
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class Installer extends ModuleInstaller
{
    public function install()
    {
        // import the sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install the module in the database
        $this->addModule('Carousel');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'Carousel');

        $this->setActionRights(1, 'Carousel', 'Index');
        $this->setActionRights(1, 'Carousel', 'Add');
        $this->setActionRights(1, 'Carousel', 'Edit');
        $this->setActionRights(1, 'Carousel', 'Delete');
        $this->setActionRights(1, 'carousel', 'Sequence');
        $this->setActionRights(1, 'Carousel', 'Categories');
        $this->setActionRights(1, 'Carousel', 'AddCategory');
        $this->setActionRights(1, 'Carousel', 'EditCategory');
        $this->setActionRights(1, 'Carousel', 'DeleteCategory');
        $this->setActionRights(1, 'Carousel', 'SequenceCategories');

        $this->insertExtra('Carousel', 'block', 'CarouselCategory', 'Category', null, 'N', 1002);
        $this->insertExtra('Carousel', 'widget', 'Categories', 'Categories', null, 'N', 1003);


        $this->makeSearchable('Carousel');


        // add extra's
        $subnameID = $this->insertExtra('Carousel', 'block', 'Carousel');
        $this->insertExtra('Carousel', 'block', 'CarouselDetail', 'Detail');

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationCarouselId = $this->setNavigation($navigationModulesId, 'Carousel');
        $this->setNavigation(
            $navigationCarouselId,
            'Carousel',
            'carousel/index',
            array('carousel/add', 'carousel/edit')
        );
        $this->setNavigation(
            $navigationCarouselId,
            'Categories',
            'carousel/categories',
            array('carousel/add_category', 'carousel/edit_category')
        );

    }
}
