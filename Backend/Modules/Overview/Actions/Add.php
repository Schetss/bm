<?php

namespace Backend\Modules\Overview\Actions;

use Backend\Core\Engine\Base\ActionAdd;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\Overview\Engine\Model as BackendOverviewModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Stijn Schets <stijn@popkorn.be>
 */
class Add extends ActionAdd
{
    /**
     * Execute the actions
     */
    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $this->frm = new Form('add');

        $this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
        $this->frm->addText('subtitle');
        $this->frm->addEditor('content');
        $this->frm->addImage('image');
        $this->frm->addText('linkurl');
        $this->frm->addFile('additionalfile');

        // get categories
        $categories = BackendOverviewModel::getCategories();
        $this->frm->addDropdown('category_id', $categories);

        // meta
        $this->meta = new Meta($this->frm, null, 'title', true);

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

            $fields['title']->isFilled(Language::err('FieldIsRequired'));
            $fields['category_id']->isFilled(Language::err('FieldIsRequired'));

            // validate meta
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build the item
                $item = array();
                $item['language'] = Language::getWorkingLanguage();
                $item['title'] = $fields['title']->getValue();
                $item['subtitle'] = $fields['subtitle']->getValue();
                $item['content'] = $fields['content']->getValue();

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/300x300')) {
                    \SpoonDirectory::create($imagePath . '/300x300');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x800')) {
                    \SpoonDirectory::create($imagePath . '/800x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/1024x1024')) {
                    \SpoonDirectory::create($imagePath . '/1024x1024');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image']->isFilled()) {
                    // build the image name
                    $item['image'] = $this->meta->getUrl() . '.' . $fields['image']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image']->generateThumbnails($imagePath, $item['image']);
                }
                $item['linkurl'] = $fields['linkurl']->getValue();

                // the file path
                $filePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/files';

                // create folders if needed
                if (!\SpoonDirectory::exists($filePath)) {
                    \SpoonDirectory::create($filePath);
                }

                // file provided?
                if ($fields['additionalfile']->isFilled()) {
                    // build the file name

                    /**
                     * @TODO when meta is added, use the meta in the file name
                     */
                    $item['additionalfile'] = time() . '.' . $fields['additionalfile']->getExtension();

                    // upload the file
                    $fields['additionalfile']->moveFile($filePath . '/' . $item['additionalfile']);
                }
                $item['sequence'] = BackendOverviewModel::getMaximumSequence() + 1;
                $item['category_id'] = $this->frm->getField('category_id')->getValue();

                $item['meta_id'] = $this->meta->save();

                // insert it
                $item['id'] = BackendOverviewModel::insert($item);

                // add search index
                BackendSearchModel::saveIndex(
                    $this->getModule(), $item['id'],
                    array('title' => $item['title'], 'subtitle' => $item['subtitle'], 'content' => $item['content'])
                );

                $this->redirect(
                    Model::createURLForAction('Index') . '&report=added&highlight=row-' . $item['id']
                );
            }
        }
    }
}
