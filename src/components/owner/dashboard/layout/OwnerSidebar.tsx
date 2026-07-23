"use client";
import React, { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useSidebar } from "@/context/SidebarContext";
import { useSession } from "next-auth/react";
import {
  PieChartIcon,
  GridIcon,
  CalenderIcon,
  UserCircleIcon,
  ChevronDownIcon
} from "@/components/admin/dashboard/icons/index";

type NavItem = {
  name: string;
  icon: React.ReactNode;
  path: string;
  statsKey?: 'properties' | 'appointments';
};

const navItems: NavItem[] = [
  {
    icon: <PieChartIcon />,
    name: "Tổng quan Dashboard",
    path: "/owner/dashboard",
  },
  {
    icon: <GridIcon />,
    name: "Quản lý tin đăng",
    path: "/owner/properties",
    statsKey: "properties",
  },
  {
    icon: <CalenderIcon />,
    name: "Lịch hẹn xem nhà",
    path: "/owner/appointments",
    statsKey: "appointments",
  },
];

const OwnerSidebar: React.FC = () => {
  const { isExpanded, isMobileOpen } = useSidebar();
  const pathname = usePathname();
  const { data: session } = useSession();
  const user = session?.user as any;
  const [stats, setStats] = useState<Record<string, number>>({
    properties: 0,
    appointments: 0,
  });

  useEffect(() => {
    async function getStats() {
      try {
        const res = await fetch("/api/profile");
        if (res.ok) {
          const json = await res.json();
          if (json?.success && json?.data) {
            // Đọc đếm lịch hẹn và tin đăng từ hồ sơ cá nhân
            const propertiesCount = json.data.properties?.length || 0;
            const appointmentsCount = json.data.receivedAppointments?.length || 0;
            setStats({
              properties: propertiesCount,
              appointments: appointmentsCount,
            });
          }
        }
      } catch (err) {
        console.error("Error loading owner sidebar counts:", err);
      }
    }
    getStats();
  }, []);

  const renderMenuItems = (items: NavItem[]) => (
    <ul className="flex flex-col gap-2 text-left">
      {items.map((nav, index) => {
        const isActive = pathname === nav.path;
        return (
          <li key={index}>
            <Link
              href={nav.path}
              className={`flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition duration-150 ${
                isActive
                  ? "bg-primary text-white font-semibold"
                  : "text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-800"
              }`}
            >
              <div className="flex items-center gap-3">
                <span className={isActive ? "text-white" : "text-slate-500"}>
                  {nav.icon}
                </span>
                {isExpanded && (
                  <span className="text-sm font-medium">{nav.name}</span>
                )}
              </div>
              
              {isExpanded && nav.statsKey && stats[nav.statsKey] > 0 && (
                <span className={`text-[10px] px-2 py-0.5 rounded-full font-bold ${
                  isActive ? "bg-white/20 text-white" : "bg-slate-100 dark:bg-gray-800 text-slate-655"
                }`}>
                  {stats[nav.statsKey]}
                </span>
              )}
            </Link>
          </li>
        );
      })}
    </ul>
  );

  return (
    <aside
      className={`fixed top-0 bottom-0 left-0 z-99999 flex flex-col bg-white border-r border-gray-200 dark:border-gray-800 dark:bg-gray-900 transition-all duration-300 ease-in-out ${
        isMobileOpen
          ? "translate-x-0"
          : "max-lg:-translate-x-full"
      } ${
        isExpanded ? "w-[290px]" : "w-[90px]"
      }`}
    >
      {/* Sidebar Header (Logo) */}
      <div className="flex items-center justify-between gap-2 px-6 py-5 border-b border-gray-100 dark:border-gray-800">
        <Link href="/" className="flex items-center gap-2">
          <div className="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white font-bold">
            <i className="fa-solid fa-house-chimney text-xs"></i>
          </div>
          {isExpanded && (
            <span className="font-extrabold text-sm text-slate-800 dark:text-white">
              BDS <span className="text-primary">System</span>
            </span>
          )}
        </Link>
      </div>

      {/* Navigation Links */}
      <div className="flex-1 py-4 overflow-y-auto px-4 space-y-6">
        <div>
          {isExpanded && (
            <span className="block px-4 mb-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
              Chức năng quản trị
            </span>
          )}
          {renderMenuItems(navItems)}
        </div>
      </div>

      {/* Sidebar Footer */}
      <div className="p-4 border-t border-gray-100 dark:border-gray-800">
        <Link
          href="/"
          className={`flex items-center gap-3 px-4 py-3 rounded-xl transition duration-150 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-800 text-left`}
        >
          <span>
            <i className="fa-solid fa-arrow-left text-sm" />
          </span>
          {isExpanded && (
            <span className="text-sm font-medium">Về web chính</span>
          )}
        </Link>
      </div>
    </aside>
  );
};

export default OwnerSidebar;
