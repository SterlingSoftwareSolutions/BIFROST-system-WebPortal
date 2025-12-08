@extends('layout.userLogin-layout')

@section('portalSelection')
    <label class="flex items-center font-bold text-l">
        <input type="radio" name="portal" value="admin" class="mr-2" onclick="enablePinFields()" checked hidden>
                   Admin Portal
    </label>
@endsection