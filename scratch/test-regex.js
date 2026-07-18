const testMessages = [
  "Tôi tên là Nguyễn Văn A, sđt 0912345678",
  "mình là Trần Thị B nha, điện thoại 0839888222",
  "Em là Lan, sđt: 0909090909",
  "Tên tôi là Lê Hoàng Nam. số đt 0977888999",
  "Tôi tên Hoàng, phone 0901112222",
  "Tôi muốn tìm căn hộ Quận 10. Mình tên Lan Anh. số 0988888888",
  "Khách hàng cung cấp sđt 0912341234" // No name scenario
]

const namePatterns = [
  /(?:tên\s+là|tôi\s+tên\s+là|mình\s+tên\s+là|em\s+tên\s+là|tên\s+của\s+tôi\s+là)\s+([A-ZÀ-Ỹ][a-zà-ỹ\w]*(?:\s+[A-ZÀ-Ỹ][a-zà-ỹ\w]*){0,3})/i,
  /(?:tôi\s+là|mình\s+là|em\s+là|anh\s+là|chị\s+là)\s+([A-ZÀ-Ỹ][a-zà-ỹ\w]*(?:\s+[A-ZÀ-Ỹ][a-zà-ỹ\w]*){0,3})/i,
  /(?:tôi\s+tên|mình\s+tên|em\s+tên|anh\s+tên|chị\s+tên)\s+([A-ZÀ-Ỹ][a-zà-ỹ\w]*(?:\s+[A-ZÀ-Ỹ][a-zà-ỹ\w]*){0,3})/i,
]

console.log("=== REGEX TEST RESULTS ===")
for (const msg of testMessages) {
  let name = ''
  for (const pattern of namePatterns) {
    const match = msg.match(pattern)
    if (match) {
      name = match[1].trim()
      break
    }
  }
  if (!name) {
    const phoneContextMatch = msg.match(new RegExp(`\\b([A-ZÀ-Ỹ][a-zà-ỹ\\w]*(?:\\s+[A-ZÀ-Ỹ][a-zà-ỹ\\w]*){0,3})\\s*[,.-]?\\s*(?:là\\s+)?(?:số|sđt|đt|lấy\\s+số|điện\\s+thoại|phone|zalo|zlo)\\b`, 'i'))
    if (phoneContextMatch) {
      name = phoneContextMatch[1].trim()
    }
  }
  
  // Clean up potential trailing non-name words like "nha", "nhé"
  if (name) {
    name = name.replace(/\s+(?:nha|nhé|nhe|nha\s+nha|nha\s+nhe|nhe\s+nha|nhe\s+nhe|nhé\s+nhé)$/i, '')
  }

  console.log(`Msg: "${msg}" => Extracted Name: "${name || 'KH ' + (msg.match(/(?:\+?84|0)(?:\s*[\d.-]){9,10}\b/)?.[0] || 'Unknown')}"`)
}
