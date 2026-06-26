<div 
    x-data="{
        open: false,
        messages: [],
        inputText: '',
        loading: false,
        suggestedChips: [
            'Căn hộ Quận 7 dưới 10tr',
            'Nhà nguyên căn Thủ Đức',
            'Căn hộ chung cư Cầu Giấy',
            'Biệt thự Bình Thạnh giá tốt'
        ],
        safeGetItem(key) {
            try {
                return localStorage.getItem(key);
            } catch (e) {
                return null;
            }
        },
        safeSetItem(key, value) {
            try {
                localStorage.setItem(key, value);
            } catch (e) {
                // Ignore
            }
        },
        init() {
            // Load chat history from localStorage or load welcome message
            const saved = this.safeGetItem('bds_chat_history');
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.messages = parsed.map(msg => ({
                            role: msg.role || 'assistant',
                            text: msg.text || '',
                            properties: Array.isArray(msg.properties) ? msg.properties : []
                        }));
                    } else {
                        this.loadWelcomeMessage();
                    }
                } catch(e) {
                    this.loadWelcomeMessage();
                }
            } else {
                this.loadWelcomeMessage();
            }
            
            // Auto scroll on init
            this.$nextTick(() => this.scrollToBottom());
        },
        loadWelcomeMessage() {
            this.messages = [{
                role: 'assistant',
                text: 'Xin chào! Tôi là Trợ lý ảo AI của BDS NKS. Tôi có thể giúp bạn tìm kiếm bất động sản cho thuê theo khu vực, giá cả hoặc loại hình từ danh sách tin đăng thực tế. Bạn muốn tìm nhà ở khu vực nào hôm nay?',
                properties: []
            }];
            this.saveHistory();
        },
        saveHistory() {
            this.safeSetItem('bds_chat_history', JSON.stringify(this.messages));
        },
        clearChat() {
            if(confirm('Bạn có muốn xóa toàn bộ lịch sử trò chuyện?')) {
                this.loadWelcomeMessage();
            }
        },
        sendMessage(text = null) {
            const query = (text || this.inputText).trim();
            if (!query || this.loading) return;

            // Add user message
            this.messages.push({
                role: 'user',
                text: query,
                properties: []
            });

            if (!text) {
                this.inputText = '';
            }
            
            this.loading = true;
            this.saveHistory();
            this.$nextTick(() => this.scrollToBottom());

            // Build request history (exclude properties for payload and map roles)
            const payloadHistory = this.messages.slice(0, -1).map(msg => ({
                role: msg.role === 'assistant' ? 'assistant' : 'user',
                content: msg.text
            }));

            // Send to server
            fetch('{{ route('api.chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message: query,
                    history: payloadHistory
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.messages.push({
                        role: 'assistant',
                        text: data.reply,
                        properties: data.properties || []
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        text: data.reply || 'Đã xảy ra lỗi không xác định. Vui lòng thử lại.',
                        properties: []
                    });
                }
            })
            .catch(err => {
                console.error(err);
                this.messages.push({
                    role: 'assistant',
                    text: 'Xin lỗi, không thể kết nối tới máy chủ lúc này. Vui lòng kiểm tra lại kết nối mạng.',
                    properties: []
                });
            })
            .finally(() => {
                this.loading = false;
                this.saveHistory();
                this.$nextTick(() => this.scrollToBottom());
            });
        },
        formatText(text) {
            if (!text) return '';
            let formatted = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/\n/g, '<br>');
            return formatted;
        },
        scrollToBottom() {
            const container = this.$refs.messageArea;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    }"
    class="relative"
>
    <!-- Floating Chat Button -->
    <button 
        @click="open = !open; if(open) { $nextTick(() => scrollToBottom()) }"
        type="button"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-primary hover:bg-primary-hover text-white flex items-center justify-center shadow-lg hover:shadow-primary/30 transition-all duration-300 transform hover:scale-105 active:scale-95 focus:outline-none"
        title="Trò chuyện với AI"
    >
        <span x-show="!open" class="flex items-center justify-center">
            <i class="fa-solid fa-robot text-2xl animate-pulse"></i>
        </span>
        <span x-show="open" x-cloak class="flex items-center justify-center">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </span>
        
        <!-- Unread badge indicator (only shown if not open and history empty/new) -->
        <span x-show="!open && messages.length <= 1" class="absolute -top-1 -right-1 flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-amber-500"></span>
        </span>
    </button>

    <!-- Chat Window Container -->
    <div 
        x-show="open" 
        x-cloak
        x-transition
        class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-2rem)] h-[550px] max-h-[calc(100vh-8rem)] bg-white rounded-2xl shadow-2xl flex flex-col z-50 overflow-hidden border border-slate-100"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary to-primary-hover px-4 py-3.5 flex items-center justify-between text-white shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                    <i class="fa-solid fa-robot text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm leading-tight">Trợ lý ảo BDS NKS</h3>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[10px] text-white/80 font-medium">Hoạt động 24/7</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button 
                    @click="clearChat()" 
                    type="button" 
                    class="p-1.5 rounded-lg hover:bg-white/10 text-white/90 hover:text-white transition focus:outline-none"
                    title="Xóa cuộc trò chuyện"
                >
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
                <button 
                    @click="open = false" 
                    type="button" 
                    class="p-1.5 rounded-lg hover:bg-white/10 text-white/90 hover:text-white transition focus:outline-none"
                    title="Đóng chat"
                >
                    <i class="fa-solid fa-times text-base"></i>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div 
            x-ref="messageArea"
            class="flex-grow p-4 overflow-y-auto space-y-4 bg-slate-50 scrollbar-thin scrollbar-thumb-slate-200"
        >
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                    
                    <!-- Bubble text content -->
                    <div 
                        class="max-w-[85%] px-4 py-2.5 rounded-2xl shadow-sm text-sm leading-relaxed"
                        :class="msg.role === 'user' 
                            ? 'bg-primary text-white rounded-tr-none' 
                            : 'bg-white text-slate-700 border border-slate-100 rounded-tl-none'"
                        x-html="formatText(msg.text)"
                    ></div>

                    <!-- Recommended Properties list (Only if it exists on bot's message) -->
                    <template x-if="msg.role === 'assistant' && msg.properties && msg.properties.length > 0">
                        <div class="w-full mt-3 space-y-2.5 max-w-[90%]">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1 pl-1">
                                <i class="fa-solid fa-paperclip text-[10px]"></i> Bất động sản đề xuất:
                            </p>
                            <template x-for="prop in (msg.properties || [])" :key="prop.id">
                                <a 
                                    :href="'/property/' + prop.id" 
                                    class="block bg-white hover:bg-slate-50 border border-slate-100 rounded-xl overflow-hidden shadow-sm hover:shadow transition duration-200"
                                >
                                    <div class="flex gap-3 p-2">
                                        <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-slate-100 relative">
                                            <img 
                                                :src="prop.image ? (prop.image.startsWith('http') ? prop.image : '/' + prop.image) : '/images/apartment_1.png'" 
                                                :alt="prop.title" 
                                                class="w-full h-full object-cover"
                                            />
                                            <template x-if="prop.is_vip">
                                                <span class="absolute top-0.5 left-0.5 bg-amber-500 text-white text-[8px] font-bold px-1 rounded">VIP</span>
                                            </template>
                                        </div>
                                        <div class="flex-grow min-w-0 flex flex-col justify-between">
                                            <div>
                                                <h4 class="font-bold text-xs text-slate-800 truncate" x-text="prop.title"></h4>
                                                <p class="text-[10px] text-slate-500 truncate mt-0.5 flex items-center gap-1">
                                                    <i class="fa-solid fa-location-dot text-[8px]"></i>
                                                    <span x-text="prop.location"></span>
                                                </p>
                                            </div>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs font-bold text-primary" x-text="prop.price"></span>
                                                <span class="text-[10px] text-slate-400 font-medium" x-text="prop.area + 'm²'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>
                    
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="loading" x-cloak class="flex items-start gap-2.5">
                <div class="bg-white border border-slate-100 px-4 py-3 rounded-2xl rounded-tl-none shadow-sm flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        </div>

        <!-- Suggestion Chips -->
        <div class="px-3 py-2 bg-white border-t border-slate-100 flex gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-none">
            <template x-for="chip in suggestedChips" :key="chip">
                <button 
                    @click="sendMessage(chip)"
                    type="button"
                    class="px-2.5 py-1.5 rounded-full bg-slate-100 hover:bg-primary-light text-slate-600 hover:text-primary font-medium text-[11px] transition cursor-pointer select-none"
                    x-text="chip"
                ></button>
            </template>
        </div>

        <!-- Input Box -->
        <div class="p-3 bg-white border-t border-slate-100">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input 
                    x-model="inputText"
                    type="text" 
                    placeholder="Nhập câu hỏi tìm nhà đất của bạn..."
                    class="flex-grow px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition"
                    maxlength="500"
                    :disabled="loading"
                />
                <button 
                    type="submit"
                    class="w-10 h-10 rounded-xl bg-primary hover:bg-primary-hover text-white flex items-center justify-center shadow transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:scale-100 focus:outline-none"
                    :disabled="!inputText.trim() || loading"
                >
                    <i class="fa-solid fa-paper-plane text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</div>
