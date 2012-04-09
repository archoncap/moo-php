<?php
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */
namespace ArrayMarshallerTests\Marshalling;

require_once(__DIR__ . "/../../TestInit.php");

class ArrayMarshallerMarshallingTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @param $config
	 * @return \MooPhp\Serialization\ArrayMarshaller
	 */
	protected function _getMarshaller($config) {
		$configurator = new \MooPhp\Serialization\ArrayConfigBaseConfig();
		$marshaller = new \MooPhp\Serialization\ArrayMarshaller($configurator->getConfig());
		return new \MooPhp\Serialization\ArrayMarshaller($marshaller->unmarshall(array("config" => $config), "Root"));
	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\Marshaller
	 * @expectedException \InvalidArgumentException
	 */
	public function testMarshalNonObject() {
		$config = array(

		);

		$marshaller = $this->_getMarshaller($config);
		$marshaller->marshall("hello", "Test");

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @expectedException \RuntimeException
	 */
	public function testMarshalNoConfigElement() {
		$config = array(

		);

		$marshaller = $this->_getMarshaller($config);
		$marshaller->marshall(new DummyClassA(), "Test");

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 * @expectedException \RuntimeException
	 */
	public function testMarshallBadTypeConfig() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"type" => "giraffe"
					)
				)
			)
		);

		$mock = $this->getMock('DummyClassA', array('getGoats'));

		$mock->expects($this->once())->method('getGoats')->will($this->returnValue("baa"));

		$marshaller = $this->_getMarshaller($config);

		$marshaller->marshall($mock, "DummyClassA");

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 * @expectedException \RuntimeException
	 */
	public function testMarshallBadMethod() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"type" => "string"
					)
				)
			)
		);

		$mock = $this->getMock('DummyClassA', array());

		$marshaller = $this->_getMarshaller($config);

		$marshaller->marshall($mock, "DummyClassA");

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 */
	public function testMarshallPrimitives() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"options" => array(
							"array" => array(
								"options" => array(
									"name" => "geets"
								)
							)
						),
						"type" => "string"
					),
					"stoats" => array(
						"type" => "int"
					),
					"boats" => array(
						"type" => "float"
					),
					"groats" => array(
						"options" => array(
							"array" => array(
								"options" => array(
									"name" => "goats"
								)
							)
						),
						"type" => "bool"
					),
				)
			)
		);

		$mock = $this->getMock('DummyClassA', array('getGoats', 'getStoats', 'getBoats', 'getGroats'));

		$mock->expects($this->once())->method('getGoats')->will($this->returnValue("baa"));
		$mock->expects($this->once())->method('getStoats')->will($this->returnValue(1));
		$mock->expects($this->once())->method('getBoats')->will($this->returnValue(5.1));
		$mock->expects($this->once())->method('getGroats')->will($this->returnValue(false));

		$marshaller = $this->_getMarshaller($config);

		$marshalled = $marshaller->marshall($mock, "DummyClassA");

		$expected = array(
			"geets" => "baa",
			"stoats" => 1,
			"boats" => 5.1,
			"goats" => false
		);

		$this->assertSame($expected, $marshalled);

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 */
	public function testMarshallPrimativeArray() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"type" => "array",
						"key" => array("type" => "int"),
						"value" => array("type" => "string")
					),
					"stoats" => array(
						"type" => "array",
						"key" => array("type" => "int"),
						"value" => array("type" => "int")
					),
					"boats" => array(
						"type" => "array",
						"key" => array("type" => "string"),
						"value" => array("type" => "float")
					),
					"groats" => array(
						"type" => "array",
						"key" => array("type" => "int"),
						"value" => array("type" => "bool")
					)
				)
			)
		);

		$mock = $this->getMock('DummyClassA', array('getGoats', 'getStoats', 'getBoats', 'getGroats'));

		$mock->expects($this->once())->method('getGoats')->will($this->returnValue(array("baa", "bar", "fishpaste wobble")));
		$mock->expects($this->once())->method('getStoats')->will($this->returnValue(array(9 => 1, 2 => 7, 378 => 9, 44 => 8)));
		$mock->expects($this->once())->method('getBoats')->will($this->returnValue(array("arr" => 1.1, "pirates" => 6.1)));
		$mock->expects($this->once())->method('getGroats')->will($this->returnValue(array(true, false, true, true, false)));

		$marshaller = $this->_getMarshaller($config);

		$marshalled = $marshaller->marshall($mock, "DummyClassA");

		$expected = array(
			"goats" => array(0 => "baa", 1 => "bar", 2 => "fishpaste wobble"),
			"stoats" => array(9 => 1, 2 => 7, 378 => 9, 44 => 8),
			"boats" => array("arr" => 1.1, "pirates" => 6.1),
			"groats" => array(0 => true, 1 => false, 2 => true, 3 => true, 4 => false),
		);


		$this->assertSame($expected, $marshalled);

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 */
	public function testMarshallNestedArray() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"type" => "array",
						"key" => array("type" => "int"),
						"value" => array(
							"type" => "array",
							"key" => array("type" => "string"),
							"value" => array("type" => "string")
						)
					),
					"stoats" => array(
						"type" => "array",
						"key" => array("type" => "string"),
						"value" => array(
							"type" => "array",
							"key" => array("type" => "string"),
							"value" => array(
								"type" => "array",
								"key" => array("type" => "int"),
								"value" => array("type" => "string")
							)
						)
					),
					"boats" => array(
						"type" => "string"
					),
					"groats" => array(
						"type" => "array",
						"key" => array("type" => "int"),
						"value" => array("type" => "bool")
					)
				)
			)
		);

		$mock = $this->getMock('DummyClassA', array('getGoats', 'getStoats', 'getBoats', 'getGroats'));

		$mock->expects($this->once())->method('getGoats')->will($this->returnValue(
			array(
				0 => array("baa" => "moo", "fish" => "wobble"),
				1 => array("florp" => "stoat", "goat" => "fork")
				)
			)
		);
		$mock->expects($this->once())->method('getStoats')->will($this->returnValue(
			array(
				"foo" => array("blip" => array(2 => "woo", 5 => "wee", 1 => "nay", 0 => "yay")),
				"oof" => array("blap" => array(0 => "woo"), "barp" => array(6 => "oof"))
				)
			)
		);
		$mock->expects($this->once())->method('getBoats')->will($this->returnValue("bloop"));
		$mock->expects($this->once())->method('getGroats')->will($this->returnValue(array(true, false, true, true, false)));

		$marshaller = $this->_getMarshaller($config);

		$marshalled = $marshaller->marshall($mock, "DummyClassA");

		$expected = array(
			"goats" =>
				array(
					0 => array("baa" => "moo", "fish" => "wobble"),
					1 => array("florp" => "stoat", "goat" => "fork")
				),
			"stoats" =>
				array(
					"foo" => array("blip" => array(2 => "woo", 5 => "wee", 1 => "nay", 0 => "yay")),
					"oof" => array("blap" => array(0 => "woo"), "barp" => array(6 => "oof"))
				),
			"boats" => "bloop",
			"groats" => array(0 => true, 1 => false, 2 => true, 3 => true, 4 => false),
		);


		$this->assertSame($expected, $marshalled);

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 */
	public function testMarshallObjects() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"type" => "string"
					),
					"snorks" => array(
						"type" => "ref",
						"ref" => "DummyClassC"
					),
					"notes" => array(
						"type" => "ref",
						"ref" => "DummyClassB"
					),
					"groats" => array(
						"type" => "bool"
					),
				)
			),
			"DummyClassB" => array(
				"type" => "DummyClassB",
				"properties" => array(
					"flibble" => array(
						"type" => "string"
					),
					"flobble" => array(
						"type" => "ref",
						"ref" => "DummyClassD"
					)
				)
			),
			"DummyClassC" => array(
				"type" => "DummyClassC"
			),
			"DummyClassD" => array(
				"type" => "DummyClassD",
				"properties" => array(
					"arfArf" => array(
						"type" => "string"
					)
				)
			)
		);

		$mockD = $this->getMock('DummyClassD', array('getArfArf'));
		$mockD->expects($this->once())->method('getArfArf')->will($this->returnValue('AAAARF!'));

		$mockB = $this->getMock('DummyClassB', array('getFlibble', 'getFlobble'));
		$mockB->expects(($this->once()))->method('getFlibble')->will($this->returnValue("foo"));
		$mockB->expects(($this->once()))->method('getFlobble')->will($this->returnValue($mockD));

		$mockC = $this->getMock('DummyClassC', array());

		$mock = $this->getMock('DummyClassA', array('getGoats', 'getSnorks', 'getNotes', 'getGroats'));
		$mock->expects($this->once())->method('getGoats')->will($this->returnValue("baa"));
		$mock->expects($this->once())->method('getSnorks')->will($this->returnValue($mockC));
		$mock->expects($this->once())->method('getNotes')->will($this->returnValue($mockB));
		$mock->expects($this->once())->method('getGroats')->will($this->returnValue(false));

		$marshaller = $this->_getMarshaller($config);

		$marshalled = $marshaller->marshall($mock, "DummyClassA");

		$expected = array(
			"goats" => "baa",
			"snorks" => array(),
			"notes" => array(
				"flibble" => "foo",
				"flobble" => array(
					"arfArf" => "AAAARF!"
				)
			),
			"groats" => false
		);

		$this->assertSame($expected, $marshalled);

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 */
	public function testMarshallNulls() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"type" => "string"
					),
					"stoats" => array(
						"type" => "array",
						"key" => array("type" => "int"),
						"value" => array("type" => "string")
					),
					"boats" => array(
						"type" => "float"
					),
					"groats" => array(
						"type" => "bool"
					),
				)
			),
		);

		$mock = $this->getMock('DummyClassA', array('getGoats', 'getStoats', 'getBoats', 'getGroats'));

		$mock->expects($this->once())->method('getGoats')->will($this->returnValue(null));
		$mock->expects($this->once())->method('getStoats')->will($this->returnValue(array(2 => null)));
		$mock->expects($this->once())->method('getBoats')->will($this->returnValue(5.1));
		$mock->expects($this->once())->method('getGroats')->will($this->returnValue(null));

		$marshaller = $this->_getMarshaller($config);

		$marshalled = $marshaller->marshall($mock, "DummyClassA");

		$expected = array(
			"stoats" => array(2 => null),
			"boats" => 5.1,
		);

		$this->assertSame($expected, $marshalled);

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 */
	public function testMarshallJson() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"properties" => array(
					"goats" => array(
						"type" => "string"
					),
					"stoats" => array(
						"type" => "json",
						"value" => array("type" => "ref", "ref" => "DummyClassB")
					)
				)
			),
			"DummyClassB" => array(
				"type" => "DummyClassB",
				"properties" => array(
					"flibble" => array(
						"type" => "string"
					),
					"flobble" => array(
						"type" => "ref",
						"ref" => "DummyClassD"
					)
				),
			),
			"DummyClassD" => array(
				"type" => "DummyClassD",
				"properties" => array(
					"arfArf" => array(
						"type" => "string"
					)
				)
			)
		);
		$mockD = $this->getMock('DummyClassD', array('getArfArf'));
		$mockD->expects($this->once())->method('getArfArf')->will($this->returnValue('AAAARF!'));

		$mockB = $this->getMock('DummyClassB', array('getFlibble', 'getFlobble'));
		$mockB->expects(($this->once()))->method('getFlibble')->will($this->returnValue("foo"));
		$mockB->expects(($this->once()))->method('getFlobble')->will($this->returnValue($mockD));

		$mock = $this->getMock('DummyClassA', array('getGoats', 'getStoats'));

		$mock->expects($this->once())->method('getGoats')->will($this->returnValue("foo"));
		$mock->expects($this->once())->method('getStoats')->will($this->returnValue($mockB));

		$marshaller = $this->_getMarshaller($config);

		$marshalled = $marshaller->marshall($mock, "DummyClassA");

		$expected = array(
			"goats" => "foo",
			"stoats" => json_encode(array(
				"flibble" => "foo",
				"flobble" => array(
					"arfArf" => "AAAARF!"
				)), true)
		);

		$this->assertSame($expected, $marshalled);

	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 */
	public function testMarshallWithDiscriminators() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"discriminator" => array(
					"options" => array(
						"array" => array(
							"options" => array(
								"name" => "tipe"
							)
						)
					),
					"property" => "tripe",
					"values" => array(
						"Flork" => "DummyClassAA",
						"fLark" => "DummyClassAB"
					)
				)
			),
			"DummyClassAA" => array(
				"type" => "DummyClassAA",
				"properties" => array(
					"foo" => array(
						"type" => "string"
					)
				)
			),
			"DummyClassAB" => array(
				"type" => "DummyClassAB",
			)
		);

		$mockAA = $this->getMock('DummyClassAA', array('getFoo', 'getTripe'));
		$mockAA->expects($this->once())->method('getFoo')->will($this->returnValue('bar'));
		$mockAA->expects($this->once())->method('getTripe')->will($this->returnValue('Flork'));

		$mockAB = $this->getMock('DummyClassAB', array('getTripe'));
		$mockAB->expects($this->once())->method('getTripe')->will($this->returnValue('fLark'));

		$mock = $this->getMock('DummyClassA', array('getTripe'));
		$mock->expects($this->once())->method('getTripe')->will($this->returnValue("dunno"));

		$marshaller = $this->_getMarshaller($config);

		$marshalled = $marshaller->marshall($mockAA, "DummyClassA");
		$expected = array(
			"tipe" => "Flork",
			"foo" => "bar",
		);
		$this->assertSame($expected, $marshalled);

		$marshalled = $marshaller->marshall($mockAB, "DummyClassA");
		$expected = array(
			"tipe" => "fLark",
		);
		$this->assertSame($expected, $marshalled);

		$marshalled = $marshaller->marshall($mock, "DummyClassA");
		$expected = array(
			"tipe" => "dunno",
		);
		$this->assertSame($expected, $marshalled);
	}

	/**
	 * @covers \MooPhp\Serialization\ArrayMarshaller::__construct
	 * @covers \MooPhp\Serialization\ArrayMarshaller::marshall
	 * @covers \MooPhp\Serialization\ArrayMarshaller::_propertyAsType
	 * @expectedException \RuntimeException
	 */
	public function testMarshallWithMissingDiscriminatorMethod() {

		$config = array(
			"DummyClassA" => array(
				"type" => 'DummyClassA',
				"discriminator" => array(
					"property" => "tripe",
					"values" => array(
						"fLark" => "DummyClassAB"
					)
				)
			),
			"DummyClassAB" => array(
				"type" => "DummyClassAB",
			)
		);

		$mockAB = $this->getMock('DummyClassAB', array());

		$marshaller = $this->_getMarshaller($config);

		$marshaller->marshall($mockAB, "DummyClassA");
	}
}

class DummyClassA {
	public static $mockery;

	public function __call($name, $args) {
		return call_user_func_array(array(self::$mockery, $name), $args);
	}
}

class DummyClassB {
	public static $mockery;

	public function __call($name, $args) {
		return call_user_func_array(array(self::$mockery, $name), $args);
	}
}

class DummyClassC {
	public static $mockery;

	public function __call($name, $args) {
		return call_user_func_array(array(self::$mockery, $name), $args);
	}
}

class DummyClassD {
	public static $mockery;

	public function __call($name, $args) {
		return call_user_func_array(array(self::$mockery, $name), $args);
	}
}

class DummyClassAA extends DummyClassA {

}

class DummyClassAB extends DummyClassA {

}
