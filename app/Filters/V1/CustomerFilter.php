<?php

namespace App\Filters\V1;

use Illuminate\Http\Request;

class CustomersFilter {

    // List columns and allowable query operators
    protected $safeParms = [
        'name' => ['eq'],
        'type' => ['eq'],
        'email' => ['eq'],
        'address' => ['eq'],
        'city' => ['eq'],
        'state' => ['eq'],
        'postaclCode' => ['eq', 'gt', 'lt']
    ];

    // Translate JSON name to database name 
    protected $columnMap = [
        'postalCode' => 'postal_code'
    ];

    // Translate operators to actual database notation
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ];

    public function transform(Request $request) {
        $eloQuery = []; // Array to pass to eloquent

        // Iterate over what is safe first
        foreach ($this->safeParms as $parm => $operators) {
            $query = $request->query($parm);

            if (!isset($query)) {
                continue;
            }

            $column = $this->columnMap[$parm] ?? $parm;
            
            // Check to make sure operator is allowed
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        return $eloQuery;
    } 
}