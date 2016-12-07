<?php

namespace Backend\Modules\ImageBlock\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the Image Block module
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
        $this->addModule('ImageBlock');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'ImageBlock');

        $this->setActionRights(1, 'ImageBlock', 'Index');
        $this->setActionRights(1, 'ImageBlock', 'Add');
        $this->setActionRights(1, 'ImageBlock', 'Edit');
        $this->setActionRights(1, 'ImageBlock', 'Delete');
        $this->setActionRights(1, 'image_block', 'Sequence');
        $this->setActionRights(1, 'ImageBlock', 'Categories');
        $this->setActionRights(1, 'ImageBlock', 'AddCategory');
        $this->setActionRights(1, 'ImageBlock', 'EditCategory');
        $this->setActionRights(1, 'ImageBlock', 'DeleteCategory');
        $this->setActionRights(1, 'ImageBlock', 'SequenceCategories');

        $this->insertExtra('ImageBlock', 'block', 'ImageBlockCategory', 'Category', null, 'N', 1002);
        $this->insertExtra('ImageBlock', 'widget', 'Categories', 'Categories', null, 'N', 1003);


        $this->makeSearchable('ImageBlock');


        // add extra's
        $subnameID = $this->insertExtra('ImageBlock', 'block', 'ImageBlock');
        $this->insertExtra('ImageBlock', 'block', 'ImageBlockDetail', 'Detail');

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationImageBlockId = $this->setNavigation($navigationModulesId, 'ImageBlock');
        $this->setNavigation(
            $navigationImageBlockId,
            'ImageBlock',
            'image_block/index',
            array('image_block/add', 'image_block/edit')
        );
        $this->setNavigation(
            $navigationImageBlockId,
            'Categories',
            'image_block/categories',
            array('image_block/add_category', 'image_block/edit_category')
        );

    }
}
