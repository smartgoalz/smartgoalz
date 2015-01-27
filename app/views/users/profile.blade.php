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

@extends('layouts.master')

@section('head')

<script type="text/javascript">

$(document).ready(function() {

});

</script>

@stop

@section('page-title', 'Profile')

@section('content')

{{ HTML::linkAction('UsersController@getEditprofile', 'Edit Profile', array(), array('class' => 'btn btn-primary')) }}

<br />
<br />

<table class="table borderless table-pad">
<tr>
        <td width="120px">Fullname</td><td class="colon">:</td><td>{{ $user->fullname }}</td>
</tr>
<tr>
        <td>Username</td><td class="colon">:</td><td>{{ $user->username }}</td>
</tr>
<tr>
        <td>Email</td><td class="colon">:</td><td>{{ $user->email }}</td>
</tr>
<tr>
        <td>Gender</td><td class="colon">:</td>
        <td>
                @if ($user->gender == 'M')
                        {{ 'Male' }}
                @elseif ($user->gender == 'F')
                        {{ 'Female' }}
                @elseif ($user->gender == 'U')
                        {{ 'Undisclosed' }}
                @else
                        {{ 'ERROR' }}
                @endif
        </td>
</tr>
<tr>
        <td>Date format</td><td class="colon">:</td>
        <td>
                @if ($user->dateformat == 'd-M-Y|dd-M-yy')
                        {{ 'Day-Month-Year' }}
                @elseif ($user->dateformat == 'M-d-Y|M-dd-yy')
                        {{ 'Month-Day-Year' }}
                @elseif ($user->dateformat == 'Y-M-d|yy-M-dd')
                        {{ 'Year-Month-Day' }}
                @else
                        {{ 'ERROR' }}
                @endif
        </td>
</tr>
<tr>
        <td>Date of birth</td><td class="colon">:</td><td>{{ $dob }}</td>
</tr>
<tr>
        <td>Timezone</td><td class="colon">:</td><td>{{ $timezone_options[$user->timezone] }}</td>
</tr>
<tr>
        <td>Last Login</td><td class="colon">:</td><td>{{ date_format(date_create_from_format('Y-m-d H:i:s', $user->last_login), explode('|', $user->dateformat)[0] . ' h:i A') }}</td>
</tr>
<tr>
        <td>Created On</td><td class="colon">:</td><td>{{ date_format(date_create_from_format('Y-m-d H:i:s', $user->created_at), explode('|', $user->dateformat)[0]) }}</td>
</tr>
</table>

{{ HTML::linkAction('UsersController@getChangepass', 'Change Password', array()) }}

@stop
