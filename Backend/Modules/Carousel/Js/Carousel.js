/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the Carousel module
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
jsBackend.carousel =
{
    // constructor
    init: function()
    {
        // do meta
        if ($('#title').length > 0) $('#title').doMeta();

    }
}

$(jsBackend.carousel.init);
