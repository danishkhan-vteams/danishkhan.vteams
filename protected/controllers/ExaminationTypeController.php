<?php

class ExaminationTypeController extends Controller
{
/**
* @var string the default layout for the views. Defaults to '//layouts/column2', meaning
* using two-column layout. See 'protected/views/layouts/column2.php'.
*/
public $layout='//layouts/column2';

/**
* @return array action filters
*/
public function filters()
{
return array(
'accessControl', // perform access control for CRUD operations
);
}

/**
* Displays a particular model.
* @param integer $id the ID of the model to be displayed
*/
public function actionView($id)
{
		$model = $this->loadModel($id);
		
		if( Yii::app()->request->isAjaxRequest )
		  {
			// Stop jQuery from re-initialization
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		 	echo CJSON::encode( array(
			  'status' => 'failure',
			  'content' => $this->renderPartial( 'view', array('model' => $model ), true, true ),));
			exit;
		  }
		  else
			$this->render( 'view', array( 'model' => $model ) );
	  
	  
}

/**
* Manages all models.
*/
public function actionAdmin()
{
	
	$ExaminationType=new ExaminationType;
	$this->layout='//layouts/column1';
	// Uncomment the following line if AJAX validation is needed
	$this->performAjaxValidation($ExaminationType);
	if(isset($_POST['ExaminationType'])){
		$ExaminationType->attributes=$_POST['ExaminationType'];
		//$TestMain->user_id = 1 ; //Yii::app()->user->id;
		if($ExaminationType->save()){	Yii::app()->user->setFlash('success','Examination Type successfully Created'); $ExaminationType->unsetAttributes(); } //end model save	
	}//end post
	else{	  }
	
	
$model=new ExaminationType('search');
$model->unsetAttributes();  // clear any default values
if(isset($_GET['ExaminationType']))
$model->attributes=$_GET['ExaminationType'];

$this->render('admin',array('model'=>$model,'ExaminationType'=>$ExaminationType));
}

/**
* Creates a new model.
* If creation is successful, the browser will be redirected to the 'view' page.
*/
public function actionCreate(){

$model=new ExaminationType;
// Uncomment the following line if AJAX validation is needed

$this->performAjaxValidation($model);

if(isset($_POST['ExaminationType'])){
$model->attributes=$_POST['ExaminationType'];
if($model->save()){
	if( Yii::app()->request->isAjaxRequest ) {
			// Stop jQuery from re-initialization
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
	 
	 		$json_arr = array(
			  'status' => 'success',
			  'grid' => 'categories-grid',
			  'url' => $_SERVER['REQUEST_URI'],
			  'content' => 'Categories successfully Created',
			);
			if(isset($_GET['dropDownList'])){
			$json_arr['inputType']='dropDownList';
			$json_arr['inputId']=$_GET['dropDownList'];
			$json_arr['inputData']=CHtml::tag('option',array('value'=>$model->id,'selected'=>true),CHtml::encode($model->name),true);
			}
			echo CJSON::encode($json_arr);
			exit;
		  }else{
          	Yii::app()->user->setFlash('success','ExaminationType successfully created.');	
			$this->redirect(array('admin'));
		  }
}//end model save
}//end post

				if( Yii::app()->request->isAjaxRequest ){
					// Stop jQuery from re-initialization
					Yii::app()->clientScript->scriptMap['jquery.js'] = false;
				 	echo CJSON::encode( array(
					  'status' => 'failure',
					  'url' => $_SERVER['REQUEST_URI'],
					  'content' => $this->renderPartial( '_form', array(
					  'model' => $model), true, true ),
					));
					exit;
				  }else{
					$this->render( 'create', array( 'model' => $model) );
				  }
}//end function
/**
* Updates a particular model.
* If update is successful, the browser will be redirected to the 'view' page.
* @param integer $id the ID of the model to be updated
*/
public function actionUpdate($id)
{
$model=$this->loadModel($id);

// Uncomment the following line if AJAX validation is needed
$this->performAjaxValidation($model);

if(isset($_POST['ExaminationType']))
{
$model->attributes=$_POST['ExaminationType'];
	
	if($model->save()){
		
		if( Yii::app()->request->isAjaxRequest ){
			// Stop jQuery from re-initialization
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
	 		echo CJSON::encode( array(
			  'status' => 'success',
			  'url' => $_SERVER['REQUEST_URI'],
			  'grid' => 'asset-grid',//grid to update
			  'content' => 'ExaminationType successfully Updated',
			));
		exit;
		}else{
		Yii::app()->user->setFlash('success','ExaminationType successfully Updated');	
		$this->redirect(array('admin'));
		}
	}//end if model save
}//end if POST


				if( Yii::app()->request->isAjaxRequest ){
					// Stop jQuery from re-initialization
					Yii::app()->clientScript->scriptMap['jquery.js'] = false;
				 	echo CJSON::encode( array(
					  'status' => 'failure',
					  'url' => $_SERVER['REQUEST_URI'],
					  'content' => $this->renderPartial( '_form', array(
					  'model' => $model), true, true ),
					));
					exit;
				  }else{
					$this->render( 'update', array( 'model' => $model) );
				  }

}//end function
/**
* Deletes a particular model.
* If deletion is successful, the browser will be redirected to the 'admin' page.
* @param integer $id the ID of the model to be deleted
*/
public function actionDelete($id)
{
if(Yii::app()->request->isPostRequest)
{
// we only allow deletion via POST request
$this->loadModel($id)->delete();

// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
if(!isset($_GET['ajax']))
$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
}
else
throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
}

/**
* Lists all models.
*/
public function actionIndex()
{
$this->actionAdmin();
exit;
$dataProvider=new CActiveDataProvider('ExaminationType');
$this->render('index',array(
'dataProvider'=>$dataProvider,
));
}



/**
* Returns the data model based on the primary key given in the GET variable.
* If the data model is not found, an HTTP exception will be raised.
* @param integer the ID of the model to be loaded
*/
public function loadModel($id)
{
$model=ExaminationType::model()->findByPk($id);
if($model===null)
throw new CHttpException(404,'The requested page does not exist.');
return $model;
}

/**
* Performs the AJAX validation.
* @param CModel the model to be validated
*/
protected function performAjaxValidation($model)
{
if(isset($_POST['ajax']) && $_POST['ajax']==='examination-type-form')
{
echo CActiveForm::validate($model);
Yii::app()->end();
}
}
}
