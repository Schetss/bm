<?php

namespace Backend\Modules\Overview\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the Overview module
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
        $this->addModule('Overview');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'Overview');

        $this->setActionRights(1, 'Overview', 'Index');
        $this->setActionRights(1, 'Overview', 'Add');
        $this->setActionRights(1, 'Overview', 'Edit');
        $this->setActionRights(1, 'Overview', 'Delete');
        $this->setActionRights(1, 'overview', 'Sequence');
        $this->setActionRights(1, 'Overview', 'Categories');
        $this->setActionRights(1, 'Overview', 'AddCategory');
        $this->setActionRights(1, 'Overview', 'EditCategory');
        $this->setActionRights(1, 'Overview', 'DeleteCategory');
        $this->setActionRights(1, 'Overview', 'SequenceCategories');

        $this->insertExtra('Overview', 'block', 'OverviewCategory', 'Category', null, 'N', 1002);
        $this->insertExtra('Overview', 'widget', 'Categories', 'Categories', null, 'N', 1003);


        $this->makeSearchable('Overview');


        // add extra's
        $subnameID = $this->insertExtra('Overview', 'block', 'Overview');
        $this->insertExtra('Overview', 'block', 'OverviewDetail', 'Detail');

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationOverviewId = $this->setNavigation($navigationModulesId, 'Overview');
        $this->setNavigation(
            $navigationOverviewId,
            'Overview',
            'overview/index',
            array('overview/add', 'overview/edit')
        );
        $this->setNavigation(
            $navigationOverviewId,
            'Categories',
            'overview/categories',
            array('overview/add_category', 'overview/edit_category')
        );

    }
}
