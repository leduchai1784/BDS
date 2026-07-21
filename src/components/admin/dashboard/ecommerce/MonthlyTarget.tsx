"use client";
import { ApexOptions } from "apexcharts";
import dynamic from "next/dynamic";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {
  ssr: false,
});

export default function MonthlyTarget() {
  const series = [65, 35]; // Cho thuê 65%, Đang bán 35%
  const options: ApexOptions = {
    colors: ["#0077bb", "#10b981"],
    chart: {
      fontFamily: "Outfit, sans-serif",
      type: "donut",
      height: 260,
    },
    labels: ["Cho thuê", "Đang bán"],
    legend: {
      show: true,
      position: "bottom",
      fontFamily: "Outfit",
    },
    dataLabels: {
      enabled: true,
      formatter: function (val: number) {
        return Math.round(val) + "%";
      },
    },
    plotOptions: {
      pie: {
        donut: {
          size: "70%",
          labels: {
            show: true,
            total: {
              show: true,
              label: "Tổng tin",
              formatter: () => "72"
            }
          }
        }
      }
    }
  };

  return (
    <div className="rounded-3xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-800/90 p-6 shadow-sm text-left h-full flex flex-col justify-between">
      <div>
        <h3 className="text-base font-extrabold text-slate-850 dark:text-white">
          Cơ cấu Giao dịch BĐS
        </h3>
        <p className="text-xs text-slate-400 dark:text-slate-400 font-semibold mt-0.5">
          Tỷ lệ bất động sản Đang cho thuê và Đang bán trên hệ thống
        </p>
      </div>

      <div className="py-4">
        <ReactApexChart
          options={options}
          series={series}
          type="donut"
          height={240}
        />
      </div>

      <div className="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100 dark:border-slate-700/60 text-center">
        <div className="p-2.5 rounded-2xl bg-blue-50/60 dark:bg-blue-900/20 border border-blue-100/60 dark:border-blue-800/40">
          <span className="block text-[10px] font-extrabold uppercase text-blue-600 dark:text-blue-400">Cho thuê</span>
          <strong className="text-base font-black text-slate-800 dark:text-white">47 tin</strong>
        </div>
        <div className="p-2.5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-900/20 border border-emerald-100/60 dark:border-emerald-800/40">
          <span className="block text-[10px] font-extrabold uppercase text-emerald-600 dark:text-emerald-400">Đang bán</span>
          <strong className="text-base font-black text-slate-800 dark:text-white">25 tin</strong>
        </div>
      </div>
    </div>
  );
}
