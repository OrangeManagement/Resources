<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 *
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Writer\Word2007\Element;

use PhpOffice\PhpWord\Element\AbstractElement as Element;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Text as SharedText;
use PhpOffice\PhpWord\Shared\XMLWriter;

/**
 * Abstract element writer.
 *
 * @since 0.11.0
 */
abstract class AbstractElement
{
    /**
     * XML writer.
     *
     * @var \PhpOffice\PhpWord\Shared\XMLWriter
     */
    private $xmlWriter;

    /**
     * Element.
     *
     * @var \PhpOffice\PhpWord\Element\AbstractElement
     */
    private $element;

    /**
     * Without paragraph.
     *
     * @var bool
     */
    protected $withoutP = false;

    /**
     * Write element.
     */
    abstract public function write();

    /**
     * Create new instance.
     *
     * @param bool $withoutP
     */
    public function __construct(XMLWriter $xmlWriter, Element $element, $withoutP = false)
    {
        $this->xmlWriter = $xmlWriter;
        $this->element = $element;
        $this->withoutP = $withoutP;
    }

    /**
     * Get XML Writer.
     *
     * @return \PhpOffice\PhpWord\Shared\XMLWriter
     */
    protected function getXmlWriter()
    {
        return $this->xmlWriter;
    }

    /**
     * Get element.
     *
     * @return \PhpOffice\PhpWord\Element\AbstractElement
     */
    protected function getElement()
    {
        return $this->element;
    }

    /**
     * Start w:p DOM element.
     *
     * @uses \PhpOffice\PhpWord\Writer\Word2007\Element\PageBreak::write()
     */
    protected function startElementP(): void
    {
        if (!$this->withoutP) {
            $this->xmlWriter->startElement('w:p');
            // Paragraph style
            if (\method_exists($this->element, 'getParagraphStyle')) {
                $this->writeParagraphStyle();
            }
        }
        $this->writeCommentRangeStart();
    }

    /**
     * End w:p DOM element.
     */
    protected function endElementP(): void
    {
        $this->writeCommentRangeEnd();
        if (!$this->withoutP) {
            $this->xmlWriter->endElement(); // w:p
        }
    }

    /**
     * Writes the w:commentRangeStart DOM element.
     */
    protected function writeCommentRangeStart(): void
    {
        if ($this->element->getCommentRangeStart() != null) {
            $comment = $this->element->getCommentRangeStart();
            //only set the ID if it is not yet set, otherwise it will overwrite it
            if ($comment->getElementId() == null) {
                $comment->setElementId();
            }

            $this->xmlWriter->writeElementBlock('w:commentRangeStart', ['w:id' => $comment->getElementId()]);
        }
    }

    /**
     * Writes the w:commentRangeEnd DOM element.
     */
    protected function writeCommentRangeEnd(): void
    {
        if ($this->element->getCommentRangeEnd() != null) {
            $comment = $this->element->getCommentRangeEnd();
            //only set the ID if it is not yet set, otherwise it will overwrite it, this should normally not happen
            if ($comment->getElementId() == null) {
                $comment->setElementId(); // @codeCoverageIgnore
            } // @codeCoverageIgnore

            $this->xmlWriter->writeElementBlock('w:commentRangeEnd', ['w:id' => $comment->getElementId()]);
            $this->xmlWriter->startElement('w:r');
            $this->xmlWriter->writeElementBlock('w:commentReference', ['w:id' => $comment->getElementId()]);
            $this->xmlWriter->endElement();
        } elseif ($this->element->getCommentRangeStart() != null && $this->element->getCommentRangeStart()->getEndElement() == null) {
            $comment = $this->element->getCommentRangeStart();
            //only set the ID if it is not yet set, otherwise it will overwrite it, this should normally not happen
            if ($comment->getElementId() == null) {
                $comment->setElementId(); // @codeCoverageIgnore
            } // @codeCoverageIgnore

            $this->xmlWriter->writeElementBlock('w:commentRangeEnd', ['w:id' => $comment->getElementId()]);
            $this->xmlWriter->startElement('w:r');
            $this->xmlWriter->writeElementBlock('w:commentReference', ['w:id' => $comment->getElementId()]);
            $this->xmlWriter->endElement();
        }
    }

    /**
     * Write ending.
     */
    protected function writeParagraphStyle(): void
    {
        $this->writeTextStyle('Paragraph');
    }

    /**
     * Write ending.
     */
    protected function writeFontStyle(): void
    {
        $this->writeTextStyle('Font');
    }

    /**
     * Write text style.
     *
     * @param string $styleType Font|Paragraph
     */
    private function writeTextStyle($styleType): void
    {
        $method = "get{$styleType}Style";
        $class = "PhpOffice\\PhpWord\\Writer\\Word2007\\Style\\{$styleType}";
        $styleObject = $this->element->$method();

        /** @var \PhpOffice\PhpWord\Writer\Word2007\Style\AbstractStyle $styleWriter Type Hint */
        $styleWriter = new $class($this->xmlWriter, $styleObject);
        if (\method_exists($styleWriter, 'setIsInline')) {
            $styleWriter->setIsInline(true);
        }

        $styleWriter->write();
    }

    /**
     * Convert text to valid format.
     *
     * @param string $text
     *
     * @return string
     */
    protected function getText($text)
    {
        return SharedText::controlCharacterPHP2OOXML($text);
    }

    /**
     * Write an XML text, this will call text() or writeRaw() depending on the value of Settings::isOutputEscapingEnabled().
     *
     * @param string $content The text string to write
     *
     * @return bool Returns true on success or false on failure
     */
    protected function writeText($content)
    {
        if (Settings::isOutputEscapingEnabled()) {
            return $this->getXmlWriter()->text($content);
        }

        return $this->getXmlWriter()->writeRaw($content);
    }
}
