<?php

namespace JuniorShyko\Phpextensive;

class Extensive {

    const COIN			= 0; // MOEDA
	const MALE_NUMBER	= 1; // NÚMERO MASCULIMO
	const FEMALE_NUMBER	= 2; // NÚMERO FEMININO

	private string $comma;
	private $separador;
	private string $connector;
	private string $centsSingular;
	private string $centsPlural;
	private string $coinSingular;
	private string $coinPlural;
	private array $trioExtensiveMale;
	private array $trioExtensoF;
	private array $classeExtenso;
	private string $singularComplement;
	private string $pluralComplement;
	private array $decimals;
	private string $decimalSingularSuffix;
	private string $decimalPluralSuffix;


	public function __construct() {

		$this->comma = 'e'; // 'vírgula' trocado por 'e'
		$this->separador = ''; // ',' -> símbolo de vírgula não deve ser utilizado como separador
		$this->connector = 'e';

		$this->centsSingular = 'centavo';
		$this->centsPlural = 'centavos';

		$this->coinSingular = 'real';
		$this->coinPlural = 'reais';


		// array 4 x 10
		$this->trioExtensiveMale = [
			[ 'zero', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove' ],
            [ 'dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove' ],
			[ '', '', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa' ],
            [ 'cem', 'cento', 'duzentos', 'trezentos', 'quatrocentos','quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos']
        ];

		// array 4 x 10
		$this->trioExtensoF = [
			[ 'zero', 'uma', 'duas', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove' ],
            [ 'dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove' ],
			[ '', '', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa' ],
            [ 'cem', 'cento', 'duzentas', 'trezentas', 'quatrocentas','quinhentas', 'seiscentas', 'setecentas', 'oitocentas', 'novecentas']
        ];

		$this->classeExtenso = [
			'cem', 'mil', 'milh', 'bilh', 'trilh', 'quatrilh', 'quintilh', 'sextilh', 'septilh', 'octilh', 'nonilh',
			'decilh', 'undecilh', 'duodecilh', 'tredecilh', 'quatordecilh', 'quindecilh', 'sexdecilh', 'setedecilh', 'octodecilh', 'novedecilh',
			'vigesilh'
        ];

		$this->singularComplement = 'ão';
		$this->pluralComplement = 'ões';

		// Decimais

		$this->decimals = [ 'déc', 'centés', 'milés', 'milhonés', 'bilhonés', 'trilhonés', 'quatrilhonés', 'quintilhonés',
			'sextilhonés', 'septilhonés', 'octilhonés', 'nonilhonés',
			'decilhonés', 'undecilhonés', 'duodecilhonés', 'tredecilhonés', 'quatordecilhonés', 'quindecilhonés',
			'sexdecilhonés', 'setedecilhonés', 'octodecilhonés', 'novedecilhonés', 'vigesilhonés'
        ];

		$this->decimalSingularSuffix = 'imo';
		$this->decimalPluralSuffix = 'imos';
	}


	/**
	 *  Retorna um valor por extenso.
	 *
	 *  @param	double	$valor	Valor a ser retornado por extenso.
	 *  @param	int		$style	Estilo do valor por extenso:
	 *  					COIN, MALE_NUMBER ou MALE_NUMBER.
	 *
	 */
	function extensive( $valueExtensive = 0.0, int $style = 0 ): string 
    {
		$coin = self::COIN == $style;
		$masculine = $coin || self::MALE_NUMBER == $style;

		$text = $valueExtensive . '';

		$point = mb_strpos( $text, '.' );
		$hasPoint = $point !== false;

		$wholePart = $hasPoint ? mb_substr( $text, 0, $point ) : $text;
		$fractionalPart = $hasPoint ? mb_substr( $text, $point + 1 ) : '';

		if ( 0 == $wholePart && $fractionalPart > 0 ) {
			return $this->sentencaParteFracionaria( $fractionalPart, $coin, $masculine );
		}

		$sentence = $this->wholePartSentence( $wholePart, $coin, $masculine );

		if ( $hasPoint ) {
			$sentence .= ' ' . ( $coin ? $this->connector : $this->comma ) . ' '
				. $this->sentencaParteFracionaria( $fractionalPart, $coin, $masculine );
		}
		return $sentence;
	}

	//
	// Parte Inteira da Sentença
	//

	private function wholePartSentence( $valor, $coin, $masculine ): string
    {

		$text = (string) $valor;
		$numeroDigitos = mb_strlen( $text );

		$resto = $numeroDigitos % 3;
		$complemento = 0 === $resto ? 0 : 3 - $resto;

		// Preenche com zero à esquerda para que o número possa ser quebrado em grupos de três
		$ajustado = str_pad( $valor, $numeroDigitos + $complemento, '0', STR_PAD_LEFT ); // ex: '12345' --> '012345'

		// Quebra em grupos de três
		$gruposTres = str_split( $ajustado, 3 );	// ex: '012345' --> [ '012', '345' ]

		$numeroGrupos = count( $gruposTres ); // ex: 1 = centenas, 2 = milhares, 3 = milhões, ...

		if ( 1 === $numeroGrupos && $gruposTres[ 0 ] === '000' ) {
			return $coin ? 'zero centavo' : 'zero';
		}

		$reverso = array_reverse( $gruposTres );
		$partes = [];
		foreach ( $reverso as $classe => $trio ) {

			if ( '000' === $trio ) { continue; }

			// Corrige o gênero feminino para milhões ou acima
			$masc = $masculine ? true : $classe > 1;

			$extenso = $this->sentencaTrio( $trio, $coin, $masc, $numeroGrupos );
			$extenso .= $classe > 0 ? ' ' . $this->classeExtenso[ $classe ] : '';
			if ( $classe >= 2 ) {
				$extenso .= $trio !== '001' ? $this->pluralComplement : $this->singularComplement;
			}

			array_unshift( $partes, $extenso );
		}

		$sentence = '';

		$cnt = mb_strlen( $valor );
		if ( $cnt >= 3 && '0' === mb_substr( $valor, $cnt - 3, 1 ) ) {
			$sentence = implode( ' ' . $this->connector . ' ', $partes );
		} else {
			$sentence = implode( ' ', $partes );
		}

		$sentence = $this->corrigir( $sentence, $valor );

		// Moeda
		if ( $coin ) {
			$sentence = $this->aplicarMoedaParteInteira( $sentence, $valor );
		}

		return $sentence;
	}


	function corrigir( $sentence, $valor ) {
		$text = trim( $sentence );
		if ( $valor >= 1000 && $valor < 2000 ) {
			if ( mb_strpos( $text, 'uma mil' ) === 0 ) {
				$text = mb_substr( $text, 4 ); // Retira o "uma"
			} else if ( mb_strpos( $text, 'um mil' ) === 0 ) {
				$text = mb_substr( $text, 3 ); // Retira o "um"
			}
		}
		return $text;
	}

	function aplicarMoedaParteInteira( $sentence, $valor ) {
		if ( 0 == $valor ) {
			return $sentence . ' ' . $this->centsSingular;
		} else if ( 1 == $valor ) {
			return $sentence . ' ' . $this->coinSingular;
		}
		return $sentence . ' ' . $this->coinPlural;
	}


	function sentencaTrio( $trio, $coin, $masculine, $numeroGrupos ) {

		$extenso = $masculine ? $this->trioExtensiveMale : $this->trioExtensoF;

		$c = $trio[ 0 ]; // centena
		$d = $trio[ 1 ]; // dezena
		$u = $trio[ 2 ]; // unidade

		$partes = [];

		if ( $trio == '100' ) {
			return $extenso[ 3 ][ 0 ]; // cem
		}

		if ( $c != '0' ) {
			$partes []= $extenso[ 3 ][ $c ]; // cento, duzentos, ...
		}

		if ( '1' == $d ) {
			$partes []= $extenso[ 1 ][ $u ]; // dezenas de 11-19
		} else { // unidades

			if ( $d != '0' ) {  // dezenas
				$partes []= $extenso[ 2 ][ $d ]; // vinte, trinta, ...
			}

			if ( $u != '0' ) { // unidades
				$partes []= $extenso[ 0 ][ $u ]; // dois, três
			}
		}

		$sentence = implode( ' ' . $this->connector . ' ', $partes );

		return $sentence;
	}


	function sentencaParteFracionaria( $valor, $coin, $masculine ) {

		$fracao = $valor;
		if ( $coin ) {

			if ( mb_strlen( $fracao ) <= 2 ) {

				$fracao = str_pad( $fracao, 2, '0' );
				$fracao = mb_substr( $fracao, 0, 2 );

				if ( 0 == $fracao ) {
					return ''; // nenhuma
				}

				return $this->wholePartSentence( (int) $fracao, false, true )
					. $this->adicionarParteFracionaria( $valor, $fracao );

			}
		}

		return $this->wholePartSentence( (int) $fracao, false, true )
			. ' ' . $this->complementoDecimais( $fracao )
			;
	}



	function complementoDecimais( $valor ) {
		$text = $valor . '';
		$numeroDigitos = mb_strlen( $text );
		return $this->decimals[ $numeroDigitos - 1 ]
			. ( 1 == $valor ? $this->decimalSingularSuffix : $this->decimalPluralSuffix );
	}



	function adicionarParteFracionaria( $valor, $fractionalPart ) {
		if ( 0 == $fractionalPart ) {
			return ''; // sem acréscimos
		}

		if ( 1 == $fractionalPart ) {
			return ' ' . $this->centsSingular;
		}

		return ' ' . $this->centsPlural;
	}

}

?>