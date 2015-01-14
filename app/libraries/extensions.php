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

/* This function outputs a simple label tag */
Form::macro('rawLabel', function($name, $value = null, $options = array())
{
	return '<label>' . $name . '</label>';
});

/* This function allows variable declarations in blade templates using @define */
Blade::extend(function($value)
{
    return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value);
});

/* This function appends input query parameters to paginator links */
View::composer(Paginator::getViewName(), function($view)
{
	$query = array_except(Input::query(), Paginator::getPageName());
	$view->paginator->appends($query);
});

/* Hex validation */
Validator::extend('hexcolor', function($attribute, $value, $parameters)
{
	if(preg_match("/^#?([a-f0-9]{6}|[a-f0-9]{3})$/", $value))
	{
		return true;
	}
	return false;
});

Validator::replacer('hexcolor', function($message, $attribute, $rule, $parameters)
{
	return sprintf('The %s must be a valid color', $attribute);
});
