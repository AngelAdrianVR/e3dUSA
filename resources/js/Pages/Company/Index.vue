<template>
    <div>
        <AppLayout title="Cartera de clientes">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Cartera de Clientes</h2>
                    </div>
                    <div class="flex items-center space-x-2" v-if="$page.props.auth.user.permissions.includes('Crear clientes')">
                        <!-- <ThirthButton @click="openReport()" class="text-secondary border-secondary focus:ring-secondary">Reporte de contactos</ThirthButton> -->
                        <Link :href="route('companies.create')">
                            <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="relative overflow-hidden min-h-[60vh]">
                <NotificationCenter module="companies" />
                <!-- tabla -->
                <div class="lg:w-5/6 mx-auto mt-6">
                    <div class="flex justify-between">
                        <!-- pagination -->
                        <div>
                            <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                                :total="companies.data.length" />
                        </div>
                        <!-- buttons -->
                        <div v-if="$page.props.auth.user.permissions.includes('Eliminar clientes')">
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                title="¿Continuar?" @confirm="deleteSelections">
                                <template #reference>
                                    <el-button type="danger" plain class="mb-3"
                                        :disabled="disableMassiveActions">Eliminar</el-button>
                                </template>
                            </el-popconfirm>
                        </div>
                        <!-- buscador -->
                        <IndexSearchBar @search="handleSearch" />
                    </div>

                    <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                        @selection-change="handleSelectionChange" ref="multipleTableRef"
                        :row-class-name="tableRowClassName">
                        <el-table-column type="selection" width="30" />
                        <el-table-column prop="id" label="ID" width="55" />
                        <el-table-column prop="business_name" label="Nombre" width="120" />
                        <el-table-column prop="phone" label="Teléfono" width="120" />
                        <el-table-column prop="rfc" label="RFC" width="100" />
                        <el-table-column prop="post_code" label="Código postal" width="120" />
                        <el-table-column label="Vendedor" width="180">
                            <template #default="scope">
                                <div class="flex items-center">
                                    <p class="mr-2" :style="{ color: getColorHex(scope.row.seller?.id) }">
                                        <i class="fa-solid fa-star"></i>
                                    </p>
                                    <p class="flex-0 w-[80%]">{{ scope.row.seller?.name }}</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="company_branches_names" label="Sucursales" />
                        <el-table-column prop="fiscal_address" label="Domicilio Fiscal" />
                        <el-table-column align="right">
                            <template #default="scope">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <button @click.stop class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'show-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                Ver</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="$page.props.auth.user.permissions.includes('Editar clientes')"
                                                :command="'edit-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Editar</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="$page.props.auth.user.permissions.includes('Crear clientes')"
                                                :command="'clone-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                                </svg>
                                                Clonar</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
                <!-- tabla -->
            </div>
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';


export default {
    data() {


        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            // pagination
            itemsPerPage: 10,
            start: 0,
            end: 10,
        };
    },
    components: {
        NotificationCenter,
        SecondaryButton,
        IndexSearchBar,
        PrimaryButton,
        ThirthButton,
        AppLayout,
        TextInput,
        Link,
    },
    props: {
        companies: Object
    },
    methods: {
        openReport() {
            const url = this.route('companies.contacts-report');
            window.open(url, '_blank');
        },
        getColorHex(number) {
            if (number) {
                // Ajusta el tono (hue) en función del número proporcionado
                let tono = (number * 30) % 360;

                // Saturation y lightness se mantienen constantes para colores vibrantes
                let saturacion = 80;
                let luminosidad = 40;

                // Convierte de HSL a hexadecimal
                let colorHex = this.hslToHex(tono, saturacion, luminosidad);

                return colorHex;
            } else {

                return '#cccccc';
            }
        },
        // Función para convertir de HSL a hexadecimal
        hslToHex(h, s, l) {
            h /= 360;
            s /= 100;
            l = l > 40 ? 40 : l;
            l /= 100;

            let r, g, b;

            if (s === 0) {
                r = g = b = l;
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1 / 6) return p + (q - p) * 6 * t;
                    if (t < 1 / 2) return q;
                    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                    return p;
                };

                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;

                r = hue2rgb(p, q, h + 1 / 3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1 / 3);
            }

            const toHex = x => {
                const hex = Math.round(x * 255).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            };

            return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
        },
        handleSearch(search) {
            this.search = search;
        },
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        handleRowClick(row) {
            this.$inertia.get(route('companies.show', row));
        },

        async clone(company_id) {
            try {
                const response = await axios.post(route('companies.clone', {
                    company_id: company_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.companies.data.unshift(response.data.newItem);

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message + ". Actualizar página",
                    type: 'error'
                });
                console.log(err);
            }
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('companies.massive-delete', {
                    companies: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of companies
                    let deletedIndexes = [];
                    this.companies.data.forEach((company, index) => {
                        if (this.$refs.multipleTableRef.value.includes(company)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar clientes por índice
                    for (const index of deletedIndexes) {
                        this.companies.data.splice(index, 1);
                    }

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },

        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('companies.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.companies.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.companies.data.filter(
                    (company) =>
                        company.business_name.toLowerCase().includes(this.search.toLowerCase()) ||
                        company.rfc.toLowerCase().includes(this.search.toLowerCase()) ||
                        company.company_branches_names.toLowerCase().includes(this.search.toLowerCase()) ||
                        company.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                        company.seller_name?.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
