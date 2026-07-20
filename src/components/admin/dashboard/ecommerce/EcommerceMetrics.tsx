"use client";
import React from "react";
import Badge from "../ui/badge/Badge";
import { ArrowUpIcon, BoxIconLine, GroupIcon, CalenderIcon, PageIcon } from "@/icons";

interface EcommerceMetricsProps {
  users: number;
  properties: number;
  appointments: number;
  leads: number;
}

export const EcommerceMetrics: React.FC<EcommerceMetricsProps> = ({
  users,
  properties,
  appointments,
  leads
}) => {
  return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
      
      {/* Users Card */}
      <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-0.5 cursor-pointer">
        <div className="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
          <GroupIcon className="text-gray-800 size-6 dark:text-white/90" />
        </div>
        <div className="flex items-end justify-between mt-5">
          <div>
            <span className="text-sm text-gray-500 dark:text-gray-400">
              Thành viên
            </span>
            <h4 className="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
              {users.toLocaleString()}
            </h4>
          </div>
          <Badge color="success">
            <ArrowUpIcon />
            +100%
          </Badge>
        </div>
      </div>

      {/* Properties Card */}
      <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-0.5 cursor-pointer">
        <div className="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
          <BoxIconLine className="text-gray-800 dark:text-white/90" />
        </div>
        <div className="flex items-end justify-between mt-5">
          <div>
            <span className="text-sm text-gray-500 dark:text-gray-400">
              Tin đăng bất động sản
            </span>
            <h4 className="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
              {properties.toLocaleString()}
            </h4>
          </div>
          <Badge color="success">
            <ArrowUpIcon />
            Live
          </Badge>
        </div>
      </div>

      {/* Appointments Card */}
      <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-0.5 cursor-pointer">
        <div className="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800 text-gray-800 dark:text-white/90">
          <CalenderIcon className="size-6" />
        </div>
        <div className="flex items-end justify-between mt-5">
          <div>
            <span className="text-sm text-gray-500 dark:text-gray-400">
              Lịch hẹn xem nhà
            </span>
            <h4 className="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
              {appointments.toLocaleString()}
            </h4>
          </div>
          <Badge color="success">
            <ArrowUpIcon />
            Active
          </Badge>
        </div>
      </div>

      {/* Leads Card */}
      <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 transition-all duration-300 transform hover:-translate-y-0.5 cursor-pointer">
        <div className="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800 text-gray-800 dark:text-white/90">
          <PageIcon className="size-6" />
        </div>
        <div className="flex items-end justify-between mt-5">
          <div>
            <span className="text-sm text-gray-500 dark:text-gray-400">
              CRM Leads (AI)
            </span>
            <h4 className="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">
              {leads.toLocaleString()}
            </h4>
          </div>
          <Badge color="success">
            <ArrowUpIcon />
            Sync
          </Badge>
        </div>
      </div>

    </div>
  );
};
