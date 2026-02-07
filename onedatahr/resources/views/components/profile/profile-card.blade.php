<div x-data="{saveProfile(){
    console.log('Saving profile...');
}}">
    <div class="mb-6 rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="h-20 w-20 overflow-hidden rounded-full border border-gray-200 dark:border-gray-800 flex-shrink-0">
                    <img class="h-full w-full object-cover" src="{{ auth()->user() && auth()->user()->photo ? asset('storage/'.auth()->user()->photo) : asset('/images/user/owner.jpg') }}" alt="{{ auth()->user()->name }}" />
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ auth()->user()->name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ auth()->user()->roles->pluck('name')->map(function($r){ return ucwords(str_replace('_',' ',$r)); })->join(', ') ?? '—' }}
                        <span class="mx-2">•</span>
                        {{ auth()->user()->jabatan ?? '—' }}
                    </p>
                </div>
            </div>

            <div class="mt-3 lg:mt-0">
                <button @click="$dispatch('open-profile-info-modal')" type="button" aria-label="Edit profile"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill="" />
                    </svg>
                    Edit Profile
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Info Modal -->
    <x-ui.modal x-data="{ open: false }" @open-profile-info-modal.window="open = true" :isOpen="false" class="max-w-[700px]">
        <div
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
            <div class="px-2 pr-14">
                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Edit Personal Information
                </h4>
                <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                    Update your details to keep your profile up-to-date.
                </p>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800 dark:bg-green-900/20">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-800 dark:bg-red-900/20">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 p-2">
                    <!-- Left: Photo preview + controls -->
                    <div class="col-span-1 flex flex-col items-center gap-4">
                        <div class="h-36 w-36 overflow-hidden rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-center">
                            <img id="profile-photo-preview" data-original="{{ auth()->user() && auth()->user()->photo ? asset('storage/'.auth()->user()->photo) : asset('/images/user/owner.jpg') }}" src="{{ auth()->user() && auth()->user()->photo ? asset('storage/'.auth()->user()->photo) : asset('/images/user/owner.jpg') }}" alt="Profile photo preview" class="h-full w-full object-cover" />
                        </div>
                        <div class="w-full text-center">
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Change photo</label>
                            <input type="file" name="photo" accept="image/*" class="mx-auto text-sm text-gray-700 dark:text-gray-200" />
                            <div class="flex justify-center gap-2 mt-3">
                                <button type="button" id="profile-photo-crop" class="rounded-lg bg-yellow-500 px-3 py-2 text-sm text-white disabled:opacity-50" disabled aria-label="Open cropper">Crop</button>
                                <button type="button" id="profile-photo-reset" class="rounded-lg bg-gray-200 px-3 py-2 text-sm text-gray-800 dark:bg-gray-700 dark:text-gray-200 disabled:opacity-50" disabled aria-label="Reset photo">Reset</button>
                            </div>

                            <!-- Cropper Modal -->
                            <div id="profile-cropper-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-4">
                                <div class="bg-white dark:bg-gray-900 rounded-lg max-w-3xl w-full p-4">
                                    <div class="h-80 md:h-96 w-full overflow-hidden flex items-center justify-center">
                                        <img id="profile-cropper-img" src="" alt="Crop image" class="max-h-full max-w-full object-contain" />
                                    </div>
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <div class="flex gap-2">
                                            <button type="button" id="cropper-zoom-in" class="rounded bg-gray-200 px-3 py-2 dark:bg-gray-700">Zoom +</button>
                                            <button type="button" id="cropper-zoom-out" class="rounded bg-gray-200 px-3 py-2 dark:bg-gray-700">Zoom -</button>
                                            <button type="button" id="cropper-rotate-left" class="rounded bg-gray-200 px-3 py-2 dark:bg-gray-700">⟲</button>
                                            <button type="button" id="cropper-rotate-right" class="rounded bg-gray-200 px-3 py-2 dark:bg-gray-700">⟳</button>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" id="cropper-cancel" class="rounded bg-white border px-3 py-2 dark:bg-gray-800 dark:text-gray-200">Cancel</button>
                                            <button type="button" id="cropper-done" class="rounded bg-brand-500 px-3 py-2 text-white">Done</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Recommended: square image, max 2MB.</p>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-300 focus:ring-0" />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-300 focus:ring-0" />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-300 focus:ring-0" />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Role</label>
                                <input disabled type="text" value="{{ auth()->user()->roles->pluck('name')->map(function($r){ return ucwords(str_replace('_',' ',$r)); })->join(', ') ?? '—' }}" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm text-gray-800 bg-gray-50 dark:bg-gray-800 dark:text-gray-200" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Bio</label>
                                <input type="text" name="bio" value="{{ old('bio', auth()->user()->bio ?? '') }}" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-300 focus:ring-0" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">Social Links (optional)</label>
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <input type="text" name="facebook" value="{{ old('facebook', '') }}" placeholder="Facebook" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-300 focus:ring-0" />
                                    <input type="text" name="linkedin" value="{{ old('linkedin', '') }}" placeholder="LinkedIn" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900 placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-300 focus:ring-0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                    <button @click="open = false" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 sm:w-auto">Close</button>
                    <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">Save Changes</button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>
