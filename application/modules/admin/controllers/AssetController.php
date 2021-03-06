<?php


    use \Yii;
    use \CException as Exception;
    use \application\components\Controller;
    use \application\components\Form;
    use \application\models\form\Insert;
    use \application\models\form\ListUsers;
    use \application\models\form\Img_upload;
    use \application\models\form\Feature;
    use \application\models\db\Address;
    use \application\models\db\Assets;
    use \application\models\db\Images;
    use \application\models\db\Features;

    class AssetController extends Controller{

        public $identity;

        public function actionaddFeature($id){
            if(Yii::app()->user->isGuest){
                $this->redirect(array('/login'));
            }
            else if (Yii::app()->user->priv >=50){
                $form = new Form('application.forms.feature', new Feature);
            }
            else {
                $this->redirect(array('/home'));
            }

            if($form->submitted() && $form->validate()) {
                $feature = new Features;
                $feature->attributes = $form->model->attributes;
                $feature->asset = $id;
                $feature->created = time();
                $feature->created_by = Yii::app()->user->id;
                if($feature->save()){
                    Yii::app()->user->setFlash('feature_saved', 'Successfully added new feature to asset.');
                    $this->redirect(array('/admin/asset/images?id='.$id ));
                }
                else {
                    Yii::app()->user->setFlash('failed_saving_feature', 'Failed saving feature to asset.');
                }
            }

            $this->render('addFeature', array ( 'form' => $form));
        }

        public function actionIndex(){
            if(Yii::app()->user->isGuest){
                $this->redirect(array('/login'));
            }
            else if (Yii::app()->user->priv >=50){
                $form = new Form('application.forms.insert', new Insert);
            }
            else {
                $this->redirect(array('/home'));
            }
            if($form->submitted() && $form->validate()) {
                $asset = Assets::model()->findAllByAttributes(array('name' => $form->model->name));
                if($asset){
                    $form->model->addError('name', 'An asset with this name already exists.');
                }
                else {
                    var_dump ( $form->model->status );
                    $address = New Address;
                    $address->attributes = $form->model->attributes;
                    $address->created = time();
                    (empty($asset->errors))?($address->save()):'';
                    //$address_name = Address::model()->findByAttributes(array('name'=> $address->name));
                    $asset = New Assets;
                    $asset->attributes = $form->model->attributes;
                    // switch ($asset->status)
                    $asset->address = $address->id;
                    $asset->created_by = Yii::app()->user->getId();
                    $asset->active = ( $form->model->active == '' || $form->model->active == null)?(1):($form->model->active);
                    $asset->owner = $form->model->owner;
                    $asset->created = time();
                    if(!$asset->save()){
                        echo 'Error saving asset - Line: ' . __LINE__ ;
                        echo "<br><pre class='pre-scrollable'>"; var_dump($asset->errors); echo "</pre>";
                    }
                    else{
                        Yii::app()->user->setFlash('success', 'The new Asset has been saved successfully.');
                        $this->redirect(array('/admin/asset/addFeature?id='.$asset->id));
                    }
                }
                /**
                No longer need to save owner like this as it is now a 1 to 1 relationship.
                */
                // $owner = New Owners;
                // $owner->asset = $asset->id;
                // $owner->user = $form->model->owner;
                // $owner->created = time();
                // if(!$owner->save()){
                //     echo 'Error saving owner - Line: ' . __LINE__ ;
                //     echo "<br><pre class='pre-scrollable'>"; var_dump($owner->errors); echo "</pre>";
                // }
                // $image = New Images;
                // $image->image_upload();

                // This will display a success message.
            }

            $this->render('index',array('form' => $form));
        }

        public function actionListAssets(){
            if(Yii::app()->user->isGuest){
                $this->redirect(array('/login'));
            }
            else if (Yii::app()->user->priv >=50){
                $form = new Form('application.forms.listusers', new ListUsers);
            }
            else {
                $this->redirect(array('/home'));
            }

            if ($form->submitted() && $form->validate()){
               $asset = Assets::model()->findByAttributes(array ('name' => $form->model->search));
               $this->redirect (array('/admin/asset/editasset', 'id' => $asset->id));
            }
            // if ($form->submitted() && $form->validate()){
            //     $found_user = Users::model()->findByAttributes(array('username' => $form->model->search));
            //     $this->redirect (array('/admin/user', 'id' => $found_user->id));
            // }
            $criteria = new \CDbCriteria;
            $count = Assets::model()->count($criteria);
            $pages = new \CPagination( $count );
            $pages->pageSize = 10;
            $pages->applyLimit($criteria);
            $assets = Assets::model()->findAll($criteria);
            $this->render('listassets',
                array('assets'=>$assets,
                    'form'=>$form,
                    'pages' => $pages
                ));//, array('form' => $form, 'assets' => $assets,'pages' => $pages));
        }

        public function actionEditAsset(){
            if(Yii::app()->user->isGuest){
                $this->redirect(array('/login'));
            }
            else if (Yii::app()->user->priv >=50){
                $form = new Form('application.forms.insert', new Insert);
            }
            else {
                $this->redirect(array('/home'));
            }

            $asset = (isset($_GET['id']) && !empty($_GET['id']))?(Assets::model()->findByPk($_GET['id'])):'';
            if ($asset!=null){

                $form->model->attributes = $asset->attributes;
                $address = Address::model()->findByPk($asset->address);
                $form->model->attributes = $address->attributes;

                if($form->submitted() && $form->validate()) {

                    $asset->attributes = $form->model->attributes;
                    $address->attributes = $form->model->attributes;
                    $address->save();
                    // $asset->active = $form->model->active;
                    // $asset->status = $form->model->status;
                    $asset->owner = $form->model->owner;
                    $asset->address = $address->id;
                    $asset->active = ( $form->model->active == '' )?(1):($form->model->active);
                    ($asset->save())?(Yii::app()->user->setFlash('success','Asset updated successfully.')):'';
                    // echo '</pre>';
                }
            }
            else {
                $this->redirect(array('/admin/asset/listassets'));
            }
            $this->render('editasset', array('form' => $form));
        }

        public function ActionImages(){

            if(Yii::app()->user->isGuest){
                $this->redirect(array('/login'));
            }
            else if (Yii::app()->user->priv >=50){
                $form = new Form('application.forms.insert', new Insert);
            }
            else {
                $this->redirect(array('/home'));
            }

            if (isset($_GET['id']) && !empty($_GET['id'])){
                $form = new Form('application.forms.img_upload', new Img_upload);
                $asset = Assets::model()->findByPk($_GET['id']);
                $images = ($asset)?($asset->Images):'';

                if($form->submitted() && $form->validate()){
                    $image = New Images;
                    $form->model->asset = $_GET['id'];
                    $image->attributes = $form->model->attributes;
                    $image->image_upload( $_GET['id'], $form );
                }
            }
            $this->render('images', array('form' => $form, 'images' => $images));
        }
        public function ActionDeleteImage($id){

            if(Yii::app()->user->isGuest){
                $this->redirect(array('/login'));
            }
            else if (Yii::app()->user->priv >=50){
                $form = new Form('application.forms.insert', new Insert);
            }
            else {
                $this->redirect(array('/home'));
            }

            $image = Images::model()->findByPk($id);
            ( $image->unlink_path() &&
            $image->delete() ) ?
            (Yii::app()->user->setFlash('success','Deleted image successfully.')):
            (Yii::app()->user->setFlash('warning','Something went wrong, try redeleting.'));
            $this->render('delete');
        }

    }
