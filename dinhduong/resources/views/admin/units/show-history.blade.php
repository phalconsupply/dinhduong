@extends('admin.units.show')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('show-content')
    @include('admin.layouts.tab-history')
@endsection
