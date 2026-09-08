@extends('layouts.app')
@section('title', 'Pusat Inventaris')
@section('content')
    <script id="operations-data" type="application/json">@json($dashboardData)</script>
    <div id="operations-dashboard"></div>
@endsection
