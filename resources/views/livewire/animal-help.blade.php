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
                                    <label for="agree" class="text-gray-700 cursor-pointer" @click="openModal = true">
                                        I agree to the 
                                        <span class="text-blue-600 underline">Data Privacy Policy</span>
                                    </label>
                                </div>
                            </div>
                            <div></div>
                            <button type="submit" 
                                class="bg-green-500 sm w-full p-3 rounded-lg text-lg text-white hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!$wire.agree">
                                <b>SUBMIT APPLICATION</b>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Data Privacy Policy -->
    <div x-data="{ openModal: false }">
        <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-3/4 max-w-2xl">
                <h2 class="text-xl font-semibold mb-4">Data Privacy Policy</h2>
                <iframe src="{{ asset('storage/privacy-policy.pdf') }}" class="w-full h-96"></iframe>
                <div class="flex justify-end mt-4">
                    <button @click="openModal = false" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
