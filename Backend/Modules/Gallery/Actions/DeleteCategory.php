<?php

namespace Backend\Modules\Gallery\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Gallery\Engine\Model as BackendGalleryModel;

/**
 * This action will delete a category
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class DeleteCategory extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id == null || !BackendGalleryModel::existsCategory($this->id)) {
            $this->redirect(
                Model::createURLForAction('categories') . '&error=non-existing'
            );
        }

        // fetch the category
        $this->record = (array) BackendGalleryModel::getCategory($this->id);

        // delete item
        BackendGalleryModel::deleteCategory($this->id);

        // category was deleted, so redirect
        $this->redirect(
            Model::createURLForAction('categories') . '&report=deleted-category&var=' .
            urlencode($this->record['title'])
        );
    }
}
