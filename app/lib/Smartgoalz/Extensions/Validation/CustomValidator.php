<?php
/**
 * The MIT License (MIT)
 *
 * SMARTGoalz - SMART Goals made easier
 *
 * http://smartgoalz.github.io
 *
 * Copyright (c) 2015 Prashant Shah <pshah.smartgoalz@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Smartgoalz\Extensions\Validation;

Use DateTime;

class CustomValidator extends \Illuminate\Validation\Validator
{
        public function validateBeforetime($attribute, $value, $parameters)
        {
                $dt1 = $value;
                $dt2 = $this->getValue($parameters[0]);

                /* Convert to PHP DateTime class */
		$phpDt1 = DateTime::createFromFormat('Y-m-d H:i:s', $dt1);
		$phpDt2 = DateTime::createFromFormat('Y-m-d H:i:s', $dt2);

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
