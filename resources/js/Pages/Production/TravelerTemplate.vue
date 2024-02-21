<template>
    <Head :title="cpcs.id" />
    <div>
        template
    </div>
</template>
<script>
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        Head,
    },
    props: {
        cpcs: Object,
    }
}
</script>