"use client";

import React from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableHeader,
  TableRow,
} from "../ui/table";
import Badge from "../ui/badge/Badge";

interface AppointmentItem {
  id: string;
  user: {
    name: string;
    email: string;
    avatar: string;
  };
  propertyTitle: string;
  date: string;
  status: string; // 'Delivered' | 'Pending' | 'Cancelled'
}

interface RecentOrdersProps {
  appointments: AppointmentItem[];
}

export default function RecentOrders({ appointments }: RecentOrdersProps) {
  return (
    <div className="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-6 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 text-left">
      <div className="flex justify-between items-center mb-6">
        <div>
          <h3 className="text-lg font-bold text-gray-800 dark:text-white/90">
            Lịch hẹn xem nhà gần đây
          </h3>
          <p className="mt-1 text-gray-500 text-theme-sm dark:text-gray-400">
            Danh sách các khách hàng đặt lịch hẹn xem bất động sản mới nhất
          </p>
        </div>
      </div>

      <div className="max-w-full overflow-x-auto">
        <Table>
          <TableHeader className="border-b border-gray-100 dark:border-gray-800">
            <TableRow>
              <TableCell isHeader className="px-5 py-3 text-left text-theme-xs font-semibold text-gray-500 uppercase">
                Khách hàng
              </TableCell>
              <TableCell isHeader className="px-5 py-3 text-left text-theme-xs font-semibold text-gray-500 uppercase">
                Mã Bất động sản
              </TableCell>
              <TableCell isHeader className="px-5 py-3 text-left text-theme-xs font-semibold text-gray-500 uppercase">
                Ngày hẹn
              </TableCell>
              <TableCell isHeader className="px-5 py-3 text-left text-theme-xs font-semibold text-gray-500 uppercase">
                Trạng thái
              </TableCell>
            </TableRow>
          </TableHeader>

          <TableBody className="divide-y divide-gray-100 dark:divide-gray-800">
            {appointments.length === 0 ? (
              <TableRow>
                <TableCell colSpan={4} className="text-center py-6 text-slate-400">
                  Chưa có lịch hẹn xem nhà nào.
                </TableCell>
              </TableRow>
            ) : (
              appointments.map((ap) => (
                <TableRow key={ap.id}>
                  <TableCell className="px-5 py-4">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full overflow-hidden border border-gray-150 flex items-center justify-center flex-shrink-0 bg-gray-50">
                        <img
                          src={ap.user.avatar}
                          alt={ap.user.name}
                          className="w-full h-full object-cover"
                          onError={(e) => {
                            (e.target as any).src = "/images/user/user-01.png";
                          }}
                        />
                      </div>
                      <div>
                        <span className="block font-semibold text-gray-800 dark:text-white/90">
                          {ap.user.name}
                        </span>
                        <span className="block text-gray-500 text-theme-xs dark:text-gray-400">
                          {ap.user.email}
                        </span>
                      </div>
                    </div>
                  </TableCell>
                  <TableCell className="px-5 py-4 text-gray-600 dark:text-gray-300 font-medium">
                    {ap.propertyTitle}
                  </TableCell>
                  <TableCell className="px-5 py-4 text-gray-600 dark:text-gray-300">
                    {ap.date}
                  </TableCell>
                  <TableCell className="px-5 py-4">
                    <Badge
                      color={
                        ap.status === "Delivered"
                          ? "success"
                          : ap.status === "Pending"
                          ? "warning"
                          : "error"
                      }
                    >
                      {ap.status === "Delivered"
                        ? "Đã duyệt"
                        : ap.status === "Pending"
                        ? "Đang chờ"
                        : "Đã hủy"}
                    </Badge>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
