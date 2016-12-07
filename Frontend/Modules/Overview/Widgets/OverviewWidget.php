<?php

namespace Frontend\Modules\Overview\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Overview\Engine\Model as FrontendOverviewModel;

/**
 * This is a widget with the Overview-categories
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class OverviewWidget extends Widget
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
            'overview',
            FrontendOverviewModel::getAllItems());
    }
}
