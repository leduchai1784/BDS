"use client";

import React from "react";
import { SidebarProvider, useSidebar } from "@/context/SidebarContext";
import AppHeader from "@/components/admin/dashboard/layout/AppHeader";
import AppSidebar from "@/components/admin/dashboard/layout/AppSidebar";
import Backdrop from "@/components/admin/dashboard/layout/Backdrop";

import { ThemeProvider } from "@/context/ThemeContext";

function DashboardLayoutContent({ children }: { children: React.ReactNode }) {
  const { isExpanded, isHovered, isMobileOpen } = useSidebar();

  // Dynamic class for main content margin based on sidebar state
  const mainContentMargin = isMobileOpen
    ? "ml-0"
    : isExpanded || isHovered
    ? "lg:ml-[290px]"
    : "lg:ml-[90px]";

  return (
    <div className="min-h-screen xl:flex bg-slate-50 text-slate-800 font-sans">
      {/* Sidebar and Backdrop */}
      <AppSidebar />
      <Backdrop />
      {/* Main Content Area */}
      <div
        className={`flex-1 transition-all duration-300 ease-in-out ${mainContentMargin}`}
      >
        {/* Header */}
        <AppHeader />
        {/* Page Content */}
        <div className="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 text-left">{children}</div>
      </div>
    </div>
  );
}

export default function AdminDashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <ThemeProvider>
      <SidebarProvider>
        <DashboardLayoutContent>{children}</DashboardLayoutContent>
      </SidebarProvider>
    </ThemeProvider>
  );
}
