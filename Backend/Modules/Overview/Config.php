<?php

namespace Backend\Modules\Overview;

use Backend\Core\Engine\Base\Config as BaseConfig;

/**
 * This is the configuration-object for the Overview module
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
