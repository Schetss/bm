/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the Image Block module
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
jsBackend.image_block =
{
    // constructor
    init: function()
    {
        // do meta
        if ($('#title').length > 0) $('#title').doMeta();

    }
}

$(jsBackend.image_block.init);
