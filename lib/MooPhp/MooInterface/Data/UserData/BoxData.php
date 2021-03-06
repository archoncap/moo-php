<?php
namespace MooPhp\MooInterface\Data\UserData;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;
use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonTypeName;

/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 *
 * @JsonTypeName("boxData")
 */

class BoxData extends Datum
{

    /**
     * @var \MooPhp\MooInterface\Data\Types\Colour
     */
    protected $_colour;

    public function __construct($linkId = null)
    {
        parent::__construct($linkId);
    }

    /**
     * @return \MooPhp\MooInterface\Data\Types\Colour
     * @JsonProperty(type="\MooPhp\MooInterface\Data\Types\Colour")
     */
    public function getColour()
    {
        return $this->_colour;
    }

    /**
     * @param \MooPhp\MooInterface\Data\Types\Colour $colour
     * @return \MooPhp\MooInterface\Data\UserData\BoxData
     * @JsonProperty(type="\MooPhp\MooInterface\Data\Types\Colour")
     */
    public function setColour($colour)
    {
        $this->_colour = $colour;
        return $this;
    }

}
