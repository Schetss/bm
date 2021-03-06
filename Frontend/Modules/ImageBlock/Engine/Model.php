<?php

namespace Frontend\Modules\ImageBlock\Engine;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation;

/**
 * In this file we store all generic functions that we will be using in the Image Block module
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class Model
{
    /**
     * Fetches a certain item
     *
     * @param string $URL
     * @return array
     */
    public static function get($URL)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM image_block AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE m.url = ?',
            array((string) $URL)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('ImageBlock', 'Detail') . '/' . $item['url'];

        return $item;
    }

    /**
     * Get all items (at least a chunk)
     *
     * @param int $limit The number of items to get.
     * @param int $offset The offset.
     * @return array
     */
    public static function getAll($limit = 10, $offset = 0)
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.*, m.url
             FROM image_block AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.language = ?
             ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
            array(FRONTEND_LANGUAGE, (int) $offset, (int) $limit)
        );

        // no results?
        if (empty($items)) {
            return array();
        }

        // get detail action url
        $detailUrl = Navigation::getURLForBlock('ImageBlock', 'Detail');

        // prepare items for search
        foreach ($items as &$item) {
            $item['full_url'] =  $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }

    /**
     * Get the number of items
     *
     * @return int
     */
    public static function getAllCount()
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM image_block AS i
             WHERE i.language = ?',
            array(FRONTEND_LANGUAGE)
        );
    }

    /**
     * Get all category items (at least a chunk)
     *
     * @param int $categoryId
     * @param int $limit The number of items to get.
     * @param int $offset The offset.
     * @return array
     */
    public static function getAllByCategory($categoryId, $limit = 10, $offset = 0)
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.*, m.url
             FROM image_block AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.category_id = ? AND i.language = ?
             ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
            array($categoryId, FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

        // no results?
        if (empty($items)) {
            return array();
        }

        // get detail action url
        $detailUrl = Navigation::getURLForBlock('ImageBlock', 'Detail');

        // prepare items for search
        foreach ($items as &$item) {
            $item['full_url'] = $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }
    /**
     * Get all categories used
     *
     * @return array
     */
    public static function getAllCategories()
    {
        $return = (array) FrontendModel::get('database')->getRecords(
            'SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total, m.data AS meta_data
             FROM image_block_categories AS c
             INNER JOIN image_block AS i ON c.id = i.category_id AND c.language = i.language
             INNER JOIN meta AS m ON c.meta_id = m.id
             GROUP BY c.id
             ORDER BY c.sequence ASC',
            array(),
            'id'
        );

        // loop items and unserialize
        foreach ($return as &$row) {
            if (isset($row['meta_data'])) {
                $row['meta_data'] = unserialize($row['meta_data']);
            }
        }

        return $return;
    }

    /**
     * Fetches a certain category
     *
     * @param string $URL
     * @return array
     */
    public static function getCategory($URL)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM image_block_categories AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE m.url = ?',
            array((string) $URL)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('ImageBlock', 'category') . '/' . $item['url'];

        return $item;
    }


     /**
     * Get all categories used
     *
     * @return array
     */
    public static function getAllItems()
    {
        $return = (array) FrontendModel::get('database')->getRecords(
            'SELECT id, title_1 as title1, title2,  title3, title4, title5, title6, title7, title8, title9, image1, image2, image3, image4, image5,
             image6, image7, image8, image9, linkurl1, linkurl2, linkurl3, linkurl4, linkurl5, linkurl6, linkurl7, linkurl8, linkurl9
             FROM image_block
             ORDER BY sequence ASC',
            array(),
            'id'
        );

        // loop items and unserialize
        foreach ($return as &$row) {
            if (isset($row['meta_data'])) {
                $row['meta_data'] = unserialize($row['meta_data']);
            }
        }

        return $return;
    }



    /**
     * Get the number of items in a category
     *
     * @param int $categoryId
     * @return int
     */
    public static function getCategoryCount($categoryId)
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id)
             FROM image_block AS i
             WHERE i.language = ? AND i.category_id = ?',
            array(FRONTEND_LANGUAGE, (int) $categoryId)
        );
    }

    /**
     * Parse the search results for this module
     *
     * Note: a module's search function should always:
     *         - accept an array of entry id's
     *         - return only the entries that are allowed to be displayed, with their array's index being the entry's id
     *
     *
     * @param array $ids The ids of the found results.
     * @return array
     */
    public static function search(array $ids)
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.id, i.title AS title, m.url
             FROM image_block AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
            array(FRONTEND_LANGUAGE),
            'id'
        );

        // get detail action url
        $detailUrl = Navigation::getURLForBlock('ImageBlock', 'Detail');

        // prepare items for search
        foreach ($items as &$item) {
            $item['full_url'] = $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }

}
