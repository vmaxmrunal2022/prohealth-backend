<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueEffectiveDate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $rxNetworkRuleId;
    public function __construct($rxNetworkRuleId)
    {
        //
        $this->rxNetworkRuleId = $rxNetworkRuleId;
    }

    
    public function passes($attribute, $value)
    {
        dd($value)  ;
        exit();
        $count = DB::table('RX_NETWORK_RULES')
        ->where('RX_NETWORK_RULE_ID', $this->rxNetworkRuleId)
        ->where('EFFECTIVE_DATE', '=',$value)
        ->count();
       return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
