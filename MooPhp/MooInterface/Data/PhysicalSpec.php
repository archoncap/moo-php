<?php
namespace MooPhp\MooInterface\Data;
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */

class PhysicalSpec {

	public function __construct($productType = \MooPhp\MooInterface\MooApi::PRODUCT_TYPE_BUSINESSCARD, $paperClassName = null, $finishingOptionName = null, $packSize = null) {
		$this->_productType = $productType;
		$this->_paperClassName = $paperClassName;
		$this->_finishingOptionName = $finishingOptionName;
		$this->_packSize = $packSize;
	}

	/**
	 * @var string
	 */
	protected $_productType;

	/**
	 * @var string
	 */
	protected $_paperClassName;

	/**
	 * @var string
	 */
	protected $_finishingOptionName;

	/**
	 * @var int
	 */
	protected $_packSize;

	/**
	 * @param string $finishingOptionName
	 * @return \MooPhp\MooInterface\Data\PhysicalSpec
	 */
	public function setFinishingOptionName($finishingOptionName) {
		$this->_finishingOptionName = $finishingOptionName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFinishingOptionName() {
		return $this->_finishingOptionName;
	}

	/**
	 * @param int $packSize
	 * @return \MooPhp\MooInterface\Data\PhysicalSpec
	 */
	public function setPackSize($packSize) {
		$this->_packSize = $packSize;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPackSize() {
		return $this->_packSize;
	}

	/**
	 * @param string $paperClassName
	 * @return \MooPhp\MooInterface\Data\PhysicalSpec
	 */
	public function setPaperClassName($paperClassName) {
		$this->_paperClassName = $paperClassName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPaperClassName() {
		return $this->_paperClassName;
	}

	/**
	 * @param string $productType
	 * @return \MooPhp\MooInterface\Data\PhysicalSpec
	 */
	public function setProductType($productType) {
		$this->_productType = $productType;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProductType() {
		return $this->_productType;
	}
}
