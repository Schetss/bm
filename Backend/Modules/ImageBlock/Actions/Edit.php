<?php

namespace Backend\Modules\ImageBlock\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\ImageBlock\Engine\Model as BackendImageBlockModel;
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
        if ($this->id == null || !BackendImageBlockModel::exists($this->id)) {
            $this->redirect(
                Model::createURLForAction('Index') . '&error=non-existing'
            );
        }

        $this->record = BackendImageBlockModel::get($this->id);
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        // create form
        $this->frm = new Form('edit');

        $this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
        $this->frm->addText('title_1', $this->record['title_1']);
        $this->frm->addText('title2', $this->record['title2']);
        $this->frm->addText('title3', $this->record['title3']);
        $this->frm->addText('title4', $this->record['title4']);
        $this->frm->addText('title5', $this->record['title5']);
        $this->frm->addText('title6', $this->record['title6']);
        $this->frm->addText('title7', $this->record['title7']);
        $this->frm->addText('title8', $this->record['title8']);
        $this->frm->addText('title9', $this->record['title9']);
        $this->frm->addImage('image1');
        $this->frm->addImage('image2');
        $this->frm->addImage('image3');
        $this->frm->addImage('image4');
        $this->frm->addImage('image5');
        $this->frm->addImage('image6');
        $this->frm->addImage('image7');
        $this->frm->addImage('image8');
        $this->frm->addImage('image9');
        $this->frm->addText('linkurl1', $this->record['linkurl1']);
        $this->frm->addText('linkurl2', $this->record['linkurl2']);
        $this->frm->addText('linkurl3', $this->record['linkurl3']);
        $this->frm->addText('linkurl4', $this->record['linkurl4']);
        $this->frm->addText('linkurl5', $this->record['linkurl5']);
        $this->frm->addText('linkurl6', $this->record['linkurl6']);
        $this->frm->addText('linkurl7', $this->record['linkurl7']);
        $this->frm->addText('linkurl8', $this->record['linkurl8']);
        $this->frm->addText('linkurl9', $this->record['linkurl9']);

        // get categories
        $categories = BackendImageBlockModel::getCategories();
        $this->frm->addDropdown('category_id', $categories, $this->record['category_id']);

        // meta
        $this->meta = new Meta($this->frm, $this->record['meta_id'], 'title', true);
        $this->meta->setUrlCallBack('Backend\Modules\ImageBlock\Engine\Model', 'getUrl', array($this->record['id']));

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
                $item['title_1'] = $fields['title_1']->getValue();
                $item['title2'] = $fields['title2']->getValue();
                $item['title3'] = $fields['title3']->getValue();
                $item['title4'] = $fields['title4']->getValue();
                $item['title5'] = $fields['title5']->getValue();
                $item['title6'] = $fields['title6']->getValue();
                $item['title7'] = $fields['title7']->getValue();
                $item['title8'] = $fields['title8']->getValue();
                $item['title9'] = $fields['title9']->getValue();

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image1';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image1']->isFilled()) {
                    // build the image name
                    $item['image1'] = $this->meta->getUrl() . '.' . $fields['image1']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image1']->generateThumbnails($imagePath, $item['image1']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image2';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image2']->isFilled()) {
                    // build the image name
                    $item['image2'] = $this->meta->getUrl() . '.' . $fields['image2']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image2']->generateThumbnails($imagePath, $item['image2']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image3';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image3']->isFilled()) {
                    // build the image name
                    $item['image3'] = $this->meta->getUrl() . '.' . $fields['image3']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image3']->generateThumbnails($imagePath, $item['image3']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image4';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image4']->isFilled()) {
                    // build the image name
                    $item['image4'] = $this->meta->getUrl() . '.' . $fields['image4']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image4']->generateThumbnails($imagePath, $item['image4']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image5';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image5']->isFilled()) {
                    // build the image name
                    $item['image5'] = $this->meta->getUrl() . '.' . $fields['image5']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image5']->generateThumbnails($imagePath, $item['image5']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image6';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image6']->isFilled()) {
                    // build the image name
                    $item['image6'] = $this->meta->getUrl() . '.' . $fields['image6']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image6']->generateThumbnails($imagePath, $item['image6']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image7';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image7']->isFilled()) {
                    // build the image name
                    $item['image7'] = $this->meta->getUrl() . '.' . $fields['image7']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image7']->generateThumbnails($imagePath, $item['image7']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image8';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image8']->isFilled()) {
                    // build the image name
                    $item['image8'] = $this->meta->getUrl() . '.' . $fields['image8']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image8']->generateThumbnails($imagePath, $item['image8']);
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image9';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/160x160')) {
                    \SpoonDirectory::create($imagePath . '/160x160');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x600')) {
                    \SpoonDirectory::create($imagePath . '/600x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/800x600')) {
                    \SpoonDirectory::create($imagePath . '/800x600');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x800')) {
                    \SpoonDirectory::create($imagePath . '/600x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image9']->isFilled()) {
                    // build the image name
                    $item['image9'] = $this->meta->getUrl() . '.' . $fields['image9']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image9']->generateThumbnails($imagePath, $item['image9']);
                }
                $item['linkurl1'] = $fields['linkurl1']->getValue();
                $item['linkurl2'] = $fields['linkurl2']->getValue();
                $item['linkurl3'] = $fields['linkurl3']->getValue();
                $item['linkurl4'] = $fields['linkurl4']->getValue();
                $item['linkurl5'] = $fields['linkurl5']->getValue();
                $item['linkurl6'] = $fields['linkurl6']->getValue();
                $item['linkurl7'] = $fields['linkurl7']->getValue();
                $item['linkurl8'] = $fields['linkurl8']->getValue();
                $item['linkurl9'] = $fields['linkurl9']->getValue();
                $item['category_id'] = $this->frm->getField('category_id')->getValue();

                $item['meta_id'] = $this->meta->save();

                BackendImageBlockModel::update($item);
                $item['id'] = $this->id;

                // add search index
                BackendSearchModel::saveIndex(
                    $this->getModule(), $item['id'],
                    array('title_1' => $item['title_1'], 'title2' => $item['title2'], 'title3' => $item['title3'], 'title4' => $item['title4'], 'title5' => $item['title5'], 'title6' => $item['title6'], 'title7' => $item['title7'], 'title8' => $item['title8'], 'title9' => $item['title9'])
                );

                $this->redirect(
                    Model::createURLForAction('Index') . '&report=edited&highlight=row-' . $item['id']
                );
            }
        }
    }
}
