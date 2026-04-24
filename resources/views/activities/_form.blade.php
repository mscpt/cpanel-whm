<div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
    <p class="text-sm font-medium text-gray-700 mb-3">Registar atividade</p>
    <form method="POST" action="{{ route('activities.store') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="activityable_type" value="{{ $activityableType }}">
        <input type="hidden" name="activityable_id" value="{{ $activityableId }}">
        <div class="grid grid-cols-2 gap-3">
            <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="note">📝 Nota</option>
                <option value="call">📞 Chamada</option>
                <option value="email">✉️ Email</option>
                <option value="meeting">📅 Reunião</option>
            </select>
            <input type="datetime-local" name="occurred_at" value="{{ now()->format('Y-m-d\TH:i') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <input type="text" name="subject" placeholder="Assunto"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <textarea name="description" rows="2" placeholder="Descrição (opcional)"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
    </form>
</div>
