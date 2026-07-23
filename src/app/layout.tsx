import type { Metadata } from "next";
import { Be_Vietnam_Pro } from "next/font/google";
import "./globals.css";
import { SessionProvider } from "next-auth/react";
import ChatBot from "@/components/ai/ChatBot";
import ShareModal from "@/components/ShareModal";
import { ThemeProvider } from "@/context/ThemeContext";
import { cookies } from "next/headers";

const beVietnamPro = Be_Vietnam_Pro({
  subsets: ["vietnamese", "latin"],
  weight: ["300", "400", "500", "600", "700", "800", "900"],
  variable: "--font-be-vietnam-pro",
  display: "swap",
})

export const metadata: Metadata = {
  title: "BDS Rental - Thuê Bất Động Sản Giá Tốt",
  description: "Kênh tìm kiếm phòng trọ, căn hộ chung cư, nhà nguyên căn, mặt bằng kinh doanh cho thuê uy tín, cập nhật liên tục với bộ lọc giá, diện tích thông minh.",
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const cookieStore = await cookies();
  const themeCookie = cookieStore.get("theme")?.value;
  const initialTheme = themeCookie === "dark" ? "dark" : "light";

  return (
    <html lang="vi" className={`h-full ${initialTheme === "dark" ? "dark bg-gray-950 text-white" : "bg-slate-50 text-slate-800"} ${beVietnamPro.variable}`}>
      <head>
        {/* Font Awesome Icons */}
        <link 
          rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          precedence="default"
        />
      </head>
      <body className="flex flex-col min-h-full font-sans antialiased bg-slate-50 dark:bg-gray-950 text-slate-800 dark:text-gray-100 transition-colors duration-200">
        <SessionProvider>
          <ThemeProvider initialTheme={initialTheme}>
            {children}
            <ChatBot />
            <ShareModal />
          </ThemeProvider>
        </SessionProvider>
      </body>
    </html>
  );
}
