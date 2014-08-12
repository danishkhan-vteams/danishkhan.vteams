<?php

/**
 * This is the model class for table "patient_test_detail".
 *
 * The followings are the available columns in table 'patient_test_detail':
 * @property integer $id
 * @property integer $test_main_id
 * @property integer $test_id
 * @property string $normal_reading
 * @property string $measured_reading
 * @property string $remarks
 * @property integer $test_summary_id
 * @property integer $user_id
 * @property string $created_date
 * @property string $updated_date
 *
 * The followings are the available model relations:
 * @property TestMain $testMain
 * @property Test $test
 * @property PatientTestSummary $testSummary
 */
class PatientTestDetail extends CActiveRecord
{
	
	public $test_ids;
	public $test_summary_id;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PatientTestDetail the static model class
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
		return 'patient_test_detail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('test_main_id, test_id, measured_reading, test_summary_id, user_id', 'required'),
			array('test_main_id, test_id, test_summary_id, user_id', 'numerical', 'integerOnly'=>true),
			array('normal_reading, measured_reading', 'length', 'max'=>250),
			array('remarks, created_date, updated_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, test_main_id, test_id, normal_reading, measured_reading, remarks, test_summary_id, user_id, created_date, updated_date', 'safe', 'on'=>'search'),
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
			'testMain' => array(self::BELONGS_TO, 'TestMain', 'test_main_id'),
			'test' => array(self::BELONGS_TO, 'Test', 'test_id'),
			'testSummary' => array(self::BELONGS_TO, 'PatientTestSummary', 'test_summary_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'Measured Reading'=>'',
			'test_main_id' => 'Test Main',
			'test_id' => 'Test',
			'normal_reading' => 'Normal Reading',
			'measured_reading' => 'Measured Reading',
			'remarks' => 'Remarks',
			'test_summary_id' => 'Test Summary',
			'user_id' => 'User',
			'created_date' => 'Created Date',
			'updated_date' => 'Updated Date',
		);
	}
	
	public static function getTestValue($id=0, $tsummary_id =0){
		if($id >0 && $tsummary_id > 0){
			$res = PatientTestDetail::model()->find("test_id = $id AND test_summary_id = $tsummary_id");
			return $res['measured_reading'];	
		}else return $res['measured_reading']="";
	}
	
	public static function getTestRemarks($id=0, $tsummary_id =0){
		if($id >0 && $tsummary_id > 0){
			$remarks = PatientTestDetail::model()->find("test_id = $id AND test_summary_id = $tsummary_id")->remarks;
			return $remarks;	
		}else return "dummy text";
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

		$criteria->compare('id',$this->id);
		$criteria->compare('test_main_id',$this->test_main_id);
		$criteria->compare('test_id',$this->test_id);
		$criteria->compare('normal_reading',$this->normal_reading,true);
		$criteria->compare('measured_reading',$this->measured_reading,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('test_summary_id',$this->test_summary_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('updated_date',$this->updated_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function search2($id=0)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('test_main_id',$this->test_main_id);
		$criteria->compare('test_id',$this->test_id);
		$criteria->compare('normal_reading',$this->normal_reading,true);
		$criteria->compare('measured_reading',$this->measured_reading,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('test_summary_id',$this->test_summary_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('updated_date',$this->updated_date,true);
		
		$criteria->condition = " test_summary_id = $id";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
}