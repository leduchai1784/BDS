"use client";
import Link from "next/link";
import React, { useState } from "react";
import { useSession, signOut } from "next-auth/react";
import { Dropdown } from "../ui/dropdown/Dropdown";
import { DropdownItem } from "../ui/dropdown/DropdownItem";

export default function UserDropdown() {
  const { data: session } = useSession();
  const [isOpen, setIsOpen] = useState(false);

  function toggleDropdown(e: React.MouseEvent<HTMLButtonElement, MouseEvent>) {
    e.stopPropagation();
    setIsOpen((prev) => !prev);
  }

  function closeDropdown() {
    setIsOpen(false);
  }

  const user = session?.user as any;
  const userName = user?.name || "Quản trị viên";
  const userEmail = user?.email || "admin@example.com";
  const userAvatar = user?.avatar || "";

  return (
    <div className="relative">
      <button
        onClick={toggleDropdown} 
        className="flex items-center text-gray-700 dark:text-gray-400 dropdown-toggle focus:outline-none cursor-pointer"
      >
        {userAvatar && userAvatar.startsWith('http') ? (
          <span className="mr-3 overflow-hidden rounded-full h-11 w-11 border border-gray-200 flex items-center justify-center bg-gray-50 flex-shrink-0">
            <img
              src={userAvatar}
              alt="Avatar"
              className="w-full h-full object-cover"
            />
          </span>
        ) : (
          <span className="mr-3 overflow-hidden rounded-full h-11 w-11 flex items-center justify-center bg-brand-50 text-brand-650 font-bold text-sm uppercase border border-brand-100 flex-shrink-0">
            {userName.charAt(0)}
          </span>
        )}

        <span className="block mr-1 font-medium text-theme-sm">{userName}</span>

        <svg
          className={`stroke-gray-500 dark:stroke-gray-400 transition-transform duration-200 ${
            isOpen ? "rotate-180" : ""
          }`}
          width="18"
          height="20"
          viewBox="0 0 18 20"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M4.3125 8.65625L9 13.3437L13.6875 8.65625"
            stroke="currentColor"
            strokeWidth="1.5"
            strokeLinecap="round"
            strokeLinejoin="round"
          />
        </svg>
      </button>

      <Dropdown
        isOpen={isOpen}
        onClose={closeDropdown}
        className="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark text-left"
      >
        <div className="pb-3 border-b border-gray-100 dark:border-gray-800">
          <span className="block font-bold text-gray-850 text-theme-sm dark:text-white">
            {userName}
          </span>
          <span className="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400 truncate">
            {userEmail}
          </span>
        </div>

        <ul className="flex flex-col gap-1 pt-3 pb-3 border-b border-gray-100 dark:border-gray-800">
          <li>
            <DropdownItem
              onItemClick={closeDropdown}
              tag="a"
              href="/profile"
              className="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
            >
              <i className="fa-regular fa-circle-user text-lg text-gray-400 group-hover:text-gray-700" />
              Trang cá nhân
            </DropdownItem>
          </li>
          <li>
            <DropdownItem
              onItemClick={closeDropdown}
              tag="a"
              href="/"
              className="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
            >
              <i className="fa-solid fa-globe text-lg text-gray-400 group-hover:text-gray-700" />
              Xem Website
            </DropdownItem>
          </li>
        </ul>

        <button
          onClick={() => signOut({ callbackUrl: "/" })}
          className="flex w-full items-center gap-3 px-3 py-2 mt-3 font-medium text-red-600 rounded-lg group text-theme-sm hover:bg-red-50 hover:text-red-700 transition cursor-pointer focus:outline-none"
        >
          <i className="fa-solid fa-arrow-right-from-bracket text-lg text-red-400 group-hover:text-red-650" />
          Đăng xuất
        </button>
      </Dropdown>
    </div>
  );
}
