<?php
namespace MooPhp\MooInterface\Response;
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */

abstract class CommonPack extends Response {

	protected $_packId;
	protected $_pack;
	protected $_warnings;
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

	/**
	 * @return \MooPhp\MooInterface\Data\Pack
	 */
	public function getPack() {
		return $this->_pack;
	}

	/**
	 * @return \MooPhp\MooInterface\Warning[] Any warnings we've triggered
	 */
	public function getWarnings() {
		return $this->_warnings;
	}

	/**
	 * @return array ArraySerialization of dropin urls we can use at this point
	 */
	public function getDropIns() {
		return $this->_dropIns;
	}

	public function setDropIns($dropIns) {
		$this->_dropIns = $dropIns;
	}

	public function setPack($pack) {
		$this->_pack = $pack;
	}

	public function setPackId($packId) {
		$this->_packId = $packId;
	}

	public function setWarnings($warnings) {
		$this->_warnings = $warnings;
	}

	/**
	 * @param \MooPhp\MooInterface\Data\PhysicalSpec $physicalSpec
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
