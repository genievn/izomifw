<?php
namespace Izomi\Doctrine\Extension\DbFunction;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
class YearFunction extends FunctionNode
{
    public $stringPrimary;

    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        //TODO: Use platform to get SQL
        return 'YEAR(' . $sqlWalker->walkStringPrimary($this->stringPrimary) . ')';
    }

    /**
     * @override
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $lexer = $parser->getLexer();
        
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        
        $this->stringPrimary = $parser->StringPrimary();
        
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
?>