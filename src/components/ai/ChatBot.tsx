'use client'

import { useState, useRef, useEffect } from 'react'
import Link from 'next/link'
import { toast } from 'sonner'

interface Message {
  role: 'user' | 'assistant'
  content: string
  options?: string[]
  properties?: {
    id: string
    title: string
    price: string
    area: string
    location: string
    image?: string
    isVip?: boolean
  }[]
}

export default function ChatBot() {
  const [isOpen, setIsOpen] = useState(false)
  const [messages, setMessages] = useState<Message[]>([])
  const [input, setInput] = useState('')
  const [isTyping, setIsTyping] = useState(false)
  
  const scrollRef = useRef<HTMLDivElement>(null)

  // 1. Initialize: Load chat history from localStorage or load welcome message
  useEffect(() => {
    try {
      const saved = localStorage.getItem('bds_chat_history')
      if (saved) {
        const parsed = JSON.parse(saved)
        if (Array.isArray(parsed) && parsed.length > 0) {
          // Auto upgrade welcome message if old/invalid structure
          if (parsed[0] && parsed[0].role === 'assistant' && (!parsed[0].options || parsed[0].options.length === 0)) {
            loadWelcomeMessage()
          } else {
            setMessages(parsed)
          }
        } else {
          loadWelcomeMessage()
        }
      } else {
        loadWelcomeMessage()
      }
    } catch (e) {
      loadWelcomeMessage()
    }
  }, [])

  // 2. Save chat history to localStorage whenever messages change
  useEffect(() => {
    if (messages.length > 0) {
      try {
        localStorage.setItem('bds_chat_history', JSON.stringify(messages))
      } catch (e) {
        // Ignore quota errors
      }
    }
  }, [messages])

  // 3. Auto-scroll to bottom of conversation
  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollIntoView({ behavior: 'smooth' })
    }
  }, [messages, isTyping])

  const loadWelcomeMessage = () => {
    const welcomeMsg: Message = {
      role: 'assistant',
      content: 'Xin chào! Tôi là Trợ lý AI của BDS Rental. Tôi có thể hỗ trợ bạn tìm kiếm thông tin gì hôm nay? Bạn có thể hỏi tôi về:',
      options: [
        'Tìm kiếm bất động sản theo khu vực, giá thuê?',
        'Kinh nghiệm thuê nhà an toàn?',
        'Giải đáp các vấn đề pháp lý khi thuê nhà?'
      ]
    }
    setMessages([welcomeMsg])
    try {
      localStorage.setItem('bds_chat_history', JSON.stringify([welcomeMsg]))
    } catch (e) {}
  }

  const clearChat = () => {
    if (window.confirm('Bạn có muốn xóa toàn bộ lịch sử trò chuyện?')) {
      loadWelcomeMessage()
      toast.success('Đã xóa lịch sử trò chuyện.')
    }
  }

  const handleSend = async (text: string) => {
    const query = text.trim()
    if (!query || isTyping) return

    // Add user message
    const userMsg: Message = { role: 'user', content: query }
    setMessages(prev => [...prev, userMsg])
    setInput('')
    setIsTyping(true)

    // Build request history (exclude properties and map model roles)
    const historyPayload = messages.map(m => ({
      role: m.role === 'user' ? 'user' : 'model',
      content: m.content
    }))

    try {
      const res = await fetch('/api/chatbot', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: query, history: historyPayload })
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

  // Formatting utility for Bold tags and Linebreaks in React JSX safely
  const formatText = (text: string) => {
    if (!text) return ''
    const parts = text.split(/(\*\*.*?\*\*)/g)
    return parts.map((part, i) => {
      if (part.startsWith('**') && part.endsWith('**')) {
        return <strong key={i} className="font-bold text-slate-900">{part.slice(2, -2)}</strong>
      }
      const subLines = part.split('\n')
      return subLines.map((sub, j) => (
        <span key={`${i}-${j}`}>
          {sub}
          {j < subLines.length - 1 && <br />}
        </span>
      ))
    })
  }

  return (
    <div className="fixed bottom-6 right-6 z-50 font-sans text-left">
      
      {/* Floating Chat Button (Matches PHP style) */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        type="button"
        className="w-14 h-14 bg-primary hover:bg-primary-hover text-white rounded-full flex items-center justify-center shadow-lg hover:shadow-primary/30 transition-all duration-300 transform hover:scale-105 active:scale-95 focus:outline-none cursor-pointer"
        title="Trò chuyện với AI"
      >
        {!isOpen ? (
          <span className="flex items-center justify-center">
            <i className="fa-solid fa-robot text-2xl animate-pulse" />
          </span>
        ) : (
          <span className="flex items-center justify-center">
            <i className="fa-solid fa-xmark text-2xl" />
          </span>
        )}
      </button>

      {/* Chat Window Box (Matches PHP style layout) */}
      {isOpen && (
        <div className="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] h-[480px] max-h-[calc(100vh-8rem)] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-slate-100 animate-in fade-in slide-in-from-bottom-5 duration-200">
          
          {/* Header */}
          <div className="bg-gradient-to-r from-primary to-primary-hover px-4 py-3.5 flex items-center justify-between text-white shadow-sm">
            <div className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                <i className="fa-solid fa-robot text-lg text-white" />
              </div>
              <div>
                <h3 className="font-bold text-sm leading-tight">Trợ lý ảo BDS Rental</h3>
                <div className="flex items-center gap-1.5 mt-0.5">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                  <span className="text-[10px] text-white/80 font-medium">Hoạt động 24/7</span>
                </div>
              </div>
            </div>
            
            <div className="flex items-center gap-1">
              <button
                onClick={clearChat}
                type="button"
                className="p-1.5 rounded-lg hover:bg-white/10 text-white/90 hover:text-white transition focus:outline-none cursor-pointer"
                title="Xóa cuộc trò chuyện"
              >
                <i className="fa-solid fa-trash-can text-sm" />
              </button>
              <button
                onClick={() => setIsOpen(false)}
                type="button"
                className="p-1.5 rounded-lg hover:bg-white/10 text-white/90 hover:text-white transition focus:outline-none cursor-pointer"
                title="Đóng chat"
              >
                <i className="fa-solid fa-xmark text-base" />
              </button>
            </div>
          </div>

          {/* Messages list */}
          <div className="flex-grow p-4 overflow-y-auto bg-slate-50 space-y-4 scrollbar-thin">
            {messages.map((m, idx) => (
              <div key={idx} className={`flex flex-col ${m.role === 'user' ? 'items-end' : 'items-start'}`}>
                {/* Text Bubble */}
                <div className={`max-w-[85%] px-4 py-2.5 rounded-2xl text-[13px] leading-relaxed shadow-xs ${
                  m.role === 'user'
                    ? 'bg-primary text-white rounded-tr-none'
                    : 'bg-white text-slate-700 border border-slate-100 rounded-tl-none'
                }`}>
                  <div>{formatText(m.content)}</div>

                  {/* Interactive Option Chips inside assistant bubble (matching PHP style option chips) */}
                  {m.role === 'assistant' && m.options && m.options.length > 0 && (
                    <div className="mt-3 flex flex-col gap-2 w-full">
                      {m.options.map((opt, i) => (
                        <button
                          key={i}
                          onClick={() => handleSend(opt)}
                          type="button"
                          className="w-full text-left px-3.5 py-2 bg-slate-50 hover:bg-primary/5 border border-slate-200 hover:border-primary/40 rounded-xl text-[12px] font-medium text-slate-700 hover:text-primary transition duration-150 shadow-sm cursor-pointer focus:outline-none"
                        >
                          {opt}
                        </button>
                      ))}
                    </div>
                  )}
                </div>

                {/* Recommended properties list (Only if it exists in bot's message, matching PHP styling) */}
                {m.role === 'assistant' && m.properties && m.properties.length > 0 && (
                  <div className="w-full mt-3 space-y-2 max-w-[90%]">
                    <p className="text-[10px] font-bold text-slate-450 uppercase tracking-wider flex items-center gap-1 pl-1">
                      <i className="fa-solid fa-paperclip text-[10px]" /> Bất động sản đề xuất:
                    </p>
                    {m.properties.map(p => (
                      <Link
                        key={p.id}
                        href={`/property/${p.id}`}
                        target="_blank"
                        className="block bg-white hover:bg-slate-50 border border-slate-100 rounded-xl overflow-hidden shadow-sm hover:shadow transition duration-200"
                      >
                        <div className="flex gap-3 p-2 text-left">
                          <div className="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-slate-100 relative">
                            <img
                              src={p.image ? (p.image.startsWith('http') ? p.image : `/${p.image}`) : '/images/apartment_1.png'}
                              alt={p.title}
                              className="w-full h-full object-cover"
                            />
                            {p.isVip && (
                              <span className="absolute top-0.5 left-0.5 bg-amber-500 text-white text-[8px] font-bold px-1 rounded shadow-xs">VIP</span>
                            )}
                          </div>
                          <div className="flex-grow min-w-0 flex flex-col justify-between">
                            <div>
                              <h4 className="font-bold text-xs text-slate-800 truncate">{p.title}</h4>
                              <p className="text-[10px] text-slate-500 truncate mt-0.5 flex items-center gap-1 font-medium">
                                <i className="fa-solid fa-location-dot text-[8px]" />
                                <span>{p.location}</span>
                              </p>
                            </div>
                            <div className="flex items-center justify-between mt-1 font-extrabold">
                              <span className="text-xs text-primary">{p.price}</span>
                              <span className="text-[9px] text-slate-405 font-medium">{p.area}</span>
                            </div>
                          </div>
                        </div>
                      </Link>
                    ))}
                  </div>
                )}
              </div>
            ))}

            {isTyping && (
              <div className="flex items-start gap-2.5">
                <div className="bg-white border border-slate-100 px-4 py-3 rounded-2xl rounded-tl-none shadow-sm flex items-center gap-1.5">
                  <span className="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style={{ animationDelay: '0ms' }}></span>
                  <span className="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style={{ animationDelay: '150ms' }}></span>
                  <span className="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style={{ animationDelay: '300ms' }}></span>
                </div>
              </div>
            )}

            <div ref={scrollRef} />
          </div>

          {/* Footer Input Bar */}
          <div className="p-3 border-t border-slate-100 bg-white">
            <form
              onSubmit={(e) => {
                e.preventDefault()
                handleSend(input)
              }}
              className="flex items-center gap-2"
            >
              <input
                type="text"
                id="chatbotInput"
                name="chatbotInput"
                placeholder="Nhập câu hỏi tìm nhà đất của bạn..."
                value={input}
                onChange={(e) => setInput(e.target.value)}
                disabled={isTyping}
                maxLength={500}
                className="flex-grow px-3 py-2.5 bg-slate-55 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition"
              />
              <button
                type="submit"
                disabled={!input.trim() || isTyping}
                className="w-10 h-10 rounded-xl bg-primary hover:bg-primary-hover text-white flex items-center justify-center shadow transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:scale-100 focus:outline-none cursor-pointer"
              >
                <i className="fa-solid fa-paper-plane text-sm" />
              </button>
            </form>
          </div>

        </div>
      )}

    </div>
  )
}
