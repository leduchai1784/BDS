'use client'

import { useState } from 'react'
import { toast } from 'sonner'

interface AdminLeadsTabProps {
  initialLeads: any[]
}

export default function AdminLeadsTab({ initialLeads }: AdminLeadsTabProps) {
  const [leads, setLeads] = useState(initialLeads)
  const [selectedLead, setSelectedLead] = useState<any>(null)
  const [drawerOpen, setDrawerOpen] = useState(false)
  const [activeDetailTab, setActiveDetailTab] = useState('demand')
  const [filterSource, setFilterSource] = useState('all')
  const [searchTerm, setSearchTerm] = useState('')
  const [sourceOpen, setSourceOpen] = useState(false)

  const openLead = (lead: any) => {
    setSelectedLead(lead)
    setDrawerOpen(true)
    setActiveDetailTab('demand')
  }

  const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
      'new': 'Mới nhận',
      'contacting': 'Đang liên hệ',
      'qualified': 'Tiềm năng',
      'unqualified': 'Không khớp',
      'closed': 'Đã chốt'
    }
    return labels[status] || status
  }

  const getStatusClass = (status: string) => {
    const classes: Record<string, string> = {
      'new': 'bg-blue-50 text-blue-600 border-blue-100',
      'contacting': 'bg-amber-50 text-amber-600 border-amber-100',
      'qualified': 'bg-emerald-50 text-emerald-600 border-emerald-100',
      'unqualified': 'bg-slate-50 text-slate-500 border-slate-100',
      'closed': 'bg-rose-50 text-rose-600 border-rose-100'
    }
    return 'px-2.5 py-1 text-[10px] font-bold rounded-lg border ' + (classes[status] || 'bg-slate-100 text-slate-600')
  }

  const getSourceLabel = (source: string) => {
    const labels: Record<string, string> = {
      'all': 'Tất cả',
      'chatbot': 'AI Chatbot',
      'web': 'Form Web',
      'unknown': 'Chưa xác định'
    }
    return labels[source] || 'Tất cả'
  }

  const getInitials = (name: string) => {
    if (!name) return 'L'
    const parts = name.trim().split(' ')
    return parts.length === 1 
      ? name[0].toUpperCase() 
      : (parts[parts.length - 2][0] + parts[parts.length - 1][0]).toUpperCase()
  }

  // Filter leads
  const filteredLeads = leads.filter(lead => {
    const sourceMatch = filterSource === 'all' || lead.source === filterSource
    const textMatch = searchTerm.trim() === '' || 
      (lead.name && lead.name.toLowerCase().includes(searchTerm.toLowerCase())) ||
      (lead.phone && lead.phone.includes(searchTerm)) ||
      (lead.email && lead.email.toLowerCase().includes(searchTerm.toLowerCase())) ||
      (lead.demand && lead.demand.toLowerCase().includes(searchTerm.toLowerCase())) ||
      (lead.company && lead.company.toLowerCase().includes(searchTerm.toLowerCase())) ||
      (lead.position && lead.position.toLowerCase().includes(searchTerm.toLowerCase()))
    return sourceMatch && textMatch
  })

  const syncLeads = async () => {
    const toastId = toast.loading('Đang đồng bộ danh sách Leads từ CRM...')
    try {
      const res = await fetch('/api/admin/leads')
      const data = await res.json()
      if (data.success && Array.isArray(data.leads)) {
        setLeads(data.leads)
        toast.success('Đồng bộ dữ liệu Leads thành công!', { id: toastId })
      } else {
        toast.error(data.error || 'Đồng bộ thất bại', { id: toastId })
      }
    } catch (e: any) {
      toast.error(e.message || 'Lỗi mạng', { id: toastId })
    }
  }

  const handleSaveLeadChanges = async (leadId: string, updatedAcf: any) => {
    const toastId = toast.loading('Đang lưu thay đổi lên CRM...')
    try {
      const res = await fetch(`/api/admin/leads/${leadId}/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ acf: updatedAcf })
      })
      const data = await res.json()
      if (data.success) {
        toast.success(data.message || 'Lưu thay đổi thành công!', { id: toastId })
        // Update local state
        setLeads(prev => prev.map(l => l.id === leadId ? { 
          ...l, 
          status: updatedAcf.status || l.status,
          notes: updatedAcf.note || l.notes 
        } : l))
      } else {
        toast.error(data.error || 'Có lỗi xảy ra', { id: toastId })
      }
    } catch (e: any) {
      toast.error(e.message || 'Lỗi mạng', { id: toastId })
    }
  }

  const handleDeleteLead = async (leadId: string) => {
    if (!window.confirm('Bạn có chắc chắn muốn xóa khách hàng tiềm năng này khỏi hệ thống CRM?')) return
    const toastId = toast.loading('Đang xóa lead trên CRM...')
    try {
      const res = await fetch(`/api/admin/leads/${leadId}/delete`, {
        method: 'POST'
      })
      const data = await res.json()
      if (data.success) {
        toast.success(data.message || 'Xóa Lead thành công!', { id: toastId })
        setLeads(prev => prev.filter(l => l.id !== leadId))
        setDrawerOpen(false)
        setSelectedLead(null)
      } else {
        toast.error(data.error || 'Có lỗi xảy ra', { id: toastId })
      }
    } catch (e: any) {
      toast.error(e.message || 'Lỗi mạng', { id: toastId })
    }
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="pb-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-left">
        <div>
          <h2 className="text-xl font-bold text-slate-800">Quản lý Khách hàng (Leads)</h2>
          <p className="text-xs text-slate-400 mt-1 font-semibold">Theo dõi nhu cầu, tương tác của khách hàng từ chatbot AI và đồng bộ CRM.</p>
        </div>
        <div className="flex items-center gap-3">
          <button 
            type="button" 
            onClick={syncLeads}
            className="px-4 py-2 border border-slate-200 hover:border-primary text-slate-600 hover:text-primary rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer bg-white"
          >
            <i className="fa-solid fa-arrows-rotate"></i> Đồng bộ Leads
          </button>
        </div>
      </div>

      {/* Stats Row */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 text-left">
        <div className="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
          <div className="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
            <i className="fa-solid fa-users"></i>
          </div>
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tổng số Lead</p>
            <h3 className="text-base font-extrabold text-slate-800 mt-0.5">{leads.length}</h3>
          </div>
        </div>
        <div className="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
          <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
            <i className="fa-solid fa-globe"></i>
          </div>
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Từ Web Form</p>
            <h3 className="text-base font-extrabold text-slate-800 mt-0.5">{leads.filter(l => l.source === 'web').length}</h3>
          </div>
        </div>
        <div className="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
          <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
            <i className="fa-solid fa-robot"></i>
          </div>
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Từ Chatbot AI</p>
            <h3 className="text-base font-extrabold text-slate-800 mt-0.5">{leads.filter(l => l.source === 'chatbot').length}</h3>
          </div>
        </div>
        <div className="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
          <div className="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
            <i className="fa-solid fa-phone"></i>
          </div>
          <div>
            <p className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Có Số Điện Thoại</p>
            <h3 className="text-base font-extrabold text-slate-800 mt-0.5">{leads.filter(l => l.phone && l.phone !== '').length}</h3>
          </div>
        </div>
      </div>

      {/* Filters and Search */}
      <div className="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 text-left">
        {/* Search bar */}
        <div className="relative max-w-xs w-full">
          <i className="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input 
            type="text" 
            id="leadSearchTerm"
            name="leadSearchTerm"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Tìm khách hàng, số điện thoại..."
            className="w-full pl-10 pr-4 py-2.5 bg-slate-55 border border-slate-200 focus:border-primary rounded-xl text-xs font-semibold outline-none transition"
          />
        </div>

        {/* Filter Selects */}
        <div className="flex items-center gap-3 self-end md:self-auto flex-wrap">
          <div className="flex items-center gap-2 relative">
            <span className="text-[10px] font-bold text-slate-400 uppercase">Nguồn:</span>
            <button 
              type="button"
              onClick={() => setSourceOpen(!sourceOpen)}
              className="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-600 focus:border-primary focus:outline-none transition cursor-pointer flex items-center gap-2 min-w-[125px] justify-between"
            >
              <span>{getSourceLabel(filterSource)}</span>
              <i className={`fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 ${sourceOpen ? 'rotate-180' : ''}`}></i>
            </button>
            
            {sourceOpen && (
              <div 
                onMouseLeave={() => setSourceOpen(false)}
                className="absolute z-30 right-0 top-full mt-1.5 w-44 bg-white border border-slate-100 rounded-2xl shadow-xl py-1 overflow-hidden"
              >
                {['all', 'chatbot', 'web', 'unknown'].map(opt => (
                  <button 
                    key={opt}
                    type="button"
                    onClick={() => {
                      setFilterSource(opt)
                      setSourceOpen(false)
                    }}
                    className={`w-full text-left px-4 py-2.5 text-xs font-semibold transition cursor-pointer ${filterSource === opt ? 'bg-primary/5 text-primary font-bold' : 'text-slate-600 hover:bg-slate-50'}`}
                  >
                    {getSourceLabel(opt)}
                  </button>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Leads Table */}
      <div className="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm text-left">
        {filteredLeads.length === 0 ? (
          <div className="p-12 text-center space-y-3">
            <div className="w-12 h-12 rounded-full bg-slate-50 text-slate-450 flex items-center justify-center mx-auto">
              <i className="fa-solid fa-address-book text-xl"></i>
            </div>
            <div className="space-y-1">
              <h4 className="font-bold text-slate-700 text-sm">Không tìm thấy khách hàng nào</h4>
              <p className="text-xs text-slate-400 font-semibold max-w-xs mx-auto">Thử đổi từ khóa hoặc điều chỉnh bộ lọc tìm kiếm.</p>
            </div>
          </div>
        ) : (
          <div className="overflow-x-auto max-h-[500px] overflow-y-auto pr-1 thin-scrollbar">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-slate-50 border-b border-slate-100 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                  <th className="px-6 py-4">Khách hàng</th>
                  <th className="px-6 py-4">Nhu cầu</th>
                  <th className="px-6 py-4">Thông tin công việc</th>
                  <th className="px-6 py-4 text-center">Nguồn</th>
                  <th className="px-6 py-4 text-center">Ngày nhận</th>
                  <th className="px-6 py-4 text-right">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filteredLeads.map(lead => (
                  <tr key={lead.id} className="hover:bg-slate-50/50 transition duration-150 border-b border-slate-50">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center gap-3">
                        <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary to-primary-hover text-white font-black text-xs flex items-center justify-center shadow-sm">
                          {getInitials(lead.name)}
                        </div>
                        <div>
                          <h4 className="font-bold text-slate-800 text-xs">{lead.name}</h4>
                          <p className="text-[10px] text-slate-400 mt-0.5 font-bold">{lead.phone || 'Chưa cung cấp SĐT'}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="max-w-[200px] truncate text-xs font-semibold text-slate-700" title={lead.demand}>{lead.demand || '-'}</div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                      {lead.company || lead.position ? (
                        <div>
                          <span className="font-bold text-slate-700">{lead.position || 'Chức vụ'}</span>
                          {lead.position && lead.company && <span className="text-slate-400"> tại </span>}
                          <span className="font-semibold text-slate-500">{lead.company || 'Công ty'}</span>
                        </div>
                      ) : (
                        <div className="text-slate-400 font-semibold">-</div>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-center">
                      {lead.source === 'chatbot' ? (
                        <span className="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                          <i className="fa-solid fa-robot text-[9px]"></i> AI Chatbot
                        </span>
                      ) : lead.source === 'web' ? (
                        <span className="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                          <i className="fa-solid fa-globe text-[9px]"></i> Web Form
                        </span>
                      ) : (
                        <span className="inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200">
                          <i className="fa-solid fa-circle-question text-[9px]"></i> Chưa xác định
                        </span>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-center text-xs font-semibold text-slate-500">{lead.created_at}</td>
                    <td className="px-6 py-4 whitespace-nowrap text-right text-xs">
                      <button 
                        type="button" 
                        onClick={() => openLead(lead)}
                        className="px-3 py-1.5 bg-slate-50 hover:bg-primary-light border border-slate-200 hover:border-primary/20 text-slate-600 hover:text-primary rounded-lg font-bold transition cursor-pointer focus:outline-none"
                      >
                        Chi tiết
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* DRAWER FOR LEAD DETAILS */}
      {drawerOpen && selectedLead && (
        <div className="fixed inset-0 z-[99999] overflow-hidden" role="dialog" aria-modal="true">
          <div className="absolute inset-0 overflow-hidden">
            {/* Backdrop */}
            <div 
              onClick={() => setDrawerOpen(false)}
              className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            />

            <div className="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
              <div className="pointer-events-auto w-screen max-w-md">
                <div className="flex h-full flex-col bg-white shadow-2xl overflow-y-auto border-l border-slate-100 text-left">
                  {/* Drawer Header */}
                  <div className="px-5 py-5 bg-gradient-to-r from-primary to-primary-hover text-white flex items-center justify-between">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center border border-white/20 text-sm font-bold">
                        {getInitials(selectedLead.name)}
                      </div>
                      <div>
                        <h3 className="font-bold text-sm">{selectedLead.name}</h3>
                        <p className="text-[10px] text-white/80 font-semibold mt-0.5">{selectedLead.phone}</p>
                      </div>
                    </div>
                    <button 
                      type="button" 
                      onClick={() => setDrawerOpen(false)}
                      className="w-8 h-8 rounded-lg hover:bg-white/10 text-white flex items-center justify-center transition cursor-pointer focus:outline-none"
                    >
                      <i className="fa-solid fa-xmark text-lg"></i>
                    </button>
                  </div>

                  {/* Subtabs */}
                  <div className="border-b border-slate-150 px-5 flex items-center bg-slate-50/50 flex-shrink-0">
                    <button 
                      onClick={() => setActiveDetailTab('demand')}
                      className={`px-4 py-3 border-b-2 text-xs transition focus:outline-none cursor-pointer font-bold ${activeDetailTab === 'demand' ? 'border-primary text-primary' : 'border-transparent text-slate-500'}`}
                    >
                      Chi tiết & Ghi chú
                    </button>
                    <button 
                      onClick={() => setActiveDetailTab('chat')}
                      className={`px-4 py-3 border-b-2 text-xs transition focus:outline-none cursor-pointer flex items-center gap-1.5 font-bold ${activeDetailTab === 'chat' ? 'border-primary text-primary' : 'border-transparent text-slate-500'}`}
                    >
                      <i className="fa-solid fa-message text-[10px]"></i> Lịch sử Chat AI
                    </button>
                    <button 
                      onClick={() => setActiveDetailTab('matching')}
                      className={`px-4 py-3 border-b-2 text-xs transition focus:outline-none cursor-pointer flex items-center gap-1.5 font-bold ${activeDetailTab === 'matching' ? 'border-primary text-primary' : 'border-transparent text-slate-500'}`}
                    >
                      <i className="fa-solid fa-fire text-[10px] text-orange-500"></i> Đối khớp ({selectedLead.match_score}%)
                    </button>
                  </div>

                  {/* Body */}
                  <div className="flex-grow p-5 space-y-6 overflow-y-auto">
                    {activeDetailTab === 'demand' && (
                      <div className="space-y-6">
                        {/* Contact info */}
                        <div className="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3 text-xs">
                          <h4 className="font-bold text-slate-800 text-[11px] uppercase tracking-wider border-b border-slate-200 pb-2">Thông tin liên hệ</h4>
                          <div className="space-y-2">
                            <div className="flex justify-between">
                              <span className="text-slate-400 font-semibold">Họ tên:</span>
                              <span className="font-bold text-slate-700">{selectedLead.name}</span>
                            </div>
                            <div className="flex justify-between">
                              <span className="text-slate-400 font-semibold">Số điện thoại:</span>
                              <span className="font-bold text-slate-700">{selectedLead.phone || 'Chưa cung cấp'}</span>
                            </div>
                            <div className="flex justify-between">
                              <span className="text-slate-400 font-semibold">Email:</span>
                              <span className="font-bold text-slate-700">{selectedLead.email || 'Chưa cung cấp'}</span>
                            </div>
                            <div className="flex justify-between">
                              <span className="text-slate-400 font-semibold">Zalo:</span>
                              <span className="font-bold text-slate-700">{selectedLead.zalo || 'Chưa cung cấp'}</span>
                            </div>
                          </div>
                        </div>

                        {/* Job */}
                        <div className="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3 text-xs">
                          <h4 className="font-bold text-slate-800 text-[11px] uppercase tracking-wider border-b border-slate-200 pb-2">Thông tin công việc</h4>
                          <div className="space-y-2">
                            <div className="flex justify-between">
                              <span className="text-slate-400 font-semibold">Công ty:</span>
                              <span className="font-bold text-slate-700">{selectedLead.company || 'Chưa cung cấp'}</span>
                            </div>
                            <div className="flex justify-between">
                              <span className="text-slate-400 font-semibold">Chức vụ:</span>
                              <span className="font-bold text-slate-700">{selectedLead.position || 'Chưa cung cấp'}</span>
                            </div>
                            <div className="flex justify-between">
                              <span className="text-slate-400 font-semibold">Quy mô công ty:</span>
                              <span className="font-bold text-slate-700">{selectedLead.comsize || 'Chưa cung cấp'}</span>
                            </div>
                          </div>
                        </div>

                        {/* Demand */}
                        <div className="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3 text-xs">
                          <h4 className="font-bold text-slate-800 text-[11px] uppercase tracking-wider border-b border-slate-200 pb-2">Yêu cầu & Nhu cầu</h4>
                          <div className="space-y-1">
                            <span className="block text-slate-400 font-semibold mb-1">Chi tiết nhu cầu:</span>
                            <p className="font-semibold text-slate-700 leading-relaxed bg-white border border-slate-150 p-2.5 rounded-xl whitespace-pre-line">{selectedLead.demand || '-'}</p>
                          </div>
                        </div>

                        {/* Care status */}
                        <div className="space-y-1.5">
                          <label className="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-1">Cập nhật trạng thái chăm sóc</label>
                          <div className="flex flex-wrap gap-2 pt-1">
                            {['new', 'contacting', 'qualified', 'unqualified', 'closed'].map(st => (
                              <button 
                                key={st}
                                type="button" 
                                onClick={() => {
                                  selectedLead.status = st
                                  handleSaveLeadChanges(selectedLead.id, {
                                    status: st,
                                    note: selectedLead.notes || ''
                                  })
                                }}
                                className={`px-2.5 py-1.5 text-[10px] font-bold rounded-lg border transition cursor-pointer focus:outline-none ${selectedLead.status === st ? 'bg-primary text-white border-primary shadow-sm' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'}`}
                              >
                                {getStatusLabel(st)}
                              </button>
                            ))}
                          </div>
                        </div>

                        {/* Notes */}
                        <div className="space-y-1.5">
                          <label className="block text-[9px] font-bold uppercase tracking-wider text-slate-400 px-1">Ghi chú chăm sóc chi tiết</label>
                          <textarea 
                            rows={4} 
                            id="leadCareNotes"
                            name="leadCareNotes"
                            value={selectedLead.notes}
                            onChange={(e) => {
                              selectedLead.notes = e.target.value
                              setLeads([...leads])
                            }}
                            className="w-full p-3 bg-slate-50 border border-slate-200 focus:border-primary rounded-xl text-xs font-semibold outline-none transition resize-none"
                            placeholder="Nhập ghi chú chăm sóc khách hàng..."
                          />
                          <div className="flex justify-between items-center pt-1">
                            <button 
                              type="button" 
                              onClick={() => handleDeleteLead(selectedLead.id)}
                              className="px-3 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-650 rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer"
                            >
                              Xóa Lead
                            </button>
                            <button 
                              type="button" 
                              onClick={() => handleSaveLeadChanges(selectedLead.id, {
                                status: selectedLead.status || 'new',
                                note: selectedLead.notes || ''
                              })}
                              className="px-3 py-1.5 bg-primary hover:bg-primary-hover text-white rounded-lg text-[10px] font-bold transition shadow-sm cursor-pointer"
                            >
                              Lưu ghi chú
                            </button>
                          </div>
                        </div>
                      </div>
                    )}

                    {activeDetailTab === 'chat' && (
                      <div className="space-y-4">
                        <div className="pb-3 border-b border-slate-100">
                          <h4 className="font-bold text-slate-700 text-xs">Đoạn hội thoại với trợ lý ảo</h4>
                          <p className="text-[10px] text-slate-400 font-medium">Bản ghi chat chi tiết để môi giới hiểu sâu mong muốn của khách.</p>
                        </div>

                        {!selectedLead.chat_history || selectedLead.chat_history.length === 0 ? (
                          <div className="p-8 text-center text-slate-400 text-xs font-semibold">
                            Không có dữ liệu hội thoại chat (Lead thêm thủ công hoặc từ nguồn webform).
                          </div>
                        ) : (
                          <div className="space-y-3.5 max-h-[380px] overflow-y-auto pr-1 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                            {selectedLead.chat_history.map((msg: any, idx: number) => (
                              <div key={idx} className={`flex flex-col mb-2.5 ${msg.role === 'user' ? 'items-end' : 'items-start'}`}>
                                <span className="text-[8px] font-bold text-slate-400 mb-1 px-1">{msg.role === 'user' ? 'Khách hàng' : 'AI Assistant'}</span>
                                <div 
                                  className={`max-w-[85%] px-3.5 py-2 rounded-xl text-[11px] leading-relaxed shadow-sm ${msg.role === 'user' ? 'bg-primary text-white rounded-tr-none' : 'bg-white text-slate-700 border border-slate-200/60 rounded-tl-none'}`}
                                >
                                  {msg.content}
                                </div>
                              </div>
                            ))}
                          </div>
                        )}
                      </div>
                    )}

                    {activeDetailTab === 'matching' && (
                      <div className="space-y-4">
                        <div className="pb-3 border-b border-slate-100">
                          <h4 className="font-bold text-slate-700 text-xs flex items-center gap-1">
                            <i className="fa-solid fa-fire text-orange-500"></i> Gợi ý BĐS phù hợp cho khách
                          </h4>
                          <p className="text-[10px] text-slate-400 font-medium">Đối khớp tự động dựa trên vị trí, khoảng giá và loại hình BĐS.</p>
                        </div>

                        {!selectedLead.matched_properties || selectedLead.matched_properties.length === 0 ? (
                          <div className="p-8 text-center text-slate-400 text-xs font-semibold">
                            Không tìm thấy bất động sản nào trùng khớp trong kho hàng của bạn.
                          </div>
                        ) : (
                          <div className="space-y-3">
                            {selectedLead.matched_properties.map((prop: any, pIdx: number) => (
                              <div key={pIdx} className="p-3 bg-white border border-slate-100 hover:border-primary/20 rounded-xl shadow-sm transition flex gap-3">
                                <div className="w-12 h-12 rounded-lg bg-slate-100 flex-shrink-0 relative overflow-hidden">
                                  <img 
                                    src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=200&q=80" 
                                    className="w-full h-full object-cover" 
                                    alt={prop.title}
                                  />
                                </div>
                                <div className="flex-grow min-w-0 flex flex-col justify-between">
                                  <div>
                                    <h5 className="font-bold text-slate-800 text-[11px] truncate">{prop.title}</h5>
                                    <p className="text-[9px] text-slate-400 truncate mt-0.5 flex items-center gap-1">
                                      <i className="fa-solid fa-location-dot"></i> <span>{prop.location}</span>
                                    </p>
                                  </div>
                                  <div className="flex items-center justify-between mt-1">
                                    <span className="text-xs font-black text-primary">{prop.price}</span>
                                    <span className="text-[9px] text-slate-400 font-bold">{prop.area}</span>
                                  </div>
                                </div>
                              </div>
                            ))}
                          </div>
                        )}

                        <div className="pt-4 border-t border-slate-100 flex items-center gap-3">
                          <button 
                            type="button" 
                            onClick={() => toast.success('Đã gửi email đề xuất giỏ hàng thành công!')}
                            className="flex-grow px-3 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer focus:outline-none"
                          >
                            <i className="fa-solid fa-paper-plane"></i> Gửi giỏ hàng cho khách
                          </button>
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
