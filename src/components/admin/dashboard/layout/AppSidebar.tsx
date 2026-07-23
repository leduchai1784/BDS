"use client";
import React, { useEffect, useRef, useState,useCallback } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useSidebar } from "@/context/SidebarContext";
import {
  BoxCubeIcon,
  CalenderIcon,
  ChevronDownIcon,
  GridIcon,
  HorizontaLDots,
  ListIcon,
  PageIcon,
  PieChartIcon,
  PlugInIcon,
  TableIcon,
  UserCircleIcon,
} from "../icons/index";


type NavItem = {
  name: string;
  icon: React.ReactNode;
  path?: string;
  statsKey?: 'users' | 'properties' | 'appointments' | 'leads';
  subItems?: { name: string; path: string; pro?: boolean; new?: boolean }[];
};

const overviewItems: NavItem[] = [
  {
    icon: <PieChartIcon />,
    name: "Dashboard",
    path: "/admin/dashboard",
  },
];

const managementItems: NavItem[] = [
  {
    icon: <UserCircleIcon />,
    name: "Quản lý thành viên",
    path: "/admin/users",
    statsKey: "users",
  },
  {
    icon: <GridIcon />,
    name: "Quản lý tin đăng",
    path: "/admin/properties",
    statsKey: "properties",
  },
  {
    icon: <CalenderIcon />,
    name: "Quản lý lịch hẹn",
    path: "/admin/appointments",
    statsKey: "appointments",
  },
  {
    icon: <PageIcon />,
    name: "Quản lý Lead",
    path: "/admin/leads",
    statsKey: "leads",
  },
  {
    icon: <TableIcon />,
    name: "Báo cáo & Thống kê",
    path: "/admin/reports",
  },
];

const othersItems: NavItem[] = [];

const AppSidebar: React.FC = () => {
  const { isExpanded, isMobileOpen } = useSidebar();
  const pathname = usePathname();
  const [stats, setStats] = useState<Record<string, number>>({
    users: 0,
    properties: 0,
    appointments: 0,
    leads: 0,
  });

  useEffect(() => {
    async function getStats() {
      try {
        const res = await fetch("/api/admin/dashboard-stats");
        if (res.ok) {
          const data = await res.json();
          if (data?.success && data?.stats) {
            setStats(data.stats);
          }
        }
      } catch (err) {
        console.error("Error loading sidebar counts:", err);
      }
    }
    getStats();
  }, []);

  const renderMenuItems = (
    navItems: NavItem[],
    menuType: "main" | "others"
  ) => (
    <ul className="flex flex-col gap-4">
      {navItems.map((nav, index) => (
        <li key={nav.name}>
          {nav.subItems ? (
            <button
              onClick={() => handleSubmenuToggle(index, menuType)}
              className={`menu-item group  ${
                openSubmenu?.type === menuType && openSubmenu?.index === index
                  ? "menu-item-active"
                  : "menu-item-inactive"
               } cursor-pointer ${
                !isExpanded
                  ? "lg:justify-center"
                  : "lg:justify-start"
              }`}
            >
              <span
                className={` ${
                  openSubmenu?.type === menuType && openSubmenu?.index === index
                    ? "menu-item-icon-active"
                    : "menu-item-icon-inactive"
                }`}
              >
                {nav.icon}
              </span>
              <span className={`menu-item-text whitespace-nowrap transition-all duration-150 ease-out ${
                isExpanded || isMobileOpen ? "opacity-100 max-w-[200px]" : "opacity-0 max-w-0 overflow-hidden"
              }`}>{nav.name}</span>
              <ChevronDownIcon
                className={`ml-auto w-5 h-5 transition-all duration-150 ease-out ${
                  openSubmenu?.type === menuType &&
                  openSubmenu?.index === index
                    ? "rotate-180 text-brand-500"
                    : ""
                } ${isExpanded || isMobileOpen ? "opacity-100 scale-100" : "opacity-0 scale-0 w-0 overflow-hidden"}`}
              />
            </button>
          ) : (
            nav.path && (
              <Link
                href={nav.path}
                className={`menu-item group ${
                  isActive(nav.path) ? "menu-item-active" : "menu-item-inactive"
                }`}
              >
                <span
                  className={`${
                    isActive(nav.path)
                      ? "menu-item-icon-active"
                      : "menu-item-icon-inactive"
                  }`}
                >
                  {nav.icon}
                </span>
                    <span className={`menu-item-text whitespace-nowrap transition-all duration-150 ease-out ${
                      isExpanded || isMobileOpen ? "opacity-100 max-w-[200px]" : "opacity-0 max-w-0 overflow-hidden"
                    }`}>{nav.name}</span>
                    {nav.statsKey && (
                      <span className={`ml-auto text-xs font-semibold px-2 py-0.5 rounded-full transition-all duration-150 ease-out ${
                        isActive(nav.path)
                          ? "bg-brand-600 text-white"
                          : "bg-gray-100 text-slate-550 dark:bg-gray-800 dark:text-slate-400"
                      } ${isExpanded || isMobileOpen ? "opacity-100 scale-100" : "opacity-0 scale-0 w-0 overflow-hidden"}`}>
                        {stats[nav.statsKey] || 0}
                      </span>
                    )}
              </Link>
            )
          )}
          {nav.subItems && (isExpanded || isMobileOpen) && (
            <div
              ref={(el) => {
                subMenuRefs.current[`${menuType}-${index}`] = el;
              }}
              className="overflow-hidden transition-all duration-300"
              style={{
                height:
                  openSubmenu?.type === menuType && openSubmenu?.index === index
                    ? `${subMenuHeight[`${menuType}-${index}`]}px`
                    : "0px",
              }}
            >
              <ul className="mt-2 space-y-1 ml-9">
                {nav.subItems.map((subItem) => (
                  <li key={subItem.name}>
                    <Link
                      href={subItem.path}
                      className={`menu-dropdown-item ${
                        isActive(subItem.path)
                          ? "menu-dropdown-item-active"
                          : "menu-dropdown-item-inactive"
                      }`}
                    >
                      {subItem.name}
                      <span className="flex items-center gap-1 ml-auto">
                        {subItem.new && (
                          <span
                            className={`ml-auto ${
                              isActive(subItem.path)
                                ? "menu-dropdown-badge-active"
                                : "menu-dropdown-badge-inactive"
                            } menu-dropdown-badge `}
                          >
                            new
                          </span>
                        )}
                        {subItem.pro && (
                          <span
                            className={`ml-auto ${
                              isActive(subItem.path)
                                ? "menu-dropdown-badge-active"
                                : "menu-dropdown-badge-inactive"
                            } menu-dropdown-badge `}
                          >
                            pro
                          </span>
                        )}
                      </span>
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </li>
      ))}
    </ul>
  );

  const [openSubmenu, setOpenSubmenu] = useState<{
    type: "main" | "others";
    index: number;
  } | null>(null);
  const [subMenuHeight, setSubMenuHeight] = useState<Record<string, number>>(
    {}
  );
  const subMenuRefs = useRef<Record<string, HTMLDivElement | null>>({});

  // const isActive = (path: string) => path === pathname;
   const isActive = useCallback((path: string) => path === pathname, [pathname]);

  useEffect(() => {
    // Check if the current path matches any submenu item
    let submenuMatched = false;
    ["main", "others"].forEach((menuType) => {
      const items = menuType === "main" ? [...overviewItems, ...managementItems] : othersItems;
      items.forEach((nav, index) => {
        if (nav.subItems) {
          nav.subItems.forEach((subItem) => {
            if (isActive(subItem.path)) {
              setOpenSubmenu({
                type: menuType as "main" | "others",
                index,
              });
              submenuMatched = true;
            }
          });
        }
      });
    });

    // If no submenu item matches, close the open submenu
    if (!submenuMatched) {
      setOpenSubmenu(null);
    }
  }, [pathname,isActive]);

  useEffect(() => {
    // Set the height of the submenu items when the submenu is opened
    if (openSubmenu !== null) {
      const key = `${openSubmenu.type}-${openSubmenu.index}`;
      if (subMenuRefs.current[key]) {
        setSubMenuHeight((prevHeights) => ({
          ...prevHeights,
          [key]: subMenuRefs.current[key]?.scrollHeight || 0,
        }));
      }
    }
  }, [openSubmenu]);

  const handleSubmenuToggle = (index: number, menuType: "main" | "others") => {
    setOpenSubmenu((prevOpenSubmenu) => {
      if (
        prevOpenSubmenu &&
        prevOpenSubmenu.type === menuType &&
        prevOpenSubmenu.index === index
      ) {
        return null;
      }
      return { type: menuType, index };
    });
  };

  return (
    <aside
      className={`fixed mt-[72px] flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-150 ease-out z-50 border-r border-gray-200 
        ${
          isExpanded || isMobileOpen
            ? "w-[290px]"
            : "w-[90px]"
        }
        ${isMobileOpen ? "translate-x-0" : "-translate-x-full"}
        lg:translate-x-0`}
    >
      <div
        className={`flex items-center h-[72px] border-b border-gray-200 dark:border-gray-800 -mx-5 px-5 mb-6 ${
          !isExpanded ? "lg:justify-center" : "justify-start"
        }`}
      >
        <Link href="/" className="flex items-center gap-3">
          <div className="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-md shadow-primary/20 flex-shrink-0 text-white font-bold">
            <i className="fa-solid fa-house-chimney text-sm" />
          </div>
          <div className={`flex flex-col text-left transition-all duration-150 ease-out origin-left ${
            isExpanded || isMobileOpen ? "opacity-100 max-w-[200px] translate-x-0" : "opacity-0 max-w-0 overflow-hidden -translate-x-2"
          }`}>
            <span className="font-black text-sm text-slate-800 dark:text-white leading-none whitespace-nowrap">
              BDS <span className="text-primary">Rental</span>
            </span>
            <span className="text-[9px] font-extrabold text-slate-400 uppercase tracking-wider mt-1.5 leading-none whitespace-nowrap">
              Trung tâm quản lý
            </span>
          </div>
        </Link>
      </div>
      <div className="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav className="mb-6">
          <div className="flex flex-col gap-6">
            {/* Overview section */}
            <div>
              <h2
                className={`mb-3 text-[10px] font-extrabold uppercase flex leading-[20px] text-slate-400 transition-all duration-150 ease-out ${
                  !isExpanded ? "lg:justify-center" : "justify-start px-4"
                }`}
              >
                <span className={`transition-all duration-150 ease-out whitespace-nowrap ${
                  isExpanded || isMobileOpen ? "opacity-100 max-h-5" : "opacity-0 max-h-0 overflow-hidden"
                }`}>
                  Tổng quan
                </span>
                {(!isExpanded && !isMobileOpen) && (
                  <HorizontaLDots />
                )}
              </h2>
              {renderMenuItems(overviewItems, "main")}
            </div>

            {/* Management section */}
            <div>
              <h2
                className={`mb-3 text-[10px] font-extrabold uppercase flex leading-[20px] text-slate-400 transition-all duration-150 ease-out ${
                  !isExpanded ? "lg:justify-center" : "justify-start px-4"
                }`}
              >
                <span className={`transition-all duration-150 ease-out whitespace-nowrap ${
                  isExpanded || isMobileOpen ? "opacity-100 max-h-5" : "opacity-0 max-h-0 overflow-hidden"
                }`}>
                  Quản lý
                </span>
                {(!isExpanded && !isMobileOpen) && (
                  <HorizontaLDots />
                )}
              </h2>
              {renderMenuItems(managementItems, "main")}
            </div>
          </div>
        </nav>
      </div>
    </aside>
  );
};

export default AppSidebar;
