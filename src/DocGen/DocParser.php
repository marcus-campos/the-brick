<?php

namespace Sympla\Search\DocGen;

class DocParser
{
    private $docComment;

    private $bruteComment;

    private $parsedDoc;

    /**
     * [__construct description]
     * @param [type] $docComment [description]
     */
    public function __construct($docComment)
    {
        $this->boot($docComment);
    }

    /**
     * Parse the docblock and populate the $parsedDoc
     * @param  $docComment
     */
    private function boot($docComment)
    {
        $this->bruteComment = $docComment;
        $docComment = str_replace("\r\n", "\n", $docComment);
        $this->docComment = $this->removeDecorations($docComment);
        $this->parsedDoc = [
            'shortDesc' => $this->parseShortDesc(),
            'longDesc' => $this->parseLongDesc(),
            'tags' => $this->parseDoc()
        ];
    }

    /**
     * Return a collection
     * @return collection
     */
    public function get()
    {
        return $this->parsedDoc;
    }


    /**
     * Get tag value
     * @return string | null
     */
    public function getTagValue($tagName)
    {
        if (!empty($this->parsedDoc['tags'])) {
            foreach ($this->parsedDoc['tags'] as $key => $value) {
                if ($key == $tagName) {
                    return $value;
                }
            }
        }
        return null;
    }

    /**
     * Get tag value
     * @return array | null
     */
    public function getTagsByName($tagName)
    {
        $tags = null;
        $keyCount = 0;
        if (!empty($this->parsedDoc['tags'])) {
            foreach ($this->parsedDoc['tags'] as $key => $value) {
                if ($key == $tagName) {
                    $tags[$key.($keyCount++)] = $value;
                }
            }
        }
        return $tags;
    }

    /**
     * Get tag value
     * @return array | null
     */
    public function getTagsIfCointansString($tagName)
    {
        $tags = null;
        $keyCount = 0;
        if (!empty($this->parsedDoc['tags'])) {
            foreach ($this->parsedDoc['tags'] as $key => $value) {
                if (strpos(strtoupper($key), strtoupper($tagName)) !== false) {
                    $tags[$key] = $value;
                }
            }
        }
        return $tags;
    }

    /**
     * Get the short description (e.g. summary).
     *
     * @return string
     */

    public function getShortDesc()
    {
        return $this->parsedDoc['shortDesc'];
    }

    /**
     * Get the full description
     *
     * @return string
     */
    public function getLongDesc()
    {
        return $this->parsedDoc['longDesc'];
    }

    /**
     * Remove unused cruft from PHPdoc comment.
     *
     * @param string $comment PHPdoc comment.
     * @return string
     */
    private function removeDecorations( $comment )
    {
        $comment = preg_replace('|^/\*\*[\r\n]+|', '', $comment);
        $comment = preg_replace('|\n[\t ]*\*/$|', '', $comment);
        $comment = preg_replace('|^[\t ]*\* ?|m', '', $comment);
        return $comment;
    }

    /**
     * Parse the doc and return the parans in a array
     *
     * @return array
     */
    private function parseDoc()
    {
        $result = null;
        if (preg_match_all('/@(\w+)\s+(.*)\r?\n/m', $this->bruteComment, $matches)) {
            $result = array_combine($matches[1], $matches[2]);
        }
        return $result;
    }

    /**
     * Get the short description (e.g. summary).
     *
     * @return string
     */
    private function parseShortDesc()
    {
        if (!preg_match('|^([^@][^\n]+)\n*|', $this->docComment, $matches)) {
            return '';
        }
        return $matches[1];
    }

    /**
     * Get the full description
     *
     * @return string
     */
    private function parseLongDesc()
    {
        $shortdesc = $this->parseShortDesc();
        if (!$shortdesc) {
            return '';
        }
        $longdesc = substr($this->docComment, strlen($shortdesc));
        $lines = array();
        foreach (explode("\n", $longdesc) as $line) {
            if (0 === strpos($line, '@' )) {
                break;
            }
            $lines[] = $line;
        }
        $longdesc = trim(implode($lines, "\n"));
        return $longdesc;
    }
}
