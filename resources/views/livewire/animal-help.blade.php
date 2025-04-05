<div class="bg-gradient-to-r from-blue-200 to-cyan-200 py-5 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
            ANIMAL HELPLINE
        </h1>
        <div class="grid grid-cols-12 gap-4">
            <div class="md:col-span-12 lg:col-span-12 col-span-12">
                <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
                    <form wire:submit.prevent="submitApplication">
                        {{ $this->form }}
                        <div class="grid grid-cols-3 gap-4 mt-3 ">
                            <div>
                                 <div class="flex items-center mt-4">
                                    <input type="checkbox" id="agree" wire:model="agree" class="mr-2 w-5 h-5 text-blue-600">
                                    <label for="agree" class="text-gray-700 cursor-pointer" >
                                        I agree to the 
                                        <span class="text-blue-600 underline" wire:click.prevent="test">Data Privacy Policy</span>
                                    </label>
                                </div>
                            </div>
                            <div></div>
                            <button type="submit" 
                                class="bg-green-500 sm w-full p-3 rounded-lg text-lg text-white hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                <b>SUBMIT APPLICATION</b>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <x-filament::modal id="privacy" class="z-[999] mt-10" alignment="center" width="7xl">
        <x-slot name="heading">
            Provincial Government Of Davao de Oro Data Privacy and Policy
        </x-slot>
        <iframe src="{{ asset('images/Privacy-notice.pdf') }}#toolbar=0&navpanes=0&scrollbar=0&Content-Disposition=inline;filename='Privacy-notice.pdf'" class="w-full h-[400px] "></iframe>
    </x-filament::modal>

</div>
