<template>
    <div>
        <AppLayout title="Editar orden de producción">
            <template #header>
                <div class="flex justify-between">
                    <Link :href="route('productions.index')"
                        class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
                    <i class="fa-solid fa-chevron-left"></i>
                    </Link>
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">
                            Editar orden de producción
                        </h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="update">
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
                    <div class="flex items-center">
                        <el-tooltip content="orden de venta" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                <i class="fa-solid fa-building"></i>
                            </span>
                        </el-tooltip>
                        <p>{{ sale.data.folio }} | {{ sale.data.company_branch.name }}</p>
                    </div>
                    <!-- products ordered to generate production -->
                    <div>

                        <el-divider content-position="left" class="col-span-full">Órdenes de producción</el-divider>

                        <InputError :message="errorMessage" class="col-span-full" />
                        <ol v-if="form.productions.length"
                            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
                            <template v-for="(item, index) in form.productions" :key="index">
                                <li class="flex justify-between items-center">
                                    <p class="text-sm">
                                        <span class="text-primary">{{ index + 1 }}.</span>
                                        {{ sale.data.catalogProductCompanySales.find(cpcs => cpcs.id ==
                                            item.catalog_product_company_sale_id).catalog_product_company.catalog_product.name
                                        }}
                                        | {{ item.tasks?.length }} operador(es) asignado(s)
                                    </p>
                                    <div class="flex space-x-2 items-center">
                                        <el-tag v-if="editProductionIndex == index">En edición</el-tag>
                                        <el-button @click="editProduction(index)" type="primary" circle>
                                            <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                                        </el-button>
                                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                            title="¿Continuar?" @confirm="deleteProduction(index)">
                                            <template #reference>
                                                <el-button type="danger" circle><i
                                                        class="fa-sharp fa-solid fa-trash"></i></el-button>
                                            </template>
                                        </el-popconfirm>
                                    </div>
                                </li>
                            </template>
                        </ol>

                        <div class="space-y-3 md:w-[92%] mx-auto border-2 border-[#b8b7b7] rounded-lg p-5 my-3">
                            <div class="flex items-center mt-3">
                                <el-tooltip content="Seleccionar producto ordenado" placement="top">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                </el-tooltip>
                                <el-select v-model="production.catalog_product_company_sale_id" clearable filterable
                                    placeholder="Busca en productos ordenados" no-data-text="No hay productos registrados"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="item in orderedProducts" :key="item.id"
                                        :label="item.catalog_product_company.catalog_product.name" :value="item.id" />
                                </el-select>
                            </div>

                            <el-divider v-if="production.catalog_product_company_sale_id" content-position="left"
                                class="col-span-full">Tareas</el-divider>

                            <ol v-if="tasks.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
                                <template v-for="(item, index) in tasks" :key="index">
                                    <li class="flex justify-between items-center">
                                        <p class="text-sm">
                                            <span class="text-primary">{{ index + 1 }}.</span>
                                            {{ operators.find(user => user.id == item.operator_id)?.name }}
                                            | estimado: {{ item.estimated_time_hours }}h {{ item.estimated_time_minutes }}m
                                        </p>
                                        <div class="flex space-x-2 items-center">
                                            <el-tag v-if="editTaskIndex == index">En edición</el-tag>
                                            <el-button @click="editTask(index)" type="primary" circle>
                                                <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                                            </el-button>
                                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No"
                                                icon-color="#0355B5" title="¿Continuar?" @confirm="deleteTask(index)">
                                                <template #reference>
                                                    <el-button type="danger" circle><i
                                                            class="fa-sharp fa-solid fa-trash"></i></el-button>
                                                </template>
                                            </el-popconfirm>
                                        </div>
                                    </li>
                                </template>
                            </ol>

                            <div v-if="production.catalog_product_company_sale_id"
                                class="space-y-3 md:w-[92%] mx-auto border-2 border-[#b8b7b7] rounded-lg p-5 my-3">
                                <div class="flex items-center">
                                    <el-tooltip content="Seleccionar a operador(es)" placement="top">
                                        <span
                                            class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </span>
                                    </el-tooltip>
                                    <el-select v-model="task.operator_id" clearable filterable
                                        placeholder="Busca en operadores" no-data-text="No hay operadores registrados"
                                        no-match-text="No se encontraron coincidencias">
                                        <el-option v-for="item in operators" :key="item.id" :label="item.name"
                                            :value="item.id" />
                                    </el-select>
                                </div>
                                <div class="flex items-center mt-3">
                                    <el-tooltip content="Seleccionar proceso de producción" placement="top">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                        <i class="fa-solid fa-person-digging text-sm"></i>
                                    </span>
                                    </el-tooltip>
                                    <el-select @change="getproductionProcess" v-model="task.tasks" clearable filterable
                                    placeholder="Selecciona el proceso de producción" no-data-text="No hay opciones registradas"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="item in production_processes.data" :key="item.id" :label="item.name"
                                        :value="item.name" />
                                    </el-select>
                                </div>
                                <!-- <div class="flex items-center">
                                    <el-tooltip content="Tareas" placement="top">
                                        <span
                                            class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                            <i class="fa-solid fa-list-check"></i>
                                        </span>
                                    </el-tooltip>
                                    <textarea v-model="task.tasks" class="textarea" autocomplete="off"
                                        placeholder="Tareas *"></textarea>
                                    <InputError :message="form.errors.user_tasks?.tasks" />
                                </div> -->
                                <div class="flex items-center">
                                    <el-tooltip content="Tiempo estimado de producción" placement="top">
                                        <span
                                            class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                            <i class="fa-regular fa-clock"></i>
                                        </span>
                                    </el-tooltip>

                                    <el-select disabled v-model="task.estimated_time_hours" clearable placeholder="Horas"
                                        no-data-text="No hay información" no-match-text="No se encontraron coincidencias">
                                        <el-option v-for="hour in 50" :key="hour" :label="(hour - 1)" :value="(hour - 1)" />
                                    </el-select>
                                    <el-select disabled v-model="task.estimated_time_minutes" clearable placeholder="Minutos"
                                        no-data-text="No hay información" no-match-text="No se encontraron coincidencias">
                                        <el-option v-for="minute in 59" :key="minute" :label="minute" :value="minute" />
                                    </el-select>

                                    <!-- <IconInput inputType="time" v-model="task.estimated_time" placeholder="Tiempo estimado de producción *">
                    <el-tooltip content="Tiempo estimado de producción" placement="top">
                      <i class="fa-solid fa-clock"></i>
                    </el-tooltip>
                  </IconInput> -->
                                    <!-- <el-time-picker v-model="task.estimated_time" placeholder="Tiempo estimado de producción"
                    format="HH:mm" /> -->
                                    <!-- <InputError :message="form.errors.user_tasks?.estimated_time" /> -->
                                </div>

                                <div calss="col-span-full">
                                    <SecondaryButton @click="addTask" type="button"
                                        :disabled="form.processing || !task.operator_id || !task.tasks">
                                        {{ editTaskIndex !== null ? 'Actualizar tareas' : 'Agregar tareas' }}
                                    </SecondaryButton>
                                </div>
                            </div>

                            <div calss="col-span-full">
                                <SecondaryButton @click="addProduction" type="button"
                                    :disabled="form.processing || !production.catalog_product_company_sale_id || !tasks.length">
                                    {{ editProductionIndex !== null ? 'Actualizar orden' : 'Agregar orden' }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>

                    <el-divider />

                    <div class="md:text-right">
                        <PrimaryButton :disabled="form.processing || orderedProducts.length != 0"> Actualizar órden de
                            producción
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </AppLayout>
    </div>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";

export default {
    data() {
        const form = useForm({
            productions: [],
            editedIndexes: [], //guarda los index editados para buscarlos en el controlador
        });

        return {
            form,
            tasks: [],
            saleId: null,
            editProductionIndex: null,
            editTaskIndex: null,
            errorMessage: null,
            orderedProducts: [],
            production: {
                tasks: [],
                user_id: this.$page.props.auth.user.id,
                catalog_product_company_sale_id: null,
            },
            task: {
                operator_id: null,
                tasks: null,
                estimated_time_hours: 0,
                estimated_time_minutes: 0,
            },
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        PrimaryButton,
        Link,
        InputError,
        IconInput,
    },
    props: {
        operators: Array,
        sale: Object,
        production_processes: Object,
    },
    methods: {
        update() {
            this.form.put(route("productions.update", this.sale.data.id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Órdenes de producción editadas",
                        type: "success",
                    });

                    this.form.reset();
                },
            });
        },
        // productions
        addTask() {
            const task = { ...this.task };

            if (this.editTaskIndex !== null) {
                this.tasks[this.editTaskIndex] = task;
                this.form.editedIndexes.push(this.editTaskIndex);
                this.editTaskIndex = null;
            } else {
                this.tasks.push(task);
            }

            this.task.operator_id = null;
            this.task.tasks = null;
            this.task.estimated_time_minutes = 0;
            this.task.estimated_time_hours = 0;

        },
        deleteTask(index) {
            this.tasks.splice(index, 1);
        },
        editTask(index) {
            const task = { ...this.tasks[index] };
            this.task = task;
            this.editTaskIndex = index;
        },

        // productions
        addProduction() {
            this.form.editedIndexes = null;
            let production = { ...this.production };
            production.tasks = this.tasks;

            if (this.editProductionIndex !== null) {
                this.form.productions[this.editProductionIndex] = production;
                this.editProductionIndex = null;
            } else {
                this.form.productions.push(production);
            }

            // reset production form & list of tasks
            this.tasks = [];
            this.production.user_id = this.$page.props.auth.user.id;
            this.production.catalog_product_company_sale_id = null;

            // remove ordered product from list
            const index = this.orderedProducts.findIndex(item => item.id == production.catalog_product_company_sale_id);
            this.orderedProducts.splice(index, 1);
        },
        deleteProduction(index) {
            // add ordered product to list
            const product = this.sale.data.catalogProductCompanySales.find(item => item.id == this.form.productions[index].catalog_product_company_sale_id);
            this.orderedProducts.push(product);

            this.form.productions.splice(index, 1);
        },
        editProduction(index) {
            const production = { ...this.form.productions[index] };

            this.form.productions[index].tasks.forEach((element) => {
                const tasks = {
                    operator_id: element.operator_id,
                    tasks: element.tasks,
                    estimated_time_hours: element.estimated_time_hours,
                    estimated_time_minutes: element.estimated_time_minutes,
                };

                this.tasks.push(tasks);
            });

            this.production = production;
            this.editProductionIndex = index;
        },
        getproductionProcess() {
            this.task.estimated_time_hours = null;
            this.task.estimated_time_minutes = null;
            const productionProcess = this.production_processes.data.find(item => item.name == this.task.tasks );
            const orderedProduct = this.sale.data.catalogProductCompanySales.find(item => item.id == this.production.catalog_product_company_sale_id);

            // Verificamos si productionProcess existe y tiene la propiedad "time"
                if (productionProcess && productionProcess.time) {
                const [hours, minutes, seconds] = productionProcess.time.split(':');

                // Convertimos las horas y minutos a números
                const hoursNumeric = parseInt(hours, 10);
                const minutesNumeric = parseInt(minutes, 10);
                const secondsNumeric = parseInt(seconds, 10);

                // Realizamos la multiplicación
                const totalSeconds = (hoursNumeric * 3600 + minutesNumeric * 60 + secondsNumeric) * orderedProduct.quantity;

                // Convertimos el resultado a horas y minutos
                const totalHours = Math.floor(totalSeconds / 3600);
                const remainingSeconds = totalSeconds % 3600;
                const totalMinutes = Math.floor(remainingSeconds / 60);

                // Sumamos el tiempo resultante al tiempo actual en task (si existe)
                this.task.estimated_time_hours = (this.task.estimated_time_hours || 0) + totalHours;
                this.task.estimated_time_minutes = (this.task.estimated_time_minutes || 0) + totalMinutes;

                // Ajustamos los minutos si superan 60
                this.task.estimated_time_hours += Math.floor(this.task.estimated_time_minutes / 60);
                this.task.estimated_time_minutes %= 60;
                }
            }
    },
    // watch: {
    //     saleId(newVal) {
    //         this.orderedProducts = this.sale.data.find(item => item.id == newVal).catalogProductsCompany;
    //     }
    // },
    mounted() {
        const groupedByCatalogId = this.sale.data.productions.reduce((result, currentObject) => {
            const catalogId = currentObject.catalog_product_company_sale_id;
            if (!result[catalogId]) {
                result[catalogId] = [];
            }
            result[catalogId].push(currentObject);
            return result;
        }, {});

        let productions = [];

        for (const item in groupedByCatalogId) {
            let user_id = groupedByCatalogId[item][0].user_id;
            let catalog_product_company_sale_id = groupedByCatalogId[item][0].catalog_product_company_sale_id;
            let _tasks = [];

            for (const currentTask in groupedByCatalogId[item]) {
                let task = {
                    operator_id: groupedByCatalogId[item][currentTask].operator_id,
                    tasks: groupedByCatalogId[item][currentTask].tasks,
                    estimated_time_hours: groupedByCatalogId[item][currentTask].estimated_time_hours,
                    estimated_time_minutes: groupedByCatalogId[item][currentTask].estimated_time_minutes,
                };

                _tasks.push(task);
            }

            let production = {
                tasks: _tasks,
                catalog_product_company_sale_id: catalog_product_company_sale_id,
                user_id: user_id
            }

            productions.push(production);
        }

        this.form.productions = productions;
    }

};
</script>
  