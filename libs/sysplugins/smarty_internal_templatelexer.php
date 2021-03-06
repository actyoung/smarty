<?php
/**
 * Smarty Internal Plugin Templatelexer
 * This is the lexer to break the template source into tokens
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty_Internal_Templatelexer
 * This is the template file lexer.
 * It is generated from the smarty_internal_templatelexer.plex file
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */
class Smarty_Internal_Templatelexer
{

    /**
     * Source
     *
     * @var string
     */
    public $data;

    /**
     * byte counter
     *
     * @var int
     */
    public $counter;

    /**
     * token number
     *
     * @var int
     */
    public $token;

    /**
     * token value
     *
     * @var string
     */
    public $value;

    /**
     * current line
     *
     * @var int
     */
    public $line;

    /**
     * tag start line
     *
     * @var
     */
    public $taglineno;

    /**
     * flag if parsing php script
     *
     * @var bool
     */
    public $is_phpScript = false;

    /**
     * php code type
     *
     * @var string
     */
    public $phpType = '';

    /**
     * escaped left delimiter
     *
     * @var string
     */
    public $ldel = '';

    /**
     * escaped left delimiter length
     *
     * @var int
     */
    public $ldel_length = 0;

    /**
     * escaped right delimiter
     *
     * @var string
     */
    public $rdel = '';

    /**
     * escaped right delimiter length
     *
     * @var int
     */
    public $rdel_length = 0;

    /**
     * state number
     *
     * @var int
     */
    public $state = 1;

    /**
     * Smarty object
     *
     * @var Smarty
     */
    public $smarty = null;

    /**
     * compiler object
     *
     * @var Smarty_Internal_TemplateCompilerBase
     */
    private $compiler = null;

    /**
     * literal tag nesting level
     *
     * @var int
     */
    private $literal_cnt = 0;

    /**
     * trace file
     *
     * @var resource
     */
    public $yyTraceFILE;

    /**
     * trace prompt
     *
     * @var string
     */
    public $yyTracePrompt;

    /**
     * state names
     *
     * @var array
     */
    public $state_name = array(1 => 'TEXT', 2 => 'SMARTY', 3 => 'LITERAL', 4 => 'DOUBLEQUOTEDSTRING', 5 => 'CHILDBODY', 6 => 'CHILDBLOCK', 7 => 'CHILDLITERAL');

    /**
     * storage for assembled token patterns
     *
     * @var sring
     */
    private $yy_global_pattern1 = null;

    private $yy_global_pattern2 = null;

    private $yy_global_pattern3 = null;

    private $yy_global_pattern4 = null;

    private $yy_global_pattern5 = null;

    private $yy_global_pattern6 = null;

    private $yy_global_pattern7 = null;

    /**
     * token names
     *
     * @var array
     */
    public $smarty_token_names = array(        // Text for parser error messages
                                               'NOT' => '(!,not)', 'OPENP' => '(', 'CLOSEP' => ')', 'OPENB' => '[', 'CLOSEB' => ']', 'PTR' => '->', 'APTR' => '=>', 'EQUAL' => '=', 'NUMBER' => 'number', 'UNIMATH' => '+" , "-', 'MATH' => '*" , "/" , "%', 'INCDEC' => '++" , "--', 'SPACE' => ' ', 'DOLLAR' => '$', 'SEMICOLON' => ';', 'COLON' => ':', 'DOUBLECOLON' => '::', 'AT' => '@', 'HATCH' => '#', 'QUOTE' => '"', 'BACKTICK' => '`', 'VERT' => '"|" modifier', 'DOT' => '.', 'COMMA' => '","', 'QMARK' => '"?"', 'ID' => 'id, name', 'TEXT' => 'text', 'LDELSLASH' => '{/..} closing tag', 'LDEL' => '{...} Smarty tag', 'COMMENT' => 'comment', 'AS' => 'as', 'TO' => 'to', 'PHP' => '"<?php", "<%", "{php}" tag', 'LOGOP' => '"<", "==" ... logical operator', 'TLOGOP' => '"lt", "eq" ... logical operator; "is div by" ... if condition', 'SCOND' => '"is even" ... if condition',);

    /**
     * constructor
     *
     * @param   string                             $data template source
     * @param Smarty_Internal_TemplateCompilerBase $compiler
     */
    function __construct($data, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $this->data = $data;
        $this->counter = 0;
        if (preg_match('/^\xEF\xBB\xBF/', $this->data, $match)) {
            $this->counter += strlen($match[0]);
        }
        $this->line = 1;
        $this->smarty = $compiler->smarty;
        $this->compiler = $compiler;
        $this->ldel = preg_quote($this->smarty->left_delimiter, '/');
        $this->ldel_length = strlen($this->smarty->left_delimiter);
        $this->rdel = preg_quote($this->smarty->right_delimiter, '/');
        $this->rdel_length = strlen($this->smarty->right_delimiter);
        $this->smarty_token_names['LDEL'] = $this->smarty->left_delimiter;
        $this->smarty_token_names['RDEL'] = $this->smarty->right_delimiter;
    }

    public function PrintTrace()
    {
        $this->yyTraceFILE = fopen('php://output', 'w');
        $this->yyTracePrompt = '<br>';
    }

    private $_yy_state = 1;

    private $_yy_stack = array();

    public function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    public function yypushstate($state)
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState push %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }

    public function yypopstate()
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState pop %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
        $this->_yy_state = array_pop($this->_yy_stack);
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }

    public function yybegin($state)
    {
        $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState set %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }

    public function yylex1()
    {
        if (!isset($this->yy_global_pattern1)) {
            $this->yy_global_pattern1 = "/\G(\\{\\})|\G(" . $this->ldel . "\\*([\S\s]*?)\\*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*strip\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*\/strip\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*(if|elseif|else if|while)\\s+)|\G(" . $this->ldel . "\\s*for\\s+)|\G(" . $this->ldel . "\\s*foreach(?![^\s]))|\G(" . $this->ldel . "\\s*setfilter\\s+)|\G((" . $this->ldel . "\\s*php\\s*(.)*?" . $this->rdel . "([\S\s]*?)" . $this->ldel . "\\s*\/php\\s*" . $this->rdel . ")|(" . $this->ldel . "\\s*[\/]?php\\s*(.)*?" . $this->rdel . "))|\G(" . $this->ldel . "\\s*\/)|\G(" . $this->ldel . "\\s*)|\G(\\s*" . $this->rdel . ")|\G(<\\?xml\\s+([\S\s]*?)\\?>)|\G(<%((('[^'\\\\]*(?:\\\\.[^'\\\\]*)*')|(\"[^\"\\\\]*(?:\\\\.[^\"\\\\]*)*\")|(\/\\*[\S\s]*?\\*\/)|[\S\s])*?)%>)|\G((<\\?(?:php\\s+|=)?)((('[^'\\\\]*(?:\\\\.[^'\\\\]*)*')|(\"[^\"\\\\]*(?:\\\\.[^\"\\\\]*)*\")|(\/\\*[\S\s]*?\\*\/)|[\S\s])*?)\\?>)|\G(<script\\s+language\\s*=\\s*[\"']?\\s*php\\s*[\"']?\\s*>((('[^'\\\\]*(?:\\\\.[^'\\\\]*)*')|(\"[^\"\\\\]*(?:\\\\.[^\"\\\\]*)*\")|(\/\\*[\S\s]*?\\*\/)|[\S\s])*?)<\/script>)|\G((<(\\?(?:php\\s+|=)?|(script\\s+language\\s*=\\s*[\"']?\\s*php\\s*[\"']?\\s*>)|%))|\\?>|%>)|\G([\S\s])/iS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern1, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' . substr($this->data, $this->counter, 5) . '... state TEXT');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);
    } // end function

    const TEXT = 1;

    function yy_r1_1()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r1_2()
    {

        $this->token = Smarty_Internal_Templateparser::TP_COMMENT;
    }

    function yy_r1_4()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_STRIPON;
        }
    }

    function yy_r1_5()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_STRIPOFF;
        }
    }

    function yy_r1_6()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LITERALSTART;
            $this->yypushstate(self::LITERAL);
        }
    }

    function yy_r1_7()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELIF;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r1_9()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELFOR;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r1_10()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELFOREACH;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r1_11()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELSETFILTER;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r1_12()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_PHP;
            $this->phpType = 'tag';
            $this->taglineno = $this->line;
        }
    }

    function yy_r1_18()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELSLASH;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r1_19()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDEL;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r1_20()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r1_21()
    {

        $this->token = Smarty_Internal_Templateparser::TP_XMLTAG;
        $this->taglineno = $this->line;
    }

    function yy_r1_23()
    {

        $this->phpType = 'asp';
        $this->taglineno = $this->line;
        $this->token = Smarty_Internal_Templateparser::TP_PHP;
    }

    function yy_r1_29()
    {

        $this->phpType = 'php';
        $this->taglineno = $this->line;
        $this->token = Smarty_Internal_Templateparser::TP_PHP;
    }

    function yy_r1_36()
    {

        $this->phpType = 'script';
        $this->taglineno = $this->line;
        $this->token = Smarty_Internal_Templateparser::TP_PHP;
    }

    function yy_r1_42()
    {

        $this->phpType = 'unmatched';
        $this->taglineno = $this->line;
        $this->token = Smarty_Internal_Templateparser::TP_PHP;
    }

    function yy_r1_46()
    {

        $to = strlen($this->data);
        preg_match("/{$this->ldel}|<\?|<%|\?>|%>|<script\s+language\s*=\s*[\"\']?\s*php\s*[\"\']?\s*>/", $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[0][1])) {
            $to = $match[0][1];
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    public function yylex2()
    {
        if (!isset($this->yy_global_pattern2)) {
            $this->yy_global_pattern2 = "/\G(\")|\G('[^'\\\\]*(?:\\\\.[^'\\\\]*)*')|\G([$]smarty\\.block\\.(child|parent))|\G(\\$)|\G(\\s*" . $this->rdel . ")|\G(\\s+is\\s+in\\s+)|\G(\\s+as\\s+)|\G(\\s+to\\s+)|\G(\\s+step\\s+)|\G(\\s+instanceof\\s+)|\G(\\s*(([!=][=]{1,2})|([<][=>]?)|([>][=]?)|[&|]{2})\\s*)|\G(\\s+(eq|ne|neg|gt|ge|gte|lt|le|lte|mod|and|or|xor|(is\\s+(not\\s+)?(odd|even|div)\\s+by))\\s+)|\G(\\s+is\\s+(not\\s+)?(odd|even))|\G(!\\s*|not\\s+)|\G(\\((int(eger)?|bool(ean)?|float|double|real|string|binary|array|object)\\)\\s*)|\G(\\s*\\(\\s*)|\G(\\s*\\))|\G(\\[\\s*)|\G(\\s*\\])|\G(\\s*->\\s*)|\G(\\s*=>\\s*)|\G(\\s*=\\s*)|\G(\\+\\+|--)|\G(\\s*(\\+|-)\\s*)|\G(\\s*([*]{1,2}|[%\/^&]|[<>]{2})\\s*)|\G(@)|\G(#)|\G(\\s+[0-9]*[a-zA-Z_][a-zA-Z0-9_\-:]*\\s*=\\s*)|\G(([0-9]*[a-zA-Z_]\\w*)?(\\\\[0-9]*[a-zA-Z_]\\w*)+)|\G([0-9]*[a-zA-Z_]\\w*)|\G(\\d+)|\G(`)|\G(\\|)|\G(\\.)|\G(\\s*,\\s*)|\G(\\s*;)|\G(::)|\G(\\s*:\\s*)|\G(\\s*&\\s*)|\G(\\s*\\?\\s*)|\G(0[xX][0-9a-fA-F]+)|\G(\\s+)|\G(" . $this->ldel . "\\s*(if|elseif|else if|while)\\s+)|\G(" . $this->ldel . "\\s*for\\s+)|\G(" . $this->ldel . "\\s*foreach(?![^\s]))|\G(" . $this->ldel . "\\s*\/)|\G(" . $this->ldel . "\\s*)|\G([\S\s])/iS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern2, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' . substr($this->data, $this->counter, 5) . '... state SMARTY');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r2_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);
    } // end function

    const SMARTY = 2;

    function yy_r2_1()
    {

        $this->token = Smarty_Internal_Templateparser::TP_QUOTE;
        $this->yypushstate(self::DOUBLEQUOTEDSTRING);
    }

    function yy_r2_2()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SINGLEQUOTESTRING;
    }

    function yy_r2_3()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SMARTYBLOCKCHILDPARENT;
        $this->taglineno = $this->line;
    }

    function yy_r2_5()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOLLAR;
    }

    function yy_r2_6()
    {

        $this->token = Smarty_Internal_Templateparser::TP_RDEL;
        $this->yypopstate();
    }

    function yy_r2_7()
    {

        $this->token = Smarty_Internal_Templateparser::TP_ISIN;
    }

    function yy_r2_8()
    {

        $this->token = Smarty_Internal_Templateparser::TP_AS;
    }

    function yy_r2_9()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TO;
    }

    function yy_r2_10()
    {

        $this->token = Smarty_Internal_Templateparser::TP_STEP;
    }

    function yy_r2_11()
    {

        $this->token = Smarty_Internal_Templateparser::TP_INSTANCEOF;
    }

    function yy_r2_12()
    {

        $this->token = Smarty_Internal_Templateparser::TP_LOGOP;
    }

    function yy_r2_17()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TLOGOP;
    }

    function yy_r2_22()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SINGLECOND;
    }

    function yy_r2_25()
    {

        $this->token = Smarty_Internal_Templateparser::TP_NOT;
    }

    function yy_r2_26()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TYPECAST;
    }

    function yy_r2_30()
    {

        $this->token = Smarty_Internal_Templateparser::TP_OPENP;
    }

    function yy_r2_31()
    {

        $this->token = Smarty_Internal_Templateparser::TP_CLOSEP;
    }

    function yy_r2_32()
    {

        $this->token = Smarty_Internal_Templateparser::TP_OPENB;
    }

    function yy_r2_33()
    {

        $this->token = Smarty_Internal_Templateparser::TP_CLOSEB;
    }

    function yy_r2_34()
    {

        $this->token = Smarty_Internal_Templateparser::TP_PTR;
    }

    function yy_r2_35()
    {

        $this->token = Smarty_Internal_Templateparser::TP_APTR;
    }

    function yy_r2_36()
    {

        $this->token = Smarty_Internal_Templateparser::TP_EQUAL;
    }

    function yy_r2_37()
    {

        $this->token = Smarty_Internal_Templateparser::TP_INCDEC;
    }

    function yy_r2_38()
    {

        $this->token = Smarty_Internal_Templateparser::TP_UNIMATH;
    }

    function yy_r2_40()
    {

        $this->token = Smarty_Internal_Templateparser::TP_MATH;
    }

    function yy_r2_42()
    {

        $this->token = Smarty_Internal_Templateparser::TP_AT;
    }

    function yy_r2_43()
    {

        $this->token = Smarty_Internal_Templateparser::TP_HATCH;
    }

    function yy_r2_44()
    {

        // resolve conflicts with shorttag and right_delimiter starting with '='
        if (substr($this->data, $this->counter + strlen($this->value) - 1, $this->rdel_length) == $this->smarty->right_delimiter) {
            preg_match("/\s+/", $this->value, $match);
            $this->value = $match[0];
            $this->token = Smarty_Internal_Templateparser::TP_SPACE;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_ATTR;
        }
    }

    function yy_r2_45()
    {

        $this->token = Smarty_Internal_Templateparser::TP_NAMESPACE;
    }

    function yy_r2_48()
    {

        $this->token = Smarty_Internal_Templateparser::TP_ID;
    }

    function yy_r2_49()
    {

        $this->token = Smarty_Internal_Templateparser::TP_INTEGER;
    }

    function yy_r2_50()
    {

        $this->token = Smarty_Internal_Templateparser::TP_BACKTICK;
        $this->yypopstate();
    }

    function yy_r2_51()
    {

        $this->token = Smarty_Internal_Templateparser::TP_VERT;
    }

    function yy_r2_52()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOT;
    }

    function yy_r2_53()
    {

        $this->token = Smarty_Internal_Templateparser::TP_COMMA;
    }

    function yy_r2_54()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SEMICOLON;
    }

    function yy_r2_55()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOUBLECOLON;
    }

    function yy_r2_56()
    {

        $this->token = Smarty_Internal_Templateparser::TP_COLON;
    }

    function yy_r2_57()
    {

        $this->token = Smarty_Internal_Templateparser::TP_ANDSYM;
    }

    function yy_r2_58()
    {

        $this->token = Smarty_Internal_Templateparser::TP_QMARK;
    }

    function yy_r2_59()
    {

        $this->token = Smarty_Internal_Templateparser::TP_HEX;
    }

    function yy_r2_60()
    {

        $this->token = Smarty_Internal_Templateparser::TP_SPACE;
    }

    function yy_r2_61()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELIF;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r2_63()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELFOR;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r2_64()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELFOREACH;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r2_65()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELSLASH;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r2_66()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDEL;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r2_67()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    public function yylex3()
    {
        if (!isset($this->yy_global_pattern3)) {
            $this->yy_global_pattern3 = "/\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*\/literal\\s*" . $this->rdel . ")|\G([\S\s])/iS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern3, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' . substr($this->data, $this->counter, 5) . '... state LITERAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r3_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);
    } // end function

    const LITERAL = 3;

    function yy_r3_1()
    {

        $this->literal_cnt ++;
        $this->token = Smarty_Internal_Templateparser::TP_LITERAL;
    }

    function yy_r3_2()
    {

        if ($this->literal_cnt) {
            $this->literal_cnt --;
            $this->token = Smarty_Internal_Templateparser::TP_LITERAL;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LITERALEND;
            $this->yypopstate();
        }
    }

    function yy_r3_3()
    {

        $to = strlen($this->data);
        preg_match("/{$this->ldel}\/?literal{$this->rdel}/", $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[0][1])) {
            $to = $match[0][1];
        } else {
            $this->compiler->trigger_template_error("missing or misspelled literal closing tag");
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_LITERAL;
    }

    public function yylex4()
    {
        if (!isset($this->yy_global_pattern4)) {
            $this->yy_global_pattern4 = "/\G(" . $this->ldel . "\\s*(if|elseif|else if|while)\\s+)|\G(" . $this->ldel . "\\s*for\\s+)|\G(" . $this->ldel . "\\s*foreach(?![^\s]))|\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*\/literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*\/)|\G(" . $this->ldel . "\\s*)|\G(\")|\G(`\\$)|\G(\\$[0-9]*[a-zA-Z_]\\w*)|\G(\\$)|\G(([^\"\\\\]*?)((?:\\\\.[^\"\\\\]*?)*?)(?=(" . $this->ldel . "|\\$|`\\$|\")))|\G([\S\s])/iS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern4, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' . substr($this->data, $this->counter, 5) . '... state DOUBLEQUOTEDSTRING');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r4_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);
    } // end function

    const DOUBLEQUOTEDSTRING = 4;

    function yy_r4_1()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELIF;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r4_3()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELFOR;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r4_4()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELFOREACH;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r4_5()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r4_6()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r4_7()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDELSLASH;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r4_8()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_TEXT;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_LDEL;
            $this->yypushstate(self::SMARTY);
            $this->taglineno = $this->line;
        }
    }

    function yy_r4_9()
    {

        $this->token = Smarty_Internal_Templateparser::TP_QUOTE;
        $this->yypopstate();
    }

    function yy_r4_10()
    {

        $this->token = Smarty_Internal_Templateparser::TP_BACKTICK;
        $this->value = substr($this->value, 0, - 1);
        $this->yypushstate(self::SMARTY);
        $this->taglineno = $this->line;
    }

    function yy_r4_11()
    {

        $this->token = Smarty_Internal_Templateparser::TP_DOLLARID;
    }

    function yy_r4_12()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r4_13()
    {

        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    function yy_r4_17()
    {

        $to = strlen($this->data);
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_TEXT;
    }

    public function yylex5()
    {
        if (!isset($this->yy_global_pattern5)) {
            $this->yy_global_pattern5 = "/\G(" . $this->ldel . "\\s*strip\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*\/strip\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*block)|\G([\S\s])/iS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern5, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' . substr($this->data, $this->counter, 5) . '... state CHILDBODY');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r5_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);
    } // end function

    const CHILDBODY = 5;

    function yy_r5_1()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            return false;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_STRIPON;
        }
    }

    function yy_r5_2()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            return false;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_STRIPOFF;
        }
    }

    function yy_r5_3()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            return false;
        } else {
            $this->yypopstate();
            return true;
        }
    }

    function yy_r5_4()
    {

        $to = strlen($this->data);
        preg_match("/" . $this->ldel . "\s*((\/)?strip\s*" . $this->rdel . "|block\s+)/", $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[0][1])) {
            $to = $match[0][1];
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        return false;
    }

    public function yylex6()
    {
        if (!isset($this->yy_global_pattern6)) {
            $this->yy_global_pattern6 = "/\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*block)|\G(" . $this->ldel . "\\s*\/block)|\G(" . $this->ldel . "\\s*[$]smarty\\.block\\.(child|parent))|\G([\S\s])/iS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern6, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' . substr($this->data, $this->counter, 5) . '... state CHILDBLOCK');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r6_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);
    } // end function

    const CHILDBLOCK = 6;

    function yy_r6_1()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
            $this->yypushstate(self::CHILDLITERAL);
        }
    }

    function yy_r6_2()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
        } else {
            $this->yypopstate();
            return true;
        }
    }

    function yy_r6_3()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
        } else {
            $this->yypopstate();
            return true;
        }
    }

    function yy_r6_4()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
        } else {
            $this->yypopstate();
            return true;
        }
    }

    function yy_r6_6()
    {

        $to = strlen($this->data);
        preg_match("/" . $this->ldel . "\s*(literal\s*" . $this->rdel . "|(\/)?block(\s|" . $this->rdel . ")|[\$]smarty\.block\.(child|parent))/", $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[0][1])) {
            $to = $match[0][1];
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
    }

    public function yylex7()
    {
        if (!isset($this->yy_global_pattern7)) {
            $this->yy_global_pattern7 = "/\G(" . $this->ldel . "\\s*literal\\s*" . $this->rdel . ")|\G(" . $this->ldel . "\\s*\/literal\\s*" . $this->rdel . ")|\G([\S\s])/iS";
        }
        if ($this->counter >= strlen($this->data)) {
            return false; // end of input
        }

        do {
            if (preg_match($this->yy_global_pattern7, $this->data, $yymatches, null, $this->counter)) {
                $yysubmatches = $yymatches;
                $yymatches = preg_grep("/(.|\s)+/", $yysubmatches);
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' . ' an empty string.  Input "' . substr($this->data, $this->counter, 5) . '... state CHILDLITERAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r7_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line . ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);
    } // end function

    const CHILDLITERAL = 7;

    function yy_r7_1()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
            $this->yypushstate(self::CHILDLITERAL);
        }
    }

    function yy_r7_2()
    {

        if ($this->smarty->auto_literal && isset($this->value[$this->ldel_length]) ? strpos(" \n\t\r", $this->value[$this->ldel_length]) !== false : false) {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
        } else {
            $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
            $this->yypopstate();
        }
    }

    function yy_r7_3()
    {

        $to = strlen($this->data);
        preg_match("/{$this->ldel}\/?literal\s*{$this->rdel}/", $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[0][1])) {
            $to = $match[0][1];
        } else {
            $this->compiler->trigger_template_error("missing or misspelled literal closing tag");
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Templateparser::TP_BLOCKSOURCE;
    }
}

     