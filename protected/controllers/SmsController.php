<?php
class SmsController extends Controller
{
	
	
	
	public function actionSmsInputs(){
		
		$model = new SmsForm;
		if(isset($_POST['SmsForm'])){
				$phone = $_POST['SmsForm']['mobile_no'];	
				$phone = str_replace(" ","",$phone);
				$msg = $_POST['SmsForm']['message'];				
				$val_res = $this->validatePhone($phone);
				//$val_res = 1;
				if($val_res==0){
				Yii::app()->user->setFlash('error', '-- Invalid Phone Number ('.$phone.'). Please Use a Valid Mobile Number.e.g (03xx xxxxxxx)');
				$this->redirect(array('SmsInputs'));
				}else {
					$res = $this->actionHandleSms($phone, $msg);
						if($res) { Yii::app()->user->setFlash('success', 'Message sent successfully');	}
						else { Yii::app()->user->setFlash('error', 'Error... Make sure Mobile number is valid.');	}						
						$this->redirect(array('SmsInputs'));
				}					
				//print_r($_POST);			
		}
			$this->render( 'smsinputs', array( 'model' => $model ) );	  
	}
	//////////////////////////////////////////////////////////
	public function validatePhone($phoneNumber){
		//if(preg_match('/^\d{10}$/',$phoneNumber)) // phone number is valid
		 if(preg_match("/^03(0|1|2|3|4|5)\d{8}$/", $phoneNumber))
    	{
      		$phoneNumber = $phoneNumber;
      		// your other code here
    	}
    	else // phone number is not valid
    	{
      		//echo 'Phone number invalid !';
			$phoneNumber = 0;
    	}
		//echo "----->$phoneNumber<-----"; 
		return $phoneNumber;
	}	
	//////////////////////////////////////////////////////////////////	
	public function actionHandleSms($phone, $msg ){
		$sms = new Sms;		
		$res = $sms->websmsSend($phone, $msg);
		return $res;		
	}
}