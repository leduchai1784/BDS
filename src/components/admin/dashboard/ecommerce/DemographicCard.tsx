"use client";
import React, { useState } from "react";
import { MoreDotIcon } from "@/icons";

interface DemographicCardProps {
  sale: number;
  rent: number;
}

export default function DemographicCard({ sale, rent }: DemographicCardProps) {
  const total = sale + rent || 1;
  const salePercent = Math.round((sale / total) * 100);
  const rentPercent = Math.round((rent / total) * 100);

  return (
    <div className="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 text-left">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h3 className="text-lg font-bold text-gray-800 dark:text-white/90">
            Phân loại bất động sản
          </h3>
          <p className="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
            Tỉ lệ tin đăng bán và cho thuê trong hệ thống
          </p>
        </div>
      </div>

      <div className="space-y-6">
        {/* Tin đăng bán */}
        <div>
          <div className="flex items-center justify-between mb-2">
            <div className="flex items-center gap-3">
              <div className="w-3 h-3 bg-brand-500 rounded-full"></div>
              <div>
                <p className="font-semibold text-gray-800 text-theme-sm dark:text-white/90">
                  Cần bán
                </p>
                <span className="block text-gray-500 text-theme-xs dark:text-gray-400">
                  {sale.toLocaleString()} Tin đăng
                </span>
              </div>
            </div>
            <p className="font-bold text-gray-800 text-theme-sm dark:text-white/90">
              {salePercent}%
            </p>
          </div>
          <div className="relative block h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
            <div 
              style={{ width: `${salePercent}%` }} 
              className="absolute left-0 top-0 h-full rounded-full bg-brand-500 transition-all duration-500"
            ></div>
          </div>
        </div>

        {/* Tin đăng cho thuê */}
        <div>
          <div className="flex items-center justify-between mb-2">
            <div className="flex items-center gap-3">
              <div className="w-3 h-3 bg-cyan-500 rounded-full"></div>
              <div>
                <p className="font-semibold text-gray-800 text-theme-sm dark:text-white/90">
                  Cho thuê
                </p>
                <span className="block text-gray-500 text-theme-xs dark:text-gray-400">
                  {rent.toLocaleString()} Tin đăng
                </span>
              </div>
            </div>
            <p className="font-bold text-gray-800 text-theme-sm dark:text-white/90">
              {rentPercent}%
            </p>
          </div>
          <div className="relative block h-2.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
            <div 
              style={{ width: `${rentPercent}%` }} 
              className="absolute left-0 top-0 h-full rounded-full bg-cyan-500 transition-all duration-500"
            ></div>
          </div>
        </div>
      </div>
    </div>
  );
}
