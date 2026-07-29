@extends('layouts.admin', ['title' => 'Add Facility'])
@section('content')
<div class="form-shell"><form method="POST" action="{{ route('facilities.store') }}">@csrf @include('facilities.form')<div class="mt-3 d-flex gap-2"><button class="btn-primaryx">Save Facility</button><a class="btn-soft" href="{{ route('facilities.index') }}">Cancel</a></div></form></div>
@endsection
