<?php

namespace Backend\Modules\Carousel\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Carousel\Engine\Model as BackendCarouselModel;

/**
 * Alters the sequence of Carousel articles
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class SequenceCategories extends AjaxAction
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
            if (BackendCarouselModel::existsCategory($id)) {
                BackendCarouselModel::updateCategory($item);
            }
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}
