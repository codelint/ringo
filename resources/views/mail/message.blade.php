<?php date_default_timezone_set('Asia/Shanghai'); ?>
@if(!isset($detail))
    <h3>{{ isset($mail_message) ? $mail_message : '' }}</h3>
@else
    @if(is_array($detail))
        @if(isset($detail[0]) && is_array($detail[0]))
            <?php $fields = array_keys($detail[0]) ?>
            <div style="padding: 4px;overflow-x: scroll">
                <table style="border-collapse: collapse;font-size: 15px;width: 100%;">
                    <thead>
                        <tr>
                            @foreach($fields as $field)
                            <td style="white-space:pre;border:solid 1px;vertical-align:top;padding: 4px;text-align: center;font-weight: bold;background-color: #f8f8f8">{{$field}}</td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($detail as $item)
                        <tr>
                            @foreach($fields as $field)
                            <td style="border: solid 1px;vertical-align:top;padding: 4px;text-align: center">{{ is_string($item[$field]) ? $item[$field] : json_encode(value($item[$field])) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
        <div style="padding: 4px">
            <table style="border-collapse: collapse;font-size: 15px;width: 100%;">
                <tbody>
                @foreach($detail as $k => $v)
                    <tr style="border: solid 1px;">
                        <td style="vertical-align:top;width:20%;padding: 4px;text-align: right;font-weight: bold;background-color: #f8f8f8">{{ $k }}</td>
                        <td style="vertical-align:top;padding: 4px;text-align: left">{{ is_string($v) ? $v : json_encode(value($v)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
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
                <td style="vertical-align:top;width:20%;padding: 4px;text-align: right;font-weight: bold;background-color: #f8f8f8">{{ $k }}</td>
                <td style="vertical-align:top;padding: 4px;text-align: left">{{ value($v) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
