<?php

namespace Backend\Modules\Overview\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Overview\Engine\Model as BackendOverviewModel;

/**
 * Alters the sequence of Overview articles
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class Sequence extends AjaxAction
{
    public function execute()
    {
        parent::execute();

        // get parameters
        $newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

        // list id
        $ids = (array) explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            $item['id'] = $id;
            $item['sequence'] = $i + 1;

            // update sequence
            if (BackendOverviewModel::exists($id)) {
                BackendOverviewModel::update($item);
            }
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}
