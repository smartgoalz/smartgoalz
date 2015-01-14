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

function timezone_list()
{

        static $timezones = null;

        if ($timezones === null)
        {
                $timezones = [];
                $offsets = [];
                $now = new DateTime();

                foreach (DateTimeZone::listIdentifiers() as $timezone)
                {
                    $now->setTimezone(new DateTimeZone($timezone));
                    $offsets[] = $offset = $now->getOffset();
                    $timezones[$timezone] = format_timezone_name($timezone) .
                            ' (' . format_GMT_offset($offset) . ')';
                }

                array_multisort($offsets, $timezones);
        }

        return $timezones;
}

function format_GMT_offset($offset)
{
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return 'GMT ' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}

function format_timezone_name($name)
{
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);
        return $name;
}
