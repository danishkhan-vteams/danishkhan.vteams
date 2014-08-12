<?php
/**
 * This is the model class for table "settings".
 *
 * The followings are the available columns in table 'settings':
 * @property integer $id
 * @property string $unit
 * @property string $value
 * @property string $fieldtype
 * @property string $htmlcode
 */
class Settings extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Settings the static model class
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
		return 'settings';
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
			array('unit, value,description', 'required'),
			array('unit, value', 'length', 'max'=>50),
			array('fieldtype', 'length', 'max'=>15),
			array('htmlcode, text', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, unit, value, text, description, fieldtype, htmlcode,settingsection_id', 'safe', 'on'=>'search'),
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
		'settingsection' => array(self::BELONGS_TO, 'Settingsection', 'settingsection_id'),
		);
	}
	
	public static function getFieldSettings($id=0){
	$res_set = Settings::model()->find("id = $id");
	$options = json_decode($res_set['htmlcode'],true);
	return $options['data'];
	}
	
	public static function getPhysicianName($id=0){
	$res_set = Settings::model()->find("id = $id");	
	return $res_set['value'];
	}
	
	public static function getMaritalStatus($id=0){
		$res_set = Settings::model()->find("id = 14");
		$options = json_decode($res_set['htmlcode'],true);
		$arr = $options['data'];
		
		return $arr[$id];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'unit' => 'Unit',
			'description'=>'Description',
			'value' => 'Value',
			'text' => 'Text',
			'fieldtype' => 'Fieldtype',
			'htmlcode' => 'Htmlcode',
			'settingsection_id'=>'Group',
		);
	}
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($param='')
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria=new CDbCriteria;
		if(! isset($_GET['Settings_sort'])){$criteria->order="settingsection.id";}
		if($param=='nosample'){$criteria->condition="settingsection.name!='Sample'";}
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.unit',$this->unit,true);
		$criteria->compare('t.value',$this->value,true);
		$criteria->compare('t.description',$this->description,true);
		$criteria->compare('t.fieldtype',$this->fieldtype,true);
		$criteria->compare('t.htmlcode',$this->htmlcode,true);
		$criteria->compare('t.text',$this->text,true);
		$criteria->compare('settingsection.name',$this->settingsection_id,true);
		$criteria->with=array('settingsection');
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>30),//false
		));
	}
}