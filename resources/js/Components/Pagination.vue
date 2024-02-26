<template>
    <div v-if="links.links.length > 3">
        <div class="flex flex-wrap my-8 justify-center">
            <template v-for="(link, key) in links.links" :key="key">
                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                    v-html="link.label" />

                <Link v-else
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded focus:border-primary focus:text-primary"
                    :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url" v-html="link.label" />
            </template>
        </div>
    </div>

    <div class="mr-1 mb-1 px-4 py-3 text-sm leading-4 rounded">
        Página {{ links.current_page }} de {{ links.last_page }}, mostrando o intervalo de {{ links.from }} até {{ links.to
        }} de {{ links.total }} registro(s).
    </div>
</template>

<script>

import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    props: {
        links: Object,
    }
};
</script>
