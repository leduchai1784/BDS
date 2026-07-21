"use client";
import React from "react";

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
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 col-span-12">
      
      {/* Users Card */}
      <div className="rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/90 p-5 md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 dark:hover:border-primary/40 transition-all duration-300 transform hover:-translate-y-1 text-left relative overflow-hidden group">
        <div className="flex items-center justify-between">
          <div className="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg font-bold group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
            <i className="fa-solid fa-users" />
          </div>
          <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center gap-1 border border-emerald-100 dark:border-emerald-800/50">
            <i className="fa-solid fa-arrow-trend-up text-[9px]" /> +12%
          </span>
        </div>
        <div className="mt-4">
          <span className="text-xs text-slate-400 dark:text-slate-400 font-bold uppercase tracking-wider">
            Tổng số thành viên
          </span>
          <h3 className="mt-1 font-black text-slate-850 dark:text-white text-2xl tracking-tight">
            {users.toLocaleString()}
          </h3>
        </div>
      </div>

      {/* Properties Card */}
      <div className="rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/90 p-5 md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 dark:hover:border-primary/40 transition-all duration-300 transform hover:-translate-y-1 text-left relative overflow-hidden group">
        <div className="flex items-center justify-between">
          <div className="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg font-bold group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
            <i className="fa-solid fa-house-chimney" />
          </div>
          <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center gap-1 border border-teal-100 dark:border-teal-800/50">
            Live (NKS)
          </span>
        </div>
        <div className="mt-4">
          <span className="text-xs text-slate-400 dark:text-slate-400 font-bold uppercase tracking-wider">
            Tin đăng BĐS
          </span>
          <h3 className="mt-1 font-black text-slate-850 dark:text-white text-2xl tracking-tight">
            {properties.toLocaleString()}
          </h3>
        </div>
      </div>

      {/* Appointments Card */}
      <div className="rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/90 p-5 md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 dark:hover:border-primary/40 transition-all duration-300 transform hover:-translate-y-1 text-left relative overflow-hidden group">
        <div className="flex items-center justify-between">
          <div className="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300">
            <i className="fa-solid fa-calendar-check" />
          </div>
          <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center gap-1 border border-amber-100 dark:border-amber-800/50">
            Active
          </span>
        </div>
        <div className="mt-4">
          <span className="text-xs text-slate-400 dark:text-slate-400 font-bold uppercase tracking-wider">
            Lịch hẹn xem nhà
          </span>
          <h3 className="mt-1 font-black text-slate-850 dark:text-white text-2xl tracking-tight">
            {appointments.toLocaleString()}
          </h3>
        </div>
      </div>

      {/* Leads Card */}
      <div className="rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/90 p-5 md:p-6 shadow-sm hover:shadow-md hover:border-primary/20 dark:hover:border-primary/40 transition-all duration-300 transform hover:-translate-y-1 text-left relative overflow-hidden group">
        <div className="flex items-center justify-between">
          <div className="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg font-bold group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
            <i className="fa-solid fa-user-gear" />
          </div>
          <span className="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center gap-1 border border-indigo-100 dark:border-indigo-800/50">
            SCRM AI
          </span>
        </div>
        <div className="mt-4">
          <span className="text-xs text-slate-400 dark:text-slate-400 font-bold uppercase tracking-wider">
            Khách tiềm năng (Leads)
          </span>
          <h3 className="mt-1 font-black text-slate-850 dark:text-white text-2xl tracking-tight">
            {leads.toLocaleString()}
          </h3>
        </div>
      </div>

    </div>
  );
};
