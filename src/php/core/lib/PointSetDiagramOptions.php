<?php
namespace core\lib;

/**
 * Represents the configuration options for a point set diagram image.
 *
 * This class provides a structure for storing and manipulating the various
 * parameters needed to generate a point set diagram image, such as cell dimensions,
 * coordinate range, scaling factors, and graphical properties like thickness of lines.
 *
 * @package core\lib
 */

class PointSetDiagramOptions
{
    /** @var int The width of each cell in the diagram. */
    private $cellWidth;

    /** @var int The height of each cell in the diagram. */
    private $cellHeight;

    /** @var int The starting x-coordinate for the diagram. */
    private $xfrom;

    /** @var int The ending x-coordinate for the diagram. */
    private $xto;

    /** @var int The starting y-coordinate for the diagram. */
    private $yfrom;

    /** @var int The ending y-coordinate for the diagram. */
    private $yto;

    /** @var int The scale factor for the x-axis. */
    private $xscale;

    /** @var int The scale factor for the y-axis. */
    private $yscale;

    /** @var int The offset for the axes from the edge of the diagram. */
    private $axesOffset;

    /** @var int The offset for loops from the axes. */
    private $loopOffset;

    /** @var int The starting point of the loop on the diagram. */
    private $loopStart;

    /** @var int The ending point of the loop on the diagram. */
    private $loopEnd;

    /** @var int The center point of the loop on the diagram. */
    private $loopCenter;

    /** @var int The thickness of the grid lines on the diagram. */
    private $gridThickness;

    /** @var int The thickness of the axes lines on the diagram. */
    private $axesThickness;

    /** @var int The overall width of the diagram. */
    private $width;

    /** @var int The overall height of the diagram. */
    private $height;

    /** @var int The x-coordinate of the left edge of the diagram image. */
    private $imageLeftEdgeX;

    /** @var int The y-coordinate of the top edge of the diagram image. */
    private $imageTopY;

    /** @var int The x-axes of the diagram. */
    private $xAxesAbsoluteYCoordinate;

    /** @var int The y-axes of the diagram. */
    private $yAxesAbsoluteXCoordinate;

    /** @var int The y-position of the x-axes on the diagram. */
    private $xAxesYPosition;

    /** @var int The x-position of the y-axes on the diagram. */
    private $yAxesXPosition;

    /**
     * Constructor for PointSetDiagramOptions.
     *
     * Initializes default values for the point set diagram settings and computes additional parameters.
     */
    public function __construct()
    {
        $this->cellWidth = 30;
        $this->cellHeight = 30;
        $this->xfrom = -10;
        $this->xto = 10;
        $this->yfrom = -10;
        $this->yto = 10;
        $this->xscale = 1;
        $this->yscale = 1;

        $this->computeParams();
    }


    /**
     * Get the value of cellWidth
     */
    public function getCellWidth()
    {
        return $this->cellWidth;
    }

    /**
     * Set the value of cellWidth
     *
     * @return  void
     */
    public function setCellWidth($cellWidth)
    {
        $this->cellWidth = $cellWidth;
    }

    /**
     * Get the value of cellHeight
     */
    public function getCellHeight()
    {
        return $this->cellHeight;
    }

    /**
     * Set the value of cellHeight
     *
     * @return  void
     */
    public function setCellHeight($cellHeight)
    {
        $this->cellHeight = $cellHeight;
    }

    /**
     * Get the value of xto
     */
    public function getXto()
    {
        return $this->xto;
    }

    /**
     * Set the value of xto
     *
     * @return  void
     */
    public function setXto($xto)
    {
        $this->xto = $xto;
    }

    /**
     * Get the value of xfrom
     */
    public function getXfrom()
    {
        return $this->xfrom;
    }

    /**
     * Set the value of xfrom
     *
     * @return  void
     */
    public function setXfrom($xfrom)
    {
        $this->xfrom = $xfrom;
    }

    /**
     * Get the value of yfrom
     */
    public function getYfrom()
    {
        return $this->yfrom;
    }

    /**
     * Set the value of yfrom
     *
     * @return  void
     */
    public function setYfrom($yfrom)
    {
        $this->yfrom = $yfrom;
    }

    /**
     * Get the value of yto
     */
    public function getYto()
    {
        return $this->yto;
    }

    /**
     * Set the value of yto
     *
     * @return  void
     */
    public function setYto($yto)
    {
        $this->yto = $yto;
    }

    /**
     * Get the value of xscale
     */
    public function getXscale()
    {
        return $this->xscale;
    }

    /**
     * Set the value of xscale
     *
     * @return  void
     */
    public function setXscale($xscale)
    {
        $this->xscale = $xscale;

    }

    /**
     * Get the value of yscale
     */
    public function getYscale()
    {
        return $this->yscale;
    }

    /**
     * Set the value of yscale
     *
     * @return  void
     */
    public function setYscale($yscale)
    {
        $this->yscale = $yscale;
    }

    /**
     * Get the value of axesOffset
     */
    public function getAxesOffset()
    {
        return $this->axesOffset;
    }

    /**
     * Set the value of axesOffset
     *
     * @return  void
     */
    public function setAxesOffset($axesOffset)
    {
        $this->axesOffset = $axesOffset;
    }

    /**
     * Get the value of loopOffset
     */
    public function getLoopOffset()
    {
        return $this->loopOffset;
    }

    /**
     * Set the value of loopOffset
     *
     * @return  void
     */
    public function setLoopOffset($loopOffset)
    {
        $this->loopOffset = $loopOffset;
    }

    /**
     * Get the value of loopStart
     */
    public function getLoopStart()
    {
        return $this->loopStart;
    }

    /**
     * Set the value of loopStart
     *
     * @return  void
     */
    public function setLoopStart($loopStart)
    {
        $this->loopStart = $loopStart;
    }

    /**
     * Get the value of loopEnd
     */
    public function getLoopEnd()
    {
        return $this->loopEnd;
    }

    /**
     * Set the value of loopEnd
     *
     * @return  void
     */
    public function setLoopEnd($loopEnd)
    {
        $this->loopEnd = $loopEnd;
    }

    /**
     * Get the value of loopCenter
     */
    public function getLoopCenter()
    {
        return $this->loopCenter;
    }

    /**
     * Set the value of loopCenter
     *
     * @return  void
     */
    public function setLoopCenter($loopCenter)
    {
        $this->loopCenter = $loopCenter;
    }

    /**
     * Get the value of gridThickness
     */
    public function getGridThickness()
    {
        return $this->gridThickness;
    }

    /**
     * Set the value of gridThickness
     *
     * @return  void
     */
    public function setGridThickness($gridThickness)
    {
        $this->gridThickness = $gridThickness;
    }

    /**
     * Get the value of axesThickness
     */
    public function getAxesThickness()
    {
        return $this->axesThickness;
    }

    /**
     * Set the value of axesThickness
     *
     * @return  void
     */
    public function setAxesThickness($axesThickness)
    {
        $this->axesThickness = $axesThickness;
    }

    /**
     * Get the value of width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the value of width
     *
     * @return  void
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Get the value of height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the value of height
     *
     * @return  void
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Get the value of imageLeftEdgeX
     */
    public function getImageLeftEdgeX()
    {
        return $this->imageLeftEdgeX;
    }

    /**
     * Set the value of imageLeftEdgeX
     *
     * @return  void
     */
    public function setImageLeftEdgeX($imageLeftEdgeX)
    {
        $this->imageLeftEdgeX = $imageLeftEdgeX;
    }

    /**
     * Get the value of imageTopY
     */
    public function getImageTopY()
    {
        return $this->imageTopY;
    }

    /**
     * Set the value of imageTopY
     *
     * @return  void
     */
    public function setImageTopY($imageTopY)
    {
        $this->imageTopY = $imageTopY;
    }

    /**
     * Get the value of xAxes
     */
    public function getXAxesAbsoluteYCoordinate()
    {
        return $this->xAxesAbsoluteYCoordinate;
    }

    /**
     * Set the value of xAxes
     *
     * @return  void
     */
    public function setXAxesAbsoluteYCoordinate($xAxesAbsoluteYCoordinate)
    {
        $this->xAxesAbsoluteYCoordinate = $xAxesAbsoluteYCoordinate;
    }

    /**
     * Get the value of yAxes
     */
    public function getYAxesAbsoluteXCoordinate()
    {
        return $this->yAxesAbsoluteXCoordinate;
    }

    /**
     * Set the value of yAxes
     *
     * @return  void
     */
    public function setYAxesAbsoluteXCoordinate($yAxesAbsoluteXCoordinate)
    {
        $this->yAxesAbsoluteXCoordinate = $yAxesAbsoluteXCoordinate;
    }

    /**
     * Get the value of XAxesYPosition
     */
    public function getXAxesYPosition()
    {
        return $this->xAxesYPosition;
    }

    /**
     * Set the value of xAxesYPosition
     *
     * @return  void
     */
    public function setXAxesYPosition($xAxesYPosition)
    {
        $this->xAxesYPosition = $xAxesYPosition;
    }

    /**
     * Get the value of YAxesXPosition
     */
    public function getYAxesXPosition()
    {
        return $this->yAxesXPosition;
    }

    /**
     * Set the value of YAxesXPosition
     *
     * @return  void
     */
    public function setYAxesXPosition($yAxesXPosition)
    {
        $this->yAxesXPosition = $yAxesXPosition;
    }

    /**
     * Converts a directed coordinate to an absolute coordinate on the image.
     *
     * This method takes a coordinate value and a direction (represented as a string)
     * and calculates the absolute position of that coordinate on the image based on
     * the current axes positions.
     *
     * @param int $coordinate The coordinate value to be converted.
     * @param string $directedAxes The direction of the axis ('-x', '+x', '-y', '+y').
     * @return int The absolute coordinate value on the image.
     */

    public function convertDirectedCoordinateToAbsoluteCoordinate($coordinate, $directedAxes)
    {
        switch ($directedAxes) {
            case '-x':
                return $this->xAxesAbsoluteYCoordinate - $coordinate;
            case '+x':
                return $this->xAxesAbsoluteYCoordinate + $coordinate;
            case '+y':
                return $this->yAxesAbsoluteXCoordinate - $coordinate;
            case '-y':
                return $this->yAxesAbsoluteXCoordinate + $coordinate;
        }

    }

    /**
     * Computes and sets the internal parameters for the image.
     *
     * This private method is called during the construction of the object to calculate
     * and set various internal parameters such as the start and end points of loops,
     * the center of the loop, and the positions of the axes based on the provided
     * cell dimensions and coordinate range.
     *
     * @return void
     */
    private function computeParams()
    {
        $this->axesOffset = 2;
        $this->loopOffset = 1;
        $this->loopStart = 0;
        $this->loopEnd = ($this->xto - $this->xfrom) + $this->axesOffset;
        $this->loopCenter = ($this->yto - $this->yfrom) / 2 + 1;
        $this->gridThickness = 1;
        $this->axesThickness = 3;
        $this->width = (($this->xto - $this->xfrom) + $this->axesOffset) * $this->cellWidth;
        $this->height = (($this->yto - $this->yfrom) + $this->axesOffset) * $this->cellHeight;
        $this->imageLeftEdgeX = 0;
        $this->imageTopY = 0;
        $this->xAxesAbsoluteYCoordinate = ($this->yto - $this->yfrom) / 2 + 1;
        $this->yAxesAbsoluteXCoordinate = ($this->xto - $this->xfrom) / 2 + 1;
        $this->xAxesYPosition = $this->xAxesAbsoluteYCoordinate * $this->cellHeight;
        $this->yAxesXPosition = $this->yAxesAbsoluteXCoordinate * $this->cellWidth;
    }

}