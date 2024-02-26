<script setup>

import AppLayout from '@/Layouts/AppLayout.vue';
import PrincipalTable from '@/Components/PrincipalTable.vue';

defineProps({
    data: Object,
    schools: Object
});

</script>

<template>
    <AppLayout title="Manager alunos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manager alunos
            </h2>
            <h5 class="leading-tight">
                Visualize, adicione, atualize ou exclua os alunos
            </h5>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <PrincipalTable :data="data" :schools="schools"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
