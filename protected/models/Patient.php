<?php

/**
 * This is the model class for table "patient".
 *
 * The followings are the available columns in table 'patient':
 * @property integer $id
 * @property string $reg_no
 * @property integer $patient_type_id
 * @property integer $referrer_id
 * @property string $ref_no
 * @property string $name
 * @property string $father_name
 * @property string $gender
 * @property string $age
 * @property string $marital_status
 * @property string $address
 * @property string $phone
 * @property integer $country
 * @property integer $country_applied_for
 * @property string $cnic
 * @property string $visa_type
 * @property string $passport_no
 * @property string $place_of_issue
 * @property string $position_applied_for
 * @property string $recruiting_agent
 * @property integer $panel_id
 * @property integer $user_id
 * @property string $created_date
 * @property string $updated_date
 *
 * The followings are the available model relations:
 * @property Country $country0
 * @property Country $countryAppliedFor
 * @property Panel $panel
 * @property User $user
 * @property Referrer $referrer
 * @property PatientTestSummary[] $patientTestSummaries
 */
class Patient extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Patient the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'patient';
	}
	
	

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, gender, phone, country, panel_id, user_id', 'required'),
			array('patient_type_id, referrer_id, country, country_applied_for, panel_id, user_id', 'numerical', 'integerOnly'=>true),
			array('reg_no, ref_no, phone', 'length', 'max'=>50),
			array('visa_type, passport_no', 'length', 'max'=>100),
			array('reg_no','unique', 'message'=>'This reg number is already used.', 'on'=>'register'),
			
			array('phone', 'length', 'min'=>3, 'max'=>11),			
			//array('phone','unique', 'message'=>'This phone number is already registered.'),
			
			
			array('name, position_applied_for, recruiting_agent', 'length', 'max'=>250),
			array('father_name, place_of_issue', 'length', 'max'=>150),
			array('gender, age, marital_status', 'length', 'max'=>10),
			array('address', 'length', 'max'=>400),
			array('cnic', 'length', 'max'=>15),
			array('date_of_issue, created_date, updated_date', 'safe'),
			
			array('photo_file_name', 'file', 'allowEmpty'=>true, 'types'=>'jpg,jpeg,gif,png'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, reg_no, patient_type_id, referrer_id, ref_no, name, father_name, gender, age, marital_status, address, phone, country, country_applied_for, cnic, visa_type, passport_no, place_of_issue, date_of_issue, position_applied_for, recruiting_agent, photo_file_name, panel_id, user_id, created_date, updated_date', 'safe', 'on'=>'search'),
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
			'country0' => array(self::BELONGS_TO, 'Country', 'country'),
			'countryAppliedFor' => array(self::BELONGS_TO, 'Country', 'country_applied_for'),
			'panel' => array(self::BELONGS_TO, 'Panel', 'panel_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'referrer' => array(self::BELONGS_TO, 'Referrer', 'referrer_id'),
			'patientTestSummaries' => array(self::HAS_MANY, 'PatientTestSummary', 'patient_id'),
		);
	}
	
	public function beforeSave(){		 	
		if(isset($_POST['Patient']['date_of_issue'])){			
			$this->date_of_issue = date('Y-m-d', strtotime($_POST['Patient']['date_of_issue']));
		}	
		return parent::beforeSave();
	} 
	///////////////////////////////////////////////////////////////////////
	 protected function afterFind(){		
		if(!empty($this->date_of_issue)){
			if($this->date_of_issue=='0000-00-00'){ $this->date_of_issue=''; }
			$this->date_of_issue =date('d-m-Y', strtotime($this->date_of_issue));
		}else { $this->date_of_issue=''; }		
			
		return parent::afterFind();
	}
	//////////////////////////////////////////////////////////////////////

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'reg_no' => 'Reg No',
			'patient_type_id' => 'Patient Type',			
			'referrer_id' => 'Referrer',
			'ref_no' => 'Ref No',
			'name' => 'Name',
			'father_name' => 'Father Name',
			'gender' => 'Gender',
			'age' => 'Age',
			'marital_status' => 'Marital Status',
			'address' => 'Address',
			'phone' => 'Phone',
			'country' => 'Country',
			'country_applied_for' => 'Applied For',
			'cnic' => 'Cnic',
			'visa_type' => 'Visa Type',
			'passport_no' => 'Passport No',
			'place_of_issue' => 'Place Of Issue',
			'date_of_issue' => 'Date Of Issue',
			'position_applied_for' => 'Position Applied For',
			'recruiting_agent' => 'Recruiting Agent',
			'photo_file_name' => 'Image',			
			'panel_id' => 'Report Format',
			'user_id' => 'User',
			'created_date' => 'Created Date',
			'updated_date' => 'Updated Date',
		);
	}
	
	public static function getMaxRegValue(){
		return Yii::app()->db->createCommand()->select("count(id)")->from("patient")->queryScalar();
	}
	public static function getReferrerCount($id){
		return Yii::app()->db->createCommand()->select("count(id)")->from("patient")->where("referrer_id = $id")->queryScalar();
	}
	
	public static function totalPatients($id){		
		return Yii::app()->db->createCommand()->select("count(id)")->from("patient")->where("panel_id = $id")->queryScalar();
	}
	public static function totalPatients2($id){		
		return Yii::app()->db->createCommand()->select("count(id)")->from("patient")->where("referrer_id = $id")->queryScalar();
	}
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		
		$criteria->order = "id DESC";

		$criteria->compare('id',$this->id);
		$criteria->compare('reg_no',$this->reg_no,true);
		$criteria->compare('patient_type_id',$this->patient_type_id);		
		$criteria->compare('referrer_id',$this->referrer_id);
		$criteria->compare('ref_no',$this->ref_no,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('father_name',$this->father_name,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('age',$this->age,true);
		$criteria->compare('marital_status',$this->marital_status,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('country',$this->country);
		$criteria->compare('country_applied_for',$this->country_applied_for);
		$criteria->compare('cnic',$this->cnic,true);
		$criteria->compare('visa_type',$this->visa_type,true);
		$criteria->compare('passport_no',$this->passport_no,true);
		$criteria->compare('place_of_issue',$this->place_of_issue,true);
		$criteria->compare('date_of_issue',$this->date_of_issue,true);
		$criteria->compare('position_applied_for',$this->position_applied_for,true);
		$criteria->compare('recruiting_agent',$this->recruiting_agent,true);
		$criteria->compare('photo_file_name',$this->photo_file_name,true);		
		$criteria->compare('panel_id',$this->panel_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('updated_date',$this->updated_date,true);

		return new CActiveDataProvider($this, array('criteria'=>$criteria,));
	}
}