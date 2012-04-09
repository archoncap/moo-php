<?php
namespace MooPhp\MooInterface\Response;
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */

class UpdatePhysicalSpec extends Response {

	protected $_packId;
	protected $_dropIns;

	/**
	 * @var \MooPhp\MooInterface\Data\PhysicalSpec
	 */
	private $_physicalSpec;

	/**
	 * @return string the builder ID
	 */
	public function getPackId() {
		return $this->_packId;
	}

	public function setPackId($packId) {
		$this->_packId = $packId;
	}

	/**
	 * @param \MooPhp\MooInterface\Data\PhysicalSpec $physicalSpec
	 * @return \MooPhp\MooInterface\Response\UpdatePhysicalSpec
	 */
	public function setPhysicalSpec($physicalSpec) {
		$this->_physicalSpec = $physicalSpec;
		return $this;
	}

	/**
	 * @return \MooPhp\MooInterface\Data\PhysicalSpec
	 */
	public function getPhysicalSpec() {
		return $this->_physicalSpec;
	}

}
