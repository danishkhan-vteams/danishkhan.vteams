<?php

class PatientTestSummaryController extends Controller
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

public function actionMail() { //Email Function
	 
	 $model = new PatientTestSummary('send_email');
	 if(isset($_POST['PatientTestSummary'])){
		 
		$model->attributes=$_POST['PatientTestSummary'];
		$email_to = $model->p_email;
		$subject = $model->subject;
		$email_message = $model->email_message;
		
		//echo "--$email_to---$subject--------$email_message------";
		//print_r($_POST['PatientTestSummary']); exit;
		
        $message = new YiiMailMessage();
        $message->setTo(array('faisal2u4u@yahoo.com'=>'Faisal Shahzad'));
        $message->setFrom(array(Yii::app()->params['adminEmail']=>'Nayab Labs - Islamabad'));
        
		$message->setSubject($subject);
        $message->setBody($email_message);
		
		// MAIL ATTACHMENT CODE regcard
		//->attach(Swift_Attachment::fromPath('my-document.pdf'))
		$file_path = "docs/regcard.pdf";
		$attachment = Swift_Attachment::fromPath($file_path)->setFilename('report.pdf');
		$message->attach($attachment);
        
		//<li>attach(Swift_Attachment::fromPath('my-document.pdf'))</li>
		
		$numsent = Yii::app()->mail->send($message);
		if($numsent)Yii::app()->user->setFlash('success','Email Message Sent Successfully.');	
		else Yii::app()->user->setFlash('error','Uknown error occured in email sending.');	
		$this->redirect( array( 'mail' ) );
	 }	
		$this->render('sendmail', array('model'=>$model));
}
///////////////////////////////////////////////////////////////
public function actionTestInputs($id=0){
	if($id==0){
		Yii::app()->user->setFlash('success','Cant Proceed, No Patient Selected for Tests. Click Patinet Test in Grid Below.');	
			$this->redirect( array( 'Patient/admin') );
	}
	$this->layout = '//layouts/column1';
	if(isset($_POST['PatientTestSummary'])){
		
		$arr_remarks = $_POST['PatientTestSummary']['remarks'];
		$rmks = array_keys($arr_remarks); // getting array key
		$remarks_key = $rmks[0];		
		$remarks = $_POST['PatientTestSummary']['remarks'][$remarks_key]; // getting the value 
		
		$test_summary_id = $_POST['PatientTestSummary']['test_summary_id'];
		$array1 = $_POST['PatientTestSummary']['test_ids'];
		$array2 = $_POST['PatientTestSummary']['measured_reading'];			
		$result = array_combine($array1, $array2);	// combining test key and values
		ksort($result);	
		
		$test_ids = array();
		$prev_results = PatientTestDetail::model()->findAll("test_summary_id = $test_summary_id");
		foreach($prev_results as $row){	array_push($test_ids, $row['test_id']);	}
		//ksort($test_ids);
		asort($test_ids);
			
		foreach($result as $test_id =>$measured_reading ){
				$flag = in_array($test_id, $test_ids); 
				if(!$flag){ // Add new Test Values 
					$new_model = new PatientTestDetail;
					$new_model->unsetAttributes();  // clear any default values
					list($test_main_id, $normal_reading) = $this->getMainID($test_id);
					
					$new_model->test_main_id=$test_main_id;
					$new_model->test_id=$test_id;
					$new_model->normal_reading=$normal_reading;
					$new_model->measured_reading=$measured_reading;
					$new_model->test_id=$test_id;					
					$new_model->test_summary_id=$test_summary_id;
					$new_model->user_id = Yii::app()->user->id;
					$new_model->created_date = date("Y-m-d H:i:s");
					
					if($test_id == $remarks_key) { $new_model->remarks = $remarks ;
						//echo "<br ><br > i- test id: $test_id- key: $remarks_key--> $new_model->remarks -true";						
					} 
					else { $new_model->remarks = "" ;}					
					
					if($new_model->save()){		}
					else { $new_model->getErrors(); }					
				} // end if empty
				else { //Update Test Values
					$new_model = $this->loadModel2($test_id, $test_summary_id);
					$new_model->measured_reading=$measured_reading;
					
					if($test_id == $remarks_key) { $new_model->remarks = $remarks ;
						//echo "<br ><br > u- test id: $test_id- key: $remarks_key--> $new_model->remarks -true";
						//exit;
					} 
					else { $new_model->remarks = "" ;  }					
					
					if($new_model->save()){		}
					else { $new_model->getErrors(); }	
					//echo "<br ><br > - $test_id- update";					
				}
		} // end foreach
	} // end if post
	
	
	//$this->layout = '//layouts/print';
	$model=$this->loadModel($id);	
	$this->render('testinputs', array('model'=>$model));	
}
protected function getMainID($id=0){
	if($id > 0){
		$res = Yii::app()->db->createCommand()->select("main_id, normal_reading")->from("test")->where("id = $id")->queryRow();
		$mid = $res['main_id'];
		$nr = $res['normal_reading'];
	}
	return array($mid , $nr);	
}
///////////////////////////////////////////////////////////
/////////////////////////////////////
public function actionInvoice($id=0){
	if($id==0){
		Yii::app()->user->setFlash('success','Cant Proceed, Error in finding patient invoice.');	
			$this->redirect( array( 'admin') );
	}	
	//$cs = Yii::app()->clientScript;
 	//$cs->scriptMap['bootstrap-yii.css'] = false;
	//$cs->scriptMap['bootstrap.min.css'] = false;
	//$cs->scriptMap['bootstrap-notify.css'] = false;
	//$cs->scriptMap['jquery-ui-bootstrap.css'] = false;
	
	$this->layout = '//layouts/print';
	$this->render('invoice');
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
///////////////////////////////////////////////////////////////////
public function actionLabform($id=0){
	if($id==0){
		Yii::app()->user->setFlash('success','Cant Proceed, No Patient Selected for Tests. Click Patinet Test in Grid Below.');	
			$this->redirect( array( 'Patient/admin') );
	}
	$this->layout = '//layouts/print';
	$model=$this->loadModel($id);
	$this->render('labform', array('model'=>$model));
	
}
///////////////////////////////////////////////////////////////////
public function Mytest($id=0){
		$arr_tests = Yii::app()->db->createCommand()->select("t.name, p.measured_reading")->from("patient_test_detail p")->join('test t', 't.id=p.test_id')->where("p.test_summary_id = $id")->order("p.test_main_id")->queryAll();	
			
		$aa = CHtml::listData($arr_tests,'name','measured_reading');
		$ar = array();		
			foreach ($aa as $k =>$v){  
				$key = $this->Stringcleaner($k); //str_replace(' ', '_', $k);      
				$ar[$key] = $v;
			}		
		return json_encode($ar);		
}

public function Stringcleaner($string) {
		$str = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
		return str_replace(' ', '', $str);
		//return str_replace('-', '', $str);
   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  // return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
public function Stringcleaner2($string) {
		$str = preg_replace('/[^A-Za-z0-9]/', '', $string);
		return str_replace(' ', '-', $str);
		
   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  // return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
///////////////////////////////////////////////////////////////////
public function actionPdf($id=0)   {
	
		$cs = Yii::app()->clientScript;
		$cs->scriptMap['bootstrap-yii.css'] = false;
		$cs->scriptMap['bootstrap.min.css'] = false;
		$cs->scriptMap['bootstrap-notify.css'] = false;
		$cs->scriptMap['jquery-ui-bootstrap.css'] = false;
		
		
		
		$sql = "select p.id, p.panel_id from patient p left join patient_test_summary pts on pts.patient_id = p.id where pts.id = $id";
		$res = Yii::app()->db->createCommand($sql)->queryRow();
		$panel_id = $res['panel_id'];
		$patient_id = $res['id'];
		
		//echo "----$id-----$panel_id----$patient_id-" ; exit; 
	
		if($panel_id > 0){
			$this->layout = '//layouts/oec';		
			$patient = $patient_id. '-Profile.pdf';		
			///$pdf->SetFont('dejavusans', '', 14, '', true);
			//$html2pdf->addFont('times new roman', '', 14, '', true);	
			//$html2pdf->addFont('dejavusans', '', 14, '', true);		
			
        	$html2pdf = Yii::app()->ePdf->HTML2PDF();	
				
				
			$html2pdf->WriteHTML($this->renderPartial('oec_pdf', array('test_summary_id'=>$id), true));
        	$html2pdf->Output($patient); 
		}
	
		      
	}
//////////////
public function actionMedReport($id=0){
	
	$cs = Yii::app()->clientScript;
 	$cs->scriptMap['bootstrap-yii.css'] = false;
	$cs->scriptMap['bootstrap.min.css'] = false;
	$cs->scriptMap['bootstrap-notify.css'] = false;
	$cs->scriptMap['jquery-ui-bootstrap.css'] = false;
	
	$sql = "select p.panel_id from patient p left join patient_test_summary pts on pts.patient_id = p.id where pts.id = $id";
	$res = Yii::app()->db->createCommand($sql)->queryRow();
	$panel_id = $res['panel_id'];
	
	if($panel_id == 1){
	$this->layout = '//layouts/oec';	
	$this->render('oec');
	}
	else if($panel_id ==2){
	$this->layout = '//layouts/medical_examination';	
	$this->render('medical_examination'); exit;
	}
	
	else if($panel_id ==3){
	$this->layout = '//layouts/western_global';	
	$this->render('western_global');
	}
	
	else if($panel_id ==4){
	$this->layout = '//layouts/sinopec';	
	$this->render('sinopec');
	}
	
	else { echo "Unable to perform Request...Layout Error."; exit;}
}
///////////////////////////////////////////////////////////////////
/**
* Creates a new model.
* If creation is successful, the browser will be redirected to the 'view' page.
*/
public function actionCreate($id=0)
{
	
	if($id==0){
		Yii::app()->user->setFlash('success','Cant Proceed, No Patient Selected for Tests. Click Patinet Test in Grid Below.');	
			$this->redirect( array( 'Patient/admin') );
	}

$model=new PatientTestSummary('register');

// Uncomment the following line if AJAX validation is needed

$this->performAjaxValidation($model);

if(isset($_POST['PatientTestSummary']))
{
$model->attributes=$_POST['PatientTestSummary'];
$model->user_id = Yii::app()->user->id;
$model->patient_id = $id;
$panel_id = Yii::app()->db->createCommand()->select("panel_id")->from("patient")->queryScalar();
$model->panel_id = $panel_id;

if($model->save()){

	if( Yii::app()->request->isAjaxRequest )
		  {
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
          	Yii::app()->user->setFlash('success','PatientTestSummary successfully created.');	
			$this->redirect( array( 'admin' ) );
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
//$this->performAjaxValidation($model);

if(isset($_POST['PatientTestSummary'])){
	
$model->attributes=$_POST['PatientTestSummary'];

$model->user_id = Yii::app()->user->id;
$res_summary = Yii::app()->db->createCommand()->select("patient_id, panel_id")->from("patient_test_summary")->where("id = $id")->queryRow();
$model->patient_id = $res_summary['patient_id'];
$model->panel_id = $res_summary['panel_id'];
	
	if($model->save()){
		
		if( Yii::app()->request->isAjaxRequest ){
			
		}else{
		Yii::app()->user->setFlash('success','PatientTestSummary successfully Updated');	
		//$this->redirect(array('view','id'=>$model->id));
		$this->redirect( array( 'admin') );
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
$dataProvider=new CActiveDataProvider('PatientTestSummary');
$this->render('index',array(
'dataProvider'=>$dataProvider,
));
}

/**
* Manages all models.
*/
public function actionAdmin(){
	
	 $this->layout='//layouts/column1';
$model=new PatientTestSummary('search');
$model->unsetAttributes();  // clear any default values
if(isset($_GET['PatientTestSummary']))
$model->attributes=$_GET['PatientTestSummary'];

$this->render('admin',array('model'=>$model));
}

/**
* Returns the data model based on the primary key given in the GET variable.
* If the data model is not found, an HTTP exception will be raised.
* @param integer the ID of the model to be loaded
*/
public function loadModel($id)
{
$model=PatientTestSummary::model()->findByPk($id);
if($model===null)
throw new CHttpException(404,'The requested page does not exist.');
return $model;
}
////////////////////
public function loadModel2($test_id, $test_summary_id)
{
	//$model=PatientTestDetail::model()->findByPk($id);
	$model = PatientTestDetail::model()->findByAttributes(array('test_id'=>$test_id,'test_summary_id'=>$test_summary_id)); 
	
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
if(isset($_POST['ajax']) && $_POST['ajax']==='patient-test-summary-form')
{
echo CActiveForm::validate($model);
Yii::app()->end();
}
}
}
