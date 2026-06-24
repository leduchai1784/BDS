<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khách hàng đã hủy lịch hẹn xem nhà</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #f97316; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 800; letter-spacing: 0.5px; }
        .content { padding: 32px 24px; }
        .welcome { font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; }
        .desc { font-size: 13px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; background-color: #f8fafc; border-radius: 12px; overflow: hidden; }
        .details-table td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .details-table tr:last-child td { border-bottom: none; }
        .details-table td.label { font-weight: 700; color: #64748b; width: 140px; }
        .details-table td.value { color: #1e293b; font-weight: 500; }
        .footer { background-color: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ THÔNG BÁO HỦY LỊCH HẸN</h1>
        </div>
        <div class="content">
            <h2 class="welcome">Xin chào {{ $appointment->property->owner->name }},</h2>
            <p class="desc">BDS Rental xin thông báo: Khách hàng đã hủy lịch hẹn xem nhà cho tin đăng của bạn. Chi tiết lịch hẹn bị hủy như sau:</p>
            
            <table class="details-table">
                <tr>
                    <td class="label">Bất động sản</td>
                    <td class="value">
                        <strong>{{ $appointment->property->title }}</strong><br>
                        <span style="font-size: 11px; color: #64748b;">Địa chỉ: {{ $appointment->property->address }}, {{ $appointment->property->ward }}, {{ $appointment->property->district }}, {{ $appointment->property->city }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Thời gian hẹn ban đầu</td>
                    <td class="value">
                        📅 {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}<br>
                        ⏰ Khung giờ: {{ $appointment->time }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Khách hàng đặt lịch</td>
                    <td class="value">
                        <strong>{{ $appointment->name }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="label">Lý do hủy</td>
                    <td class="value" style="color: #f97316; font-weight: bold;">Khách thuê hủy lịch hẹn</td>
                </tr>
            </table>

            <p class="desc">Bạn không cần phải thực hiện bất kỳ hành động nào khác. Cuộc hẹn này đã tự động được đánh dấu là đã hủy trên hệ thống.</p>
        </div>
        <div class="footer">
            Cảm ơn bạn đã hợp tác cùng BDS Rental!<br>
            Đây là email tự động từ hệ thống, vui lòng không trả lời trực tiếp email này.
        </div>
    </div>
</body>
</html>
