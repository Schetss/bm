<?php

namespace Backend\Modules\Carousel;

use Backend\Core\Engine\Base\Config as BaseConfig;

/**
 * This is the configuration-object for the Carousel module
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
final class Config extends BaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var array
     */
    protected $disabledActions = array();
}
