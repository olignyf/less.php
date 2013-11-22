<?php


class Less_Tree_Operation extends Less_Tree{

	public function __construct($op, $operands, $isSpaced = false){
		$this->op = trim($op);
		$this->operands = $operands;
		$this->isSpaced = $isSpaced;
	}

	function accept($visitor) {
		$visitor->visit($this->operands);
	}

	public function compile($env){
		$a = $this->operands[0]->compile($env);
		$b = $this->operands[1]->compile($env);


		if( $env->isMathOn() ){
			if( $a instanceof Less_Tree_Dimension && $b instanceof Less_Tree_Color ){
				if ($this->op === '*' || $this->op === '+') {
					$temp = $b;
					$b = $a;
					$a = $temp;
				} else {
					throw new Less_CompilerException("Operation on an invalid type");
				}
			}
			if ( !Less_Parser::is_method($a,'operate') ) {
				throw new Less_CompilerException("Operation on an invalid type");
			}

			return $a->operate($env,$this->op, $b);
		} else {
			return new Less_Tree_Operation($this->op, array($a, $b), $this->isSpaced );
		}
	}

	function genCSS( $env, &$strs ){
		$this->operands[0]->genCSS( $env, $strs );
		if( $this->isSpaced ){
			self::OutputAdd( $strs, " " );
		}
		self::OutputAdd( $strs, $this->op );
		if( $this->isSpaced ){
			self::OutputAdd( $strs, ' ' );
		}
		$this->operands[1]->genCSS( $env, $strs );
	}

}
