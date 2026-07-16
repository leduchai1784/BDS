import { NextResponse } from 'next/server'
import { auth } from '@/lib/auth'
import { prisma } from '@/lib/prisma'
import { GoogleGenerativeAI } from '@google/generative-ai'

function getPropertyDetailsString(property: any): string {
  return `Tiêu đề: ${property.title}
Loại hình: ${property.transactionType === 'sale' ? 'Bán' : 'Cho thuê'}
Giá: ${property.priceLabel}
Diện tích: ${property.area} m2
Địa chỉ: ${property.address}, ${property.ward}, ${property.district}, ${property.city}
Phòng ngủ: ${property.bedroom}, Phòng tắm: ${property.bathroom}
Hướng: ${property.direction || 'Không xác định'}
Nội thất: ${property.furniture || 'Không xác định'}
Pháp lý: ${property.legal || 'Sổ hồng riêng/Hợp pháp'}
Mô tả chi tiết: ${property.description}`
}

export async function POST(req: Request) {
  try {
    const session = await auth()
    if (!session?.user?.id) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const userId = Number(session.user.id)
    const body = await req.json()
    const { feature, property_id, campaign_goal, campaign_tone, freeform_data } = body

    if (!feature) {
      return NextResponse.json({ error: 'Missing feature parameter' }, { status: 400 })
    }

    let detailsString = ''
    let systemInstruction = ''
    let prompt = ''
    let maxTokens = 4096

    const apiKey = process.env.GEMINI_API_KEY || ''
    const modelName = process.env.GEMINI_MODEL || 'gemini-3.1-flash-lite'

    if (!apiKey) {
      return NextResponse.json({ error: 'Chưa cấu hình API Key cho Gemini trong file .env' }, { status: 500 })
    }

    const genAI = new GoogleGenerativeAI(apiKey)

    // ─────────────────────────────────────────────────────────────────────────
    // 1. Resolve Property Details (Mock or DB)
    // ─────────────────────────────────────────────────────────────────────────
    if (feature !== 'freeform') {
      if (!property_id || !campaign_goal || !campaign_tone) {
        return NextResponse.json({ error: 'Missing property or campaign parameters' }, { status: 400 })
      }

      let property: any = null

      if (property_id === 'mock_prop_1') {
        property = {
          title: 'Căn hộ dịch vụ Hà Đô Centrosa Quận 10',
          transactionType: 'rent',
          priceLabel: '14.5 triệu/tháng',
          area: 60,
          bedroom: 2,
          bathroom: 2,
          address: 'Đường Ba Tháng Hai, Phường 12',
          ward: 'Phường 12',
          district: 'Quận 10',
          city: 'Thành phố Hồ Chí Minh',
          furniture: 'Đầy đủ nội thất cao cấp: Tivi, Tủ lạnh, Máy giặt, Điều hòa, Giường đệm mới 100%',
          direction: 'Đông Nam',
          description: 'Căn hộ dịch vụ nằm tại tòa Jasmine thuộc dự án Hà Đô Centrosa. Thiết kế hiện đại, ban công rộng lộng gió. Tiện ích nội khu đẳng cấp: Hồ bơi vô cực, Gym miễn phí, Shophouse tiện lợi. An ninh bảo vệ và khóa thông minh 24/7.'
        }
      } else if (property_id === 'mock_prop_2') {
        property = {
          title: 'Nhà nguyên căn Hẻm xe hơi Lê Quang Định Bình Thạnh',
          transactionType: 'sale',
          priceLabel: '4.2 tỷ',
          area: 75,
          bedroom: 3,
          bathroom: 3,
          address: 'Đường Lê Quang Định, Phường 11',
          ward: 'Phường 11',
          district: 'Quận Bình Thạnh',
          city: 'Thành phố Hồ Chí Minh',
          furniture: 'Nội thất cơ bản, hệ thống đèn led thông minh, kệ bếp tủ bếp gỗ tự nhiên',
          direction: 'Tây Nam',
          description: 'Nhà phố đúc 2 lầu kiên cố, hẻm xe hơi đỗ cửa, khu phân lô dân trí cao cực kỳ yên tĩnh. Rất gần chợ Bà Chiểu, công viên Gia Định và các trường học điểm. Sổ hồng chính chủ hoàn công đầy đủ, công chứng ngay.'
        }
      } else {
        // Fetch from database
        const dbProp = await prisma.property.findUnique({
          where: { id: property_id }
        })

        if (!dbProp) {
          return NextResponse.json({ error: 'Property not found' }, { status: 404 })
        }

        if (Number(dbProp.ownerId) !== userId && session.user.role !== 'admin') {
          return NextResponse.json({ error: 'Permission denied' }, { status: 403 })
        }

        property = {
          title: dbProp.title,
          transactionType: dbProp.propertyType === 'sale' ? 'sale' : 'rent',
          priceLabel: dbProp.priceLabel,
          area: dbProp.area,
          bedroom: dbProp.bedroom,
          bathroom: dbProp.bathroom,
          address: dbProp.address,
          ward: dbProp.ward,
          district: dbProp.district,
          city: dbProp.city,
          furniture: dbProp.furniture,
          direction: dbProp.direction,
          description: dbProp.description,
          legal: dbProp.legal
        }
      }

      detailsString = getPropertyDetailsString(property)
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Set Prompt & System Instruction by Feature
    // ─────────────────────────────────────────────────────────────────────────
    if (feature === 'facebook') {
      systemInstruction = `Bạn là một chuyên gia viết Content Marketing Bất động sản hàng đầu tại Việt Nam. Nhiệm vụ của bạn là tạo ra đúng 20 bài đăng Facebook độc đáo, thu hút khách hàng dựa trên thông tin bất động sản được cung cấp.
Yêu cầu nội dung:
1. Đa dạng hóa góc nhìn cho 20 bài đăng (Ví dụ: Góc nhìn lợi ích đầu tư, góc nhìn gia đình tìm tổ ấm, góc nhìn tiện ích nội/ngoại khu, bài viết dạng danh sách tiện ích, bài đăng dạng kêu gọi khẩn cấp ưu đãi, bài review chi tiết, bài viết ngắn gọn dí dỏm, v.v.).
2. Sử dụng tiếng Việt tự nhiên, trẻ trung hoặc chuyên nghiệp phù hợp với cấu hình tone giọng và mục tiêu chiến dịch.
3. Mỗi bài viết cần chèn các emoji sinh động, hashtag phù hợp, lời kêu gọi hành động CTA rõ ràng.
4. Đầu ra bắt buộc phải là một mảng JSON chứa đúng 20 phần tử, mỗi phần tử có cấu trúc: {"id": số thứ tự từ 1 đến 20, "title": "Tiêu đề bài đăng", "content": "Nội dung chi tiết bài đăng Facebook"}.`

      prompt = `Thông tin bất động sản nguồn:
${detailsString}

Mục tiêu chiến dịch: ${campaign_goal}
Giọng điệu/Tone: ${campaign_tone}

Hãy tạo 20 bài đăng Facebook đa góc nhìn và trả về mảng JSON chuẩn.`
      maxTokens = 8192
    } else if (feature === 'tiktok') {
      systemInstruction = `Bạn là chuyên gia sáng tạo nội dung video ngắn (TikTok, YouTube Shorts, Reels) chuyên nghiệp về Bất động sản tại Việt Nam. Nhiệm vụ của bạn là soạn thảo đúng 10 kịch bản video ngắn có tính lan truyền cao dựa trên thông tin bất động sản.
Yêu cầu kịch bản:
1. Độ dài mỗi kịch bản khoảng 30 - 60 giây, kịch tính, lôi cuốn ngay từ 3 giây đầu tiên.
2. Mỗi kịch bản bao gồm:
   - id: Số thứ tự (1-10).
   - title: Tiêu đề kịch bản (ví dụ: 'Review 60s căn hộ Quận 10', 'Bí mật đằng sau căn nhà 4 tỷ').
   - visual: Mô tả phân cảnh hình ảnh/cảnh quay gợi ý cho người quay phim (đặt trong ngoặc vuông).
   - audio: Lời thoại Voiceover đầy cảm xúc, tự nhiên để đọc hoặc thu âm.
   - overlay: Các dòng chữ chạy trên màn hình (Text overlay) để giữ chân người xem.
3. Trả về dưới dạng một mảng JSON chứa đúng 10 kịch bản có định dạng: [{"id": 1, "title": "...", "visual": "...", "audio": "...", "overlay": "..."}].`

      prompt = `Thông tin bất động sản:
${detailsString}

Mục tiêu chiến dịch: ${campaign_goal}
Tone giọng: ${campaign_tone}

Hãy tạo 10 kịch bản video ngắn TikTok/Shorts.`
      maxTokens = 8192
    } else if (feature === 'seo') {
      systemInstruction = `Bạn là một chuyên gia SEO Copywriter chuyên nghiệp về Bất động sản tại Việt Nam. Hãy dựa trên thông tin bất động sản để tạo ra đúng 5 bài viết chuẩn SEO Website chất lượng cao.
Yêu cầu bài viết:
1. Mỗi bài viết cần giải quyết một chủ đề cụ thể liên quan đến bất động sản đó (Ví dụ: Phân tích tiềm năng đầu tư, Đánh giá chi tiết các tiện ích sống, Hướng dẫn pháp lý và quy trình giao dịch, So sánh giá cả khu vực, Cẩm nang định cư cho gia đình trẻ tại địa phương).
2. Định dạng bài viết: Phần thân bài viết cần được viết dưới dạng HTML chuẩn sử dụng các thẻ <h2>, <h3>, <p>, <ul>, <li> để trình bày khoa học và dễ đọc.
3. Mỗi bài viết bao gồm:
   - title: Tiêu đề bài viết thu hút click chuẩn SEO.
   - meta: Thẻ Meta Description tóm tắt nội dung hấp dẫn dưới 160 ký tự.
   - content: Nội dung bài viết định dạng HTML chi tiết.
4. Trả về dưới dạng một mảng JSON chứa đúng 5 bài viết có cấu trúc: [{"title": "...", "meta": "...", "content": "..."}].`

      prompt = `Thông tin bất động sản:
${detailsString}

Mục tiêu chiến dịch: ${campaign_goal}
Tone giọng: ${campaign_tone}

Hãy tạo 5 bài viết SEO Website định dạng HTML.`
      maxTokens = 8192
    } else if (feature === 'emailsms') {
      systemInstruction = `Bạn là một chuyên gia Email Marketing và Copywriting. Dựa trên thông tin bất động sản được cung cấp, hãy biên soạn các tài liệu marketing bổ trợ sau:
1. 1 mẫu Email chào mời khách hàng hoặc chăm sóc khách quan tâm (gồm subject và content chi tiết).
2. 3 biến thể tin nhắn SMS ngắn gọn hoặc Zalo ZNS (dưới 250 ký tự, không dấu hoặc có dấu nhưng cực kỳ súc tích kèm link demo/hotline).
3. 2 đoạn Prompt mô tả hình ảnh bằng tiếng Anh chi tiết để đưa vào các công cụ sinh ảnh AI (như Midjourney, DALL-E, Stable Diffusion) để vẽ hình ảnh minh họa cho căn nhà này.
4. Trả về dưới dạng một đối tượng JSON có cấu trúc cụ thể:
{
  "emailTemplates": [{"subject": "Tiêu đề email", "content": "Nội dung chi tiết email"}],
  "smsTemplates": ["Nội dung SMS 1", "Nội dung SMS 2", "Nội dung SMS 3"],
  "prompts": ["Prompt tiếng Anh 1", "Prompt tiếng Anh 2"]
}`

      prompt = `Thông tin bất động sản:
${detailsString}

Mục tiêu chiến dịch: ${campaign_goal}
Tone giọng: ${campaign_tone}

Hãy sinh nội dung Email, SMS và Prompts ảnh.`
      maxTokens = 4096
    } else if (feature === 'freeform') {
      if (!freeform_data) {
        return NextResponse.json({ error: 'Missing freeform data' }, { status: 400 })
      }

      systemInstruction = `Bạn là Giám đốc Sáng tạo AI Content cho một đại lý bất động sản cao cấp. Dựa trên thông tin bất động sản do người dùng nhập tự do, hãy thiết kế một gói nội dung truyền thông toàn diện.
Gói nội dung bao gồm:
1. 3 bài đăng mạng xã hội (Facebook/Zalo) chất lượng cao.
2. 2 kịch bản video TikTok ngắn (gồm title, visual, audio, overlay, video_prompt, ai_suggestion).
   - video_prompt: Đoạn prompt tiếng Anh chi tiết, mô tả kỹ góc quay, chuyển động camera chậm (camera panning, flythrough), ánh sáng điện ảnh, độ phân giải cao để đưa vào các công cụ sinh video AI (Runway Gen-3, Luma Dream Machine, Sora, Kling AI).
   - ai_suggestion: Gợi ý công cụ AI tạo video tốt nhất cho cảnh này (Ví dụ: "Runway Gen-3 (tốt nhất cho flythrough nội thất)", "Luma Dream Machine (miễn phí và tạo chuyển động mượt mà)", "Kling AI (tốt nhất cho ngoại cảnh thực tế)").
3. 1 kịch bản lời thoại thu âm (Voiceover) liền mạch, KHÔNG chứa emoji hay ký tự đặc biệt, viết bằng văn nói tự nhiên trôi chảy để máy đọc (Text-to-Speech) phát âm tiếng Việt chuẩn xác (độ dài 120-180 từ).
4. 1 đoạn Prompt vẽ ảnh bằng tiếng Anh (Dùng tạo Thumbnail căn hộ, mô tả không gian thực tế, ánh sáng, góc chụp rộng, chất lượng photorealistic, cinematic).
5. Danh sách 10 hashtags thịnh hành liên quan.
6. 1 bài viết chuẩn SEO Website ngắn (tiêu đề, mô tả meta, nội dung HTML) kèm theo danh sách 5 từ khóa SEO đề xuất.

Trả về bắt buộc là một đối tượng JSON chuẩn có cấu trúc chính xác sau:
{
  "posts": [{"id": 1, "title": "...", "content": "..."}],
  "videos": [
    {
      "id": 1, 
      "title": "...", 
      "visual": "...", 
      "audio": "...", 
      "overlay": "...",
      "video_prompt": "Prompt tiếng Anh chi tiết để sinh video AI cho kịch bản này...",
      "ai_suggestion": "Gợi ý công cụ AI khuyên dùng (ví dụ Runway Gen-3 hoặc Luma Dream Machine) kèm lý do..."
    }
  ],
  "voice_script": "nội dung văn bản thoại thu âm...",
  "thumbnail_prompt": "Đoạn prompt tiếng Anh để sinh ảnh thumbnail...",
  "hashtags": ["tag1", "tag2", ...],
  "seo": {
     "title": "Tiêu đề bài SEO",
     "meta": "Mô tả meta",
     "content": "Nội dung bài viết chuẩn SEO dạng HTML",
     "keywords": ["keyword1", "keyword2", ...]
  }
}`

      const fd = freeform_data
      detailsString = `Tên/Tiêu đề BĐS: ${fd.title}
Loại giao dịch: ${fd.transaction_type === 'rent' ? 'Cho thuê' : 'Bán'}
Loại hình: ${fd.property_type || 'Chung cư'}
Giá: ${fd.price || 'Thương lượng'}
Diện tích: ${fd.area || 'Chưa rõ'}
Địa điểm: ${fd.address}
Đặc điểm nổi bật: ${fd.highlights || 'Đầy đủ tiện nghi'}
Tone giọng AI: ${fd.tone}`

      prompt = `Thông tin nhập vào từ người dùng:
${detailsString}

Hãy tạo gói nội dung AI Content Studio.`
      maxTokens = 8192
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Call Google Gemini Generative Model
    // ─────────────────────────────────────────────────────────────────────────
    const model = genAI.getGenerativeModel({
      model: modelName,
      systemInstruction: systemInstruction
    })

    const result = await model.generateContent({
      contents: [{ role: 'user', parts: [{ text: prompt }] }],
      generationConfig: {
        temperature: 0.6,
        topP: 0.95,
        maxOutputTokens: maxTokens,
        responseMimeType: 'application/json'
      }
    })

    let replyText = result.response.text().trim()

    // Robust JSON extraction
    function extractJSON(text: string): string {
      const firstCurly = text.indexOf('{')
      const firstSquare = text.indexOf('[')
      let startIdx = -1
      let endIdx = -1

      if (firstCurly !== -1 && (firstSquare === -1 || firstCurly < firstSquare)) {
        startIdx = firstCurly
        endIdx = text.lastIndexOf('}')
      } else if (firstSquare !== -1) {
        startIdx = firstSquare
        endIdx = text.lastIndexOf(']')
      }

      if (startIdx !== -1 && endIdx !== -1 && endIdx > startIdx) {
        return text.substring(startIdx, endIdx + 1)
      }
      return text
    }

    const cleanedJson = extractJSON(replyText)
    const parsedData = JSON.parse(cleanedJson)

    return NextResponse.json({
      success: true,
      data: parsedData
    })
  } catch (error: any) {
    console.error('Gemini API Integration Error:', error)
    return NextResponse.json({ error: error.message || 'Lỗi xử lý AI Content.' }, { status: 500 })
  }
}
