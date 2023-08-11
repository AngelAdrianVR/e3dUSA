<template>
    <AppLayout title="Catalogo de productos - Crear">
        <template #header>
            <div class="flex justify-between">
                <Link :href="route('catalog-products.index')"
                    class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center md:ml-5">
                <i class="fa-solid fa-chevron-left"></i>
                </Link>
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Agregar nuevo producto a catálogo</h2>
                </div>
            </div>
        </template>

        <!-- Form -->
        <form @submit.prevent="store">
            <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
                <div class="flex items-center mb-2">
                    <el-tooltip content="Tipo de producto (necesario para generar el número de parte)" placement="top">
                        <span
                            class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                            <i class="fa-solid fa-tag"></i>
                        </span>
                    </el-tooltip>
                    <el-select @change="generatePartNumber" v-model="productType" placeholder="Tipo de producto *">
                        <el-option v-for="item in productTypes" :key="item.value" :label="item.label" :value="item.value">
                            <span style="float: left">{{ item.label }}</span>
                            <span style="
                  float: right;
                  color: #cccccc;
                  font-size: 13px;
                  ">{{ item.value }}</span>
                        </el-option>
                    </el-select>
                </div>
                <div class="mb-2">
                    <IconInput v-model="brand" @change="generatePartNumber" inputPlaceholder="Marca del producto *"
                        inputType="text">
                        <el-tooltip content="Marca del producto (si no tiene marca colocar 'Generico')" placement="top">
                            <i class="fa-solid fa-copyright"></i>
                        </el-tooltip>
                    </IconInput>
                </div>
                <div class="mb-2">
                    <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
                        <el-tooltip content="Nombre" placement="top">
                            A
                        </el-tooltip>
                    </IconInput>
                    <InputError :message="form.errors.name" />
                </div>
                <div class="md:grid gap-6 mb-6 grid-cols-2">
                    <div class="flex items-center">
                        <el-tooltip content="Número de parte *" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                #
                            </span>
                        </el-tooltip>
                        <input v-model="form.part_number" type="text"
                            class="input disabled:cursor-not-allowed disabled:opacity-80" placeholder="Número de parte *"
                            disabled>
                        <InputError :message="form.errors.part_number" />
                    </div>
                    <div class="flex items-center">
                        <el-tooltip content="Materias primas" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                <i class="fa-solid fa-ruler-vertical"></i>
                            </span>
                        </el-tooltip>
                        <el-select v-model="form.measure_unit" clearable placeholder="Busca unidad de medida"
                            no-data-text="No hay unidades de medida registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="(item, index) in mesureUnits" :key="index" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.measure_unit" />
                    </div>
                    <div>
                        <IconInput v-model="form.min_quantity" inputPlaceholder="Cantidad mínima" inputType="number">
                            <el-tooltip content="Cantidad mínima" placement="top">
                                <i class="fa-solid fa-minus"></i>
                            </el-tooltip>
                        </IconInput>
                        <InputError :message="form.errors.min_quantity" />
                    </div>
                    <div>
                        <IconInput v-model="form.max_quantity" inputPlaceholder="Cantidad máxima" inputType="number">
                            <el-tooltip content="Cantidad máxima" placement="top">
                                <i class="fa-solid fa-plus"></i>
                            </el-tooltip>
                        </IconInput>
                        <InputError :message="form.errors.max_quantity" />
                    </div>
                    <div class="flex col-span-full">
                        <el-tooltip content="Descripción" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                        </el-tooltip>
                        <textarea v-model="form.description" class="textarea mb-1" autocomplete="off"
                            placeholder="Descripción"></textarea>
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="col-span-full">
                        <div class="flex space-x-2 mb-1">
                            <IconInput v-model="newFeature" inputPlaceholder="Ingresa una caracteristica" inputType="text"
                                class="w-full">
                                <el-tooltip content="Caracteristicas" placement="top">
                                    <i class="fa-solid fa-palette"></i>
                                </el-tooltip>
                            </IconInput>
                            <SecondaryButton @click="addFeature" type="button">
                                Agregar
                                <i class="fa-solid fa-arrow-down ml-2"></i>
                            </SecondaryButton>
                        </div>
                        <el-select v-model="form.features" multiple clearable placeholder="Caracteristicas"
                            no-data-text="Agrega primero una caracteristica">
                            <el-option v-for="feature in features" :key="feature" :label="feature"
                                :value="feature"></el-option>
                        </el-select>
                    </div>
                    <div class="col-span-full mt-2">
                        <div class="flex items-center">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                <el-tooltip content="Imagen del producto" placement="top">
                                    <i class="fa-solid fa-images"></i>
                                </el-tooltip>
                            </span>
                            <input @input="form.media = $event.target.files[0]" class="input h-12 rounded-lg
                            file:mr-4 file:py-1 file:px-2
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary file:text-white
                            file:cursor-pointer
                            hover:file:bg-red-600" aria-describedby="file_input_help" id="file_input" type="file">
                        </div>
                        <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">SVG, PNG, JPG o
                            GIF (MAX. 4 MB).</p>
                    </div>

                    <el-divider content-position="left" class="col-span-full">Componentes de este producto</el-divider>

                    <!-- product components -->
                    <InputError :message="form.errors.rawMaterials" class="col-span-full" />
                    <ol v-if="form.raw_materials.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
                        <template v-for="(item, index) in form.raw_materials" :key="index">
                            <li class="flex justify-between items-center">
                                <p class="text-sm">
                                    <span class="text-primary">{{ index + 1 }}.</span>
                                    {{ raw_materials.find(prd => prd.id === item.raw_material_id)?.name }}
                                    (x{{ item.quantity }} unidades)
                                </p>
                                <div class="flex space-x-2 items-center">
                                    <el-tag v-if="editIndex == index">En edición</el-tag>
                                    <el-button @click="editProduct(index)" type="primary" circle>
                                        <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                                    </el-button>
                                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                                        title="¿Continuar?" @confirm="deleteProduct(index)">
                                        <template #reference>
                                            <el-button type="danger" circle><i
                                                    class="fa-sharp fa-solid fa-trash"></i></el-button>
                                        </template>
                                    </el-popconfirm>
                                </div>
                            </li>
                        </template>
                    </ol>
                    <div class="flex items-center my-2">
                        <el-tooltip content="Materias primas" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </el-tooltip>
                        <el-select v-model="rawMaterial.raw_material_id" clearable filterable
                            placeholder="Busca en materias primas" no-data-text="No hay materias primas registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in raw_materials" :key="item.id" :label="item.name" :value="item.id" />
                        </el-select>
                        <!-- <el-tooltip content="Agregar materia prima" placement="top">
                            <Link class="ml-3" :href="route('raw-materials.create')">
                            <PrimaryButton class="!rounded-lg">+</PrimaryButton>
                            </Link>
                        </el-tooltip> -->
                    </div>
                    <div class="flex items-center mb-2">
                        <el-tooltip content="proceso(s) de produccion" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </el-tooltip>
                        <el-select v-model="rawMaterial.production_costs" multiple clearable filterable
                            placeholder="Selecciona proceso(s) de produccion" no-data-text="No hay procesos registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in production_costs" :key="item.id" :label="item.name"
                                :value="item.id" />
                        </el-select>
                        <!-- <el-tooltip content="Agregar proceso de producción" placement="top">
                            <Link class="ml-3" :href="route('production-costs.create')">
                            <PrimaryButton class="!rounded-lg">+</PrimaryButton>
                            </Link>
                        </el-tooltip> -->
                    </div>
                    <div class="grid grid-cols-3 gap-x-1">
                        <IconInput v-model="rawMaterial.quantity" inputPlaceholder="Cantidad necesaria *" inputType="number"
                            inputStep="0.1" class="col-span-2">
                            <el-tooltip content="Cantidad necesaria de materia prima seleccionada" placement="top">
                                #
                            </el-tooltip>
                        </IconInput>
                        <span class="text-sm pt-2">{{ raw_materials.find(item => item.id ==
                            rawMaterial.raw_material_id)?.measure_unit }}</span>
                    </div>
                    <div calss="col-span-full">
                        <SecondaryButton @click="addProduct" type="button"
                            :disabled="!rawMaterial.raw_material_id || !rawMaterial.quantity || form.processing">
                            {{ editIndex !== null ? 'Actualizar componente' : 'Agregar componente a lista' }}
                        </SecondaryButton>
                    </div>

                </div>

                <el-divider />
                <div class="md:text-right">
                    <PrimaryButton :disabled="form.processing || !form.raw_materials.length"> Crear producto </PrimaryButton>
                </div>
            </div>
        </form>
    </AppLayout>
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
            name: null,
            part_number: null,
            measure_unit: null,
            min_quantity: null,
            max_quantity: null,
            description: null,
            raw_materials: [],
            features: [],
            media: null,
        });

        return {
            form,
            editIndex: null,
            rawMaterial: {
                raw_material_id: null,
                quantity: null,
                production_costs: [],
            },
            newFeature: null,
            features: [],
            mesureUnits: [
                'Pieza(s)',
                'Litro(s)',
                'Par(es)',
                'kilogramo(s)',
                'Metro(s)',
                'Rollo(s)',
                'Galon(es)',
                'Cubeta(s)',
                'Bote(s)',
            ],
            productType: 'PP',
            brand: null,
            productTypes: [
                {
                    label: 'Porta-placa',
                    value: 'PP',
                },
                {
                    label: 'Emblema',
                    value: 'EM',
                },
                {
                    label: 'Llavero',
                    value: 'LL',
                },
                {
                    label: 'Parasol',
                    value: 'PS',
                },
                {
                    label: 'Tapete',
                    value: 'TP',
                },
                {
                    label: 'Manta',
                    value: 'MT',
                },
                {
                    label: 'Carpeta',
                    value: 'CP',
                },
                {
                    label: 'Separador',
                    value: 'SP',
                },
                {
                    label: 'Porta-documento',
                    value: 'PD',
                },
                {
                    label: 'Termo',
                    value: 'TM',
                },
                {
                    label: 'Placa de estireno',
                    value: 'PE',
                },
                {
                    label: 'Etiqueta',
                    value: 'ET',
                },
                {
                    label: 'Overlay',
                    value: 'OV',
                },
                {
                    label: 'Accesorio para llavero',
                    value: 'ALL',
                },
                {
                    label: 'Pin',
                    value: 'PI',
                },
                {
                    label: 'Prenda',
                    value: 'PR',
                },
                {
                    label: 'Botella',
                    value: 'BT',
                },
                {
                    label: 'Hielera',
                    value: 'HI',
                },
                {
                    label: 'Funda para auto',
                    value: 'FA',
                },
                {
                    label: 'Perfumero',
                    value: 'PF',
                },
                {
                    label: 'Funda para llave',
                    value: 'FLL',
                },
                {
                    label: 'Bocina',
                    value: 'BC',
                },
                {
                    label: 'Impresión',
                    value: 'IM',
                },
            ],
        };

    },
    components: {
        AppLayout,
        PrimaryButton,
        Link,
        InputError,
        IconInput,
        SecondaryButton
    },
    props: {
        raw_materials: Array,
        production_costs: Array,
        consecutive: String,
    },
    methods: {
        store() {
            this.form.post(route('catalog-products.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Nuevo producto agregado al catálogo',
                        type: 'success'
                    });

                    this.form.reset();
                }
            });
        },
        generatePartNumber() {
            const partNumber = 'C-' + this.productType + '-' + this.brand?.toUpperCase().substr(0, 3) + '-';
            this.form.part_number = partNumber;
        },
        addProduct() {
            const product = { ...this.rawMaterial };

            if (this.editIndex !== null) {
                this.form.raw_materials[this.editIndex] = product;
                this.editIndex = null;
            } else {
                this.form.raw_materials.push(product);
            }

            this.resetProductForm();
        },
        deleteProduct(index) {
            this.form.raw_materials.splice(index, 1);
        },
        editProduct(index) {
            const product = { ...this.form.raw_materials[index] };
            this.rawMaterial = product;
            this.editIndex = index;
        },
        resetProductForm() {
            this.rawMaterial.raw_material_id = null;
            this.rawMaterial.quantity = null;
            this.rawMaterial.production_costs = [];
        },
        addFeature() {
            if (this.newFeature.trim() !== '') {
                this.form.features.push(this.newFeature);
                this.features.push(this.newFeature);
                this.newFeature = '';
            }
        }
    },
};
</script>
