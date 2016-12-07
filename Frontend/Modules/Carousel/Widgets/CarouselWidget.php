<?php

namespace Frontend\Modules\Carousel\Widgets;

use Frontend\Core\Engine\Base\Widget;
use Frontend\Core\Engine\Navigation;
use Frontend\Modules\Carousel\Engine\Model as FrontendCarouselModel;

/**
 * This is a widget with the Carousel-categories
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class CarouselWidget extends Widget
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
            'carousel',
            FrontendCarouselModel::getAllItems());
    }
}
