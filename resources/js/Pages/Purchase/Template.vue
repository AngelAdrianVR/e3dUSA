<template>
    <div class="text-[11px]">

        <Head :title="'Orden de compra ' + String(purchase.id).padStart(4, '0')" />
        <header class="mt-10">
            <div class="flex items-center justify-between ml-8">
                <ApplicationLogo class="w-[30%]" />
                <p v-if="purchase.is_spanish_template" class="bg-gray2 py-px font-bold text-center px-20">Orden de compra</p>
                <p v-else class="bg-gray2 py-px font-bold text-center px-20">Purchase order</p>
            </div>
            <div class="flex flex-col items-end mr-8">
                <p class="w-48">
                    <span class="mr-6 w-1/2">Folio:</span>
                    <span class="w-1/2 mr-2">OC-{{ String(purchase.id).padStart(4, "0") }}</span>
                    <el-tooltip v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de compra')"
                        content="Redactar correo" placement="top">
                        <button @click="showModal = true" class="text-green-500"><i
                                class="fa-solid fa-check"></i></button>
                    </el-tooltip>
                </p>
                <p class="w-48">
                    <span v-if="purchase.is_spanish_template" class="mr-6 w-1/2">Fecha:</span>
                    <span v-else class="mr-6 w-1/2">Date:</span>
                    <span class="w-1/2">{{ formatDate((purchase.created_at)) }}</span>
                </p>
            </div>
            <section class="mx-8 px-4 py-1 bg-gray2 grid grid-cols-2 gap-1 mt-4">
                <span>EMBLEMS 3D USA</span>
                <span>Av. Aurelio Ortega 518, Seattle, 45150 Zapopan, Jal.</span>
                <span>EDU211206DC9</span>
                <span>33 38338209</span>
            </section>
            <section class="mx-8 px-4 py-1 grid grid-cols-7 gap-1 mt-4">
                <span v-if="purchase.is_spanish_template">Proveedor:</span>
                <span v-else>Supplier:</span>
                <span class="col-span-6">{{ purchase.supplier.name }}</span>
                <span v-if="purchase.is_spanish_template">Domicilio:</span>
                <span v-else>Address:</span>
                <span class="col-span-6">{{ purchase.supplier.address }}</span>
                <span v-if="purchase.is_spanish_template">Telefono:</span>
                <span v-else>Phone:</span>
                <span class="col-span-6">{{ purchase.supplier.phone }}</span>
                <span v-if="purchase.is_spanish_template">Cuenta bancaria:</span>
                <span v-else>Bank account:</span>
                <span class="col-span-6">
                    {{ getBankInfo }}
                </span>
                <span v-if="purchase.is_spanish_template">Observaciones: </span>
                <span v-else>Notes: </span>
                <span class="col-span-6">{{ purchase.notes ?? '-' }}</span>
            </section>
            <section class="mx-8 px-4 py-1 grid grid-cols-4 gap-4 mt-4">
                <article v-for="item in raw_materials" class="px-2">
                    <p class="text-[#525252]">{{ purchase.is_spanish_template ? 'Producto:' : 'Product:'}} <span class="text-secondary">{{ item.part_number + ' ' + item.name }}</span></p>
                    <p class="text-[#525252]">{{ purchase.is_spanish_template ? 'Piezas que quedarán pendientes:' : 'pieces that remain pending:'}} <span class="text-black">{{ purchase.products.find(prd => prd.id == item.id)?.additional_stock?.replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</span></p>
                    <p class="text-[#525252]">{{ purchase.is_spanish_template ? 'Piezas que viajan en avión:' : 'pieces that travel by plane:'}} <span class="text-black">{{ purchase.products.find(prd => prd.id == item.id)?.plane_stock?.replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</span></p>
                    <p class="text-[#525252]">{{ purchase.is_spanish_template ? 'Piezas que viajan en barco:' : 'pieces that travel by boat:'}} <span class="text-black">{{ purchase.products.find(prd => prd.id == item.id)?.ship_stock?.replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</span></p>
                </article>
            </section>
        </header>
        <main class="mx-8 mt-8">
            <table class="w-full">
                <thead>
                    <tr class="*:bg-gray2 *:px-4 *:py-3 *:border *:border-gray1 text-left">
                        <th>{{ purchase.is_spanish_template ? 'Número de parte' : 'Part number'}}</th>
                        <th>{{ purchase.is_spanish_template ? 'Nombre' : 'Name'}}</th>
                        <th>{{ purchase.is_spanish_template ? 'Descripción' : 'Description'}}</th>
                        <th>{{ purchase.is_spanish_template ? 'Cantidad' : 'Quantity'}}</th>
                        <th>{{ purchase.is_spanish_template ? 'Unidad' : 'measure unity'}}</th>
                        <th v-if="purchase.show_prices">{{ purchase.is_spanish_template ? 'Valor unit.' : 'Unit price'}}</th>
                        <th v-if="purchase.show_prices">{{ purchase.is_spanish_template ? 'Importe' : 'Total'}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in raw_materials" :key="item.id"
                        class="*:px-4 *:py-3 *:border *:border-gray1 text-left">
                        <td>{{ item.part_number }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.description }}</td>
                        <td v-if="editQuantity === item.id" class="cursor-pointer" title="Editar cantidad">
                            <div class="flex space-x-2 items-center">
                                <input type="number" v-model="quantity"
                                    class="border border-gray1 rounded-[3px] text-xs w-24" />
                                <button @click="updateQuantity(item.id)"
                                    class="size-5 text-xs rounded-full bg-primary text-white"><i
                                        class="fa-solid fa-check"></i></button>
                                <button @click="editQuantity = null"
                                    class="size-5 text-xs rounded-full bg-gray1 text-white"><i
                                        class="fa-solid fa-xmark"></i></button>
                            </div>
                        </td>
                        <td v-else class="group" title="Editar cantidad">
                            <span>{{ purchase.products.find(prd => prd.id === item.id)?.quantity }}</span>
                            <button
                                @click="editQuantity = item.id; quantity = purchase.products.find(prd => prd.id === item.id)?.quantity"
                                class="size-5 text-xs ml-4 rounded-full bg-gray1 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-100 ease-in-out"><i
                                    class="fa-solid fa-pen"></i></button>

                        </td>
                        <td>{{ item.measure_unit }}</td>
                        <td v-if="purchase.show_prices">${{ item.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                            }}</td>
                        <td v-if="purchase.show_prices">${{ (item.cost * purchase.products.find(prd => prd.id ===
            item.id)?.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                    </tr>
                </tbody>
            </table>
            <!-- imagenes -->
            <section>
                <div class="w-11/12 mx-auto my-3 grid grid-cols-5 gap-4 ">
                    <template v-for="item in raw_materials" :key="item.id">
                        <div class="bg-gray-200 rounded-t-xl rounded-b-md border" style="font-size: 8px;">
                            <img class="rounded-t-xl max-h-52 mx-auto" :src="item.media[0]?.original_url">
                            <p class="py-px px-1 uppercase text-gray-600">{{ item.name }}</p>
                        </div>
                    </template>
                </div>
            </section>
        </main>

        <footer v-if="purchase.show_prices" class="mx-8 mt-8 grid grid-cols-4 gap-x-3 gap-y-1s">
            <section class="flex flex-col col-span-3">
                <header v-if="purchase.is_spanish_template" class="bg-gray2 text-center py-1">Importe con letra</header>
                <header v-else class="bg-gray2 text-center py-1">Amount with lyrics</header>
                <p class="text-center mt-3">
                    {{ purchase.is_iva_included
            ? turnNumberIntoText(getSubtotal * 1.16)
            : turnNumberIntoText(getSubtotal)
                    }}
                </p>
            </section>
            <section class="grid grid-cols-2 gap-x-4 gap-y-2">
                <span>Subtotal</span>
                <span>$ {{ getSubtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                <!-- <span>Descuento</span>
                <span>0.00</span> -->
                <span v-if="purchase.is_iva_included">IVA</span>
                <span v-if="purchase.is_iva_included">$ {{ (getSubtotal *
            0.16).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                ",") }}</span>
                <span>Total</span>
                <span class="font-bold border-y-2 border-gray1">
                    $ {{
            purchase.is_iva_included
                ? (getSubtotal * 1.16).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                : getSubtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }}
                </span>
            </section>
        </footer>

        <!-- Firmas -->
        <div class="grid grid-cols-4 gap-3 mt-20 mx-10 lg:mx-28 text-sm">
            <p class="text-center border-t border-gray-500 w-64">Quién la hace</p>
            <p class="text-center border-t border-gray-500 w-64">Quién la solicita</p>
            <p class="text-center border-t border-gray-500 w-64">Revisión</p>
            <p class="text-center border-t border-gray-500 w-64">Autorizacón</p>
        </div>

        <DialogModal :show="showModal" @close="showModal = false">
            <template #title>
                <h1 class="flex items-center space-x-2 font-bold">
                    <i class="fa-regular fa-envelope text-gray1"></i>
                    <span>Redactar correo</span>
                </h1>
            </template>
            <template #content>
                <form @submit.prevent="sendEmail">
                    <div class="lg:grid grid-cols-2 gap-x-2 gap-y-1">
                        <div>
                            <label class="ml-2 mb-1">Proveedor</label>
                            <p class="rounded-[3px] text-gray1 bg-[#cccccc] text-sm py-1 px-2">{{ purchase.supplier.name
                                }}
                            </p>
                        </div>
                        <div>
                            <label class="ml-2 mb-1">Contacto *</label>
                            <el-select class="w-full" v-model="form.contact_id" placeholder="Seleccione"
                                no-data-text="No hay contactos registrados"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="contact in purchase.supplier.contacts" :key="contact.id"
                                    :value="contact.id" :label="contact.name">
                                    <div class="flex justify-between items-center h-full">
                                        <p class="text-xs">{{ contact.name }}</p>
                                        <p class="text-gray1 text-[11px]">{{ contact.email }}</p>
                                    </div>
                                </el-option>
                            </el-select>
                            <InputError :message="form.errors.contact_id" />
                        </div>
                        <div>
                            <label class="ml-2 mb-1">Cuenta bancaria *</label>
                            <el-select class="w-full" v-model="form.bank_information" placeholder="Seleccione"
                                no-data-text="No hay contactos registrados"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="(bank, index) in purchase.supplier.banks" :key="index" :value="index"
                                    :label="bank.bank_name">
                                    <div class="flex justify-between items-center h-full">
                                        <p class="text-xs">{{ bank.bank_name }}</p>
                                        <p class="text-gray1 text-[11px]">{{ bank.accountNumber }}</p>
                                    </div>
                                </el-option>
                            </el-select>
                            <InputError :message="form.errors.bank_information" />
                        </div>
                    </div>
                    <div v-if="form.contact_id">
                        <h2 class="font-bold text-sm my-2">Datos del correo</h2>
                        <div>
                            <label class="ml-2 mb-1 flex items-center space-x-1">
                                <span>De</span>
                                <el-tooltip placement="top">
                                    <template #content>
                                        <p v-if="!$page.props.auth.user.email_password">
                                            Si quieres que el correo se emita desde <br>
                                            tu correo empresarial o personal, dirigete a<br>
                                            tu perfil e ingresa la contraseña de tu correo <br>
                                            para que el sistema tenga acceso.
                                        </p>
                                        <p v-else>
                                            Si el correo se sigue enviando desde <br>
                                            notificaciones@emblemas3d.com, es porque<br>
                                            la contraseña ingresada no es correcta. <br>
                                            Intenta cambiarla y vuelve a intentar.
                                        </p>
                                    </template>
                                    <div
                                        class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                    </div>
                                </el-tooltip>
                            </label>
                            <p class="rounded-[3px] bg-[#cccccc] text-sm py-1 px-2">
                                <span class="px-2 py-px bg-primarylight rounded-[2px]">
                                    {{ $page.props.auth.user.email_password ? $page.props.auth.user.email :
            'notificaciones@emblemas3d.com' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="ml-2 mb-1">Para</label>
                            <p class="rounded-[3px] bg-[#cccccc] text-sm py-1 px-2">
                                <span class="px-2 py-px bg-secondarylight rounded-[2px]">{{
            purchase.supplier.contacts.find(item => item.id === form.contact_id)?.email
        }}</span>
                            </p>
                        </div>
                        <div class="mt-1">
                            <label class="ml-2 mb-1">Asunto *</label>
                            <input v-model="form.subject" type="text" class="input"
                                placeholder="Escribe el asunto del correo">
                            <InputError :message="form.errors.subject" />
                        </div>
                        <div class="mt-1">
                            <label class="ml-2 mb-1">Descripción</label>
                            <textarea v-model="form.content" class="textarea" rows="5"
                                placeholder="Escribe la descripción del correo"></textarea>
                        </div>
                        <div class="flex mt-2 text-xs">
                            <span> Adjunto:</span>
                            <p class="ml-3 flex items-center space-x-2">
                                <i class="fa-regular fa-file-pdf"></i>
                                <span>Orden de compra OC-{{ String(purchase.id).padStart(4, "0") }}.pdf</span>
                            </p>
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <CancelButton :disabled="loading"
                    @Click="showModal = false">Cancelar</CancelButton>
                <PrimaryButton @click="sendEmail"
                    :disabled="!form.contact_id || form.bank_information === null || !form.subject || loading">Enviar
                </PrimaryButton>
            </template>
        </DialogModal>

        <div v-if="!purchase.authorized_user_name"
            class="absolute left-28 top-1/3 text-red-700 text-6xl border-4 border-red-700 p-10">
            <i class="fas fa-exclamation"></i>
            <span class="ml-2">SIN AUTORIZACIÓN</span>
        </div>
    </div>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import { NumerosALetras } from 'numero-a-letras';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            contact_id: this.purchase.contact_id,
            bank_information: this.purchase.bank_information,
            subject: null,
            content: null,
        });

        return {
            form,
            quantity: null,
            editQuantity: false,
            showModal: false,
            loading: false,
        }
    },
    components: {
        Head,
        ApplicationLogo,
        DialogModal,
        PrimaryButton,
        CancelButton,
        InputError,
    },
    props: {
        purchase: Object,
        raw_materials: Array,
    },
    computed: {
        getSubtotal() {
            const subtotal = this.raw_materials.reduce((accumulator, item) => {
                const quantity = this.purchase.products.find(prd => prd.id === item.id)?.quantity || 0;
                return accumulator + item.cost * quantity;
            }, 0);

            return subtotal;
        },
        getBankInfo() {
            if (this.purchase.bank_information === null) {
                return 'No se ha seleccionado';
            }

            return this.purchase.supplier.banks[this.purchase.bank_information].accountNumber
                + ' ('
                + this.purchase.supplier.banks[this.purchase.bank_information].bank_name
                + ')';
        },
    },
    methods: {
        async sendEmail() {
            this.loading = true;
            try {
                const response = await axios.post(route('purchases.send-email', this.purchase.id), {
                    contact_id: this.form.contact_id,
                    bank_information: this.form.bank_information,
                    subject: this.form.subject,
                    content: this.form.content,
                });

                if (response.status === 200) {
                    this.form.reset();
                    this.showModal = false;
                    this.$notify({
                        title: "Éxito",
                        message: "Correo enviado a proveedor",
                        type: "success",
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "El servidor no pudo procesar la petición",
                    message: "No se logró enviar el correo al proveedor. Inténtalo más tarde",
                    type: "error",
                });
            } finally {
                this.loading = false;
            }
        },
        async updateQuantity(id) {
            try {
                const response = await axios.put(route('purchases.update-quantity', this.purchase.id), { quantity: this.quantity, id: id });

                if (response.status === 200) {
                    this.editQuantity = null;
                    this.purchase.products.find(prd => prd.id === id).quantity = this.quantity;

                    this.$notify({
                        title: "Éxito",
                        message: "Cantidad actualizada",
                        type: "success",
                    });
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "El servidor no pudo procesar la petición",
                    message: "No se logró actualizar la cantidad. Inténtalo más tarde",
                    type: "error",
                });
            }
        },
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMMM Y', { locale: es }); // Formato personalizado
        },
        turnNumberIntoText(amount) {
            // Utiliza la función para convertir el número a letras
            const QuantityInText = NumerosALetras(amount, {
                plural: true,
                centavos: true
            });

            // Formatea el resultado según tus necesidades específicas
            const result = QuantityInText.replace(/^./, (str) => str.toUpperCase());

            return result;
        }
    },
}
</script>

<style></style>