<?php
class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				//'backColor'=>0xCCCCCC,
				'transparent'=>true,
			),			
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by user.
	 */
	public function actionIndex()
	{
		$this->render('index');
		//echo "success";
	}
	
	public function actionVoidBill()
	{
		
	}
	
	public function actionDiscount()
	{
		
	}
	
	public function actionConfirmation()
	{
		//$model=new Tableorder;
		$res = 0;
		//echo "SELECT * FROM user where username='".$_REQUEST['username']."' and password='".$_REQUEST['password']."' ";
		$rs_user = Yii::app()->db->createCommand("SELECT * FROM user where username='".$_REQUEST['username']."' and password='".$_REQUEST['password']."' ")->queryRow();
		
		if($_REQUEST['typ']=='void' && $rs_user['id']>0){
		$res = (int)Yii::app()->user->checkAccess('SiteVoidBill',array('userId'=>$rs_user['id']));
		}else if($_REQUEST['typ']=='discount'  && $rs_user['id']>0){
		$res = (int)Yii::app()->user->checkAccess('SiteDiscount',array('userId'=>$rs_user['id']));
		}
		
		//in SDbAuthManager.php
		//if(!empty($params) && $params['userId']>0){$userId=$params['userId'];}
		
		echo $res;
		
	}
	/////////////////////////////////////////////////////	
	public function actionPrint_old()
	{
		$this->layout='//layouts/print';
		$this->render('print');
	}	
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$this->layout='//layouts/main';
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	protected function performAjaxValidation($model)
	{
	if(isset($_POST['ajax']) && $_POST['ajax']==='categories-form')
	{
	echo CActiveForm::validate($model);
	Yii::app()->end();
	}
}

}