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

Please click <a href="{{ url('users/resetpass') . '?k=' . $key . '&u=' . $username }}">here</a> to reset your password.<br /><br />

If you cannot open the above link, please copy and paste the below link in your browser.<br /><br />

{{ url('users/resetpass') . '?k=' . $key . '&u=' . $username }} <br /><br />

The above link is valid only for the next 24 hours. Please reset your password before that.<br /><br />

{{ Config::get('smartgoalz.SITE_NAME') }} Team.<br /><br />

{{ Config::get('smartgoalz.SITE_URL') }}
