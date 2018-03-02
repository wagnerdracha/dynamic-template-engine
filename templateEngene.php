<?php

namespace TemplateEngeneCreador;

Class TemplateEngene {
	
	/*
	* CONSTANTES
	*/
	const JAVASCRIPT    = '<script language="javascript" type="text/javascript" charset="utf-8" />#CONSTANT#</script>';
	const CSS 		    = '<style>#CONSTANT#</style>';
	const JQUERY 	    = '<script language="javascript" type="text/javascript" />$(document).ready(function(){#CONSTANT#});</script>';
	const TEMPLATE_ERRO = '<div class="error" style="width: 200px;border: 1px solid;margin:auto;padding:15px 10px 15px 50px;background-repeat: no-repeat;background-position: 10px center;color: #D8000C;background-color: #FFBABA;-webkit-box-shadow: 0px 0px 21px -4px rgba(0,0,0,0.75);-moz-box-shadow: 0px 0px 21px -4px rgba(0,0,0,0.75);box-shadow: 0px 0px 21px -4px rgba(0,0,0,0.75);">#ERROR#</div>';
	
	/*
	* VARIÁVEIS GLOBAIS
	*/
	private $template,		//variável que retornará a template pronta
			$components,	//variável que conterá os componentes
			$templateJson,	//variável que conterá o template em JSON
			$login,			//variável que conterá o usuário
			$group,			//variável que conterá os dados do grupo
			$dataArray,		//varíavel que conterá os dados advindos do banco
			$modDebug;		//varíavel que conterá o valor para mostrar as mensagens de erros ou não
	
	/*
	* Método construtor
	*/
	public function __construct() {
		//zera a varíavel template para receber valores concatenados
		$this->template = '';
	}
	
	/*
	* Função que inicial que cria o template
	*/
	public function createTemplate($templateJson=NULL, $components=NULL, $login=NULL, $group=NULL, $dataArray=NULL, $modDebug=FALSE) {
		
		//verificam se as variáveis estão nulas
		if(is_null($templateJson) || is_null($components)) {return false;}
		
		//Variavel que liga ou desliga o modo debug
		$this->modDebug = $modDebug;
		
		//Pega o valor de login
		$this->login = $login;
		
		//Pega o valor do grupo
		$this->group = $group;
		
		//decodifica os componentes
		$this->components = $this->decodeJson($components);
		
		//decodifica o template
		$this->templateJson = $this->decodeJson($templateJson);
		
		//recebe os dados do banco de dados
		$this->dataArray = $dataArray;
		
		//laço que corre os componentes da template
		if(!is_array($this->templateJson)){
			$this->template = $this->newView($this->templateJson);
		}else{
			foreach($this->templateJson as $key => $value) {
				$this->template .= $this->newView($value);
			}
		}
		
		//retira os atributos HTML que estão sem valores
		$this->template = preg_replace('/(?:|(?<!^)\G)\h*(\s\w+="#[^"]+#")/', '', $this->template);
		//echo '<script>alert(\''.$this->template.'\');</script>';
		//Retira os caracteres de identificação
		//$this->template = preg_replace('/\\s[a-z]+=""|\\s#.*?#|"#.*?#"|(?<=>)#.*?#([^<]*)/', '', $this->template);
		$this->template = preg_replace('/\\s[a-z]+=""|\\s#.*?#|(?<=>)#.*?#([^<]*)/', '', $this->template);
		
		//retorna a template HTML
		return $this->template;
	}
	
	/*
	* Função que cria o template
	*/
	private function newView($a, $b=NULL, $c='') {
		//Verifica o acesso do componente
		if(isset($a->{key($a)}->access)) {
			$i = $this->access($a->{key($a)}->access);
			if($i <= 0) {
				return '';
			}
		}
		
		//Verifica se existe e retorna o template do componente
		if(is_null($b) && isset($this->components->{key($a)}->template)) {
			$c = $b = $this->verifica_componente(@$this->components->{key($a)}->template);
		}
		
		if(isset($a->{key($a)}->css)) {
			if(isset($a->{key($a)}->css->access)) {
				$i = $this->access($a->{key($a)}->css->access);
				if($i > 0) {
					unset($a->{key($a)}->css->access);
					$c = preg_replace(
						'/#css#/', 
						$this->css($a->{key($a)}->css),
						$c
					);
				}
			}
		}
		
		if(isset($a->css)) {
			$c = $this->css($a->css);
		}
		
		if(isset($a->{key($a)}->javascript)) {
			$c = preg_replace(
				'/#javascript#/', 
				$this->javascript($a->{key($a)}->javascript),
				$c
			);
		}
		
		if(isset($a->javascript)) {
			$c = $this->javascript($a->javascript);
		}
		
		if(isset($a->{key($a)}->jquery)) {
			$c = preg_replace(
				'/#jquery#/', 
				$this->jquery($a->{key($a)}->jquery),
				$c
			);
		}
		
		if(isset($a->jquery)) {
			$c = $this->jquery($a->jquery);
		}
		
		//Verifica se o componente tem atributos para serem subtituídos
		if(isset($a->{key($a)}->atributes)) {
			$c = $this->atributes($a->{key($a)}->atributes, $c);
		}
		
		//Verifica se existem dados do banco ou array
		if(isset($a->{key($a)}->data)) {
			//Envia o dados para a construção da tela
			$c = $this->data($a->{key($a)}->data, $c);
		}
		
		//Verifica se o componente tem filhos
		if(isset($a->{key($a)}->children)) {
			$c = preg_replace(
				'/#children#/', 
				$this->children($a->{key($a)}->children), 
				$c
			);
		}
		
		return $c;
	}
	
	/*
	* Função que verifica se os dados fornecidos são em formato associativo
	*/
	private function arrayAssociativo($a=NULL) {
		if(is_null($a)){ $this->mostraMensagemErro('Ops! Os dados enviados para a construção da View estão nulos!'); }
		
		foreach(array_keys($a) as $key) {
			if (!is_int($key)){return $a;}
		}
		
		$this->mostraMensagemErro('Ops! Os dados fornecidos não são associativos!');
	}
	
	/*
	* Função que cria componente através de dados do banco ou array
	*/
	private function data($a, $b='', $c='') {
		$primeira_tag = '#VALOR#';
		preg_match("/(<\s*?\b[^>]*>.*)\|.*\|(.*<\/\b[^>]*>)/", $b, $matches);
		if(isset($matches[1]) && isset($matches[2])) {
			$primeira_tag = $matches[1] . '#VALOR#' . $matches[2];
		}
		
		$segunda_tag = '#VALOR#';
		$have		 = null;
		preg_match("/.*\|(<\s*?\b[^>]*>.*)\[.*\](.*<\/\b[^>]*>).*\|/", $b, $matches);
		if(isset($matches[1]) && isset($matches[2])) {
			$segunda_tag = $matches[1] . '#VALOR#' . $matches[2];
			$have = 1;
		}
		
		$terceira_tag = '';
		preg_match("/.*\[(<\s*?\b[^>]*>.*)(#.*#)(.*<\/\b[^>]*>)\].*/", $b, $matches);
		if(isset($matches[2])) {
			$terceira_tag = $matches[1] . $matches[2] . $matches[3];
		}
		
		if(empty($terceira_tag)) {
			$terceira_tag = $b;
		}
		
		if(isset($a->values)) {
			$values = $a->values;
			
			if(is_array($values)) {
				foreach($values as $index => $values2) {
					$c .= $this->changeValue($values2, $terceira_tag);
				}
			}else{
				$c .= $this->changeValue($values, $terceira_tag);
			}
			$c = preg_replace('/#VALOR#/', preg_replace('/#VALOR#/', $c, $segunda_tag), $primeira_tag);
		} elseif(isset($a->colummn)) {
			//Verifica se existe a chave key
			if(isset($a->key)) {
				$values = $this->dataArray[$a->key];
			}else{
				$values = $this->dataArray;
			}
			
			foreach($values as $index => $values2) {
				if($segunda_tag == '#VALOR#') {
					$c .= $this->changeValue($values2, $terceira_tag);
				}else{
					$d = '';
					foreach($a->colummn as $index2 => $values3) {
						if(is_object($values3->{key($values3)})) {
							if(isset($values3->{key($values3)}->colummn) && isset($values3->{key($values3)}->target)) {
								if(isset($values3->{key($values3)}->children->{key($values3->{key($values3)}->children)}->atributes)) {
									$values3->{key($values3)}->children->{key($values3->{key($values3)}->children)}->atributes->{$values3->{key($values3)}->target} = 
										$values2[$values3->{key($values3)}->colummn];
								}
							}
							$e = $this->newView($values3->{key($values3)}->children);
							$d .= preg_replace('/#'.key($values3).'#/', $e, $terceira_tag);
						}else{
							$d .= preg_replace('/#'.key($values3).'#/', $values2[$values3->{key($values3)}], $terceira_tag);
						}
					}
					$c .= preg_replace('/#VALOR#/', $d, $segunda_tag);
				}
			}
			$c = preg_replace('/#VALOR#/', $c, $primeira_tag);
		}
		
		return $c;
	}
	
	/*
	* Função que ajuda a cria os filhos do componente pai
	*/
	private function children($a, $b='') {
		if(is_array($a)) {
			foreach($a as $key => $value) {
				$b .= $this->newView($value);
			}
		}else{
			if(count(get_object_vars($a)) > 1) {
				foreach($a as $key => $value) {
					$obj = (object)array($key => $value);
					$b .= $this->newView($obj);
				}
			}else{
				$b = $this->newView($a);
			}
		}
		return $b;
	}
	
	/*
	* Função que cuida de arrumar o componente com os seus atributos
	*/
	private function atributes($a, $b) {
		foreach($a as $key2 => $value2) {
			$b = preg_replace('/#'.$key2.'#/', $value2, $b);
		}
		return $b;
	}
	
	/*
	* Função que cuida de verificar o acesso
	*/
	private function access($a) {
		$i = 1;
		if(isset($a->login)) {
			if(is_array($a->login)) {
				if(!in_array($this->login, $a->login)) {
					$i = $i - 2;
				}
			}else{
				if($this->login !== $a->login) {
					$i = $i - 2;
				}
			}
			$i = $i + 1;
		}else{
			$i = $i - 1;
		}
		
		if(isset($a->group)) {
			if(is_array($a->group)) {
				if(!in_array($this->group, $a->group)) {
					$i = $i - 2;
				}
			}else{
				if($this->group !== $a->group) {
					$i = $i - 2;
				}
			}
			$i = $i + 1;
		}else{
			$i = $i - 1;
		}
		return $i;
	}
	
	/*
	* Função que cuida de criar os css's
	*/
	private function css($a, $b=NULL, $c='') {
		if(is_null($b) && isset($this->components->{__FUNCTION__}->template)) {
			$b = $this->verifica_componente(@$this->components->{__FUNCTION__}->template);
		}
		
		if(is_array($a)) {
			$c = $this->foreachFunction($a, __FUNCTION__, $b);
		}else{
			switch(key($a)) {
				case 'archive': {
					if(is_object($a->{key($a)})) {
						$c .= $this->changeValue($a->{key($a)}, $b);
					}else{
						foreach($a->{key($a)} as $key => $value) {
							$c .= $this->changeValue($value, $b);
						}
					}
					break;
				}
				case 'code': {
					if(is_array($a->{key($a)})) {
						foreach($a->{key($a)} as $key => $value) {
							$c .= $value;
						}
					}else{
						$c .= $a->{key($a)};
					}
					$c = preg_replace('/#CONSTANT#/', $c, constant("self::".strtoupper(__FUNCTION__)));
					break;
				}
				default: {
					$this->mostraMensagemErro('Ops! Não existe opção para o CSS! (' . key($a) . ')');
					break;
				}
			}
		}
		
		return $c;
	}
	
	/*
	* Função que cuida de criar os javascripts
	*/
	private function jquery($a, $b=NULL, $c='') {
		if(is_null($b) && isset($this->components->{__FUNCTION__}->template)) {
			$b = $this->verifica_componente(@$this->components->{__FUNCTION__}->template);
		}
		
		if(is_array($a)) {
			$c = $this->foreachFunction($a, __FUNCTION__, $b);
		}else{
			switch(key($a)) {
				case 'archive': {
					if(is_object($a->{key($a)})) {
						$c .= $this->changeValue($a->{key($a)}, $b);
					}else{
						foreach($a->{key($a)} as $key => $value) {
							$c .= $this->changeValue($value, $b);
						}
					}
					break;
				}
				case 'code': {
					if(is_array($a->{key($a)})) {
						foreach($a->{key($a)} as $key => $value) {
							$c .= $value;
						}
					}else{
						$c .= $a->{key($a)};
					}
					$c = preg_replace('/#CONSTANT#/', $c, constant("self::".strtoupper(__FUNCTION__)));
					break;
				}
				default: {
					$this->mostraMensagemErro('Ops! Não existe opção para o Jquery! (' . key($a) . ')');
					break;
				}
			}
		}
		
		return $c;
	}
	
	/*
	* Função que cuida de criar os javascripts
	*/
	private function javascript($a, $b=NULL, $c='') {
		if(is_null($b) && isset($this->components->{__FUNCTION__}->template)) {
			$b = $this->verifica_componente(@$this->components->{__FUNCTION__}->template);
		}
		if(is_array($a)) {
			$c = $this->foreachFunction($a, __FUNCTION__, $b);
		}else{
			switch(key($a)) {
				case 'archive': {
					if(is_object($a->{key($a)})) {
						$c = $this->changeValue($a->{key($a)}, $b);
					}else{
						foreach($a->{key($a)} as $key => $value) {
							$c .= $this->changeValue($value, $b);
						}
					}
					break;
				}
				case 'code': {
					if(is_array($a->{key($a)})) {
						foreach($a->{key($a)} as $key => $value) {
							$c .= $value;
						}
					}else{
						$c .= $a->{key($a)};
					}
					$c = preg_replace('/#CONSTANT#/', $c, constant("self::".strtoupper(__FUNCTION__)));
					break;
				}
				default: {
					$this->mostraMensagemErro('Ops! Não existe opção para o Javascript! (' . key($a) . ')');
					break;
				}
			}
		}

		return $c;
	}
	
	/*
	* Função que troca as informações pelas código: #KEY#
	*/
	private function changeValue($a, $b) {
		if(isset($a->access)) {
			$i = $this->access($a->access);
			if($i <= 0) {return '';}
			unset($a->access);
		}
		
		foreach($a as $key => $value) {
			$b = preg_replace('/#'.$key.'#/', $value, $b);
		}
		return $b;
	}
	
	/*
	* Função que faz um foreach para outra Função
	*/
	private function foreachFunction($a, $b, $c) {
		$d = '';
		foreach($a as $key => $value) {
			$d .= $this->$b($value, $c);
		}
		return $d;
	}
	
	/*
	* Verifica se o componente solicitado existe
	*/
	private function verifica_componente($dados) {
		if($dados)
		{
			return $dados;
		}
		$this->mostraMensagemErro('Ops! Mas houve uma falha na criação desta página. Não existe o componente solicitado.');
	}
	
	/*
	* Função que faz o decode do JSON
	*/
	private function decodeJson($valor=NULL) {
		$valor = json_decode($valor);
		
		//Caso tenho algum erro, retorna uma mensagem de erro
        if (json_last_error()) {
			$this->mostraMensagemErro($this->json_last_error_msg());
        }
		
		return $valor;
	}
	
	/*
	* Função que exibe a mensagem de erro
	*/
	private function mostraMensagemErro($a) {
		if($this->modDebug){
			echo preg_replace('/#ERROR#/', $a, self::TEMPLATE_ERRO);
			exit();
		}
	}
	
	/*
	* Verifica se houveram erros no JSON
	*/
    private function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'A profundidade máxima da pilha foi ultrapassada',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Erro de sintaxe, JSON mal formado',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Erro desconhecido: ({$error})";
    }
}

?>