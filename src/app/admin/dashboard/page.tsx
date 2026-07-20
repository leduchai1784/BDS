import type { Metadata } from "next";
import React from "react";
import { EcommerceMetrics } from "@/components/admin/dashboard/ecommerce/EcommerceMetrics";
import MonthlyTarget from "@/components/admin/dashboard/ecommerce/MonthlyTarget";
import MonthlySalesChart from "@/components/admin/dashboard/ecommerce/MonthlySalesChart";
import StatisticsChart from "@/components/admin/dashboard/ecommerce/StatisticsChart";
import RecentOrders from "@/components/admin/dashboard/ecommerce/RecentOrders";
import DemographicCard from "@/components/admin/dashboard/ecommerce/DemographicCard";

export const metadata: Metadata = {
  title: "Admin Dashboard | BDS Rental",
  description: "Trang tổng quan thống kê quản trị của BDS Rental",
};

export default function AdminDashboardPage() {
  return (
    <div className="w-full text-slate-800 font-sans text-left">
      <div className="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-black text-slate-900 tracking-tight">Bảng điều khiển thống kê</h1>
          <p className="text-xs text-slate-500 font-medium mt-1">Tổng quan hoạt động và dữ liệu kinh doanh dự án</p>
        </div>
      </div>

      <div className="grid grid-cols-12 gap-4 md:gap-6">
        <div className="col-span-12 space-y-6 xl:col-span-7">
          <EcommerceMetrics />
          <MonthlySalesChart />
        </div>

        <div className="col-span-12 xl:col-span-5">
          <MonthlyTarget />
        </div>

        <div className="col-span-12">
          <StatisticsChart />
        </div>

        <div className="col-span-12 xl:col-span-5">
          <DemographicCard />
        </div>

        <div className="col-span-12 xl:col-span-7">
          <RecentOrders />
        </div>
      </div>
    </div>
  );
}
