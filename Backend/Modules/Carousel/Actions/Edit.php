<?php

namespace Backend\Modules\Carousel\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\Carousel\Engine\Model as BackendCarouselModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class Edit extends ActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadData();
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the item data
     */
    protected function loadData()
    {
        $this->id = $this->getParameter('id', 'int', null);
        if ($this->id == null || !BackendCarouselModel::exists($this->id)) {
            $this->redirect(
                Model::createURLForAction('Index') . '&error=non-existing'
            );
        }

        $this->record = BackendCarouselModel::get($this->id);
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        // create form
        $this->frm = new Form('edit');

        $this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
        $this->frm->addImage('main_image');
        $this->frm->addImage('parallax_image');
        $this->frm->addEditor('content', $this->record['content']);

        // get categories
        $categories = BackendCarouselModel::getCategories();
        $this->frm->addDropdown('category_id', $categories, $this->record['category_id']);

        // meta
        $this->meta = new Meta($this->frm, $this->record['meta_id'], 'title', true);
        $this->meta->setUrlCallBack('Backend\Modules\Carousel\Engine\Model', 'getUrl', array($this->record['id']));

    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = Model::getURLForBlock($this->URL->getModule(), 'Detail');
        $url404 = Model::getURL(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
        $this->record['url'] = $this->meta->getURL();


        $this->tpl->assign('item', $this->record);
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

            // $fields['title']->isFilled(Language::err('FieldIsRequired'));
            // $fields['category_id']->isFilled(Language::err('FieldIsRequired'));


            $this->frm->getField('title')->isFilled(Language::err('FieldIsRequired'));
            $this->frm->getField('category_id')->isFilled(Language::err('FieldIsInvalid'));


            // validate meta
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                $item = array();
                $item['id'] = $this->id;
                $item['language'] = Language::getWorkingLanguage();

                $item['title'] = $fields['title']->getValue();

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/main_image';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/300x300')) {
                    \SpoonDirectory::create($imagePath . '/300x300');
                }
                if (!\SpoonDirectory::exists($imagePath . '/3000x2500')) {
                    \SpoonDirectory::create($imagePath . '/3000x2500');
                }
                if (!\SpoonDirectory::exists($imagePath . '/2000x1666')) {
                    \SpoonDirectory::create($imagePath . '/2000x1666');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['main_image']->isFilled()) {
                    // build the image name
                    $item['main_image'] = $this->meta->getUrl() . '.' . $fields['main_image']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['main_image']->generateThumbnails($imagePath, $item['main_image']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/parallax_image';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/300x300')) {
                    \SpoonDirectory::create($imagePath . '/300x300');
                }
                if (!\SpoonDirectory::exists($imagePath . '/3000x2500')) {
                    \SpoonDirectory::create($imagePath . '/3000x2500');
                }
                if (!\SpoonDirectory::exists($imagePath . '/2000x1666')) {
                    \SpoonDirectory::create($imagePath . '/2000x1666');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['parallax_image']->isFilled()) {
                    // build the image name
                    $item['parallax_image'] = $this->meta->getUrl() . '.' . $fields['parallax_image']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['parallax_image']->generateThumbnails($imagePath, $item['parallax_image']);
                }
                $item['content'] = $fields['content']->getValue();
                $item['category_id'] = $this->frm->getField('category_id')->getValue();

                $item['meta_id'] = $this->meta->save();

                BackendCarouselModel::update($item);
                $item['id'] = $this->id;

                // add search index
                BackendSearchModel::saveIndex(
                    $this->getModule(), $item['id'],
                    array('title' => $item['title'], 'content' => $item['content'])
                );

                $this->redirect(
                    Model::createURLForAction('Index') . '&report=edited&highlight=row-' . $item['id']
                );
            }
        }
    }
}
