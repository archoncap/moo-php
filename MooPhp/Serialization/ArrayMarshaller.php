<?php
namespace MooPhp\Serialization;
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */

class ArrayMarshaller implements Marshaller {

	/**
	 * @var \MooPhp\Serialization\Config\MarshallerConfig
	 */
	private $_config;

	public function __construct(Config\MarshallerConfig $config) {
		$this->_config = $config;
	}


	/**
	 * @param $data
	 * @param \MooPhp\Serialization\Config\Types\PropertyType $type
	 * @return array|bool|float|int|null|string
	 * @throws \RuntimeException
	 */
	protected function _propertyAsType($data, \MooPhp\Serialization\Config\Types\PropertyType $type) {
		if (!isset($data)) {
			return null;
		}

		switch ($type->getType()) {
			case "string":
				return (string)$data;
			case "int":
				return (int)$data;
			case "bool":
				return (bool)$data;
			case "stringbool":
				return $data ? "true" : "false";
			case "float":
				return (float)$data;
			case "ref":
				return $this->marshall($data, $type->getRef());
			case "json":
				return "".json_encode($this->_propertyAsType($data, $type->getValue()), JSON_FORCE_OBJECT);
			case "array":
				$converted = array();
				foreach ($data as $key => $value) {
					$convertedKey = $this->_propertyAsType($key, $type->getKey());
					$convertedValue = $this->_propertyAsType($value, $type->getValue());
					$converted[$convertedKey] = $convertedValue;
				}
				return $converted;
		}

		throw new \RuntimeException("Unknown type " . $type->getType());
	}

	/**
	 * @param $data
	 * @param \MooPhp\Serialization\Config\Types\PropertyType $type
	 * @return array|bool|float|int|null|object|string
	 * @throws \RuntimeException
	 */
	protected function _valueAsType($data, \MooPhp\Serialization\Config\Types\PropertyType $type) {
		if (!isset($data)) {
			return null;
		}

		switch ($type->getType()) {
			case "string":
				return (string)$data;
			case "int":
				return (int)$data;
			case "bool":
				return (bool)$data;
			case "stringbool":
				return ($data && $data != "false") ? true : false;
			case "float":
				return (float)$data;
			case "ref":
				return $this->unmarshall($data, $type->getRef());
			case "json":
				return $this->_valueAsType(json_decode($data, true), $type->getValue());
			case "array":
				$converted = array();
				foreach ($data as $key => $value) {
					$convertedKey = $this->_valueAsType($key, $type->getKey());
					$convertedValue = $this->_valueAsType($value, $type->getValue());
					$converted[$convertedKey] = $convertedValue;
				}
				return $converted;
		}
		throw new \RuntimeException("Unknown type " . $type->getType());
	}

	public function marshall($object, $ref) {
		if (!is_object($object)) {
			throw new \InvalidArgumentException("Got passed non object for marshalling of $ref");
		}

		$reflector = new \ReflectionObject($object);
		$entry = $this->_config->getConfigElement($ref);
		if (!isset($entry)) {
			throw new \RuntimeException("Cannot find config entry for $ref working on " . $reflector->getName());
		}
		/*
		TODO: Work out what to do re implementation type vs base type
		if (!$object instanceof $entry["type"]) {
			throw new \RuntimeException("Object is of invalid type");
		}
		*/
		$marshalled = array();
		if ($entry->getProperties()) {
			foreach ($entry->getProperties() as $property => $details) {
				$getter = "get" . ucfirst($property);
				try {
					$refGetter = $reflector->getMethod($getter);
					$propertyValue = $refGetter->invoke($object);
				} catch (\Exception $e) {
					throw new \RuntimeException("Unable to call getter $getter for $ref", 0, $e);
				}
				$outputName = $property;
				if ($options = $details->getOption("array")) {
					if ($options->getOption("name")) {
						$outputName = $options->getOption("name");
					}
				}
				$value = $this->_propertyAsType($propertyValue, $details);
				if (isset($value)) {
					$marshalled[$outputName] = $value;
				}
			}
		}

		if ($discriminatorConfig = $entry->getDiscriminator()) {
			// OK, this is just a base type...
			$getter = "get" . ucfirst($discriminatorConfig->getProperty());
			try {
				$refGetter = $reflector->getMethod($getter);
				$subType = $refGetter->invoke($object);
			} catch (\Exception $e) {
				throw new \RuntimeException("Unable to call getter $getter for $ref", 0, $e);
			}

			$discrimName = $discriminatorConfig->getProperty();
			if ($discrimOptions = $discriminatorConfig->getOption("array")) {
				if ($discrimOptions->getOption("name")) {
					$discrimName = $discrimOptions->getOption("name");
				}
			}
			if ($discriminatorConfig->getValue($subType)) {
				$ref = $discriminatorConfig->getValue($subType);
				// We also need to add the discriminator to the serialized data
				$marshalled[$discrimName] = $subType;
				$marshalled += $this->marshall($object, $ref);
			} else {
				// Otherwise we have no idea... serialize as the base type and add
				// the discriminator value
				$marshalled[$discrimName] = $subType;
			}

		}
		return $marshalled;

	}

	public function unmarshall($data, $ref) {
		if (!is_array($data)) {
			throw new \InvalidArgumentException("Got passed non array for unmarshalling of $ref");
		}

		$entry = $this->_config->getConfigElement($ref);
		if (!isset($entry)) {
			throw new \RuntimeException("Cannot find config entry for $ref");
		}

		$object = null;
		if ($discriminatorConfig = $entry->getDiscriminator()) {
			// We might not be the real class!
			$subTypeKey = $discriminatorConfig->getProperty();
			if ($discrimOptions = $discriminatorConfig->getOption("array")) {
				if ($discrimOptions->getOption("name")) {
					$subTypeKey = $discrimOptions->getOption("name");
				}
			}
			if (isset($data[$subTypeKey])) {
				$subTypeName = $data[$subTypeKey];
				if ($subType = $discriminatorConfig->getValue($subTypeName)) {
					// OK, lets start populating from the top down
					$object = $this->unmarshall($data, $subType);
				}
			}
		}
		$constructorArgConfig = array();
		if (!isset($object)) {
			$args = array();
			if ($entry->getConstructorArgs()) {
				foreach ($entry->getConstructorArgs() as $argName) {
					$argConfig = $entry->getProperty($argName);
					$constructorArgConfig[$argName] = $argConfig;

					$name = $argName;
					if ($options = $argConfig->getOption("array")) {
						if ($options->getOption("name")) {
							$name = $options->getOption("name");
						}
					}

					$value = isset($data[$name]) ? $data[$name] : null;
					$args[] = $this->_valueAsType($value, $argConfig);
				}
			}
			try {
				$classReflector = new \ReflectionClass($entry->getType());
				$object = $classReflector->newInstanceArgs($args);
			} catch (\Exception $e) {
				throw new \RuntimeException("Failed to create instance of " . $entry->getType() . " for $ref", 0, $e);
			}
		}
		if ($discriminatorConfig = $entry->getDiscriminator()) {
			$subTypeKey = $discriminatorConfig->getProperty();
			if ($discrimOptions = $discriminatorConfig->getOption("array")) {
				if ($discrimOptions->getOption("name")) {
					$subTypeKey = $discrimOptions->getOption("name");
				}
			}
			if (isset($data[$subTypeKey])) {
				$subTypeName = $data[$subTypeKey];
				$setter = "set" . ucfirst($discriminatorConfig->getProperty());
				try {
					$this->_callSetter($object, $setter, $subTypeName);
				} catch (\RuntimeException $e) {
					throw new \RuntimeException("Error calling $setter while processing $ref");
				}
			}
		}


		$propertiesConfigNameTypeMap = array();
		if ($entry->getProperties()) {
			foreach ($entry->getProperties() as $property => $details) {
				if (isset($constructorArgConfig[$property])) {
					// No need to look it up if it was a constructor arg
					continue;
				}
				$name = $property;
				if ($options = $details->getOption("array")) {
					if ($options->getOption("name")) {
						$name = $options->getOption("name");
					}
				}
				$propertiesConfigNameTypeMap[$name] = array($property, $details);
			}
		}

		$unknownProperties = array();
		foreach ($data as $key => $value) {
			if (!isset($propertiesConfigNameTypeMap[$key])) {
				$unknownProperties[$key] = $value;
			} else {
				$setter = "set" . ucfirst($propertiesConfigNameTypeMap[$key][0]);
				$setValue = $this->_valueAsType($value, $propertiesConfigNameTypeMap[$key][1]);
				try {
					$this->_callSetter($object, $setter, $setValue);
				} catch (\RuntimeException $e) {
					throw new \RuntimeException("Error calling $setter while processing $ref");
				}
			}
		}

		return $object;
	}

	private function _callSetter($object, $setter, $value) {
		if (is_callable(array($object, $setter))) {
			// Might be a __call function. Try it and see what happens.
			call_user_func(array($object, $setter), $value);
		} else {
			throw new \RuntimeException("Unable to call $setter on object");
		}
	}

}
