<?php

namespace Frontend\Modules\ImageBlock\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\ImageBlock\Engine\Model as FrontendImageBlockModel;

/**
 * This is a widget with the Image Block-categories
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class ImageBlockWidget extends Widget
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
            'imageblock',
            FrontendImageBlockModel::getAllItems());
    }
}
