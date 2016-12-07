<?php

namespace Backend\Modules\Carousel\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Carousel\Engine\Model as BackendCarouselModel;

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
        if ($this->id == null || !BackendCarouselModel::existsCategory($this->id)) {
            $this->redirect(
                Model::createURLForAction('categories') . '&error=non-existing'
            );
        }

        // fetch the category
        $this->record = (array) BackendCarouselModel::getCategory($this->id);

        // delete item
        BackendCarouselModel::deleteCategory($this->id);

        // category was deleted, so redirect
        $this->redirect(
            Model::createURLForAction('categories') . '&report=deleted-category&var=' .
            urlencode($this->record['title'])
        );
    }
}
