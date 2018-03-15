<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of workflow_engine
 *
 * @author David
 */
class workflow_core_engine implements  \workflow_core_iengine {
    private $adapter;
    private $config;
    private $limit;
    
    public function __construct($adapterName=null) 
    {
        //hack until the locale is removed
       if (is_null(core_framework_config::getConfig()->workflow))
       {
            $this->config = core_framework_config::getConfig()->workflow;
       }
       else
       {
           $this->config = core_framework_config::getConfig()->workflow;
       }
       $this->adapter = workflow_adapter_factoryadapter::getInstance($adapterName);
       $this->limit = $this->config->limit;
    }
    
   
    public function execute()
    {
        $instances = $this->adapter->fetchAllInstances($this->limit);
        foreach($instances as $instance)
        {
            echo nl2br("Procesando cliente:".$instance->cliente->email."\n");
            $transiciones = $this->adapter->fetchAllTransitions($instance->nodo_id);
            if (count($transiciones)==0) 
            {
                echo nl2br("Cliente sin transiciones disponibles, cerrando workflow:\n");
                $this->adapter->closeInstance($instance);
            }
            else
            {
                //Obtenemos el contexto
                $contexto = $this->adapter->fetchContext($instance->cliente_id);
                //vd($contexto);
        
                $this->procesarTransiciones($instance, $contexto, $transiciones);
            }
            echo nl2br("Finalizando Cliente\n");
        }            
    }
    
    private function procesarTransiciones($instance, $contexto, $transiciones)
    {
        //Evaluamos cada transicion!
        foreach($transiciones as $transicion)
        {
            echo nl2br("Evaluando transicion:".$transicion->condicion."\n");
            //evaluamos la transicion, la primera que se cumple? o por peso?
            if($this->evaluarCondicion($contexto, $transicion->condicion))
            {
                //Solo lo voy a mover cuando haya sido exitosa la ejecucion del handler
                if ($this->ejecutarHandler($transicion, $contexto))
                {
                    $this->adapter->updateInstance($instance, $transicion);
                }

                break;
            }
        }
    }
    
    private function ejecutarHandler($transicion, $contexto)
    {
        //ejecutar transicion
        $handler = workflow_handlers_factoryhandler::getInstance($transicion->handler);
        echo nl2br("Transicion activada, ejecutando el siguiente handler:".$transicion->handler."\n");
        $handlerSuccess = false;
        try
        {
            $handlerSuccess = $handler->execute($contexto);
        }
        catch (Exception $exc) 
        {
            echo nl2br("Ejecucion de handler con error:".$exc->getTraceAsString()."\n");
        }
        
        return $handlerSuccess;
    }

     /**
     * ALLOWED OPERATORS
    T_AND_EQUAL	&=	assignment operators
    T_BOOLEAN_AND	&&	logical operators
    T_BOOLEAN_OR	||	logical operators
    T_BOOL_CAST	(bool) or (boolean)	type-casting
    T_CHARACTER	 	not used anymore
    T_COMMENT	// or #, and	comments
    T_CONCAT_EQUAL	.=	assignment operators
    T_XOR_EQUAL	^=	assignment operators
    T_WHITESPACE	\t \r\n	 
    T_DEC	--	incrementing/decrementing operators
    T_DIV_EQUAL	/=	assignment operators
    T_DNUMBER	0.12, etc.	floating point numbers
    T_DOLLAR_OPEN_CURLY_BRACES	${	complex variable parsed syntax
    T_DOUBLE_ARROW	=>	array syntax
    T_DOUBLE_CAST	(real), (double) or (float)	type-casting
    T_DOUBLE_COLON	::	see T_PAAMAYIM_NEKUDOTAYIM below
    T_ENCAPSED_AND_WHITESPACE	" $a"	constant part of string with variables
    T_INC	++	incrementing/decrementing operators
    T_INT_CAST:	(int) or (integer)	type-casting
    T_ISSET	isset()	isset()
    T_IS_EQUAL	==	comparison operators
    T_IS_GREATER_OR_EQUAL	>=	comparison operators
    T_IS_IDENTICAL	===	comparison operators
    T_IS_NOT_EQUAL	!= or <>	comparison operators
    T_IS_NOT_IDENTICAL	!==	comparison operators
    T_IS_SMALLER_OR_EQUAL	<=	comparison operators
    T_SPACESHIP	<=>	comparison operators (available since PHP 7.0.0)
    T_LOGICAL_AND	and	logical operators
    T_LOGICAL_OR	or	logical operators
    T_LOGICAL_XOR	xor	logical operators
    T_NUM_STRING	"$a[0]"	numeric array index inside string
    T_OBJECT_CAST	(object)	type-casting
    T_OBJECT_OPERATOR	->	classes and objects
    T_STRING_CAST	(string)	type-casting
    T_STRING_VARNAME	"${a	complex variable parsed syntax
    T_POW	**	arithmetic operators (available since PHP 5.6.0)
    T_POW_EQUAL	**=	assignment operators (available since PHP 5.6.0)
    T_ELLIPSIS	...	function arguments (available since PHP 5.6.0)
    T_FUNCTION	function or cfunction	function
    T_STRING	parent, self, etc.	identifiers, e.g. keywords like parent and self, function names, class names and more are matched. See also T_CONSTANT_ENCAPSED_STRING.
    T_VARIABLE:
    */
    private function evaluarCondicion($contexto, $condicion)
    {
        try
        {
            if (strlen($condicion) == 0)
            {
                return true; //no es necesario que pongan una condicion para que funcione
            }
            
            $tokens = token_get_all($condicion);
            foreach($tokens as $token)
            {
                foreach($token as $t)
                {
                    if (is_numeric ($t))
                    {
                        switch(token_name($t))
                        {
                            case T_ABSTRACT: T_ARRAY: T_ARRAY_CAST: T_AS:
                            T_BAD_CHARACTER: T_BREAK: T_CALLABLE: T_CASE:
                            T_CATCH:T_CLASS: T_CLASS_C: T_CLONE:
                            T_CLOSE_TAG: T_CONST: T_CONSTANT_ENCAPSED_STRING:
                            T_CONTINUE: T_CURLY_OPEN: T_DECLARE: T_DEFAULT:
                            T_DIR: T_DOC_COMMENT: T_DO: T_ECHO: T_ELSE:
                            T_ELSEIF: T_EMPTY: T_ENDDECLARE: T_ENDFOR:
                            T_ENDFOREACH: T_ENDIF: T_ENDSWITCH: T_ENDWHILE:
                            T_END_HEREDOC: T_EVAL: T_EXIT: T_EXTENDS:
                            T_FILE: T_FINAL: T_FINALLY: T_FOR:
                            T_FOREACH: T_FUNC_C: T_GLOBAL: T_GOTO:
                            T_HALT_COMPILER: T_IF: T_IMPLEMENTS: T_INCLUDE:
                            T_INCLUDE_ONCE: T_INLINE_HTML: T_INSTANCEOF: T_INSTEADOF:
                            T_INTERFACE: T_LINE: T_LIST: T_LNUMBER:
                            T_METHOD_C: T_MINUS_EQUAL: T_MOD_EQUAL: T_MUL_EQUAL:
                            T_NAMESPACE: T_NS_C: T_NS_SEPARATOR: T_NEW:
                            T_VAR: T_OPEN_TAG: T_OPEN_TAG_WITH_ECHO: T_OR_EQUAL:
                            T_PAAMAYIM_NEKUDOTAYIM: T_PLUS_EQUAL:
                            T_PRINT: T_PRIVATE: T_PUBLIC: T_PROTECTED:
                            T_REQUIRE: T_REQUIRE_ONCE: T_RETURN: T_SL: T_SL_EQUAL:
                            T_SR: T_SR_EQUAL: T_START_HEREDOC:
                            T_STATIC: T_SWITCH: T_THROW:
                            T_TRAIT: T_TRAIT_C: T_TRY:  T_UNSET: T_UNSET_CAST:
                            T_USE: T_WHILE: T_YIELD:
                                return false; //NO PERMITIMOS EJECUTAR Y DEVOLVEMOS FALSE
                        }
                    }
                }
            }
            
             //verificamos que no se este llamdando al OS
            if (strpos(strtolower('exec('),strtolower($condicion))>0 || strpos(strtolower('cacheddatabase::'),strtolower($condicion))>0)
            {
                return false; //NO PODEMOS EJECUTAR OS COMMANDS
            }
            
            //verificamos que no se este llamdando al ORM
            if (strpos(strtolower('R::'),strtolower($condicion))>0 || strpos(strtolower('cacheddatabase::'),strtolower($condicion))>0)
            {
                return false; //NO PODEMOS EJECUTAR DB QUERYS
            }
            
            
            
            return eval("return ".$condicion.";");
        }
        catch (Exception $exc) 
        {
            return false; //el default no lo debe dejar pasar
        }
    }
}
