<?php
/*
 * Codeblocks, indented by four spaces
 */
 
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

use dokuwiki\Parsing\Handler\Preformatted;

class syntax_plugin_markdowku_codeblocks extends DokuWiki_Syntax_Plugin {

    function getType()  { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort()  { return 199; }
    
    function connectTo($mode) {
        $this->Lexer->addEntryPattern(
            '(?:\n\n|\A\n?)    ',
            $mode,
            'plugin_markdowku_codeblocks');

        $this->Lexer->addPattern(
            '\n    ',
            'plugin_markdowku_codeblocks');
    }
    
    function postConnect() {
        $this->Lexer->addExitPattern(
            '\n(?:(?=\n*[ ]{0,3}\S)|\Z)',
            'plugin_markdowku_codeblocks');
    }
    
    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $ReWriter = new Preformatted($handler->getCallWriter());
                $handler->setCallWriter($ReWriter);
                $handler->_addCall('preformatted_start', array($match), $pos);
                break;
            case DOKU_LEXER_MATCHED:
                $handler->_addCall('preformatted_newline', array($match), $pos);
                break;
            case DOKU_LEXER_UNMATCHED:
                $handler->_addCall('preformatted_content', array($match), $pos);
                break;
            case DOKU_LEXER_EXIT:
                $handler->_addCall('preformatted_end', array(), $pos);
                $handler->_addCall('preformatted_content', array($match), $pos);
                $handler->getCallWriter()->process();
                $ReWriter = & $handler->getCallWriter();
                $handler->setCallWriter($ReWriter->getCallWriter());
                break;
        }
        return true;
    }
    
    function render($mode, Doku_Renderer $renderer, $data) {
        return false;
    }
}
//Setup VIM: ex: et ts=4 enc=utf-8 :
