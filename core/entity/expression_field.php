<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity;

class ExpressionField extends ScalarField {
	/**
	 * @var string
	 */
	protected $expression;

	/**
	 * Full expression, recursively includes expressions from buildFrom fields
	 * @var string
	 */
	protected $fullExpression;

	/**
	 * @var ScalarField
	 */
	protected $valueField;

	/**
	 * @var array
	 */
	protected $buildFrom;

	/**
	 * @var QueryChain[]
	 */
	protected $buildFromChains;

	protected $isAggregated;

	protected $hasSubquery;

	protected static
		$aggrFunctionsMYSQL = array('AVG', 'BIT_AND', 'BIT_OR', 'BIT_XOR', 'COUNT',
		'GROUP_CONCAT', 'MAX', 'MIN', 'STD', 'STDDEV_POP', 'STDDEV_SAMP',
		'STDDEV', 'SUM', 'VAR_POP', 'VAR_SAMP', 'VARIANCE'
	),
		$aggrFunctionsMSSQL,
		$aggrFunctionsORACLE,
		$aggrFunctions;

	/**
	 * All fields in exression should be placed as %s (or as another placeholder for sprintf),
	 * and the real field names being carrying in $buildFrom array (= args for sprintf)
	 *
	 * @param string            $name
	 * @param string            $expression
	 * @param array|string|null $buildFrom
	 * @param array             $parameters
	 */
	public function __construct($name, $expression, $buildFrom = null, $parameters = array())
	{
		if (!isset($parameters['data_type']))
		{
			$parameters['data_type'] = 'string';
		}

		parent::__construct($name, $parameters);

		$this->expression = $expression;

		if (!is_array($buildFrom) && $buildFrom !== null)
		{
			$buildFrom = array($buildFrom);
		}
		elseif ($buildFrom === null)
		{
			$buildFrom = array();
		}

		$this->buildFrom = $buildFrom;
	}

	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->valueField, $name), $arguments);
	}

	/*
	public function setEntity(Base $entity)
	{
		parent::setEntity($entity);

		$parameters = $this->initialParameters;

		unset($parameters['expression']);
		$this->valueField = $this->entity->initializeField($this->name, $parameters);

		if (!($this->valueField instanceof ScalarField))
		{
			throw new SystemException('expression field can only be a scalar type.');
		}
	}
	*/

	public function getExpression()
	{
		return $this->expression;
	}

	/*
	public function getFullExpression()
	{
		if (!isset($this->fullExpression))
		{
			$SQLBuildFrom = array();

			foreach ($this->getBuildFromChains() as $chain)
			{
				if ($chain->getLastElement()->getValue() instanceof ExpressionField)
				{
					$SQLBuildFrom[] = $chain->getLastElement()->getValue()->getFullExpression();
				}
				else
				{
					$SQLBuildFrom[] = '%s';
				}
			}

			$this->fullExpression = call_user_func_array('sprintf', array_merge(array($this->expression), $SQLBuildFrom));
		}

		return $this->fullExpression;
	}
	*/

	/*
	public function isAggregated()
	{
		if (!isset($this->isAggregated))
		{
			$this->isAggregated = (bool) self::checkAggregation($this->getFullExpression());
		}

		return $this->isAggregated;
	}
	*/

	/*
	public function hasSubquery()
	{
		if (!isset($this->hasSubquery))
		{
			$this->hasSubquery = (bool) self::checkSubquery($this->getFullExpression());
		}

		return $this->hasSubquery;
	}
	*/

	public function isConstant()
	{
		return empty($this->buildFrom);
	}

	/*
	public function getBuildFromChains()
	{
		if (is_null($this->buildFromChains))
		{
			$this->buildFromChains = array();

			foreach ($this->buildFrom as $elem)
			{
				// validate if build from scalar or expression
				$chain = QueryChain::getChainByDefinition($this->entity, $elem);
				$field = $chain->getLastElement()->getValue();

				if ($field instanceof ScalarField || $field instanceof ExpressionField)
				{
					$this->buildFromChains[] = $chain;
				}
				else
				{
					throw new SystemException(sprintf(
						                          'Expected ScalarField or ExpressionField in `%s` build_from, but `%s` was given.',
						                          $this->name, is_object($field) ? get_class($field).':'.$field->getName() : gettype($field)
					                          ));
				}
			}
		}

		return $this->buildFromChains;
	}
	*/

	/*
	public static function checkAggregation($expression)
	{
		if (empty(self::$aggrFunctions))
		{
			self::$aggrFunctions = array_unique(array_merge(
				                                    self::$aggrFunctionsMYSQL, self::$aggrFunctionsMSSQL, self::$aggrFunctionsORACLE
			                                    ));
		}

		// should remove subqueries from expression here: EXISTS(..(..)..), (SELECT ..(..)..)
		$expression = static::removeSubqueries($expression);

		// then check for aggr functions
		preg_match_all('/(?:^|[^a-z0-9_])('.join('|', self::$aggrFunctions).')[\s\(]+/i', $expression, $matches);

		return isset($matches[1]) ? $matches[1] : null;
	}

	public static function checkSubquery($expression)
	{
		return (preg_match('/(?:^|[^a-zA-Z0-9_])EXISTS\s*\(/i', $expression) || preg_match('/(?:^|[^a-zA_Z0-9_])\(\s*SELECT/i', $expression));
	}

	public static function removeSubqueries($expression)
	{
		// remove double slashes
		$expression = str_replace('\\\\\\\\', '', $expression);

		// remove strings
		$expression = static::removeStrings('"', $expression);
		$expression = static::removeStrings("'", $expression);

		// remove subqueries' bodies
		$clear = static::removeSubqueryBody($expression);

		while ($clear !== $expression)
		{
			$expression = $clear;
			$clear = static::removeSubqueryBody($expression);
		}

		return $clear;
	}
	*/

	protected static function removeStrings($quote, $expression)
	{
		// remove escaped quotes
		$expression = str_replace('\\' . $quote, '', $expression);

		// remove quoted strings
		$expression = preg_replace('/' . $quote . '.*?' . $quote . '/', '', $expression);

		return $expression;
	}

	protected static function removeSubqueryBody($query)
	{
		$subqPattern = '\(\s*SELECT\s+';

		$matches = null;
		preg_match('/' . $subqPattern . '/i', $query, $matches);

		if (!empty($matches))
		{
			$substring = $matches[0];

			$subqPosition = strpos($query, $substring);
			$subqStartPosition = $subqPosition + strlen($substring);

			$bracketsCount = 1;
			$currentPosition = $subqStartPosition;

			// until initial bracket is closed
			while ($bracketsCount > 0)
			{
				$symbol = substr($query, $currentPosition, 1);

				if ($symbol == '')
				{
					// end of string
					break;
				}

				if ($symbol == '(')
				{
					$bracketsCount++;
				}
				elseif ($symbol == ')')
				{
					$bracketsCount--;
				}

				$currentPosition++;
			}

			$query = substr($query, 0, $subqPosition) . substr($query, $currentPosition);
		}

		return $query;
	}

	/**
	 * @deprecated
	 * @return null|string
	 */
	/*
	public function getDataType()
	{
		return $this->valueField->getDataType();
	}
	*/

	public function __clone()
	{
		$this->buildFromChains = null;
		$this->fullExpression = null;
	}

}