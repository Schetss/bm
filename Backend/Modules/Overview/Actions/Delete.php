<?php

namespace Backend\Modules\Overview\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Overview\Engine\Model as BackendOverviewModel;

/**
 * This is the delete-action, it deletes an item
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class Delete extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $id = $this->getParameter('id', 'int');

        // does the item exist
        if ($id === null || !BackendOverviewModel::exists($id)) {
            return $this->redirect(
                Model::createURLForAction('Index') . '&error=non-existing'
            );
        }

        parent::execute();

        $record = (array) BackendOverviewModel::get($id);
        BackendOverviewModel::delete($id);

        $this->redirect(
            Model::createURLForAction('Index') . '&report=deleted&var=' .
            urlencode($record['title'])
        );
    }
}
