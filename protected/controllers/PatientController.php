<?php
class PatientController extends Controller{
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
public function actionView($id){
	
		$model = $this->loadModel($id);		
		if( Yii::app()->request->isAjaxRequest ){
			// Stop jQuery from re-initialization
			Yii::app()->clientScript->scriptMap['jquery.js'] = false;
		 	echo CJSON::encode( array(
			  'status' => 'failure',
			  'content' => $this->renderPartial('view', array('model' =>$model), true, true),));
			exit;
		  }
		  else
			$this->render('view', array('model'=>$model));  
}
public function actiongetRefNo(){
	if(!empty($_POST['referrer_id'])){	
		$referrer_id = $_POST['referrer_id'];		
		$reg_no = Patient::getReferrerCount($referrer_id); 
		$arr['Patient_ref_no']= $referrer_id."-".($reg_no+1);
		echo json_encode($arr); 	
	}	else {
		$referrer_id = 1;		
		$count = Patient::getReferrerCount($referrer_id); 		
		$no = $referrer_id."-".($count+1);
		return $no;		
	}	
}  

public function actiongetPanelPrice(){
	if(!empty($_POST['panel_id'])){	
		$panel_id = $_POST['panel_id'];
		$sql = "select price from Panel where id = $panel_id";
		$price = Yii::app()->db->createCommand($sql)->queryScalar();		
		$arr['PatientTestSummary_price']= $price;
		echo json_encode($arr); 	
	}else { $arr['PatientTestSummary_price']= '';  echo json_encode($arr); }	
} 
/**
* Creates a new model.
* If creation is successful, the browser will be redirected to the 'view' page.
*/
public function actionCreate()
{

$model=new Patient('register');
$model_summary =new PatientTestSummary;

$max_reg_value = Patient::getMaxRegValue();
$max_ref_no = $this->actiongetRefNo(1);

// Uncomment the following line if AJAX validation is needed

//$this->performAjaxValidation($model);

if(isset($_POST['Patient'])){
	$model->attributes=$_POST['Patient'];
	$model_summary->attributes=$_POST['PatientTestSummary'];
	$model->user_id = Yii::app()->user->id;
	$model->reg_no = Patient::getMaxRegValue() + 1; // method defined in patient model
	$model->created_date = date("Y-m-d H:i:s");
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$images = CUploadedFile::getInstancesByName("Patient[photo_file_name]");					
			if (isset($images) && count($images) > 0) {
				foreach ($images as $key => $pic) { 				
				$timestamp = date('ymdHis');	
				$temp_ext = explode(".", $pic->name);
				$temp_file_name = $this->Stringcleaner($model->name).".".$temp_ext[1];			
				// set ur path where u want to save picture					
					if ($pic->saveAs(Yii::getPathOfAlias('webroot')."/patient_photos/$timestamp-".$temp_file_name)) {					
						$model->photo_file_name = $timestamp."-".$temp_file_name;
					}
				}				
			} 
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$connection=Yii::app()->db;
			$transaction=$connection->beginTransaction();
			$transaction_commit =2;
			$model_summary->user_id = $model->user_id;
			try{				
						
					if(1){
						if($model->save()){	
							$model_summary->patient_id = $model->id;
							$model_summary->panel_id = $model->panel_id;
								
							if($model_summary->save()){
								$transaction_commit =1;
							}else {  }
						}else { }
					}
			$transaction->commit();
			
			}catch(Exception $e){ 	
			echo "<br>into rollback";
			$transaction->rollback(); 
			$transaction_commit = 0;
			}		
			
			if($model->validate() & $model_summary->validate() & $transaction_commit){
				Yii::app()->user->setFlash('success','Patient successfully created.');			
				Yii::app()->session['bill_print'] = "1";				
				$this->redirect(array('PatientTestSummary/admin/'.$model_summary->id)); 				
			}
			
}//end post
				if( Yii::app()->request->isAjaxRequest ){					
				  }else{ 
				 // echo "----". $ref_no;
				  $this->render( 'create', array( 'model' => $model, 'model_summary'=>$model_summary, 'max_reg_value'=>$max_reg_value, 'max_ref_no'=>$max_ref_no));	  }
				  
}//end function

/**
* Updates a particular model.
* If update is successful, the browser will be redirected to the 'view' page.
* @param integer $id the ID of the model to be updated
*/
public function actionUpdate($id){
	
$model=$this->loadModel($id); //patient model;
$summary_id = PatientTestSummary::model()->find("patient_id = $id")->id; //get where patient_id = $id;
//$model_summary = Yii::app()->createController('PatientTestSummary/loadModel/'. $summary_id);
//print_r($model_summary);  exit;
$model_summary = $this->loadModel2($summary_id);

// Uncomment the following line if AJAX validation is needed
//$this->performAjaxValidation($model);

if(isset($_POST['Patient'])){
	
$model->attributes=$_POST['Patient'];
$model_summary->attributes=$_POST['PatientTestSummary'];
$model->user_id = Yii::app()->user->id;
$model->updated_date = date("Y-m-d H:i:s");
		//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$images = CUploadedFile::getInstancesByName("Patient[photo_file_name]");					
			if (isset($images) && count($images) > 0) {
				foreach ($images as $key => $pic) { 				
				$timestamp = date('ymdHis');	
				$temp_ext = explode(".", $pic->name);
				$temp_file_name = $this->Stringcleaner($model->name).".".$temp_ext[1];			
				// set ur path where u want to save picture					
					if ($pic->saveAs(Yii::getPathOfAlias('webroot')."/patient_photos/$timestamp-".$temp_file_name)) {					
						$model->photo_file_name = $timestamp."-".$temp_file_name;
					}
				}				
			} 
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			$connection=Yii::app()->db;
			$transaction=$connection->beginTransaction();
			$transaction_commit =2;
			$model_summary->user_id = $model->user_id;
			try{								
					if(1){
						if($model->save()){	
							$model_summary->patient_id = $model->id;
							$model_summary->panel_id = $model->panel_id;
								
							if($model_summary->save()){
								$transaction_commit =1;
							}else {  }
						}else { }
					}
			$transaction->commit();
			
			}catch(Exception $e){ 	
			echo "<br>into rollback";
			$transaction->rollback(); 
			$transaction_commit = 0;
			}		
			
			if($model->validate() & $model_summary->validate() & $transaction_commit){
				Yii::app()->user->setFlash('success','Patient successfully created.');	
				$this->redirect(array('PatientTestSummary/admin/'.$model_summary->id)); 				
			}

}//end if POST
	
	if( Yii::app()->request->isAjaxRequest ){
	// Stop jQuery from re-initialization					
	}else{		
		$this->render( 'update', array( 'model' => $model, 'model_summary'=>$model_summary, 'max_reg_value'=>$max_reg_value));
	}

}//end function
///////////////////////////////////////////////
public function Stringcleaner($string) {
		$str = preg_replace('/[^A-Za-z0-9]/', '', $string);
		return str_replace(' ', '-', $str);
   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  // return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

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
$dataProvider=new CActiveDataProvider('Patient');
$this->render('index',array(
'dataProvider'=>$dataProvider,
));
}

/**
* Manages all models.
*/
public function actionAdmin()
{
$model=new Patient('search');

$this->layout = '//layouts/column1';

$model->unsetAttributes();  // clear any default values
if(isset($_GET['Patient']))
$model->attributes=$_GET['Patient'];

$this->render('admin',array(
'model'=>$model,
));
}

/**
* Returns the data model based on the primary key given in the GET variable.
* If the data model is not found, an HTTP exception will be raised.
* @param integer the ID of the model to be loaded
*/
public function loadModel($id)
{
$model=Patient::model()->findByPk($id);
if($model===null)
throw new CHttpException(404,'The requested page does not exist.');
return $model;
}

public function loadModel2($id)
{
$model=PatientTestSummary::model()->findByPk($id);
if($model===null)
throw new CHttpException(404,'Unable to load requested Model(Test Summary).');
return $model;
}

/**
* Performs the AJAX validation.
* @param CModel the model to be validated
*/
protected function performAjaxValidation($model)
{
if(isset($_POST['ajax']) && $_POST['ajax']==='patient-form')
{
echo CActiveForm::validate($model);
Yii::app()->end();
}
}
}
