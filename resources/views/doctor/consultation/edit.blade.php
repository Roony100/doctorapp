<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Consultation Notes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="mb-4 text-gray-900 dark:text-gray-100">
                    <strong>{{ __('Patient') }}:</strong> {{ $appointment->patient->user->name }}<br>
                    <strong>{{ __('Date') }}:</strong> {{ \Carbon\Carbon::parse($appointment->date_heure_debut)->format('D, M j Y - H:i') }}
                </p>

                @if ($errors->any())
                    <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('doctor.consultation.store', $appointment) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block mb-1 text-gray-900 dark:text-gray-100">{{ __('Diagnostic') }}</label>
                        <textarea name="diagnostic" rows="3" class="border rounded w-full p-2">{{ old('diagnostic', $appointment->consultation->diagnostic ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-1 text-gray-900 dark:text-gray-100">{{ __('Prescription') }}</label>
                        <textarea name="prescription" rows="3" class="border rounded w-full p-2">{{ old('prescription', $appointment->consultation->prescription ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block mb-1 text-gray-900 dark:text-gray-100">{{ __('Notes') }}</label>
                        <textarea name="notes" rows="4" class="border rounded w-full p-2">{{ old('notes', $appointment->consultation->notes ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
                        {{ __('Save Notes') }}
                    </button>
                </form>

            </div>

        </div>
    </div>
</x-app-layout>