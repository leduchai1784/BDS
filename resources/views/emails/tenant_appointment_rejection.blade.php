<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo lịch hẹn xem nhà bị từ chối</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #ef4444; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 800; letter-spacing: 0.5px; }
        .content { padding: 32px 24px; }
        .welcome { font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; }
        .desc { font-size: 13px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .reason-box { background-color: #fef2f2; border: 1px dashed #fca5a5; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
        .reason-title { font-size: 13px; font-weight: 700; color: #b91c1c; margin-top: 0; margin-bottom: 6px; }
        .reason-content { font-size: 13px; line-height: 1.5; color: #991b1b; font-style: italic; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; background-color: #f8fafc; border-radius: 12px; overflow: hidden; }
        .details-table td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .details-table tr:last-child td { border-bottom: none; }
        .details-table td.label { font-weight: 700; color: #64748b; width: 140px; }
        .details-table td.value { color: #1e293b; font-weight: 500; }
        .agent-card { background-color: #f0f9ff; border: 1px solid #e0f2fe; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
        .agent-title { font-size: 13px; font-weight: 700; color: #0369a1; margin-top: 0; margin-bottom: 8px; }
        .agent-info { font-size: 13px; line-height: 1.5; color: #0c4a6e; }
        .footer { background-color: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ LỊCH HẸN BỊ TỪ CHỐI</h1>
        </div>
        <div class="content">
            <h2 class="welcome">Xin chào {{ $appointment->name }},</h2>
            <p class="desc">Rất tiếc, yêu cầu đặt lịch hẹn xem bất động sản của bạn đã bị chủ nhà từ chối.</p>
            
            <div class="reason-box">
                <h3 class="reason-title">⚠️ Lý do từ chối:</h3>
                <p class="reason-content">"{{ $appointment->reject_reason ?? 'Không được cung cấp lý do cụ thể.' }}"</p>
            </div>

            <table class="details-table">
                <tr>
                    <td class="label">Bất động sản</td>
                    <td class="value">
                        <strong>{{ $appointment->property->title }}</strong><br>
                        <span style="font-size: 11px; color: #64748b;">Địa chỉ: {{ $appointment->property->address }}, {{ $appointment->property->ward }}, {{ $appointment->property->district }}, {{ $appointment->property->city }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Thời gian hẹn cũ</td>
                    <td class="value">
                        📅 {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}<br>
                        ⏰ Khung giờ: {{ $appointment->time }}
                    </td>
                </tr>
            </table>

            <div class="agent-card">
                <h3 class="agent-title">👤 Thông tin liên hệ của Chủ nhà:</h3>
                <div class="agent-info">
                    <strong>Họ tên:</strong> {{ $appointment->property->owner->name }}<br>
                    <strong>Số điện thoại:</strong> {{ $appointment->property->owner->phone }}<br>
                    @if(!empty($appointment->property->owner->email))
                    <strong>Email:</strong> {{ $appointment->property->owner->email }}
                    @endif
                </div>
            </div>

            <p class="desc">Bạn có thể liên hệ trực tiếp với chủ nhà để sắp xếp một khung giờ khác phù hợp hơn hoặc tiếp tục tìm kiếm các bất động sản khác trên hệ thống.</p>
        </div>
        <div class="footer">
            Cảm ơn bạn đã lựa chọn BDS Rental!<br>
            Đây là email tự động từ hệ thống, vui lòng không trả lời trực tiếp email này.
        </div>
    </div>
</body>
</html>
