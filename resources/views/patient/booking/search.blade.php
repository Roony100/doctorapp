<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Book an Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Choose a specialty') }}</h3>

                    <form method="GET" action="{{ route('patient.booking.search') }}" class="flex gap-2">
                        <select name="specialty_id" class="border-gray-300 rounded flex-1 text-gray-900">
                            <option value="">{{ __('-- Select a specialty --') }}</option>
                            @foreach ($specialties as $specialty)
                                <option value="{{ $specialty->id }}"
                                    @selected(request('specialty_id') == $specialty->id)>
                                    {{ $specialty->libelle }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
                            {{ __('Search') }}
                        </button>
                    </form>
                </div>
            </div>

            @if (request()->filled('specialty_id'))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="font-semibold text-lg mb-4">{{ __('Doctors') }}</h3>

                        @forelse ($doctors as $doctor)
    <a href="{{ route('patient.booking.slots', $doctor) }}" class="block p-3 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
        <p class="font-semibold">Dr. {{ $doctor->user->name }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ $doctor->specialty->libelle }}
        </p>
    </a>
@empty
                            <p>{{ __('No doctors found for this specialty.') }}</p>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>