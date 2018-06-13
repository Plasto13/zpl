<?php

namespace Zpl;

class PdfBuilder extends AbstractBuilder
{
    /**
     * PDF driver - for example FPDF
     *
     * @var \Sigep\Pdf\ImprovedFPDF
     */
    protected $pdfDriver;
    
    /**
     *
     * @param string  $unit
     * @param mixed $pdfDriver PDF driver - for example FPDF
     *
     * @throws BuilderException
     */
    public function __construct($unit = 'mm', $pdfDriver)
    {
        parent::__construct($unit);
        $this->pdfDriver = $pdfDriver;
        $this->pdfDriver->AddPage();
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::setFont()
     */
    public function setFont(string $font, float $size) : void
    {
        $this->pdfDriver->SetFont($font, '', $size);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::drawText()
     */
    public function drawText(float $x, float $y, string $text, string $orientation = 'N') : void
    {
        $this->pdfDriver->Text($x, $y, $this->_($text));
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::drawLine()
     */
    public function drawLine(float $x1, float $y1, float $x2, float $y2, float $thickness = 0) : void
    {
        if ($thickness !== 0) {
            $this->pdfDriver->SetLineWidth($thickness);
        }
        $this->pdfDriver->Line($x1, $y1, $x2, $y2);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::drawRect()
     */
    public function drawRect(
        float $x,
        float $y,
        float $width,
        float $height,
        float $thickness = 0,
        string $color = 'B',
        float $round = 0
    ) : void {
        if ($thickness !== 0) {
            $this->pdfDriver->SetLineWidth($thickness);
        }
        $this->pdfDriver->Rect($x, $y, $width, $height);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::drawCell()
     */
    public function drawCell(
        float $width,
        float $height,
        string $text,
        bool $border = false,
        bool $ln = false,
        string $align = ''
    ) : void {
        $this->pdfDriver->Cell($width, $height, $this->_($text), $border, $ln, $align);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::drawCode128()
     */
    public function drawCode128(float $x, float $y, float $height, string $data, bool $printData = false) : void
    {
        $this->pdfDriver->Code128($x, $y, $height, $data);
        if ($printData === true) {
            $oldX = $this->pdfDriver->x;
            $oldY = $this->pdfDriver->y;
            $this->drawText($x, $y+$height*1.3, $data);
            $this->setXY($oldX, $oldY);
        }
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::drawQrCode()
     * @throws BuilderException
     */
    public function drawQrCode(float $x, float $y, string $data, int $size = 10) : void
    {
        throw new BuilderException('Method not yet implemented');
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::setXY()
     */
    public function setXY(float $x, float $y) : void
    {
        $this->pdfDriver->x = $x;
        $this->pdfDriver->y = $y;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::setX()
     */
    public function setX(float $x) : void
    {
        $this->pdfDriver->x = $x;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::getX()
     */
    public function getX() : float
    {
        return $this->pdfDriver->x;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::setY()
     */
    public function setY($y) : void
    {
        $this->pdfDriver->y = $y;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::getY()
     */
    public function getY() : float
    {
        return $this->pdfDriver->y;
    }
    
    /**
     * Adds a new page
     *
     * {@inheritDoc}
     * @see \Zpl\AbstractBuilder::newPage()
     */
    public function newPage() : void
    {
        $this->pdfDriver->AddPage();
    }
    
    public function getDriver()
    {
        return $this->pdfDriver;
    }
    
    /**
     * Converts the string to UTF-8
     * @param string $str
     *
     * @return string
     */
    protected function _($str) : string
    {
        if (extension_loaded('iconv')) {
            return iconv('UTF-8', 'ISO-8859-1', $str);
        } else {
            return utf8_decode($str);
        }
    }
    
    /**
     * Convert instance to pdf.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->pdfDriver->Output('', 'S');
    }
}
