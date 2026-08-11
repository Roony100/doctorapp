<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Book with Dr. :name', ['name' => $doctor->user->name]) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <p><strong>{{ __('Specialty') }}:</strong> {{ $doctor->specialty->libelle }}</p>
                @if ($rescheduling)
                    <p class="mt-2 text-sm text-indigo-700 bg-indigo-50 p-2 rounded">
                        {{ __('Rescheduling your appointment from :date', ['date' => \Carbon\Carbon::parse($rescheduling->date_heure_debut)->format('D, M j - H:i')]) }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Mini calendar -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <a href="{{ $rescheduling ? route('patient.booking.reschedule', ['doctor' => $doctor, 'appointment' => $rescheduling, 'date' => $previousMonth . '-01']) : route('patient.booking.slots', ['doctor' => $doctor, 'date' => $previousMonth . '-01']) }}" class="text-indigo-600 text-sm">
                            &larr;
                        </a>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ $monthStart->format('F Y') }}
                        </h3>
                        <a href="{{ $rescheduling ? route('patient.booking.reschedule', ['doctor' => $doctor, 'appointment' => $rescheduling, 'date' => $nextMonth . '-01']) : route('patient.booking.slots', ['doctor' => $doctor, 'date' => $nextMonth . '-01']) }}" class="text-indigo-600 text-sm">
                            &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-gray-500 mb-1">
                        <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
                    </div>

                    <div class="grid grid-cols-7 gap-1">
                        @for ($i = 0; $i < $startOffset; $i++)
                            <div></div>
                        @endfor

                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $dateKey = $monthStart->copy()->day($day)->format('Y-m-d');
                                $status = $dayStatuses[$dateKey] ?? 'red';
                                $isSelected = $dateKey === $date;

                                $colorClass = match ($status) {
                                    'green' => 'bg-green-100 text-green-800 hover:bg-green-200',
                                    'half' => 'bg-gradient-to-br from-green-100 to-red-100 text-gray-800',
                                    default => 'bg-red-100 text-red-800',
                                };

                                if ($isSelected) {
                                    $colorClass .= ' ring-2 ring-indigo-600';
                                }
                            @endphp
                            <a href="{{ $rescheduling ? route('patient.booking.reschedule', ['doctor' => $doctor, 'appointment' => $rescheduling, 'date' => $dateKey]) : route('patient.booking.slots', ['doctor' => $doctor, 'date' => $dateKey]) }}"
                            class="block text-center text-xs p-2 rounded {{ $colorClass }}">
                                {{ $day }}
                            </a>
                        @endfor
                    </div>

                    <div class="flex gap-3 mt-3 text-xs text-gray-500">
                        <span><span class="inline-block w-3 h-3 bg-green-100 rounded"></span> {{ __('Free') }}</span>
                        <span><span class="inline-block w-3 h-3 bg-red-100 rounded"></span> {{ __('Full/Absent') }}</span>
                        <span><span class="inline-block w-3 h-3 bg-gradient-to-br from-green-100 to-red-100 rounded"></span> {{ __('Partly free') }}</span>
                    </div>
                </div>

                <!-- Slots for the selected day -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg mb-4 text-gray-900 dark:text-gray-100">
                        {{ __('Slots for :date', ['date' => $date]) }}
                    </h3>

                    @if (session('status'))
                        <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (count($slots) === 0)
                        <p>{{ __('No available slots for this day.') }}</p>
                    @else
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($slots as $slot)
                                <form method="POST" action="{{ route('patient.booking.store', $doctor) }}"
                                    onsubmit="return confirm('{{ $rescheduling ? 'Reschedule to' : 'Book the' }} {{ $slot['start'] }} - {{ $slot['end'] }} slot?')">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <input type="hidden" name="start_time" value="{{ $slot['start'] }}">
                                    @if ($rescheduling)
                                        <input type="hidden" name="reschedule_appointment_id" value="{{ $rescheduling->id }}">
                                    @endif
                                    <button type="submit" class="w-full border rounded p-2 hover:bg-indigo-50 text-gray-900">
                                        {{ $slot['start'] }} - {{ $slot['end'] }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>