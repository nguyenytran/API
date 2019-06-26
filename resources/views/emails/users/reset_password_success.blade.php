@extends('emails.master')

@section('title')
    Prime Commerce
@stop

@section('description')
    Password has been reset successfully
@stop

@section('content')
    <table style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%;color: #0a0a0a;">
        <tbody>
        <tr style="padding:0;text-align:left;vertical-align:top">
            <th style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:16px;text-align:left;width:564px">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;">
                    <tbody>
                    <tr style="padding:0;text-align:left;vertical-align:top;">
                        <th style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                            <div style="color:#0a0a0a;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0 5px 0 5px!important;text-align:left">
                                <p style="margin: 0;font-size: 14px;">Hi, <strong>{{ ucwords($user->name) }}</strong></p>
                                <br>
                                <p style="margin: 0;font-size: 14px;">
                                    Your new password has been set.
                                </p>
                                <br>
                                <p style="margin: 0;font-size: 14px">
                                    You can now access your account or change your account settings.
                                </p>
                                <br>
                                <p style="margin: 0;font-size: 14px;;">
                                    Regards,
                                </p>
                                <p style="margin: 0;font-size: 16px;;">
                                    <strong>The Prime KOL Platform Team</strong>
                                </p>
                            </div>
                        </th>
                        <th style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;visibility:hidden;width:0"></th>
                    </tr>
                    </tbody>
                </table>
            </th>
        </tr>
        </tbody>
    </table>
@stop
