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

@extends('layouts.admin')

@section('head')

<script type="text/javascript">

$(document).ready(function() {

});

</script>

@stop

@section('page-title', 'Manage users')

@section('content')

<div class="admin-container">

<div class="header-button">
        {{ HTML::linkAction('AdminUsersController@getCreate', 'Create user', array(),
                array('class' => 'btn btn-primary')) }}
</div>

@if ($users->count() < 1)

<div>No users found.</div>

@else

<div>
	<table class="table table-hover">
		<thead>
		<tr>
			<th>Username</th>
			<th>Fullname</th>
			<th>Email</th>
			<th class="text-center">Gender</th>
			<th class="text-center">Admin</th>
			<th class="text-center">Admin verified</th>
			<th class="text-center">Email verified</th>
			<th class="text-center">Status</th>
			<th>Created at</th>
			<th>Actions</th>
		</tr>
		</thead>

		<tbody>
                @foreach ($users as $user)
		<tr>
                        <td class="text-left">
                                {{ $user->username }}
                        </td>
                        <td class="text-left">
                                {{ $user->fullname }}
                        </td>
                        <td class="text-left">
                                {{ $user->email }}
                        </td>
                        <td class="text-center">
				@if ($user->gender == 'M')
					Male
				@elseif ($user->gender == 'F')
					Female
				@elseif ($user->gender == 'U')
					Undisclosed
				@else
				@endif
                        </td>
                        <td class="text-center">
				@if ($user->is_admin == 1)
					Yes
				@else
					No
				@endif
                        </td>
                        <td class="text-center">
				@if ($user->admin_verified == 1)
					Yes
				@else
					No
				@endif
                        </td>
                        <td class="text-center">
				@if ($user->email_verified == 1)
					Yes
				@else
					No
				@endif
                        </td>
                        <td class="text-center">
				@if ($user->status == 1)
					Active
				@else
					Inactive
				@endif
                        </td>
			<td class="text-left">
				{{ date_format(date_create_from_format('Y-m-d H:i:s', $user->created_at), explode('|', $user->dateformat)[0] . ' h:i A') }}
                        </td>
			<td class="text-left">
                                {{ HTML::decode(HTML::linkAction('AdminUsersController@getEdit',
                                        '<span class="glyphicon glyphicon-pencil">', $user->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction(
                                        'AdminUsersController@deleteDestroy',
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        array($user->id),
                                        array(
                                                'class' => '',
                                                'data-method' => 'DELETE',
                                                'data-confirm' => 'Are you sure you want to delete the user ?'
                                        ))) }}
			</td>
		</tr>
                @endforeach
		</tbody>
	</table>
</div>

@endif


</div>
<!-- /.container -->

@stop
