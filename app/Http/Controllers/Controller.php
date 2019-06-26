<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Manager;

class Controller extends BaseController
{
    use ResponseTrait, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Constructor
     *
     * @param Manager|null $fractal
     */
    public function __construct(Manager $fractal = null)
    {
        $fractal = $fractal === null ? new Manager() : $fractal;
        $this->setFractal($fractal);
    }

    /**
     * Validate HTTP request against the rules
     *
     * @param Request $request
     * @param array $rules
     * @return bool|array
     */
    protected function validateRequest(Request $request, array $rules)
    {
        // Perform Validation
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->messages();

            // crete error message by using key and value
            foreach ($errorMessages as $key => $value) {
                $errorMessages[$key] = $value[0];
            }

            return $errorMessages;
        }

        return true;
    }

    /**
     * Get Update Rules
     *
     * @param Request $request
     * @param array $rules
     * @param array $specialRules
     * @return array
     */
    protected function getUpdateRules(Request $request, array $rules, $specialRules = null)
    {
        // Unset rule password if have
        if (isset($rules['password'])) {
            unset($rules['password']);
        }

        // Add for case have email
        if ($specialRules) {
            if (isset($rules['email'])) {
                $rules['email'] .= ',email,' . $specialRules;
            }
        }

        $ruleKeys = array_keys($rules);

        foreach ($ruleKeys as $ruleKey) {
            if ($request->get($ruleKey, false) === false) {
                unset($rules[$ruleKey]);
            }
        }

        return $rules;
    }

    /**
     * Update Request validation Rules
     *
     * @param Request $request
     * @param array $rules
     * @return array|bool
     */
    protected function validateRulesForUpdate(Request $request, array $rules, $specialRules = null)
    {
        $updateRules = $this->getUpdateRules($request, $rules, $specialRules);
        return $this->validateRequest($request, $updateRules);
    }

    /**
     * @param Request $request
     * @param array $rules
     * @param array $skip
     * @return array
     */
    protected function availableRules(Request $request, array $rules, $skip = [])
    {
        $results = [];
        foreach ($rules as $key => $rule) {
            if (in_array($key, $request->keys()) || in_array($key, $skip)) {
                $results[$key] = $rule;
            }
        }
        return $results;
    }
}
