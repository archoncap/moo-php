<?php
namespace MooPhp\MooInterface\Data\Template\Items;
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */

class TextItem extends Item {

	/**
	 * @var \MooPhp\MooInterface\Data\Types\BoundingBox
	 */
	protected $_clippingBox;

	/**
	 * @var \MooPhp\MooInterface\Data\Types\Font
	 */
	protected $_font;

	/**
	 * @var float
	 */
	protected $_pointSize;

	/**
	 * @var string
	 */
	protected $_alignment;

	/**
	 * @var \MooPhp\MooInterface\Data\Types\Colour
	 */
	protected $_colour;

	/**
	 * @var string
	 */
	protected $_text;

	/**
	 * @var \MooPhp\MooInterface\Data\Template\Items\TextConstraints
	 */
	protected $_constraints;

	public function __construct() {
		$this->setType("Text");
	}

	public function setType($type) {
		if ($type != "Text") {
			throw new \InvalidArgumentException("Attempting to set type of Text to $type.");
		}
		parent::setType($type);
	}

	/**
	 * @return string
	 */
	public function getAlignment() {
		return $this->_alignment;
	}

	/**
	 * @return \MooPhp\MooInterface\Data\Types\BoundingBox
	 */
	public function getClippingBox() {
		return $this->_clippingBox;
	}

	/**
	 * @return \MooPhp\MooInterface\Data\Types\Colour
	 */
	public function getColour() {
		return $this->_colour;
	}

	/**
	 * @return \MooPhp\MooInterface\Data\Template\Items\TextConstraints
	 */
	public function getConstraints() {
		return $this->_constraints;
	}

	/**
	 * @return \MooPhp\MooInterface\Data\Types\Font
	 */
	public function getFont() {
		return $this->_font;
	}

	/**
	 * @return float
	 */
	public function getPointSize() {
		return $this->_pointSize;
	}

	/**
	 * @return string
	 */
	public function getText() {
		return $this->_text;
	}

	/**
	 * @param string $alignment
	 */
	public function setAlignment($alignment) {
		$this->_alignment = $alignment;
	}

	/**
	 * @param \MooPhp\MooInterface\Data\Types\BoundingBox $clippingBox
	 */
	public function setClippingBox($clippingBox) {
		$this->_clippingBox = $clippingBox;
	}

	/**
	 * @param \MooPhp\MooInterface\Data\Types\Colour $colour
	 */
	public function setColour($colour) {
		$this->_colour = $colour;
	}

	/**
	 * @param \MooPhp\MooInterface\Data\Template\Items\TextConstraints $constraints
	 */
	public function setConstraints($constraints) {
		$this->_constraints = $constraints;
	}

	/**
	 * @param \MooPhp\MooInterface\Data\Types\Font $font
	 */
	public function setFont($font) {
		$this->_font = $font;
	}

	/**
	 * @param float $pointSize
	 */
	public function setPointSize($pointSize) {
		$this->_pointSize = $pointSize;
	}

	/**
	 * @param string $text
	 */
	public function setText($text) {
		$this->_text = $text;
	}

}