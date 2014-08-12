<?php

/**
 * This is the model class for table "test_main".
 *
 * The followings are the available columns in table 'test_main':
 * @property integer $id
 * @property string $name
 * @property integer $examination_type_id
 *
 * The followings are the available model relations:
 * @property PatientTestDetail[] $patientTestDetails
 * @property Test[] $tests
 * @property ExaminationType $examinationType
 */
class TestMain extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TestMain the static model class
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
		return 'test_main';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, examination_type_id, panel_id', 'required'),
			array('examination_type_id, panel_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>250),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, examination_type_id, panel_id', 'safe'),
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
			'patientTestDetails' => array(self::HAS_MANY, 'PatientTestDetail', 'test_main_id'),
			'tests' => array(self::HAS_MANY, 'Test', 'main_id'),
			'panel' => array(self::BELONGS_TO, 'Panel', 'panel_id'),
			'examinationType' => array(self::BELONGS_TO, 'ExaminationType', 'examination_type_id'),
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
			'panel_id' => 'Panel',
			'examination_type_id' => 'Examination Type',
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
		
		$criteria->order ="examination_type_id ASC, name";
		$criteria->condition = "panel_id  = $panel_id";

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('panel_id',$this->panel_id, true);
		$criteria->compare('examination_type_id',$this->examination_type_id);

		return new CActiveDataProvider($this, array('criteria'=>$criteria,));
	}
}