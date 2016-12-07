<?php

namespace Frontend\Modules\Gallery\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Gallery\Engine\Model as FrontendGalleryModel;

/**
 * This is a widget with the Gallery-categories
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class GalleryWidget extends Widget
{
    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse
     */
    private function parse()
    {


        // assign comments
         $this->tpl->assign(
            'gallery',
            FrontendGalleryModel::getAllItems());
    }
}
