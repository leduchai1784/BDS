'use client'

import { useState } from 'react'
import Link from 'next/link'

interface Article {
  title: string
  image: string
  date: string
  slug: string
}

export default function NewsTabs() {
  const [activeTab, setActiveTab] = useState('baocao')

  const tabData: Record<string, Article[]> = {
    baocao: [
      {
        title: 'Báo cáo thị trường căn hộ cho thuê TP.HCM Quý 2/2026',
        image: 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=600&q=80',
        date: '28/06/2026',
        slug: 'bao-cao-thi-truong-can-ho-cho-thue-tphcm-quy-2-2026'
      },
      {
        title: 'Xu hướng dịch chuyển dòng vốn bất động sản nửa cuối năm 2026',
        image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=600&q=80',
        date: '25/06/2026',
        slug: 'xu-huong-dich-chuyen-dong-von-bat-dong-san-nua-cuoi-nam-2026'
      },
      {
        title: 'Báo cáo tiêu chuẩn sống và xu hướng lựa chọn căn hộ của giới trẻ',
        image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
        date: '18/06/2026',
        slug: 'bao-cao-tieu-chuan-song-va-xu-huong-lua-chon-can-ho-cua-gioi-tre'
      },
      {
        title: 'Các Yếu Tố Ảnh Hưởng Đến Giá Trị Bất Động Sản Năm 2026',
        image: 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=600&q=80',
        date: '28/06/2026',
        slug: 'cac-yeu-to-anh-huong-den-gia-tri-bat-dong-san-nam-2026'
      }
    ],
    gocnhin: [
      {
        title: 'Góc Nhìn NKS: Căn Hộ Studio Quận 7 Đang Dần Chiếm Lĩnh Phân Khúc Cho Thuê',
        image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=600',
        date: '27/06/2026',
        slug: 'goc-nhin-nks-can-ho-studio-quan-7-dang-dan-chiem-linh-phan-khuc-cho-thue'
      },
      {
        title: 'Góc nhìn NKS: Thị trường bất động sản cuối năm 2026 sẽ đi về đâu?',
        image: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&q=80&w=600',
        date: '26/06/2026',
        slug: 'goc-nhin-nks-thi-truong-bat-dong-san-cuoi-nam-2026-se-di-ve-dau'
      },
      {
        title: 'Làm thế nào để tối ưu hóa doanh thu từ căn hộ dịch vụ cho thuê?',
        image: 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80',
        date: '29/06/2026',
        slug: 'lam-the-nao-de-toi-uu-hoa-doanh-thu-tu-can-ho-dich-vu-cho-thue'
      },
      {
        title: 'Đánh giá tiềm năng tăng trưởng của các căn hộ ven sông Sài Gòn',
        image: 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=600&q=80',
        date: '20/06/2026',
        slug: 'danh-gia-tiem-nang-tang-truong-cua-cac-can-ho-ven-song-sai-gon'
      }
    ],
    noithat: [
      {
        title: '5 xu hướng thiết kế nội thất căn hộ Studio tối giản năm 2026',
        image: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=600&q=80',
        date: '01/07/2026',
        slug: '5-xu-huong-thiet-ke-noi-that-can-ho-studio-toi-gian-nam-2026'
      },
      {
        title: 'Bí quyết lựa chọn vật liệu chống ẩm mốc cho toilet căn hộ dịch vụ',
        image: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
        date: '27/06/2026',
        slug: 'bi-quyet-lua-chong-vat-lieu-chong-am-moc-cho-toilet-can-ho-dich-vu'
      },
      {
        title: 'Cách bài trí hệ thống chiếu sáng giúp không gian sống trở nên ấm cúng',
        image: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80',
        date: '15/06/2026',
        slug: 'cach-bai-tri-he-thong-chieu-sang-giup-khong-gian-song-tro-nen-am-cung'
      },
      {
        title: 'Bố trí sofa phòng khách thông minh cho căn hộ nhỏ hẹp',
        image: 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=600&q=80',
        date: '26/06/2026',
        slug: 'bo-tri-sofa-phong-khach-thong-minh-cho-can-ho-nho-hep'
      }
    ],
    phongthuy: [
      {
        title: 'Phong thủy phòng ngủ: Những lỗi đại kỵ cần tránh khi bố trí giường',
        image: 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80',
        date: '03/07/2026',
        slug: 'phong-thuy-phong-ngu-nhung-loi-dai-ky-can-tranh-khi-bo-tri-giuong'
      },
      {
        title: 'Lựa chọn hướng nhà và màu sơn hợp tuổi mệnh Thổ năm Bính Ngọ 2026',
        image: 'https://images.unsplash.com/photo-1513584684374-8bab748fbf90?auto=format&fit=crop&w=600&q=80',
        date: '28/06/2026',
        slug: 'lua-chon-huong-nha-va-mau-son-hop-tuoi-menh-tho-nam-binh-ngo-2026'
      },
      {
        title: 'Bố trí cây xanh hợp phong thủy giúp thu hút vượng khí cho phòng khách',
        image: 'https://images.unsplash.com/photo-1530018607912-eff2daa1bac4?auto=format&fit=crop&w=600&q=80',
        date: '19/06/2026',
        slug: 'bo-tri-cay-xanh-hop-phong-thuy-giup-thu-hut-vuong-khi-cho-phong-khach'
      },
      {
        title: 'Cách hóa giải gương đối diện cửa phòng ngủ chuẩn phong thủy',
        image: 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80',
        date: '26/06/2026',
        slug: 'cach-hoa-giai-guong-doi-dien-cua-phong-ngu-chuan-phong-thuy'
      }
    ],
    tintuc: [
      {
        title: 'Đề xuất quy định mới về quản lý chung cư mini cho thuê từ Bộ Xây dựng',
        image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80',
        date: '02/07/2026',
        slug: 'de-xuat-quy-dinh-moi-ve-quan-ly-chung-cu-mini-cho-thue-tu-bo-xay-dung'
      },
      {
        title: 'Phát triển nhà ở xã hội: Lời giải cho bài toán nhà ở vừa túi tiền',
        image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=600&q=80',
        date: '29/06/2026',
        slug: 'phat-trien-nha-o-xa-hoi-loi-giai-cho-bai-toan-nha-o-vua-tui-tien'
      },
      {
        title: 'Lãi suất vay mua nhà hạ nhiệt: Cơ hội vàng cho người mua ở thực',
        image: 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
        date: '25/06/2026',
        slug: 'lai-suat-vay-mua-nha-ha-nhiet-co-hoi-vang-cho-nguoi-mua-o-thuc'
      },
      {
        title: 'Bản đồ quy hoạch đô thị TP.HCM đến năm 2030 có gì mới?',
        image: 'https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=600&q=80',
        date: '20/06/2026',
        slug: 'ban-do-quy-hoach-do-thi-tphcm-den-nam-2030-co-gi-moi'
      }
    ],
    kienthuc: [
      {
        title: 'Các điều khoản bắt buộc phải có trong hợp đồng thuê nhà để tránh rủi ro',
        image: 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=600&q=80',
        date: '01/07/2026',
        slug: 'cac-dieu-khoan-bat-buoc-phai-co-trong-hop-dong-thue-nha-de-tranh-rui-ro'
      },
      {
        title: 'Kinh nghiệm tìm phòng trọ sinh viên giá rẻ, an ninh tốt gần trường học',
        image: 'https://images.unsplash.com/photo-1527853787696-f7be74f2e39a?auto=format&fit=crop&w=600&q=80',
        date: '28/06/2026',
        slug: 'kinh-nghiem-tim-phong-tro-sinh-vien-gia-re-an-ninh-tot-gan-truong-hoc'
      },
      {
        title: 'Nghĩa vụ đóng thuế khi cho thuê nhà: Hướng dẫn chi tiết cách kê khai',
        image: 'https://images.unsplash.com/photo-1554224154-26032ffc0d07?auto=format&fit=crop&w=600&q=80',
        date: '24/06/2026',
        slug: 'nghia-vu-dong-thue-khi-cho-thue-nha-huong-dan-chi-tiet-cach-ke-khai'
      },
      {
        title: 'Mẹo phát hiện camera giấu kín khi thuê phòng trọ hoặc căn hộ mới',
        image: 'https://images.unsplash.com/photo-1557597774-9d273605dfa9?auto=format&fit=crop&w=600&q=80',
        date: '22/06/2026',
        slug: 'meo-phat-hien-camera-giau-kin-khi-thue-phong-tro-hoac-can-ho-moi'
      }
    ]
  }

  const tabs = [
    { key: 'baocao', label: 'Báo cáo Thị trường BĐS Việt Nam' },
    { key: 'gocnhin', label: 'Góc Nhìn NKS' },
    { key: 'noithat', label: 'Nội Thất' },
    { key: 'phongthuy', label: 'Phong Thủy' },
    { key: 'tintuc', label: 'Tin Tức' },
    { key: 'kienthuc', label: 'Kiến Thức' }
  ]

  const activeArticles = tabData[activeTab] || []

  return (
    <section id="news" className="py-16 bg-white text-left">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header: Title and View More */}
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl md:text-3xl font-extrabold text-[#0f172a]">Tin tức bất động sản</h2>
          <Link href="/news" className="text-sm font-semibold text-[#556070] hover:text-primary transition flex items-center gap-1 cursor-pointer">
            Xem thêm <i className="fa-solid fa-chevron-right text-[10px]"></i>
          </Link>
        </div>

        {/* Tab Buttons */}
        <div className="flex items-center justify-start gap-2.5 overflow-x-auto pb-4 mb-8 scrollbar-none" style={{ msOverflowStyle: 'none', scrollbarWidth: 'none' }}>
          {tabs.map(tab => (
            <button 
              key={tab.key}
              onClick={() => setActiveTab(tab.key)} 
              className={`px-5 py-2.5 rounded-full text-xs transition duration-200 whitespace-nowrap cursor-pointer ${
                activeTab === tab.key 
                  ? 'bg-primary text-white font-bold shadow-sm' 
                  : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>

        {/* Tab Contents */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {activeArticles.map((article, idx) => (
            <Link 
              key={idx}
              href={`/news/${article.slug}`}
              className="bg-white rounded-[24px] overflow-hidden border border-slate-100 hover:shadow-lg transition-all duration-300 flex flex-col h-full cursor-pointer group"
            >
              <div className="h-44 w-full overflow-hidden bg-slate-100 flex-shrink-0">
                <img 
                  src={article.image} 
                  alt={article.title} 
                  className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
                />
              </div>
              <div className="p-4 flex flex-col flex-grow text-left">
                <span className="text-[10px] text-slate-400 font-bold mb-2 block">{article.date}</span>
                <h4 className="text-xs font-bold text-slate-800 line-clamp-3 group-hover:text-primary transition duration-150 leading-snug mb-2 flex-grow">
                  {article.title}
                </h4>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  )
}
