<?php

namespace Backend\Modules\Gallery\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the Gallery module
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
        $this->addModule('Gallery');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'Gallery');

        $this->setActionRights(1, 'Gallery', 'Index');
        $this->setActionRights(1, 'Gallery', 'Add');
        $this->setActionRights(1, 'Gallery', 'Edit');
        $this->setActionRights(1, 'Gallery', 'Delete');
        $this->setActionRights(1, 'gallery', 'Sequence');
        $this->setActionRights(1, 'Gallery', 'Categories');
        $this->setActionRights(1, 'Gallery', 'AddCategory');
        $this->setActionRights(1, 'Gallery', 'EditCategory');
        $this->setActionRights(1, 'Gallery', 'DeleteCategory');
        $this->setActionRights(1, 'Gallery', 'SequenceCategories');

        $this->insertExtra('Gallery', 'block', 'GalleryCategory', 'Category', null, 'N', 1002);
        $this->insertExtra('Gallery', 'widget', 'Categories', 'Categories', null, 'N', 1003);


        $this->makeSearchable('Gallery');


        // add extra's
        $subnameID = $this->insertExtra('Gallery', 'block', 'Gallery');
        $this->insertExtra('Gallery', 'block', 'GalleryDetail', 'Detail');

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationGalleryId = $this->setNavigation($navigationModulesId, 'Gallery');
        $this->setNavigation(
            $navigationGalleryId,
            'Gallery',
            'gallery/index',
            array('gallery/add', 'gallery/edit')
        );
        $this->setNavigation(
            $navigationGalleryId,
            'Categories',
            'gallery/categories',
            array('gallery/add_category', 'gallery/edit_category')
        );

    }
}
