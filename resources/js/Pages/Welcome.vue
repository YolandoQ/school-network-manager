<script setup>

import { Head, Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import PrincipalTable from '@/Components/PrincipalTable.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    data: Object,
});

</script>
<template>
    <Head title="Welcome" />
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between">

            <div class="shrink-0 flex items-center">
                <ApplicationMark class="block h-9 w-auto" />
            </div>

            <div class="flex justify-end">
                <Link :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-900">
                    Log in
                </Link>
                <Link :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-900">
                    Register
                 </Link>
            </div>
        </div>
    </header>
    <div class="font-sans text-gray-900 antialiased">
        <div class="sm:fixed sm:top-0 sm:end-0 p-6 text-end z-10">
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <PrincipalTable :data="data"/>
                </div>
            </div>
        </div>
    </div>
</template>
