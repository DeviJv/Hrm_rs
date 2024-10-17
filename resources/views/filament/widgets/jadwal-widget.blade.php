<x-filament-widgets::widget>
    <x-filament::section>
        <div
            class="flex flex-col md:flex-row lg:flex-row space-y-3 items-center justify-between h-full flex-wrap md:justify-around lg:justify-between">
            <div
                class="bg-white  shadow-md p-6 rounded-2xl border-2 border-gray-50 dark:bg-gray-900 dark:border-gray-700">
                <div class="flex flex-col">
                    <div>
                        <h2 class="font-bold text-gray-600 text-center dark:text-white">Jumlah Izin
                            Saya, </h2>
                    </div>
                    <div class="my-6">
                        <div class="flex flex-row space-x-4 items-center">
                            <div id="icon">
                                <span>
                                    <svg class="w-20 h-20 fill-stroke text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </span>
                            </div>
                            <div id="temp">
                                <h4 class="text-6xl text-gray-700 text-center dark:text-white">
                                    {{ $count_jumlah_hari_izin }}</h4>
                                <p class="text-xs text-gray-500 dark:text-white">Maximal Perbulan {{ $jumlah_max_izin }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full place-items-end text-right border-t-2 border-gray-100  mt-2">
                        <a href="{{ route('filament.admin.resources.tidak-masuks.index') }}"
                            class="text-gray-500 text-xs font-medium dark:text-white">Lihat
                            Selengkapnya</a>
                    </div>
                </div>
            </div>
            <div
                class="bg-white shadow-md p-6 rounded-2xl border-2 border-gray-50 dark:bg-gray-900 dark:border-gray-700">
                <div class="flex flex-col">
                    <div>
                        <h2 class="font-bold text-gray-600 text-center dark:text-white">Jumlah Cuti Saya </h2>
                    </div>
                    <div class="my-6">
                        <div class="flex flex-row space-x-4 items-center dark:text-white">
                            <div id="icon">
                                <span>
                                    <svg class="w-20 h-20 fill-stroke text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </span>
                            </div>
                            <div id="temp">
                                <h4 class="text-6xl text-gray-600 text-center dark:text-white">
                                    {{ $count_jumlah_hari_cuti }}</h4>
                                <p class="text-xs text-gray-500 dark:text-white">Maximal Perbulan {{ $jumlah_max_cuti }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full place-items-end text-right border-t-2 border-gray-100 mt-2">
                        <a href="{{ route('filament.admin.resources.tidak-masuks.index') }}"
                            class="text-gray-500 text-xs font-medium dark:text-white">Lihat
                            Selengkapnya</a>
                    </div>
                </div>
            </div>
            <div
                class="bg-white shadow-md p-6 rounded-2xl border-2 border-gray-50 dark:bg-gray-900 dark:border-gray-700">
                <div class="flex flex-col">
                    <div>
                        <h2 class="font-bold text-gray-600 text-center dark:text-white">Jumlah Lembur Saya</h2>
                    </div>
                    <div class="my-6">
                        <div class="flex flex-row space-x-4 items-center">
                            <div id="icon">
                                <span>

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-20 h-20 fill-stroke text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                                    </svg>

                                </span>
                            </div>
                            <div id="temp">
                                <h4 class="text-6xl text-gray-600 text-center dark:text-white">
                                    {{ $count_jumlah_jam_lembur }}</h4>
                                <p class="text-xs text-gray-500 dark:text-white">Maximal Perbulan - </p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full place-items-end text-right border-t-2 border-gray-100 mt-2">
                        <a href="{{ route('filament.admin.resources.tidak-masuks.index') }}"
                            class="text-gray-500 text-xs font-medium dark:text-white">Lihat
                            Selengkapnya</a>
                    </div>
                </div>
            </div>
            <div
                class="bg-white shadow-md p-6 rounded-2xl border-2 border-gray-50 dark:bg-gray-900 dark:border-gray-700">
                <div class="flex flex-col">
                    <div>
                        <h2 class="font-bold text-gray-600 text-center dark:text-white">Pengajuan Cuti/Lembur (Pending),
                        </h2>
                    </div>
                    <div class="my-6">
                        <div class="flex flex-row space-x-4 items-center">
                            <div id="icon">
                                <span>
                                    <svg class="w-20 h-20 fill-stroke text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                </span>
                            </div>
                            <div id="temp">
                                <h4 class="text-6xl text-gray-600 text-center dark:text-white">
                                    {{ $count_jumlah_hari_cuti_pending }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-white">Maximal Perbulan {{ $jumlah_max_cuti }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full place-items-end text-right border-t-2 border-gray-100 mt-2">
                        <a href="{{ route('filament.admin.resources.tidak-masuks.index') }}"
                            class="text-gray-500 text-xs font-medium dark:text-white">Lihat
                            Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
