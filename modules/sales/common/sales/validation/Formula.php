<?php

namespace modules\sales\common\sales\validation;
use Carbon\Carbon;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


/**
 * Class Formula
 */
class Formula
{
    /**
     * @var ExpressionLanguage
     */
    private static $language;

    /**
     * @var string
     */
    private $formula;

    function __construct($formula)
    {
        $this->formula = $formula;
    }

    /**
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * @param array $variables
     * @return integer
     */
    public function evaluate($variables = [])
    {
        $variables = array_merge($variables, $this->getVariables());
        return max((int)round(self::language()->evaluate($this->formula, $variables)), 0);
    }

    private function getVariables()
    {
        return [
            'this_day' => Carbon::today()->day,
            'this_month' => Carbon::today()->month,
            'this_year' => Carbon::today()->year,
        ];
    }

    /**
     * @return ExpressionLanguage
     */
    private static function language()
    {
        if (self::$language === null) {
            self::$language = new ExpressionLanguage();
            self::$language->registerProvider(new RuleExpressionProvider());
        }
        return self::$language;
    }
}