import nodemailer from 'nodemailer'

const transporter = nodemailer.createTransport({
  host: process.env.SMTP_HOST || 'smtp.gmail.com',
  port: Number(process.env.SMTP_PORT) || 587,
  secure: Number(process.env.SMTP_PORT) === 465, // true for 465, false for other ports
  auth: {
    user: process.env.SMTP_USER || '',
    pass: process.env.SMTP_PASSWORD || ''
  }
})

interface MailOptions {
  to: string
  subject: string
  html: string
}

/**
 * Send an email using SMTP credentials
 */
export async function sendEmail({ to, subject, html }: MailOptions) {
  try {
    const fromEmail = process.env.MAIL_FROM || 'noreply@bdsrental.vn'
    const fromName = process.env.MAIL_FROM_NAME || 'BDS Rental'

    await transporter.sendMail({
      from: `"${fromName}" <${fromEmail}>`,
      to,
      subject,
      html
    })
    return { success: true }
  } catch (error: any) {
    console.error('Mail sending failed:', error.message)
    return { success: false, error: error.message }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// HTML Email Templates Builder (Port from resources/views/emails/*.blade.php)
// ─────────────────────────────────────────────────────────────────────────────

function formatTime(time: any): string {
  if (!time) return 'N/A'
  if (time instanceof Date) {
    return time.toTimeString().substring(0, 5) // Extracts "hh:mm"
  }
  if (typeof time === 'string') {
    if (time.includes('T')) {
      return time.split('T')[1].substring(0, 5)
    }
    return time.substring(0, 5)
  }
  return String(time)
}

export function getTenantConfirmationHtml(appointment: any, property: any, owner: any) {
  return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: -apple-system, sans-serif; background-color: #f8fafc; color: #334155; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
            .header { background-color: #0077bb; padding: 24px; text-align: center; color: #ffffff; }
            .content { padding: 32px 24px; }
            .details-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; background-color: #f8fafc; }
            .details-table td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
            .details-table td.label { font-weight: 700; color: #64748b; width: 140px; }
            .details-table td.value { color: #1e293b; }
            .agent-card { background-color: #e0f2fe; border: 1px solid #bae6fd; border-radius: 12px; padding: 16px; margin: 24px 0; }
            .footer { background-color: #f8fafc; padding: 16px; text-align: center; font-size: 11px; color: #94a3b8; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin:0; font-size:18px;">🏠 ĐẶT LỊCH HẸN XEM NHÀ THÀNH CÔNG</h1>
            </div>
            <div class="content">
                <h2 style="font-size:15px; color:#1e293b;">Xin chào ${appointment.name},</h2>
                <p style="font-size:13px; color:#475569;">Yêu cầu đặt lịch hẹn xem bất động sản của bạn đã được ghi nhận thành công. Dưới đây là thông tin chi tiết:</p>
                
                <table class="details-table">
                    <tr>
                        <td class="label">Bất động sản</td>
                        <td class="value">
                            <strong>${property.title}</strong><br>
                            <span style="font-size:11px; color:#64748b;">${property.address}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Thời gian hẹn</td>
                        <td class="value">📅 ${new Date(appointment.date).toLocaleDateString('vi-VN')} tại ⏰ Khung giờ: ${formatTime(appointment.time)}</td>
                    </tr>
                    <tr>
                        <td class="label">Ghi chú của bạn</td>
                        <td class="value">${appointment.message || 'Không có ghi chú.'}</td>
                    </tr>
                </table>

                <div class="agent-card">
                    <h3 style="margin:0 0 8px; font-size:13px; color:#0369a1;">👤 Thông tin Chủ nhà / Môi giới:</h3>
                    <div style="font-size:13px; color:#0c4a6e;">
                        <strong>Họ tên:</strong> ${owner?.name || 'Chủ nhà'}<br>
                        <strong>Số điện thoại:</strong> ${owner?.phone || 'Liên hệ'}<br>
                        <strong>Email:</strong> ${owner?.email || 'Liên hệ'}
                    </div>
                </div>

                <p style="font-size:12px; color:#64748b;">Chủ nhà hoặc người phụ trách sẽ chủ động liên hệ trực tiếp với bạn qua số điện thoại <strong>${appointment.phone}</strong> để xác nhận trước khi cuộc hẹn diễn ra.</p>
            </div>
            <div class="footer">
                Cảm ơn bạn đã lựa chọn BDS Rental!<br>Đây là email tự động từ hệ thống, vui lòng không trả lời trực tiếp email này.
            </div>
        </div>
    </body>
    </html>
  `
}

export function getTenantCancellationHtml(appointment: any, property: any, owner: any) {
  return `
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"></head>
    <body style="font-family:-apple-system,sans-serif; background-color:#f8fafc; color:#334155; padding:20px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden;">
            <div style="background-color:#ef4444; padding:24px; text-align:center; color:#ffffff;">
                <h1 style="margin:0; font-size:18px;">❌ LỊCH HẸN XEM NHÀ ĐÃ BỊ HỦY</h1>
            </div>
            <div style="padding:32px 24px;">
                <h2 style="font-size:15px; color:#1e293b;">Xin chào ${appointment.name},</h2>
                <p style="font-size:13px; color:#475569;">Chúng tôi xin thông báo lịch hẹn xem nhà sau đây của bạn đã bị hủy:</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Bất động sản:</strong> ${property.title}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Thời gian hẹn cũ:</strong> 📅 ${new Date(appointment.date).toLocaleDateString('vi-VN')} tại ⏰ ${formatTime(appointment.time)}</p>
                <div style="background-color:#fef2f2; border: 1px solid #fca5a5; border-radius:12px; padding:16px; margin:24px 0; font-size:13px; color:#991b1b;">
                    Lịch hẹn đã bị hủy bởi người dùng hoặc hệ thống. Nếu có thắc mắc, vui lòng liên hệ chủ nhà: <strong>${owner?.name || 'Chủ nhà'} (${owner?.phone || 'Liên hệ'})</strong>.
                </div>
            </div>
        </div>
    </body>
    </html>
  `
}

export function getTenantApprovalHtml(appointment: any, property: any, owner: any) {
  return `
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"></head>
    <body style="font-family:-apple-system,sans-serif; background-color:#f8fafc; color:#334155; padding:20px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden;">
            <div style="background-color:#10b981; padding:24px; text-align:center; color:#ffffff;">
                <h1 style="margin:0; font-size:18px;">✓ LỊCH HẸN XEM NHÀ ĐÃ ĐƯỢC CHẤP NHẬN</h1>
            </div>
            <div style="padding:32px 24px;">
                <h2 style="font-size:15px; color:#1e293b;">Xin chào ${appointment.name},</h2>
                <p style="font-size:13px; color:#475569;">Chủ nhà <strong>${owner?.name || 'Chủ nhà'}</strong> đã chấp thuận lịch hẹn xem nhà của bạn. Vui lòng đến đúng giờ hẹn:</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Bất động sản:</strong> ${property.title}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Địa chỉ:</strong> ${property.address}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Thời gian hẹn:</strong> 📅 ${new Date(appointment.date).toLocaleDateString('vi-VN')} tại ⏰ ${formatTime(appointment.time)}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>SĐT chủ nhà liên hệ:</strong> ${owner?.phone || 'Liên hệ'}</p>
            </div>
        </div>
    </body>
    </html>
  `
}

export function getTenantRejectionHtml(appointment: any, property: any, owner: any, reason: string) {
  return `
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"></head>
    <body style="font-family:-apple-system,sans-serif; background-color:#f8fafc; color:#334155; padding:20px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden;">
            <div style="background-color:#f59e0b; padding:24px; text-align:center; color:#ffffff;">
                <h1 style="margin:0; font-size:18px;">⚠️ LỊCH HẸN BỊ TỪ CHỐI</h1>
            </div>
            <div style="padding:32px 24px;">
                <h2 style="font-size:15px; color:#1e293b;">Xin chào ${appointment.name},</h2>
                <p style="font-size:13px; color:#475569;">Lịch hẹn xem nhà sau đây của bạn đã bị từ chối bởi chủ nhà:</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Bất động sản:</strong> ${property.title}</p>
                <div style="background-color:#fffbeb; border: 1px solid #fde68a; border-radius:12px; padding:16px; margin:24px 0; font-size:13px; color:#b45309;">
                    <strong>Lý do từ chối:</strong> ${reason || 'Không có lý do cụ thể.'}
                </div>
                <p style="font-size:12px; color:#64748b;">Bạn có thể liên hệ chủ nhà qua SĐT: <strong>${owner?.phone || 'Liên hệ'}</strong> để thỏa thuận lại lịch hẹn khác.</p>
            </div>
        </div>
    </body>
    </html>
  `
}

export function getOwnerNotificationHtml(appointment: any, property: any) {
  return `
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"></head>
    <body style="font-family:-apple-system,sans-serif; background-color:#f8fafc; color:#334155; padding:20px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden;">
            <div style="background-color:#0077bb; padding:24px; text-align:center; color:#ffffff;">
                <h1 style="margin:0; font-size:18px;">🔔 CÓ LỊCH HẸN XEM NHÀ MỚI</h1>
            </div>
            <div style="padding:32px 24px;">
                <p style="font-size:13px; color:#475569;">Bạn nhận được yêu cầu đặt lịch hẹn xem nhà từ khách hàng:</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Bất động sản:</strong> ${property.title}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Khách hàng:</strong> ${appointment.name} (${appointment.phone})</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Thời gian hẹn:</strong> 📅 ${new Date(appointment.date).toLocaleDateString('vi-VN')} tại ⏰ ${formatTime(appointment.time)}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Lời nhắn:</strong> ${appointment.message || 'Không có.'}</p>
                <p style="font-size:12px; color:#64748b;">Vui lòng truy cập trang cá nhân của bạn trên hệ thống để duyệt hoặc từ chối lịch hẹn này.</p>
            </div>
        </div>
    </body>
    </html>
  `
}

export function getOwnerCancellationHtml(appointment: any, property: any) {
  return `
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"></head>
    <body style="font-family:-apple-system,sans-serif; background-color:#f8fafc; color:#334155; padding:20px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden;">
            <div style="background-color:#ef4444; padding:24px; text-align:center; color:#ffffff;">
                <h1 style="margin:0; font-size:18px;">❌ LỊCH HẸN XEM NHÀ ĐÃ BỊ HỦY</h1>
            </div>
            <div style="padding:32px 24px;">
                <p style="font-size:13px; color:#475569;">Lịch hẹn xem nhà từ khách hàng <strong>${appointment.name}</strong> đã bị hủy:</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Bất động sản:</strong> ${property.title}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Thời gian cũ:</strong> 📅 ${new Date(appointment.date).toLocaleDateString('vi-VN')} tại ⏰ ${formatTime(appointment.time)}</p>
            </div>
        </div>
    </body>
    </html>
  `
}

export function getAdminNotificationHtml(appointment: any, property: any, owner: any) {
  return `
    <!DOCTYPE html>
    <html>
    <head><meta charset="utf-8"></head>
    <body style="font-family:-apple-system,sans-serif; background-color:#f8fafc; color:#334155; padding:20px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden;">
            <div style="background-color:#0f172a; padding:24px; text-align:center; color:#ffffff;">
                <h1 style="margin:0; font-size:18px;">🔔 [ADMIN] CÓ LỊCH HẸN MỚI</h1>
            </div>
            <div style="padding:32px 24px;">
                <p style="font-size:13px; color:#475569;">Hệ thống ghi nhận cuộc hẹn mới giữa tenant và owner:</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Bất động sản:</strong> ${property.title}</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Khách hàng:</strong> ${appointment.name} (${appointment.phone} - ${appointment.email})</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Chủ nhà:</strong> ${owner?.name || 'Chủ nhà'} (${owner?.phone || 'Liên hệ'} - ${owner?.email || 'Liên hệ'})</p>
                <p style="font-size:13px; color:#1e293b;"><strong>Thời gian:</strong> 📅 ${new Date(appointment.date).toLocaleDateString('vi-VN')} tại ⏰ ${formatTime(appointment.time)}</p>
            </div>
        </div>
    </body>
    </html>
  `
}
