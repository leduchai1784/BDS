'use client'

import { useState, useRef, useEffect } from 'react'
import Link from 'next/link'

interface Message {
  role: 'user' | 'assistant'
  content: string
  properties?: {
    id: string
    title: string
    price: string
    area: string
    location: string
  }[]
}

export default function ChatBot() {
  const [isOpen, setIsOpen] = useState(false)
  const [messages, setMessages] = useState<Message[]>([
    {
      role: 'assistant',
      content: 'Xin chào! Tôi là Trợ lý ảo AI của BDS Rental. Tôi có thể giúp gì cho bạn hôm nay? Bạn muốn tìm kiếm căn hộ, phòng trọ ở khu vực nào?'
    }
  ])
  const [input, setInput] = useState('')
  const [isTyping, setIsTyping] = useState(false)
  
  const scrollRef = useRef<HTMLDivElement>(null)

  // Auto-scroll to bottom of conversation
  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollIntoView({ behavior: 'smooth' })
    }
  }, [messages, isTyping])

  const handleSend = async (text: string) => {
    if (!text.trim()) return

    const userMsg: Message = { role: 'user', content: text }
    setMessages(prev => [...prev, userMsg])
    setInput('')
    setIsTyping(true)

    // Build history structure
    const history = messages.map(m => ({
      role: m.role === 'user' ? 'user' : 'model',
      content: m.content
    }))

    try {
      const res = await fetch('/api/chatbot', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: text, history })
      })

      const data = await res.json()
      if (res.ok && data.success) {
        setMessages(prev => [
          ...prev,
          {
            role: 'assistant',
            content: data.reply,
            properties: data.properties
          }
        ])
      } else {
        setMessages(prev => [
          ...prev,
          {
            role: 'assistant',
            content: data.reply || 'Xin lỗi, tôi gặp sự cố kết nối AI. Vui lòng thử lại sau.'
          }
        ])
      }
    } catch (err) {
      console.error(err)
      setMessages(prev => [
        ...prev,
        {
          role: 'assistant',
          content: 'Không thể kết nối mạng. Vui lòng kiểm tra lại đường truyền.'
        }
      ])
    } finally {
      setIsTyping(false)
    }
  }

  const starterChips = [
    'Tìm phòng trọ Quận 10 dưới 5tr',
    'Căn hộ dịch vụ Bình Thạnh',
    'Nhà nguyên căn bán 4 tỷ'
  ]

  return (
    <div className="fixed bottom-6 right-6 z-50 font-sans text-left">
      
      {/* Floating Circle Button Toggle */}
      {!isOpen && (
        <button
          onClick={() => setIsOpen(true)}
          className="w-14 h-14 bg-primary text-white rounded-full shadow-2xl flex items-center justify-center cursor-pointer hover:scale-105 transition hover:shadow-primary/35"
        >
          <i className="fa-solid fa-comment-dots text-xl" />
        </button>
      )}

      {/* Chat Conversation Box */}
      {isOpen && (
        <div className="w-80 sm:w-96 h-[500px] bg-white border border-slate-100 rounded-3xl shadow-2xl overflow-hidden flex flex-col justify-between animate-in fade-in slide-in-from-bottom-5 duration-250">
          
          {/* Header */}
          <div className="bg-slate-900 text-white p-4 flex items-center justify-between">
            <div className="flex items-center space-x-3">
              <div className="w-8 h-8 bg-primary rounded-xl flex items-center justify-center">
                <i className="fa-solid fa-robot text-sm text-white animate-pulse" />
              </div>
              <div>
                <span className="text-xs font-black block">Trợ lý ảo AI</span>
                <span className="text-[9px] text-emerald-400 font-bold block">Online</span>
              </div>
            </div>

            <button
              onClick={() => setIsOpen(false)}
              className="text-slate-400 hover:text-white transition cursor-pointer"
            >
              <i className="fa-solid fa-chevron-down text-sm" />
            </button>
          </div>

          {/* Messages list */}
          <div className="flex-grow p-4 overflow-y-auto bg-slate-50 space-y-3 scrollbar-thin">
            {messages.map((m, idx) => (
              <div key={idx} className={`flex flex-col ${m.role === 'user' ? 'items-end' : 'items-start'}`}>
                {/* Text Bubble */}
                <div className={`max-w-[85%] px-4 py-2.5 rounded-2xl text-xs leading-relaxed font-semibold whitespace-pre-wrap ${
                  m.role === 'user'
                    ? 'bg-primary text-white rounded-tr-none'
                    : 'bg-white text-slate-800 border border-slate-150 shadow-sm rounded-tl-none'
                }`}>
                  {m.content}
                </div>

                {/* Recommended properties grid */}
                {m.properties && m.properties.length > 0 && (
                  <div className="w-full mt-2 space-y-1.5 max-w-[90%]">
                    <span className="text-[9px] uppercase tracking-wider text-slate-400 font-bold px-1 block">Bất động sản gợi ý:</span>
                    {m.properties.map(p => (
                      <Link
                        key={p.id}
                        href={`/property/${p.id}`}
                        target="_blank"
                        className="block bg-white border border-slate-150 hover:border-primary rounded-xl p-2.5 shadow-xs transition"
                      >
                        <strong className="block text-[11px] text-slate-800 truncate">{p.title}</strong>
                        <div className="flex items-center justify-between mt-1 text-[9px] text-slate-400 font-bold">
                          <span className="text-primary font-black">{p.price}</span>
                          <span>{p.area}</span>
                        </div>
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            ))}

            {isTyping && (
              <div className="flex items-center space-x-1.5 bg-white border border-slate-150 p-3 rounded-2xl w-16 shadow-xs">
                <span className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce delay-75" />
                <span className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce delay-150" />
                <span className="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce delay-225" />
              </div>
            )}

            <div ref={scrollRef} />
          </div>

          {/* Quick starter chips */}
          {messages.length === 1 && (
            <div className="p-3 bg-slate-50 border-t border-slate-150 flex flex-wrap gap-1.5">
              {starterChips.map((chip, i) => (
                <button
                  key={i}
                  onClick={() => handleSend(chip)}
                  className="px-2.5 py-1 bg-white border border-slate-200 hover:border-primary rounded-lg text-[9px] font-bold text-slate-600 transition cursor-pointer"
                >
                  {chip}
                </button>
              ))}
            </div>
          )}

          {/* Footer Input Bar */}
          <div className="p-3 border-t border-slate-200 bg-white flex items-center gap-2">
            <input
              type="text"
              placeholder="Nhập tin nhắn..."
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleSend(input)}
              className="flex-grow px-3 py-2 bg-slate-50 border border-slate-250 focus:border-primary focus:bg-white rounded-xl text-xs font-semibold outline-none transition"
            />
            <button
              onClick={() => handleSend(input)}
              className="w-8 h-8 rounded-xl bg-primary text-white flex items-center justify-center shadow-xs cursor-pointer hover:bg-primary-hover transition"
            >
              <i className="fa-solid fa-paper-plane text-xs" />
            </button>
          </div>

        </div>
      )}

    </div>
  )
}
