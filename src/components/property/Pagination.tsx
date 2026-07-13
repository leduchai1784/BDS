'use client'

import Link from 'next/link'
import { usePathname, useSearchParams } from 'next/navigation'

interface PaginationProps {
  currentPage: number
  totalPages: number
}

export default function Pagination({ currentPage, totalPages }: PaginationProps) {
  const pathname = usePathname()
  const searchParams = useSearchParams()

  if (totalPages <= 1) return null

  // Generate URL for a specific page
  const createPageUrl = (pageNumber: number) => {
    const params = new URLSearchParams(searchParams.toString())
    params.set('page', pageNumber.toString())
    return `${pathname}?${params.toString()}`
  }

  // Generate page numbers to display
  const getPageNumbers = () => {
    const pages = []
    const range = 2 // Number of pages to show around current page

    for (let i = 1; i <= totalPages; i++) {
      if (
        i === 1 ||
        i === totalPages ||
        (i >= currentPage - range && i <= currentPage + range)
      ) {
        pages.push(i)
      } else if (pages[pages.length - 1] !== '...') {
        pages.push('...')
      }
    }
    return pages
  }

  return (
    <div className="flex justify-center mt-12">
      <nav className="inline-flex space-x-1 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm" aria-label="Pagination">
        {/* Previous page button */}
        {currentPage > 1 ? (
          <Link 
            href={createPageUrl(currentPage - 1)}
            className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition"
          >
            <i className="fa-solid fa-chevron-left text-xs"></i>
          </Link>
        ) : (
          <span className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-350 cursor-not-allowed">
            <i className="fa-solid fa-chevron-left text-xs"></i>
          </span>
        )}

        {/* Page numbers */}
        {getPageNumbers().map((page, idx) => {
          if (page === '...') {
            return (
              <span key={idx} className="inline-flex items-center justify-center w-10 h-10 text-slate-400 select-none">
                ...
              </span>
            )
          }

          const pageNum = page as number
          const isCurrent = pageNum === currentPage

          return (
            <Link
              key={idx}
              href={createPageUrl(pageNum)}
              className={`inline-flex items-center justify-center w-10 h-10 rounded-xl transition font-bold text-xs ${
                isCurrent 
                  ? 'bg-primary text-white shadow-md shadow-primary/20' 
                  : 'text-slate-650 hover:bg-slate-50 hover:text-primary'
              }`}
            >
              {pageNum}
            </Link>
          )
        })}

        {/* Next page button */}
        {currentPage < totalPages ? (
          <Link 
            href={createPageUrl(currentPage + 1)}
            className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-primary transition"
          >
            <i className="fa-solid fa-chevron-right text-xs"></i>
          </Link>
        ) : (
          <span className="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-350 cursor-not-allowed">
            <i className="fa-solid fa-chevron-right text-xs"></i>
          </span>
        )}
      </nav>
    </div>
  )
}
