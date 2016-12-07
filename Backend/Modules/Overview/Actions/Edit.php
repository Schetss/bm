<?php

namespace Backend\Modules\Overview\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\Overview\Engine\Model as BackendOverviewModel;
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
        if ($this->id == null || !BackendOverviewModel::exists($this->id)) {
            $this->redirect(
                Model::createURLForAction('Index') . '&error=non-existing'
            );
        }

        $this->record = BackendOverviewModel::get($this->id);
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        // create form
        $this->frm = new Form('edit');

        $this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
        $this->frm->addText('subtitle', $this->record['subtitle']);
        $this->frm->addEditor('content', $this->record['content']);
        $this->frm->addImage('image');
        $this->frm->addText('linkurl', $this->record['linkurl']);
        $this->frm->addFile('additionalfile');

        // get categories
        $categories = BackendOverviewModel::getCategories();
        $this->frm->addDropdown('category_id', $categories, $this->record['category_id']);

        // meta
        $this->meta = new Meta($this->frm, $this->record['meta_id'], 'title', true);
        $this->meta->setUrlCallBack('Backend\Modules\Overview\Engine\Model', 'getUrl', array($this->record['id']));

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

            $fields['title']->isFilled(Language::err('FieldIsRequired'));
            $fields['category_id']->isFilled(Language::err('FieldIsRequired'));

            // validate meta
            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                $item = array();
                $item['id'] = $this->id;
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
                $item['category_id'] = $this->frm->getField('category_id')->getValue();

                $item['meta_id'] = $this->meta->save();

                BackendOverviewModel::update($item);
                $item['id'] = $this->id;

                // add search index
                BackendSearchModel::saveIndex(
                    $this->getModule(), $item['id'],
                    array('title' => $item['title'], 'subtitle' => $item['subtitle'], 'content' => $item['content'])
                );

                $this->redirect(
                    Model::createURLForAction('Index') . '&report=edited&highlight=row-' . $item['id']
                );
            }
        }
    }
}
