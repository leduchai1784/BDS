"use client";

import React from "react";
import { useSidebar } from "@/context/SidebarContext";
import AppHeader from "@/components/admin/dashboard/layout/AppHeader";
import AppSidebar from "@/components/admin/dashboard/layout/AppSidebar";
import Backdrop from "@/components/admin/dashboard/layout/Backdrop";

export default function AdminLayoutContentWrapper({ children }: { children: React.ReactNode }) {
  const { isExpanded, isHovered, isMobileOpen } = useSidebar();

  const mainContentMargin = isMobileOpen
    ? "ml-0"
    : isExpanded
    ? "lg:ml-[290px]"
    : "lg:ml-[90px]";

  return (
    <div className="min-h-screen xl:flex bg-slate-50 dark:bg-gray-950 text-slate-800 dark:text-slate-200 font-sans">
      <AppSidebar />
      <Backdrop />
      <div className={`flex-1 transition-all duration-300 ease-in-out ${mainContentMargin}`}>
        <AppHeader />
        <div className="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 text-left">{children}</div>
      </div>
    </div>
  );
}
