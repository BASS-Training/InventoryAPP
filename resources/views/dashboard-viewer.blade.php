@extends('layouts.app')

@section('title', 'Katalog Barang')

@section('content')
    <script id="viewer-inventory-data" type="application/json">@json($dashboardData)</script>
    <div id="viewer-inventory-dashboard"></div>
@endsection
