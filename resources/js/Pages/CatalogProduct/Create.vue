<template>
    <AppLayout title="Catalogo de productos - Crear">
        <template #header>
            <div class="flex justify-between">
                <Back />
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Agregar nuevo producto a catálogo</h2>
                </div>
            </div>
        </template>

        <!-- Form -->
        <form @submit.prevent="store" class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
            <div class="my-2">
                <InputLabel value="Tipo de producto (necesario para generar el número de parte)" />
                <el-select @change="generatePartNumber" v-model="productType" placeholder="Tipo de producto *">
                    <el-option v-for="item in productTypes" :key="item.value" :label="item.label" :value="item.value">
                        <span style="float: left">{{ item.label }}</span>
                        <span style="float: right; color: #cccccc; font-size: 13px;">
                            {{ item.value }}
                        </span>
                    </el-option>
                </el-select>
            </div>
            <!-- <div class="flex items-center mb-2">
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
            </div> -->
            <div class="md:grid gap-x-6 gap-y-2 grid-cols-2 my-3">
                <div>
                    <InputLabel value="Marca del producto *" />
                        <el-input v-model="brand" @change="generatePartNumber" inputPlaceholder="Marca del producto *" />
                </div>
                <div>
                    <InputLabel value="Nombre del producto *" />
                    <el-input v-model="form.name" inputPlaceholder="Nombre *" />
                    <InputError :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel value="Número de parte *" />
                    <el-input v-model="form.part_number" />
                    <InputError :message="form.errors.part_number" />
                </div>
                <div>
                    <InputLabel value="Unidad de medida" />
                    <el-select v-model="form.measure_unit" clearable placeholder="Selecciona la unidad de medida"
                        no-data-text="No hay unidades de medida registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="(item, index) in mesureUnits" :key="index" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.measure_unit" />
                </div>
                <div>
                    <InputLabel value="Cantidad mínima" />
                    <el-input v-model="form.min_quantity" inputPlaceholder="Cantidad mínima" 
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"/>
                    <InputError :message="form.errors.min_quantity" />
                </div>
                <div>
                    <InputLabel value="Cantidad mínima" />
                    <el-input v-model="form.max_quantity" inputPlaceholder="Cantidad máxima" 
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"/>
                    <InputError :message="form.errors.max_quantity" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Descripción" />
                    <el-input
                        v-model="form.description"
                        rows="3"
                        maxlength="800"
                        placeholder="Descripción"
                        show-word-limit
                        type="textarea"
                    />
                    <InputError :message="form.errors.max_quantity" />
                </div>
            
            <!-- <div class="mb-2">
                <IconInput v-model="brand" @change="generatePartNumber" inputPlaceholder="Marca del producto *"
                    inputType="text">
                    <el-tooltip content="Marca del producto (si no tiene marca colocar 'Generico')" placement="top">
                        <i class="fa-solid fa-copyright"></i>
                    </el-tooltip>
                </IconInput>
            </div> -->
            <!-- <div class="mb-2">
                <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
                    <el-tooltip content="Nombre" placement="top">
                        A
                    </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.name" />
            </div> -->
            <!-- <div class="md:grid gap-x-6 gap-y-2 my-3 grid-cols-2"> -->
                <!-- <div class="flex items-center">
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
                </div> -->
                <!-- <div class="flex items-center">
                    <el-tooltip content="Unidad de medida" placement="top">
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
                </div> -->
                <!-- <div>
                    <IconInput v-model="form.min_quantity" inputPlaceholder="Cantidad mínima" inputType="number">
                        <el-tooltip content="Cantidad mínima" placement="top">
                            <i class="fa-solid fa-minus"></i>
                        </el-tooltip>
                    </IconInput>
                    <InputError :message="form.errors.min_quantity" />
                </div> -->
                <!-- <div>
                    <IconInput v-model="form.max_quantity" inputPlaceholder="Cantidad máxima" inputType="number">
                        <el-tooltip content="Cantidad máxima" placement="top">
                            <i class="fa-solid fa-plus"></i>
                        </el-tooltip>
                    </IconInput>
                    <InputError :message="form.errors.max_quantity" />
                </div> -->
                <!-- <div class="flex col-span-full">
                    <el-tooltip content="Descripción" placement="top">
                        <span
                            class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                            ...
                        </span>
                    </el-tooltip>
                    <textarea v-model="form.description" class="textarea mb-1" autocomplete="off"
                        placeholder="Descripción"></textarea>
                    <InputError :message="form.errors.description" />
                </div> -->
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

                <el-upload action="#" list-type="picture-card" :auto-upload="false" :on-change="handleChange" class="col-span-full" ref="upload">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-black">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                    <template #file="{ file }">
                    <div>
                        <img class="el-upload-list__item-thumbnail" :src="file.url" alt="" />
                        <span class="el-upload-list__item-actions">
                            <span
                                class="el-upload-list__item-preview"
                                @click="handlePictureCardPreview(file)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                                </svg>

                            </span>
                            <span
                                v-if="!disabled"
                                class="el-upload-list__item-delete"
                                @click="handleDownloadImage(file)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>

                            </span>
                            <span
                                v-if="!disabled"
                                class="el-upload-list__item-delete"
                                @click="handleRemoveImage(file)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </span>
                        </span>
                    </div>
                    </template>
                </el-upload>

                <el-dialog v-model="dialogVisible">
                    <img class="mx-auto" w-full :src="dialogImageUrl" alt="Preview Image" />
                </el-dialog>

                <!-- <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 col-span-full">
                    <InputFilePreview v-for="(file,index) in form.media" :key="index" :canDelete="index == (form.media.length - 2)"
                        @imagen="saveImage" @cleared="handleCleared(index)" class="p-2" />
                </div> -->

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
                                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
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
                    <el-select @change="fetchRawMaterial" v-model="rawMaterial.raw_material_id" clearable filterable
                        placeholder="Busca en materias primas" no-data-text="No hay materias primas registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in raw_materials" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                </div>
                <div v-if="loading" class="rounded-md bg-[#CCCCCC] text-xs text-gray-500 text-center p-4">
                    cargando imagen...
                </div>
                <figure v-else-if="selectedRawMaterial" class="rounded-md">
                    <img :src="selectedRawMaterial.media[0]?.original_url" class="rounded-md object-cover w-36">
                </figure>
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
                        :disabled="!rawMaterial.raw_material_id || !rawMaterial.quantity || form.processing || !rawMaterial.production_costs.length">
                        {{ editIndex !== null ? 'Actualizar componente' : 'Agregar componente a lista' }}
                    </SecondaryButton>
                </div>
            </div>

            <el-divider />
            <div class="md:text-right">
                <PrimaryButton :disabled="form.processing || !form.raw_materials.length">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Crear producto
                </PrimaryButton>
            </div>
            {{ form.media }}
        </form>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

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
            media: [null],
        });

        return {
            form,
            dialogVisible: false, //imagen element-plus
            dialogImageUrl: '', //imagen element-plus
            editIndex: null,
            loading: false,
            rawMaterial: {
                raw_material_id: null,
                quantity: null,
                production_costs: [],
            },
            newFeature: null,
            selectedRawMaterial: null,
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
        InputFilePreview,
        SecondaryButton,
        PrimaryButton,
        InputLabel,
        InputError,
        IconInput,
        Back,
        Link
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
        handleChange(file, fileList) {
            this.form.media = fileList.map(item => item.raw); // Actualiza form.media con los archivos
            console.log(this.form);
        },
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;
        },
        handleDownloadImage(file) {
            // Verifica si el archivo tiene una URL válida
            if (file.url) {
                // Crea un enlace temporal para descargar el archivo
                const link = document.createElement('a');
                link.href = file.url;
                link.download = file.name || 'download'; // nombre del archivo a descargar
                link.click();
            } else {
                console.error('No hay URL disponible para descargar el archivo.');
            }
        },
        handleRemoveImage(file) {
            this.$confirm('¿Estás seguro de eliminar este archivo?', 'Confirmar', {
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            type: 'warning'
            }).then(() => {
            // Remover de fileList (el-upload)
            const index = this.$refs.upload.uploadFiles.indexOf(file);
            if (index !== -1) {
                this.$refs.upload.uploadFiles.splice(index, 1);
            }
            
            // Remover de form.media
            const mediaIndex = this.form.media.indexOf(file.raw);
            if (mediaIndex !== -1) {
                this.form.media.splice(mediaIndex, 1);
            }
            }).catch(() => {
            this.$message({
                type: 'info',
                message: 'Eliminación cancelada'
            });
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
            this.selectedRawMaterial = null;
        },
        deleteProduct(index) {
            this.form.raw_materials.splice(index, 1);
        },
        editProduct(index) {
            const product = { ...this.form.raw_materials[index] };
            this.rawMaterial = product;
            this.editIndex = index;
            this.fetchRawMaterial();
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
        },
        saveImage(image) {
            const currentIndex = this.form.media.length -1;
            this.form.media[currentIndex] = image;
            this.form.media.push(null);
        },
        handleCleared(index) {
            // Eliminar el componente y su informacion correspondiente cuando se borra la imagen
            this.form.media.splice(index, 1);
        },
        async fetchRawMaterial() {
            this.loading = true;
            try {
                const response = await axios.get(route('raw-materials.fetch', this.rawMaterial.raw_material_id)); 

                if (response.status === 200) {
                this.selectedRawMaterial = response.data.item;
                }

            } catch (error) {
                console.log(error);

            } finally {
                this.loading = false;
            }

        },
    },
};
</script>
