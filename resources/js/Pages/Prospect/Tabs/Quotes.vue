<template>
    <Loading v-if="loading" class="mt-10" />
    <div v-else class="my-5 text-sm">
        <table class="w-full">
            <thead>
                <tr class="*:text-start">
                    <th class="w-[15%]">Folio</th>
                    <th class="w-[25%]">Creado por</th>
                    <th class="w-[20%]">Creado el</th>
                    <th class="w-[20%]">Receptor</th>
                    <th class="w-[20%]">Autorizado por</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                <tr v-for="item in quotes" :key="item.id" class="*:pt-2">
                    <td>
                        <button @click="$inertia.get(route('quotes.show', item))" class="text-secondary hover:underline">
                            {{ 'COT-' + String(item.id).padStart(4, '0') }}
                        </button>
                    </td>
                    <td>{{ item.user.name }}</td>
                    <td>{{ dateFormat(item.created_at) }}</td>
                    <td>{{ item.receiver }}</td>
                    <td>{{ item.authorized_user_name }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import axios from 'axios';
import Loading from '@/Components/MyComponents/Loading.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            loading: true,
            quotes: [],
        }
    },
    components: {
        Loading,
    },
    props: {
        prospect: Object,
    },
    methods: {
        dateFormat(date) {
            const formattedDate = format(new Date(date), 'dd MMM, yyyy h:mm a', { locale: es });

            return formattedDate;
        },
        async fetchQuotes() {
            try {
                this.loading = true;
                const response = await axios.get(route('prospects.get-quotes', this.prospect));

                if (response.status === 200) {
                    this.quotes = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        }
    },
    async mounted() {
        await this.fetchQuotes();
    }
}
</script>