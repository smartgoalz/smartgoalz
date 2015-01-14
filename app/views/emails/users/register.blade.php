{{--

The MIT License (MIT)

SMARTGoalz - SMART Goals made easier

http://smartgoalz.github.io

Copyright (c) 2015 Prashant Shah <pshah.smartgoalz@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

--}}

Hello {{ $username }},<br /><br />

Welcome to {{ Config::get('smartgoalz.SITE_NAME') }} !<br /><br />

Your registration is successful.<br /><br />

You can login with the username as "{{ $username }}".<br /><br />

Please do update your profile after you login for the first time.<br /><br />

Enjoy !<br /><br />

{{ Config::get('smartgoalz.SITE_NAME') }} Team.<br /><br />

{{ Config::get('smartgoalz.SITE_URL') }}