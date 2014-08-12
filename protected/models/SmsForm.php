<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class SmsForm extends CFormModel
{
	public $mobile_no;
	public $message;
	
	public function rules()
	{
		
		return array(
			// username and password are required
			array('mobile_no, message', 'required'),
			array('mobile_no','length','min'=>11,'max'=>11),			
			array('message','length','min'=>10),
			// rememberMe needs to be a boolean
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'mobile_no'=>'Mobile No',
			'message'=>'Message',
		);
	}
	//////////////////////////////////////////
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate())
				$this->addError('password','Incorrect username or password.');
		}
	}
	////////////////////////////////////////////
	
}
