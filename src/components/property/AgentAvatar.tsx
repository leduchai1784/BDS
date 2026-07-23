'use client'

import { useState } from 'react'

interface AgentAvatarProps {
  avatar: string
  name: string
  className?: string
}

export default function AgentAvatar({ avatar, name, className = "w-14 h-14" }: AgentAvatarProps) {
  const [imgSrc, setImgSrc] = useState(avatar)
  const [hasError, setHasError] = useState(false)

  const fallbackUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'User')}&background=0077bb&color=fff&bold=true`

  return (
    <div className={`${className} rounded-full overflow-hidden border border-slate-150 shadow-sm shrink-0 bg-primary/10 flex items-center justify-center`}>
      {!hasError ? (
        <img 
          src={imgSrc || fallbackUrl} 
          alt="" 
          className="w-full h-full object-cover"
          onError={() => {
            if (imgSrc !== fallbackUrl) {
              setImgSrc(fallbackUrl)
            } else {
              setHasError(true)
            }
          }}
        />
      ) : (
        <div className="w-full h-full bg-gradient-to-tr from-primary to-cyan-500 text-white font-black text-lg flex items-center justify-center uppercase">
          {(name || 'U').charAt(0)}
        </div>
      )}
    </div>
  )
}
