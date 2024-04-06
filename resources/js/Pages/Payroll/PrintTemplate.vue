<template>
    <payrollTemplate :toPrint="true" v-for="(user_id, index) in users_id_to_show" :key="index"
        :user="users.find(item => item.id == user_id)" :payrollId="payroll_id" />
</template>
  
<script>
import payrollTemplate from "@/Components/MyComponents/payrollTemplate.vue";

export default {
    data() {

        return {

        };
    },
    components: {
        payrollTemplate,
    },
    props: {
        payroll_id: Number,
        users: Array,
        users_id_to_show: Array,
    },
    methods: {
    }
};
</script>
  