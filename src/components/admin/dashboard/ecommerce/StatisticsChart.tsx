"use client";
import dynamic from "next/dynamic";
import { ApexOptions } from "apexcharts";

const Chart = dynamic(() => import("react-apexcharts"), { ssr: false });

export default function StatisticsChart() {
  const options: ApexOptions = {
    legend: {
      show: true,
      position: "top",
      horizontalAlign: "left",
      fontFamily: "Outfit",
    },
    colors: ["#0077bb", "#10b981"],
    chart: {
      fontFamily: "Outfit, sans-serif",
      height: 280,
      type: "area",
      toolbar: { show: false },
    },
    stroke: {
      curve: "smooth",
      width: 2,
    },
    fill: {
      type: "gradient",
      gradient: {
        opacityFrom: 0.4,
        opacityTo: 0.05,
      },
    },
    xaxis: {
      categories: [
        "Chung cư",
        "Nhà riêng/Phố",
        "Phòng trọ/Mini",
        "Đất nền",
        "Biệt thự",
        "Mặt bằng"
      ],
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    grid: {
      yaxis: { lines: { show: true } },
    },
    tooltip: {
      y: {
        formatter: (val: number) => `${val} lượt xem`,
      },
    },
  };

  const series = [
    {
      name: "Lượt xem tin",
      data: [1420, 2150, 980, 1840, 620, 1100],
    },
    {
      name: "Lượt tương tác / Đặt hẹn",
      data: [120, 185, 90, 140, 45, 80],
    },
  ];

  return (
    <div className="rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/90 p-6 shadow-sm text-left">
      <div className="flex items-center justify-between mb-4">
        <div>
          <h3 className="text-base font-extrabold text-slate-850 dark:text-white">
            Thống kê Lượt xem & Tương tác theo Chuyên mục
          </h3>
          <p className="text-xs text-slate-400 dark:text-slate-400 font-semibold mt-0.5">
            Lượt truy cập xem tin và đăng ký thông tin tư vấn phân theo từng loại bất động sản
          </p>
        </div>
      </div>

      <div className="custom-scrollbar overflow-x-auto">
        <div className="-ml-4 min-w-[500px]">
          <Chart options={options} series={series} type="area" height={260} />
        </div>
      </div>
    </div>
  );
}