<?php

namespace Backend\Modules\ImageBlock\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\ImageBlock\Engine\Model as BackendImageBlockModel;

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
        if ($this->id == null || !BackendImageBlockModel::existsCategory($this->id)) {
            $this->redirect(
                Model::createURLForAction('categories') . '&error=non-existing'
            );
        }

        // fetch the category
        $this->record = (array) BackendImageBlockModel::getCategory($this->id);

        // delete item
        BackendImageBlockModel::deleteCategory($this->id);

        // category was deleted, so redirect
        $this->redirect(
            Model::createURLForAction('categories') . '&report=deleted-category&var=' .
            urlencode($this->record['title'])
        );
    }
}
