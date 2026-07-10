@extends('layouts.app')
@section('content')
<div class="row justify-content-center"><div class="col-md-4">
<h1 class="h3 mb-3">Reset Password</h1>
<form method="post" action="{{ route('password.update') }}" class="bg-white border rounded p-4">@csrf
<input type="hidden" name="token" value="{{ $token }}">
<label class="form-label">Email</label><input class="form-control mb-3" name="email" type="email" required>
<label class="form-label">Password</label><input class="form-control mb-3" name="password" type="password" required>
<label class="form-label">Konfirmasi Password</label><input class="form-control mb-3" name="password_confirmation" type="password" required>
<button class="btn btn-primary w-100">Reset</button>
</form></div></div>
@endsection
