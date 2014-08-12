<?php

/**
 * This is the model class for table "test".
 *
 * The followings are the available columns in table 'test':
 * @property integer $id
 * @property string $name
 * @property integer $main_id
 * @property string $normal_reading
 * @property integer $user_id
 * @property string $created_date
 * @property string $updated_date
 *
 * The followings are the available model relations:
 * @property PatientTestDetail[] $patientTestDetails
 * @property TestMain $main
 * @property User $user
 */
class Test extends CActiveRecord
{
	
	public $et;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Test the static model class
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
		return 'test';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, main_id, user_id, panel_id', 'required'),
			array('main_id, user_id, panel_id', 'numerical', 'integerOnly'=>true),			
			array('name, normal_reading', 'length', 'max'=>250),
			array('created_date, updated_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, main_id, normal_reading, user_id, panel_id, created_date, updated_date', 'safe'),
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
			'patientTestDetails' => array(self::HAS_MANY, 'PatientTestDetail', 'test_id'),
			'main' => array(self::BELONGS_TO, 'TestMain', 'main_id'),
			'panel' => array(self::BELONGS_TO, 'Panel', 'panel_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'main_id' => 'Test Head',
			'normal_reading' => 'Normal Reading',			
			'user_id' => 'User',
			'panel_id' => 'Panel',
			'created_date' => 'Created Date',
			'updated_date' => 'Updated Date',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($panel_id)
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		
		$criteria->order = "main_id, name";		
		$criteria->condition = "panel_id  = $panel_id";

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);		
		//$criteria->compare('main.examinationType.name',$this->et,true);
		$criteria->compare('main_id',$this->main_id);
		$criteria->compare('panel_id',$this->panel_id);
		$criteria->compare('normal_reading',$this->normal_reading,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('updated_date',$this->updated_date,true);
		

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}