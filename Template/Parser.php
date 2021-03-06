<?php

namespace Floxim\Floxim\Template;

use Floxim\Floxim\System\Fx as fx;

/*
 * Class breaks the template into tokens and builds a tree
 */
class Parser
{

    /**
     * Convert template to token tree
     * @param string $source source code of the template
     * @return array token tree
     */
    public function parse($source)
    {
        //fx::log($source);
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->parse($source);
        unset($tokenizer);
        $tree = $this->makeTree($tokens);
        return $tree;
    }

    /**
     * To determine the type of the token (opening/unit) on the basis of the following tokens
     * @param fx_template_token $token the token with an unknown type
     * @param array $tokens following tokens
     * @return null
     */
    protected function solveUnclosed($token, $tokens)
    {
        if (!$token || $token->type != 'unknown') {
            return;
        }
        $token_info = Token::getTokenInfo($token->name);
        $stack = array();
        while ($next_token = array_shift($tokens)) {
            if ($next_token->type == 'unknown') {
                $this->solveUnclosed($next_token, $tokens);
            }
            switch ($next_token->type) {
                case 'open':
                    if (count($stack) == 0) {
                        if (!in_array($next_token->name, $token_info['contains'])) {
                            $token->type = 'single';
                            return;
                        }
                    }
                    $stack[] = $token;
                    break;
                case 'close':
                    if (count($stack) == 0) {
                        if ($next_token->name == $token->name) {
                            $token->type = 'open';
                            return;
                        } else {
                            $token->type = 'single';
                            return;
                        }
                    }
                    array_pop($stack);
                    break;
            }
        }
    }


    protected function makeTree($tokens)
    {
        $stack = array();
        $root = $tokens[0];
        while ($token = array_shift($tokens)) {

            if ($token->type == 'unknown') {
                $this->solveUnclosed($token, $tokens);
            }
            if (preg_match("~^else~", $token->name) && $token->type == 'single') {
                $token->type = 'open';
            }
            switch ($token->type) {
                case 'open':
                    if (count($stack) > 0) {
                        end($stack)->addChild($token);
                    }
                    $stack [] = $token;
                    break;
                case 'close':
                    if ($token->name == 'if') {
                        do {
                            $closed_token = array_pop($stack);
                        } while ($closed_token->name != 'if');
                    } else {
                        $closed_token = array_pop($stack);
                        if ($closed_token && $token->name != $closed_token->name) {
                            fx::log('Wrong template node nesting', $token->dump(), $closed_token->dump());
                        }
                    }

                    if ($token->name == 'if' || $token->name == 'elseif') {
                        // reading forward to check if there is nearby {elseif} / {else} tag
                        $count_skipped = 0;
                        foreach ($tokens as $next_token) {
                            // skip empty tokens
                            if ($next_token->isEmpty()) {
                                $count_skipped++;
                                continue;
                            }
                            if (
                                $next_token->type != 'close' &&
                                ($next_token->name == 'elseif' || $next_token->name == 'else')
                            ) {
                                $next_token->stack_extra = true;
                                $stack [] = $closed_token;
                                foreach (range(1, $count_skipped) as $skip) {
                                    array_shift($tokens);
                                }
                            }
                            break;
                        }
                    }
                    if ($token->name == 'template' && $closed_token->name == 'template') {
                        $this->templateToEach($closed_token);
                    }
                    if ($closed_token->stack_extra) {
                        array_pop($stack);
                    }
                    break;
                case 'single':
                default:
                    $stack_last = end($stack);
                    if (!$stack_last) {
                        echo "Template error: stack empty, trying to add: ";
                        echo "<pre>" . htmlspecialchars(print_r($token, 1)) . "</pre>";
                        die();
                    }
                    $stack_last->addChild($token);
                    break;
            }
        }
        return $root;
    }

    protected function templateToEach(Token $token)
    {
        $children = $token->getChildren();
        $has_items = false;
        foreach ($children as $child) {
            if ($child->name == 'item') {
                $has_items = true;
                break;
            }
        }
        if (!$has_items) {
            return;
        }
        $with_each_token = new Token('with_each', 'double', array('select' => '$.items'));
        $with_each_token->setChildren($children);
        $token->clearChildren();
        $token->addChild($with_each_token);
    }
}