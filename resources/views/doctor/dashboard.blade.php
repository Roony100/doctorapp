<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Doctor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @php $doctor = auth()->user()->doctor; @endphp

                    <h3 class="font-semibold text-lg mb-4">
                        Welcome, Dr. {{ auth()->user()->name }}
                    </h3>

                    @if ($doctor)
                        <p><strong>Specialty:</strong> {{ $doctor->specialty->libelle }}</p>
                        <p><strong>Numero d'ordre:</strong> {{ $doctor->numero_ordre }}</p>
                        <p><strong>Consultation duration:</strong> {{ $doctor->duree_consultation }} minutes</p>
                        <p><strong>Working hours set:</strong> {{ $doctor->availabilities()->count() }}</p>
                    @else
                        <p class="text-red-600">No doctor profile linked to this account yet.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <a href="{{ route('doctor.availabilities.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded inline-block">
                        Manage My Working Hours
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>