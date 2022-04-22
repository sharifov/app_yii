<?php

namespace modules\sales\common\sales\validation;

use Carbon\Carbon;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;


/**
 * Class RuleExpressionProvider
 */
class RuleExpressionProvider implements ExpressionFunctionProviderInterface
{

    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        $carbonClass = Carbon::class;

        return [
            new ExpressionFunction('date',
                function ($str) use ($carbonClass) {
                    return sprintf("(new {$carbonClass}(%1\$s))", $str);
                },
                function ($arguments, $str) {
                    return new Carbon($str);
                }
            ),
            new ExpressionFunction('today',
                function ($str) use ($carbonClass) {
                    return sprintf("(new {$carbonClass}())", $str);
                },
                function ($arguments) {
                    return new Carbon();
                }
            ),
        ];
    }
}