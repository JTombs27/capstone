<div class="bg-gradient-to-r from-blue-200 to-cyan-200 py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
            ANIMAL HELPLINE
        </h1>
        <div class="grid grid-cols-12 gap-4">
            <div class="md:col-span-12 lg:col-span-12 col-span-12">
                <!-- Card -->
                <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
                    <!-- Shipping Address -->
                    <h2 class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
                        PLEASE PROVIDE INFORMATION
                    </h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 dark:text-white mb-1" for="first_name">
                                First Name
                            </label>
                            <input wire:model.live="applicationModel.first_name" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="first_name" type="text">
                            </input>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-white mb-1" for="last_name">
                                Last Name
                            </label>
                            <input wire:model.live="applicationModel.last_name" class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="last_name" type="text">
                            </input>
                        </div>
                        <div class="">
                        <label class="block text-gray-700 dark:text-white mb-1" for="phone">
                            Phone
                        </label>
                        <input wire:model.live="applicationModel.contact_number"  class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="phone" type="text">
                        </input>
                    </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-gray-700 dark:text-white mb-1" for="city">
                            Municipality
                        </label>
                        <select wire:model.live="applicationModel.query_municipality" class="py-3 px-4 pe-9 block w-full border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            <option selected="">Select Here</option>
                            @foreach ($municipality as $item)
                                 <option value="{{$item->id}}">{{$item->municipality_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-4">
                        <label class="block text-gray-700 dark:text-white mb-1" for="city">
                            Barangay
                        </label>
                        <select wire:model.live="applicationModel.query_barangay" class="py-3 px-4 pe-9 block w-full border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            <option selected="">Select Here</option>
                            @foreach ($barangays as $item)
                                 <option value="{{$item->id}}">{{$item->barangay_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2 mt-4">
                        <h2 class="text-xl font-bold underline text-gray-700 dark:text-white mb-2">
                            APPLICATION INFORMATION
                        </h2>
                        <div class="col-span-full grid grid-cols-12 gap-2">
                            <div class="col-span-6">
                                <label class="block text-gray-700 dark:text-white mb-1" for="first_name">
                                    Select Animals (Unsa nga Hayop?)
                                </label>
                                <select wire:change.prevent="resetSymptoms()" wire:model.live="applicationModel.animal_id" class="py-3 px-4 pe-9 block w-full border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <option selected="">Select Here</option>
                                    @foreach ($animals as $item)
                                    <option value="{{$item->id}}">{{$item->animal_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="col-span-3">
                                 <label class="block text-gray-700 dark:text-white mb-1" for="phone">
                                    Affected Count
                                </label>
                                <input wire:model.live="applicationModel.affected_count"  class="w-full rounded-lg border py-2 px-4 dark:bg-gray-700 dark:text-white dark:border-none" id="phone" type="text">
                                </input>
                            </div>
                             <div class="col-span-3">
                                 <label class="block text-gray-700 dark:text-white mb-1" for="phone">
                                    Death Count
                                </label>
                                <input wire:model.live="applicationModel.death_count"  class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="phone" type="text">
                                </input>
                            </div>
                        </div>
                       
                        <div class="mt-4">
                             <hr3>Please Check Sypmtoms</hr3>
                            <div class="mt-4 flex flex-wrap gap-x-6">
                                @foreach ($symptoms as $item)
                                     <div class="flex">
                                        <input wire:model.live="symptomsAdded" value="{{$item->id}}" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" id="hs-checkbox-group-{{$item->id}}" >
                                        <label for="hs-checkbox-group-{{$item->id}}" class="text-sm text-gray-1000 ms-3 dark:text-neutral-400">{{$item->symptom_descr}}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-gray-700 dark:text-white mb-1" for="city">
                                Upload Animal Photos
                            </label>
                            <div class="max-w-sm">
                                <form>
                                    <label class="block">
                                        <span class="sr-only">Choose profile photo</span>
                                        <input type="file" class="block w-full text-sm text-gray-500
                                            file:me-4 file:py-2 file:px-4
                                            file:rounded-lg file:border
                                            file:text-sm file:font-semibold
                                            file:bg-blue-600 file:text-white
                                            hover:file:bg-blue-700
                                            file:disabled:opacity-50 file:disabled:pointer-events-none
                                            dark:text-neutral-500
                                            dark:file:bg-blue-500
                                            dark:hover:file:bg-blue-400
                                        ">
                                    </label>
                                </form>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-gray-700 dark:text-white mb-1" for="city">
                                Other Information (E describe pa ug maayo.)
                            </label>
                            <textarea wire:model.live="applicationModel.other_info" class="py-3 px-4 block w-full border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" rows="4" placeholder="Type here.."></textarea>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div></div>
                            <div></div>
                             <button wire:click.prevent='submitApplication()' class="bg-green-500 mt-4 w-full p-3 rounded-lg text-lg text-white hover:bg-green-600">
                                SUBMIT APPLICATION
                            </button>
                        </div>
                        <!-- <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-gray-700 dark:text-white mb-1" for="state">
                                State
                            </label>
                            <input class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="state" type="text">
                            </input>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-white mb-1" for="zip">
                                ZIP Code
                            </label>
                            <input class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none" id="zip" type="text">
                            </input>
                        </div>
                    </div> -->
                    </div>
                    <!-- <div class="text-lg font-semibold mb-4">
                    Select Payment Method
                </div>
                <ul class="grid w-full gap-6 md:grid-cols-2">
                    <li>
                        <input class="hidden peer" id="hosting-small" name="hosting" required="" type="radio" value="hosting-small" />
                        <label class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700" for="hosting-small">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">
                                    Cash on Delivery
                                </div>
                            </div>
                            <svg aria-hidden="true" class="w-5 h-5 ms-3 rtl:rotate-180" fill="none" viewbox="0 0 14 10" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                </path>
                            </svg>
                        </label>
                    </li>
                    <li>
                        <input class="hidden peer" id="hosting-big" name="hosting" type="radio" value="hosting-big">
                        <label class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-blue-500 peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700" for="hosting-big">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">
                                    Stripe
                                </div>
                            </div>
                            <svg aria-hidden="true" class="w-5 h-5 ms-3 rtl:rotate-180" fill="none" viewbox="0 0 14 10" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                </path>
                            </svg>
                        </label>
                        </input>
                    </li>
                </ul> -->
                </div>
                <!-- End Card -->
            </div>
            {{-- <div class="md:col-span-12 lg:col-span-4 col-span-12">
                <div class="bg-white rounded-xl shadow p-4 sm:p-7 dark:bg-slate-900">
                    
                </div>
                <button class="bg-green-500 mt-4 w-full p-3 rounded-lg text-lg text-white hover:bg-green-600">
                    SUBMIT APPLICATION
                </button>
            </div> --}}
        </div>
    </div>
</div>