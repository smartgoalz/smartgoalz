<?php namespace Smartgoalz\Extensions\Validation;

Use DateTime;

class CustomValidator extends \Illuminate\Validation\Validator
{
        public function validateBeforetime($attribute, $value, $parameters)
        {
                $dt1 = $value;
                $dt2 = $this->getValue($parameters[0]);

                /* Convert to PHP DateTime class */
		$phpDt1 = DateTime::createFromFormat('Y-m-d H:m:s', $dt1);
		$phpDt2 = DateTime::createFromFormat('Y-m-d H:m:s', $dt2);

                /* Check if above DateTime conversion failed */
                if (!$phpDt1) {
                        return false;
                }
                if (!$phpDt2) {
                        return false;
                }

                if ($phpDt1 < $phpDt2)
                {
                        return false;
                }

                return true;
        }

        /* Message to show for validateBeforetime on validation failure */
        protected function replaceBeforetime($message, $attribute, $rule, $parameters)
        {
                return sprintf("Stop time cannot be before start time.");
        }
}
