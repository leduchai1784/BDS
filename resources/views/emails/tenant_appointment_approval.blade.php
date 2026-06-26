<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch hẹn xem nhà của bạn đã được xác nhận</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #10b981; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 800; letter-spacing: 0.5px; }
        .content { padding: 32px 24px; }
        .welcome { font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; }
        .desc { font-size: 13px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; background-color: #f8fafc; border-radius: 12px; overflow: hidden; }
        .details-table td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .details-table tr:last-child td { border-bottom: none; }
        .details-table td.label { font-weight: 700; color: #64748b; width: 140px; }
        .details-table td.value { color: #1e293b; font-weight: 500; }
        .agent-card { background-color: #f0f9ff; border: 1px solid #e0f2fe; border-radius: 12px; padding: 16px; margin-bottom: 24px; }
        .agent-title { font-size: 13px; font-weight: 700; color: #0369a1; margin-top: 0; margin-bottom: 8px; }
        .agent-info { font-size: 13px; line-height: 1.5; color: #0c4a6e; }
        .btn-container { text-align: center; margin-top: 28px; }
        .btn { display: inline-block; background-color: #10b981; color: #ffffff !important; padding: 12px 24px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }
        .footer { background-color: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ LỊCH HẸN ĐÃ XÁC NHẬN</h1>
        </div>
        <div class="content">
            <h2 class="welcome">Xin chào {{ $appointment->name }},</h2>
            <p class="desc">Tuyệt vời! Yêu cầu đặt lịch hẹn xem bất động sản của bạn đã được chủ nhà đồng ý và xác nhận thành công.</p>
            
            <table class="details-table">
                <tr>
                    <td class="label">Bất động sản</td>
                    <td class="value">
                        <strong>{{ $appointment->property->title }}</strong><br>
                        <span style="font-size: 11px; color: #64748b;">Địa chỉ: {{ $appointment->property->address }}, {{ $appointment->property->ward }}, {{ $appointment->property->district }}, {{ $appointment->property->city }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Thời gian hẹn</td>
                    <td class="value">
                        📅 {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}<br>
                        ⏰ Khung giờ: {{ $appointment->time }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Ghi chú của bạn</td>
                    <td class="value">{{ $appointment->message ?? 'Không có ghi chú.' }}</td>
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

            <p class="desc" style="font-size: 12px; color: #64748b;">Vui lòng chuẩn bị và có mặt đúng giờ hẹn. Nếu có thay đổi đột xuất, bạn có thể gọi điện trực tiếp cho chủ nhà qua số điện thoại trên để thương lượng lại.</p>

            <div class="btn-container">
                <a href="{{ url('/profile?tab=appointments') }}" class="btn">Xem lịch hẹn của tôi</a>
            </div>
        </div>
        <div class="footer">
            Cảm ơn bạn đã lựa chọn BDS Rental!<br>
            Đây là email tự động từ hệ thống, vui lòng không trả lời trực tiếp email này.
        </div>
    </div>
</body>
</html>
