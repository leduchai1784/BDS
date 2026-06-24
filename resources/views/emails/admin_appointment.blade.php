<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch hẹn xem nhà mới</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #0077bb; padding: 24px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 18px; font-weight: 800; letter-spacing: 0.5px; }
        .content { padding: 32px 24px; }
        .welcome { font-size: 15px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 12px; }
        .desc { font-size: 13px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; background-color: #f8fafc; border-radius: 12px; overflow: hidden; }
        .details-table td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .details-table tr:last-child td { border-bottom: none; }
        .details-table td.label { font-weight: 700; color: #64748b; width: 140px; }
        .details-table td.value { color: #1e293b; font-weight: 500; }
        .btn-container { text-align: center; margin-top: 28px; }
        .btn { display: inline-block; background-color: #0077bb; color: #ffffff !important; padding: 12px 24px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 119, 187, 0.2); }
        .footer { background-color: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 LỊCH HẸN XEM NHÀ MỚI</h1>
        </div>
        <div class="content">
            <h2 class="welcome">Xin chào Ban Quản Trị,</h2>
            <p class="desc">Hệ thống BDS Rental vừa ghi nhận một yêu cầu đặt lịch hẹn xem nhà mới từ khách hàng. Dưới đây là thông tin chi tiết:</p>
            
            <table class="details-table">
                <tr>
                    <td class="label">Bất động sản</td>
                    <td class="value">
                        <strong>{{ $appointment->property->title }}</strong><br>
                        <span style="font-size: 11px; color: #64748b;">Địa chỉ: {{ $appointment->property->address }}, {{ $appointment->property->ward }}, {{ $appointment->property->district }}, {{ $appointment->property->city }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Họ và tên khách</td>
                    <td class="value">{{ $appointment->name }}</td>
                </tr>
                <tr>
                    <td class="label">Số điện thoại</td>
                    <td class="value">{{ $appointment->phone }}</td>
                </tr>
                <tr>
                    <td class="label">Email khách hàng</td>
                    <td class="value">{{ $appointment->email }}</td>
                </tr>
                <tr>
                    <td class="label">Thời gian xem nhà</td>
                    <td class="value">
                        📅 {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}<br>
                        ⏰ Khung giờ: {{ $appointment->time }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Ghi chú</td>
                    <td class="value">{{ $appointment->message ?? 'Không có ghi chú.' }}</td>
                </tr>
            </table>

            <div class="btn-container">
                <a href="{{ url('/admin/appointments') }}" class="btn">Quản lý lịch hẹn trên hệ thống</a>
            </div>
        </div>
        <div class="footer">
            Đây là email tự động từ hệ thống BDS Rental. Vui lòng không trả lời trực tiếp email này.
        </div>
    </div>
</body>
</html>
