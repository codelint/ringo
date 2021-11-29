<?php date_default_timezone_set('Asia/Shanghai'); ?>
@extends('ringo::layout.base')
@section('title', '消息详情')

@section('js')
    <script type="text/javascript">
    </script>
@endsection

@section('css')
    <style type="text/css">
        [v-cloak] {
            display: none;
        }

        th {
            width: 30%;
            word-break: break-all;
            vertical-align: top;
            padding: 4px;
            text-align: right;
            font-weight: bold;
            background-color: #f8f8f8
        }
    </style>
@endsection

@section('content')
    @if(!isset($detail))
        <h3>{{ isset($message) ? $message : '' }}</h3>
    @else
        @if(is_array($detail))
            <div style="padding: 4px">
                <table style="border-collapse: collapse;font-size: 15px;width: 100%;">
                    <tbody>
                    @foreach($detail as $k => $v)
                        <tr style="border: solid 1px;">
                            <th>{{ $k }}</th>
                            <td style="vertical-align:top;padding: 4px;text-align: left;word-break: break-all">{{ is_string($v) ? $v : json_encode(value($v)) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="font-weight: bold">
                {!! isset($detail) ? $detail : '' !!}
            </div>
        @endif

        <div style="padding: 4px">
            <table style="border-collapse: collapse;font-size: 15px;width: 100%;">
                <tbody>
                @foreach($meta as $k => $v)
                    <tr style="border: solid 1px;">
                        <th>{{ $k }}</th>
                        <td style="vertical-align:top;padding: 4px;text-align: left;word-break: break-all">{{ value($v) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection

