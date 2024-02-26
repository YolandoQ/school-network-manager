<script setup>

import AppLayout from '@/Layouts/AppLayout.vue';
import SchoolsTable from '@/Components/SchoolsTable.vue';

defineProps({
    data: Object,
    schools: Object
});

</script>

<template>
    <AppLayout title="Manager escolas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manager escolas
            </h2>
            <h5 class="leading-tight">
                Visualize, adicione, atualize ou exclua as escolas
            </h5>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <SchoolsTable :data="data" :schools="schools"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
