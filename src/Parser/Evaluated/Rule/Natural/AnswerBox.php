<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class AnswerBox implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') == 'g mnr-c g-blk'
            && $dom->cssQuery('._Z7', $node)->length == 1
        ) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    protected function parseNode(GoogleDom $dom, \DOMElement $node)
    {
        return [
            'title'   => function () use ($dom, $node) {
                $aTag = $dom->cssQuery('.rc .r a', $node)
                    ->item(0);
                if (!$aTag) {
                    // TODO ERROR
                    return;
                }
                return $aTag->nodeValue;
            },
            'url'     => function () use ($dom, $node) {
                $aTag = $dom->cssQuery('.rc .r a', $node)
                    ->item(0);
                if (!$aTag) {
                    // TODO ERROR
                    return;
                }
                return $dom->getUrl()->resolve($aTag->getAttribute('href'), 'string');
            },
            'destination' => function () use ($dom, $node) {
                $citeTag = $dom->cssQuery('.rc .s cite', $node)
                    ->item(0);
                if (!$citeTag) {
                    // TODO ERROR
                    return;
                }
                return $citeTag->nodeValue;
            },
            'description' => function () use ($dom, $node) {
                $citeTag = $dom->cssQuery('.mod ._Tgc', $node)
                    ->item(0);
                if (!$citeTag) {
                    // TODO ERROR
                    return;
                }
                return $citeTag->nodeValue;
            },
        ];
    }

    public function parse(GoogleDom $dom, \DOMElement $node, IndexedResultSet $resultSet)
    {
        $item = new BaseResult(
            [NaturalResultType::ANSWER_BOX],
            $this->parseNode($dom, $node)
        );
        $resultSet->addItem($item);
    }
}
