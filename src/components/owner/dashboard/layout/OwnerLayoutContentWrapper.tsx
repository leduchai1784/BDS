"use client";

import React from "react";
import { useSidebar } from "@/context/SidebarContext";
import OwnerHeader from "./OwnerHeader";
import OwnerSidebar from "./OwnerSidebar";
import Backdrop from "@/components/admin/dashboard/layout/Backdrop";

export default function OwnerLayoutContentWrapper({ children }: { children: React.ReactNode }) {
  const { isExpanded, isHovered, isMobileOpen } = useSidebar();

  const mainContentMargin = isMobileOpen
    ? "ml-0"
    : isExpanded
    ? "lg:ml-[290px]"
    : "lg:ml-[90px]";

  return (
    <div className="min-h-screen xl:flex bg-slate-50 dark:bg-gray-950 text-slate-800 dark:text-slate-200 font-sans">
      <OwnerSidebar />
      <Backdrop />
      <div className={`flex-1 transition-all duration-300 ease-in-out ${mainContentMargin}`}>
        <OwnerHeader />
        <div className="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 text-left">{children}</div>
      </div>
    </div>
  );
}
