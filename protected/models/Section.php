<?php

/**
 * This is the model class for table "section".
 *
 * The followings are the available columns in table 'section':
 * @property integer $id
 * @property string $name
 * @property string $program
 * @property string $server_name
 * @property string $printer_name
 * @property integer $outlet_id
 * @property string $printer_ip
 *
 * The followings are the available model relations:
 * @property Item[] $items
 * @property OutletStock[] $outletStocks
 * @property Outletin[] $outletins
 * @property Outletout[] $outletouts
 * @property Outlet $outlet
 * @property StockTransfer[] $stockTransfers
 * @property Storeout[] $storeouts
 */
class Section extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Section the static model class
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
		return 'section';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
    //array('doadmission, dodischarge','match','pattern'=>'/^(([0-2][0-9]|3[0-1])\-(0[1-9]|1[0-2])\-([1-2][0|1|2|9][0-9][0-9]))+$/','message'=>'{attribute} has Invalid Date'),
//array('model', 'match', 'pattern'=>'/^\w+[\w+\\.]*$/', 'message'=>'{attribute} should only contain word characters and dots.'),
//array('controller', 'match', 'pattern'=>'/^\w+[\w+\\/]*$/', 'message'=>'{attribute} should only contain word characters and slashes.'),
//array('baseControllerClass', 'match', 'pattern'=>'/^[a-zA-Z_]\w*$/', 'message'=>'{attribute} should only contain word characters.'),
//array('username', 'unique'),
//array('password', 'CCompositeUniqueKeyValidator', 'keyColumns' => 'password, branch_id'),

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, outlet_id', 'required'),
			array('outlet_id', 'numerical', 'integerOnly'=>true),
			array('name, server_name, printer_name', 'length', 'max'=>30),
			array('program', 'length', 'max'=>150),
			array('printer_ip', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, program, server_name, printer_name, outlet_id, printer_ip', 'safe', 'on'=>'search'),
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
			'items' => array(self::HAS_MANY, 'Item', 'section_id'),
			'outletStocks' => array(self::HAS_MANY, 'OutletStock', 'section_id'),
			'outletins' => array(self::HAS_MANY, 'Outletin', 'section_id'),
			'outletouts' => array(self::HAS_MANY, 'Outletout', 'section_id'),
			'outlet' => array(self::BELONGS_TO, 'Outlet', 'outlet_id'),
			'stockTransfers' => array(self::HAS_MANY, 'StockTransfer', 'section_id'),
			'storeouts' => array(self::HAS_MANY, 'Storeout', 'section_id'),
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
			'program' => 'Program',
			'server_name' => 'Server Name',
			'printer_name' => 'Printer Name',
			'outlet_id' => 'Outlet',
			'printer_ip' => 'Printer Ip',
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

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.program',$this->program,true);
		$criteria->compare('t.server_name',$this->server_name,true);
		$criteria->compare('t.printer_name',$this->printer_name,true);
		$criteria->compare('outlet.name',$this->outlet_id,true);
		$criteria->compare('t.printer_ip',$this->printer_ip,true);
		$criteria->with=array('outlet',);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>30),//false
		));
	}
}