<!-- resources/views/partials/chat-popup.blade.php -->
<div x-data="chatPopup()" x-init="init()" class="fixed bottom-6 right-6 z-50">
  <!-- Botón flotante -->
  <button @click="toggle()" 
          x-show="!open"
          class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg">
    <!-- icono chat -->
    💬
  </button>

  <!-- Popup -->
  <div x-show="open" x-cloak
       class="w-80 md:w-96 bg-white rounded-2xl shadow-xl border overflow-hidden flex flex-col"
       x-transition:enter="transition transform duration-200"
       x-transition:enter-start="translate-y-4 opacity-0"
       x-transition:enter-end="translate-y-0 opacity-100"
       x-transition:leave="transition transform duration-150"
       x-transition:leave-start="translate-y-0 opacity-100"
       x-transition:leave-end="translate-y-4 opacity-0"
       style="max-height: 480px;">

    <!-- header -->
    <div class="flex items-center justify-between p-3 border-b">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm">AI</div>
        <div>
          <div class="text-sm font-semibold">Asistente</div>
          <div class="text-xs text-gray-500">Con acceso a tu BD</div>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button @click="minimize()" class="text-gray-500 hover:text-gray-700">—</button>
        <button @click="close()" class="text-red-500 hover:text-red-700">✕</button>
      </div>
    </div>

    <!-- mensajes -->
    <div class="p-3 overflow-y-auto flex-1 space-y-3" x-ref="messages">
      <template x-for="(m, i) in messages" :key="i">
        <div :class="m.role === 'user' ? 'text-right' : 'text-left'">
          <div x-text="m.role === 'user' ? m.text : m.text" 
               class="inline-block max-w-[85%] break-words px-3 py-2 rounded-lg"
               :class="m.role === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800'"></div>
        </div>
      </template>

      <div x-show="loading" class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full animate-pulse bg-gray-400"></div>
        <div class="w-3 h-3 rounded-full animate-pulse bg-gray-500"></div>
        <div class="w-3 h-3 rounded-full animate-pulse bg-gray-400"></div>
      </div>
    </div>

    <!-- input -->
    <form @submit.prevent="send()" class="p-3 border-t">
      <div class="flex gap-2">
        <input x-model="input" type="text" placeholder="Escribe tu pregunta..."
               class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring" />
        <button type="submit" :disabled="loading || input.trim() === ''"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg disabled:opacity-50">
          Enviar
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function chatPopup(){
    return {
      open: false,
      loading: false,
      input: '',
      messages: [
        { role: 'assistant', text: 'Hola 👋, pregúntame algo sobre los registros de la base de datos.' }
      ],
      init(){
        // Si quieres restaurar historial desde localStorage podrías hacerlo aquí
      },
      toggle(){ this.open = !this.open },
      close(){ this.open = false },
      minimize(){ this.open = false },
      async send(){
        if (!this.input.trim()) return;
        const text = this.input.trim();

        // Push mensaje usuario a UI
        this.messages.push({role: 'user', text});
        this.input = '';
        this.loading = true;

        try {
          const res = await axios.post('/chat/send', { message: text });
          let answer = 'No se recibió respuesta del servidor.';

          if (res.data && res.data.answer) {
            // Convertir a string si es objeto
            answer = typeof res.data.answer === 'string' ? res.data.answer : JSON.stringify(res.data.answer);
          }

          this.messages.push({role: 'assistant', text: answer});

          // Autoscroll
          this.$nextTick(() => {
            const el = this.$refs.messages;
            el.scrollTop = el.scrollHeight;
          });
        } catch (err) {
          console.error(err);
          this.messages.push({role:'assistant', text: 'Error al enviar la pregunta.'});
        } finally {
          this.loading = false;
        }
      }
    }
  }
</script>
