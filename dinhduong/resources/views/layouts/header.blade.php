<!DOCTYPE html>
<html>

<head lang="vi">
    <title>{{$setting['site-title']}}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="cleartype" content="on">
    <link href="{{asset($setting['logo-light'])}}" rel="shortcut icon" type="image/x-icon">
    <link href="{{asset('/web/frontend/css/all.min.css')}}" rel="stylesheet">
    <!--load all styles -->
    <link href="{{asset('/web/frontend/plugins/datatimepickerbootstrap/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <!-- CSS Styles -->
    <link rel="stylesheet" href="{{asset('/web/css/a585b32.css')}}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .chosen-container-multi .chosen-choices {
            border-radius: 5px;
            min-height: 50px;
        }
    </style>
    @stack('head')
</head>

<body>
