<?php

/**
 * This is the model class for table "patient_test_summary".
 *
 * The followings are the available columns in table 'patient_test_summary':
 * @property integer $id
 * @property string $examination_date
 * @property integer $patient_id
 * @property integer $panel_id
 * @property double $price
 * @property double $discount
 * @property string $remarks
 * @property integer $user_id
 * @property string $created_date
 * @property string $updated_date
 *
 * The followings are the available model relations:
 * @property PatientTestDetail[] $patientTestDetails
 * @property Patient $patient
 * @property Panel $panel
 * @property User $user
 */
class PatientTestSummary extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PatientTestSummary the static model class
	 */
	 public $from_date; //for cgridview searching
	 public $to_date; //for cgridview searching
	 public $test_ids;
	 public $reg_no; 
	 public $ref_no;
	 public $referrer_id; // to show field in cgridview
	 public $country_applied_for; // to show field in cgridview
	 public $reporting_time;
	 
	 // Email variables
	 public $p_email;
	 public $subject;
	 public $email_message;	 
	 public $measured_reading;
	 
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'patient_test_summary';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('examination_date, reporting_date, patient_id, price, user_id', 'required'),
			array('patient_id, panel_id, patient_test_status, user_id', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical', 'min'=>'100'),			
			//array('klt_no','unique', 'on'=>'register'),
			array('price, discount, payment, balance, discount_percent, urgent_charges', 'numerical'),
			array('test_ids, payment, reporting_time, klt_no, balance, remarks, created_date, updated_date, measured_reading, discount_percent, urgent_charges', 'safe'),
			
			array('p_email', 'email', 'message'=>'Plz Enter a valid email address'),
			array('subject','length', 'min'=>3, 'max'=>25),
			array('p_email, subject, email_message', 'required', 'on'=>'send_email'),			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('from_date, to_date, examination_date, reporting_date, klt_no, patient_id, panel_id, price, discount, payment, balance, remarks, patient_test_status, user_id, created_date, updated_date, reg_no, ref_no, referrer_id, country_applied_for, urgent_charges', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'patientTestDetails' => array(self::HAS_MANY, 'PatientTestDetail', 'test_summary_id'),
			'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
			'panel' => array(self::BELONGS_TO, 'Panel', 'panel_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}
	///////////////////////////////////////////////////////////////
	public static function checkTestDetails($id=0){
		$res_id = Yii::app()->db->createCommand()->select("id")->from("patient_test_detail")->where("test_summary_id = $id")->queryScalar();
		if($res_id > 0) return false;
		else return true;
	}
	
	public static function checkTestDetails2($id=0){
		$res_id = Yii::app()->db->createCommand()->select("id")->from("patient_test_detail")->where("test_summary_id = $id")->queryScalar();
		if($res_id > 0) return true;
		else return false;
	}
	//////////////////////////////////////////////////////////////////////
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'examination_date' => 'Examination Date',
			'reporting_date' => 'Reporting Date',
			'klt_no' => 'Klt No',
			'patient_id' => 'Patient',
			'panel_id' => 'Panel',
			'price' => 'Price',
			'discount' => 'Discount',
			'discount_percent' => 'Discount(%)',
			'urgent_charges' => 'Urgent Charges',
			'payment' => 'Payment',
			'balance' => 'Balance',
			'remarks' => 'Remarks',
			'patient_test_status' => 'Status',
			'user_id' => 'User',
			'referrer_id' => 'Referrer',
			'created_date' => 'Created Date',
			'updated_date' => 'Updated Date',
		);
	}

	public function beforeSave(){		 	
		if(isset($_POST['PatientTestSummary']['examination_date'])){			
			$this->examination_date = date('Y-m-d', strtotime($_POST['PatientTestSummary']['examination_date']));
		}			
		if(isset($_POST['PatientTestSummary']['reporting_date'])){			
			$this->reporting_date = date('Y-m-d', strtotime($_POST['PatientTestSummary']['reporting_date']));
			$this->reporting_date = $this->reporting_date." ".$this->reporting_time;
		}	
		
		if($this->isNewRecord)	{	$this->created_date = date("Y-m-d H:i:s"); }
		else { $this->updated_date = date("Y-m-d H:i:s");  }
		return parent::beforeSave();
	} 
	///////////////////////////////////////////////////////////////////////
	 protected function afterFind(){		
		if(!empty($this->examination_date)){
			if($this->examination_date=='0000-00-00'){ $this->examination_date=''; }
			$this->examination_date =date('d-m-Y', strtotime($this->examination_date));
		}else { $this->examination_date=''; }	
		
		if(!empty($this->reporting_date)){
			if($this->reporting_date=='0000-00-00'){ $this->reporting_date=''; }
			$date_time = explode(" ", $this->reporting_date);
			if(count($date_time) > 0){
				$this->reporting_date = $date_time[0];
				$this->reporting_time = $date_time[1];
			}
			$this->reporting_date =date('d-m-Y', strtotime($this->reporting_date));
		}else { $this->reporting_date=''; }	
			
		return parent::afterFind();
	}
	//////////////////////////////////////////////////////////////////////

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		if(!isset($_GET['PatientTestSummary_sort'])){			
			$criteria->order = "t.id ASC";
			//$criteria->order = "t.chkin_id desc";
		}
		if(isset($_GET['PatientTestSummary']['from_date']) && isset($_GET['PatientTestSummary']['to_date'])){
			$from_date = $_GET['PatientTestSummary']['from_date'];			
			$to_date = $_GET['PatientTestSummary']['to_date'];			
			$criteria->condition = " examination_date BETWEEN '$from_date' AND '$to_date'";
		}
		if(isset($_GET['PatientTestSummary']['klt_no'])){
			$klt_no = $_GET['PatientTestSummary']['klt_no'];			
			$criteria->condition = " klt_no = '$klt_no'";
		}	
		
		$criteria->order = " t.id DESC";

		$criteria->compare('t.id',$this->id);
		$criteria->compare('examination_date', $this->examination_date,true);
		$criteria->compare('reporting_date', $this->reporting_date,true);
		$criteria->compare('klt_no',$this->klt_no,true);
		$criteria->compare('patient.reg_no',$this->reg_no,true);
		$criteria->compare('patient.ref_no',$this->ref_no,true);
		$criteria->compare('patient.referrer_id',$this->referrer_id,true);
		$criteria->compare('patient.country_applied_for',$this->country_applied_for,true);		
		$criteria->compare('patient.name',$this->patient_id, true);
		$criteria->compare('panel.id',$this->panel_id);
		$criteria->compare('t.price',$this->price);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('urgent_charges',$this->urgent_charges);
		$criteria->compare('payment',$this->payment);
		$criteria->compare('balance',$this->balance);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('patient_test_status',$this->patient_test_status,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('updated_date',$this->updated_date,true);
		
		$criteria->with = array('patient', 'panel');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array('pageSize'=>50),
			
			));
	}
	//////////////////////////
	
	public function searchParticularPatient($id=0)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		
		$criteria->condition="patient_id = $id";
		
		$criteria->order = " t.id DESC";

		$criteria->compare('t.id',$this->id);
		$criteria->compare('examination_date',$this->examination_date,true);
		$criteria->compare('reporting_date', $this->reporting_date,true);
		$criteria->compare('klt_no',$this->klt_no,true);
		$criteria->compare('patient.name',$this->patient_id, true);
		$criteria->compare('panel_id',$this->panel_id);
		$criteria->compare('price',$this->price);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('urgent_charges',$this->urgent_charges);
		$criteria->compare('payment',$this->payment);
		$criteria->compare('balance',$this->balance);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('patient_test_status',$this->patient_test_status,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('updated_date',$this->updated_date,true);
		
		$criteria->with = array('patient');

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
}