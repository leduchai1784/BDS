"use client";
import { ApexOptions } from "apexcharts";
import dynamic from "next/dynamic";
import { useState } from "react";

// Dynamically import the ReactApexChart component
const ReactApexChart = dynamic(() => import("react-apexcharts"), {
  ssr: false,
});

export default function MonthlySalesChart() {
  const options: ApexOptions = {
    colors: ["#0077bb", "#10b981"],
    chart: {
      fontFamily: "Outfit, sans-serif",
      type: "bar",
      height: 200,
      toolbar: {
        show: false,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "40%",
        borderRadius: 6,
        borderRadiusApplication: "end",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 3,
      colors: ["transparent"],
    },
    xaxis: {
      categories: [
        "Thg 2",
        "Thg 3",
        "Thg 4",
        "Thg 5",
        "Thg 6",
        "Thg 7"
      ],
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    legend: {
      show: true,
      position: "top",
      horizontalAlign: "left",
      fontFamily: "Outfit",
    },
    grid: {
      yaxis: { lines: { show: true } },
    },
    fill: { opacity: 1 },
    tooltip: {
      y: {
        formatter: (val: number) => `${val}`,
      },
    },
  };

  const series = [
    {
      name: "Tin đăng mới",
      data: [12, 28, 45, 62, 54, 72],
    },
    {
      name: "Lịch hẹn đặt",
      data: [5, 14, 22, 30, 25, 41],
    }
  ];

  return (
    <div className="overflow-hidden rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/90 p-6 shadow-sm text-left">
      <div className="flex items-center justify-between mb-4">
        <div>
          <h3 className="text-base font-extrabold text-slate-850 dark:text-white">
            Biến động Tin đăng & Lịch hẹn
          </h3>
          <p className="text-xs text-slate-400 dark:text-slate-400 font-semibold mt-0.5">
            Thống kê số lượng tin đăng mới và lượt đặt lịch xem nhà 6 tháng gần nhất
          </p>
        </div>
      </div>

      <div className="custom-scrollbar overflow-x-auto">
        <div className="-ml-4 min-w-[500px]">
          <ReactApexChart
            options={options}
            series={series}
            type="bar"
            height={220}
          />
        </div>
      </div>
    </div>
  );
}
