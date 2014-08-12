<?php

/**
 * This is the model class for table "panel".
 *
 * The followings are the available columns in table 'panel':
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $fax
 * @property string $email
 * @property string $test_ids
 * @property integer $user_id
 * @property string $created_date
 * @property string $updated_date
 *
 * The followings are the available model relations:
 * @property Patient[] $patients
 * @property PatientTestSummary[] $patientTestSummaries
 */
class Panel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Panel the static model class
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
		return 'panel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, user_id', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>250),
			array('price, discount, urgent_charges', 'numerical'),
			array('phone, fax, test_ids', 'length', 'max'=>50),
			array('email', 'length', 'max'=>150),
			array('address, created_date, updated_date, , urgent_charges', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, address, phone, fax, email, test_ids, price, discount, urgent_charges, user_id, created_date, updated_date', 'safe', 'on'=>'search'),
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
			'patients' => array(self::HAS_MANY, 'Patient', 'panel_id'),
			'patientTestSummaries' => array(self::HAS_MANY, 'PatientTestSummary', 'panel_id'),
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
			'address' => 'Address',
			'phone' => 'Phone',
			'fax' => 'Fax',
			'email' => 'Email',
			'test_ids' => 'Test Ids',
			'price' => 'Price',
			'discount' => 'Discount',
			'urgent_charges' => 'Urgent Charges',
			'user_id' => 'User',
			'created_date' => 'Created Date',
			'updated_date' => 'Updated Date',
		);
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('test_ids',$this->test_ids,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('urgent_charges',$this->urgent_charges);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_date',$this->created_date,true);
		$criteria->compare('updated_date',$this->updated_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}