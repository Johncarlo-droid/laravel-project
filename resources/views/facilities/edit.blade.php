@extends('layouts.admin', ['title' => 'Edit Facility'])
@section('content')
<div class="form-shell"><form method="POST" action="{{ route('facilities.update', $facility) }}">@csrf @method('PUT') @include('facilities.form')<div class="mt-3 d-flex gap-2"><button class="btn-primaryx">Update Facility</button><a class="btn-soft" href="{{ route('facilities.index') }}">Cancel</a></div></form></div>
@endsection
