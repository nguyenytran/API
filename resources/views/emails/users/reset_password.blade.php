@extends('emails.master')

@section('title')
    Prime Commerce
@stop

@section('description')
    Reset Password
@stop

@section('content')
    <table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%; color: #0a0a0a;">
        <tbody>
        <tr style="padding:0;text-align:left;vertical-align:top">
            <th style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0;padding-left:16px;padding-right:16px;text-align:left;width:564px">
                <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                    <tbody>
                    <tr style="padding:0;text-align:left;vertical-align:top">
                        <th style="color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">
                            <div style="color:#0a0a0a;display:block;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0 5px 0 5px!important;text-align:left">
                                <p style="margin: 0;font-size: 14px;color:#0a0a0a;">Hi <strong>{{ ucwords($user->name) }}</strong>,</p>
                                <br>
                                <p style="margin: 0;font-size: 14px;color:#0a0a0a;">
                                    You recently requested to reset password for your <strong>{{ ucwords($user->username) }}</strong>
                                    account.
                                </p>
                                <br>
                                <table cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
                                        <td width="94"></td>
                                        <td width="140">
                                            <div style="text-align:center; background-color:#28a745;">
                                                <a href="{{ getFrontendBaseUri() . '/reset-password/' . $token . '?email=' . $email }}" style="display: block;">
                                                    <table cellspacing="0" cellpadding="0" width="100%">
                                                        <tr>
                                                            <td style="background-color:#28a745;border-radius:5px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:35px;text-align:center;text-decoration:none;-webkit-text-size-adjust:none;mso-hide:all;font-weight: bold;">
                                                                <span style="color:#ffffff">Reset My Password</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </a>
                                            </div>
                                        </td>
                                        <td width="94"></td>
                                    </tr>
                                </table>
                                <br>
                                <p style="margin: 0;font-size: 14px;color:#0a0a0a;">
                                    If you did not request a password reset, please ignore this email or contact us for support.
                                </p>
                                <br>
                                <p style="margin: 0;font-size: 14px;color:#0a0a0a;">Regards,</p>
                                <p style="margin: 0;font-size: 14px;color:#0a0a0a;"><strong>The Prime KOL Platform Team</strong></p>
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
