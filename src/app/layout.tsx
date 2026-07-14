import type { Metadata } from "next";
import { Plus_Jakarta_Sans } from "next/font/google";
import "./globals.css";
import { SessionProvider } from "next-auth/react"

const plusJakartaSans = Plus_Jakarta_Sans({
  subsets: ["vietnamese", "latin"],
  weight: ["400", "500", "600", "700", "800"],
  variable: "--font-sans",
})

export const metadata: Metadata = {
  title: "BDS Rental - Thuê Bất Động Sản Giá Tốt",
  description: "Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn, mặt bằng kinh doanh cho thuê uy tín, cập nhật liên tục với bộ lọc giá, diện tích thông minh.",
};

import ChatBot from "@/components/ai/ChatBot"
import ShareModal from "@/components/ShareModal"

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="vi" className={`h-full bg-slate-50 ${plusJakartaSans.variable}`}>
      <head>
        {/* Font Awesome Icons */}
        <link 
          rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          precedence="default"
        />
      </head>
      <body className="flex flex-col min-h-full font-sans antialiased text-slate-800">
        <SessionProvider>
          {children}
          <ChatBot />
          <ShareModal />
        </SessionProvider>
      </body>
    </html>
  );
}
