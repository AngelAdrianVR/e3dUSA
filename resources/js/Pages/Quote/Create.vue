<template>
    <div>
        <AppLayout title="Cotización - Crear">
            <template #header>
                <div class="flex justify-between">
                    <Link :href="route('quotes.index')"
                        class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
                    <i class="fa-solid fa-chevron-left"></i>
                    </Link>
                    <div class="flex items-center space-x-2 text-gray-600">
                        <h2 class="font-semibold text-xl leading-tight">Crear cotización</h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="store">
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <el-radio-group v-model="form.is_spanish_template" size="small">
                                <el-radio-button :label="1">Plantilla en español</el-radio-button>
                                <el-radio-button :label="0">Plantilla en inglés</el-radio-button>
                            </el-radio-group>
                        </div>
                        <div>
                            <IconInput v-model="form.reciever" inputPlaceholder="Nombre de quien recibe *" inputType="text">
                                A
                            </IconInput>
                            <InputError :message="form.errors.reciever" />
                        </div>
                        <div>
                            <IconInput v-model="form.department" inputPlaceholder="Departamento de quien recibe *"
                                inputType="text">
                                A
                            </IconInput>
                            <InputError :message="form.errors.department" />
                        </div>
                        <div>
                            <IconInput v-model="form.tooling_cost" inputPlaceholder="Costo de herramental *"
                                inputType="number">
                                A
                            </IconInput>
                            <InputError :message="form.errors.tooling_cost" />
                        </div>
                        <div>
                            <IconInput v-model="form.freight_cost" inputPlaceholder="Costo de flete *" inputType="text">
                                <i class="fa-solid fa-ruler-vertical"></i>
                            </IconInput>
                            <InputError :message="form.errors.freight_cost" />
                        </div>
                        <div>
                            <IconInput v-model="form.first_production_days"
                                inputPlaceholder="Dias para primera produccion *" inputType="text">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </IconInput>
                            <InputError :message="form.errors.first_production_days" />
                        </div>
                        <div>
                            <IconInput v-model="form.currency" inputPlaceholder="Productos cotizados en *"
                                inputType="number">
                                <i class="fa-solid fa-minus"></i>
                            </IconInput>
                            <InputError :message="form.errors.currency" />
                        </div>
                        <div>
                            <IconInput v-model="form.max_quantity" inputPlaceholder="Cantidad máxima" inputType="number">
                                <i class="fa-solid fa-plus"></i>
                            </IconInput>
                            <InputError :message="form.errors.max_quantity" />
                        </div>
                        <div class="flex">
                            <span
                                class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="form.notes" class="textarea" autocomplete="off" placeholder="Descripción *"
                                required></textarea>
                            <InputError :message="form.errors.notes" />
                        </div>

                    </div>
                    <div class="mt-2 mx-3 md:text-right">
                        <PrimaryButton :disabled="form.processing"> Crear producto </PrimaryButton>
                    </div>
                </div>
            </form>
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import { ref } from 'vue';

export default {
    data() {
        const form = useForm({
            reciever: null,
            department: null,
            tooling_cost: null,
            freight_cost: null,
            first_production_days: null,
            notes: null,
            currency: null,
            is_spanish_template: 1,
            company_branch_id: null,
        });

        return {
            form,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        Link,
        InputError,
        IconInput,
    },
    props: {

    },
    methods: {
        store() {
            this.form.post(route('quotes.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Cotización creada',
                        type: 'success'
                    });
                }
            });
        }
    },
};
</script>

<style scoped>
.el-radio-button > span.el-radio-button__inner{
    background-color: transparent !important;
    border: solid 1px #cccccc !important;
}

.el-radio-button__original-radio:checked .el-radio-button__inner{
    background-color: #0355B5 !important;
}

</style>
