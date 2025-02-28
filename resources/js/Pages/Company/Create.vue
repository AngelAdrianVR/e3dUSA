<template>
  <div>
    <AppLayout title="Crear cliente">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Agregar nuevo cliente
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <!-- ---------------- Company starts ----------------- -->
        <div class="md:w-1/2 md:mx-auto mx-3 my-3 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md transition-all ease-linear duration-500">
          <div class="md:grid gap-3 mb-6 grid-cols-2 pb-4">
            <div>
              <InputLabel value="Razon social*" />
              <el-input v-model="form.business_name" placeholder="Ej. Bimbo SA de CV" />
              <InputError :message="form.errors.business_name" />
            </div>
            <div>
              <InputLabel value="Teléfono*" />
              <el-input v-model="form.phone" placeholder="Ej. 3336249765" />
              <InputError :message="form.errors.phone" />
            </div>
            <div>
              <InputLabel value="RFC*" />
              <el-input v-model="form.rfc" placeholder="Ej. BCGD500345RW1" />
              <InputError :message="form.errors.rfc" />
            </div>
            <div>
              <InputLabel value="C.P.*" />
              <el-input v-model="form.post_code" placeholder="Ej. 45150" />
              <InputError :message="form.errors.post_code" />
            </div>
            <div class="col-span-full">
              <InputLabel value="Domicilio fiscal*" />
              <el-input v-model="form.fiscal_address" placeholder="Ej. Av laureles #178, col. El vigia" />
              <InputError :message="form.errors.fiscal_address" />
            </div>
            <div>
              <InputLabel value="Sucursales totales del cliente*" />
              <el-input v-model="form.branches_number" placeholder="Ej. 7" />
              <InputError :message="form.errors.branches_number" />
            </div>
            <div>
              <InputLabel value="Vendedor*" />
              <el-select v-model="form.seller_id" placeholder="Selecciona">
                <el-option v-for="(item, index) in sellers" :key="item.id" :label="item.name" :value="item.id">
                  <div v-if="$page.props.jetstream.managesProfilePhotos"
                    class="flex text-sm rounded-full items-center mt-[3px]">
                    <img class="size-7 rounded-full object-cover mr-4" :src="item.profile_photo_url" :alt="item.name" />
                    <p>{{ item.name }}</p>
                  </div>
                </el-option>
              </el-select>
              <InputError :message="form.errors.seller_id" />
            </div>
          </div>

          <!-- ---------------- Company Branch starts ----------------- -->
          <el-divider content-position="left">Sucursales</el-divider>
          <button @click="prefillBranchForm" type="button"
            class="text-sm text-primary underline w-full text-right pr-7">Llenar sucursal con la información
            anterior</button>
          <InputError :message="form.errors.company_branches" />
          <div class="space-y-3 md:w-[92%] mx-auto border-2 border-[#b8b7b7] rounded-lg p-5">
            <div>
              <InputLabel value="Nombre de sucursal*" />
              <el-input v-model="branch.name" placeholder="Ej. Sucursal Aguas calientes" />
            </div>
            <div>
              <InputLabel value="Dirección*" />
              <el-input v-model="branch.address" placeholder="Ej. Sucursal Aguas calientes" />
            </div>
            <div>
              <InputLabel value="Estado*" />
              <el-select v-model="branch.state" filterable placeholder="Selecciona el estado de la república"
                class="w-full" no-data-text="No hay opciones para mostrar"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="(item, index) in states" :key="index" :label="item" :value="item" />
              </el-select>
            </div>
            <div>
              <InputLabel value="C.P.*" />
              <el-input v-model="branch.post_code" placeholder="Ej. 60589" />
            </div>
            <div>
              <InputLabel value="Cómo nos conoció el cliente*" />
              <el-select v-model="branch.meet_way" placeholder="Selecciona">
                <el-option v-for="item in meetWays" :key="item" :value="item" :label="item" />
              </el-select>
            </div>
            <div>
              <InputLabel value="Método de pago*" />
              <el-select v-model="branch.sat_method" placeholder="Selecciona">
                <el-option v-for="item in sat_method" :key="item.value" :value="item.value" :label="item.label">
                  <span style="float: left">{{ item.label }}</span>
                  <span style="
                    float: right;
                    color: #cccccc;
                    font-size: 11px;
                    ">{{ item.value }}</span>
                </el-option>
              </el-select>
            </div>
            <div>
              <InputLabel value="Medio de pago*" />
              <el-select v-model="branch.sat_way" placeholder="Selecciona">
                <el-option v-for="item in sat_ways" :key="item.value" :value="item.value" :label="item.label">
                  <span style="float: left">{{ item.label }}</span>
                  <span style="
                    float: right;
                    color: #cccccc;
                    font-size: 11px;
                    ">{{ item.value }}</span>
                </el-option>
              </el-select>
            </div>
            <div>
              <InputLabel value="Uso de factura*" />
              <el-select v-model="branch.sat_type" placeholder="Selecciona">
                <el-option v-for="item in sat_types" :key="item.value" :value="item.value" :label="item.label">
                  <span style="float: left">{{ item.label }}</span>
                  <span style="
                    float: right;
                    color: #cccccc;
                    font-size: 11px;
                    ">{{ item.value }}</span>
                </el-option>
              </el-select>
            </div>
            <div>
              <InputLabel>
                <div class="flex items-center">
                  <span>Días para convertirse en sucursal inactiva*</span>
                  <el-tooltip placement="top">
                    <template #content>
                      <p>
                        Al pasar estos días sin movimientos (cotizaciones, OV o muestras) <br>
                        Se notificará por correo para poder reactivar interacciones con el cliente.
                      </p>
                    </template>
                    <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                </div>
              </InputLabel>
              <el-input v-model="branch.days_to_reactivate" type="text"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 30" />
            </div>
            <div>
              <InputLabel>
                <div class="flex items-center">
                  <span>Notas importantes</span>
                  <el-tooltip placement="top">
                    <template #content>
                      <p>
                        Estas notas se mostraran cuando se seleccione esta sucursal <br>
                        para crear cotizacion, orden de venta u otro movimiento
                      </p>
                    </template>
                    <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                </div>
              </InputLabel>
              <el-input v-model="branch.important_notes" :rows="3" maxlength="800"
                placeholder="Ej. Precio acordado de 'x' producto en siguiente cotizacion $45.30" show-word-limit
                type="textarea" />
              <InputError :message="branch.errors?.important_notes" />
            </div>

            <!-- ---------------- Company Contacts starts ----------------- -->
            <el-divider content-position="left">Contactos</el-divider>
            <div class="md:w-[92%] mx-auto pt-3 md:space-y-3 rounded-lg p-5 border-2 border-[#b8b7b7]">
              <div>
                <InputLabel value="Nombre de contacto*" />
                <el-input v-model="contact.name" placeholder="Ej. Manuel Avila" />
              </div>
              <div>
                <InputLabel value="Puesto*" />
                <el-select v-model="contact.charge" placeholder="Selecciona">
                  <el-option v-for="item in charges" :key="item" :value="item" :label="item" />
                </el-select>
              </div>
              <div class="md:grid gap-x-6 gap-y-2 md:mb-6 grid-cols-2">
                <div class="cols-pan-full">
                  <InputLabel value="email*" />
                  <el-input v-model="contact.email" placeholder="Ej. usuario@ejemplo.com" />

                  <!-- correos adicionales -->
                  <div v-for="(additionalEmail, index) in contact.additional_emails" :key="index"
                    class="flex items-center">
                    <el-input v-model="contact.additional_emails[index]" placeholder="Ej. usuario2@ejemplo.com"
                      class="mt-2" />
                    <button @click="removeAdditionalEmail(index)" type="button"
                      class="text-sm ml-1 hover:text-primary">x</button>
                  </div>
                  <button @click="createAdditionalEmail" type="button" class="text-xs text-primary ml-6">+ Agregar otro
                    correo</button>
                </div>
                <div>
                  <InputLabel value="Teléfono principal*" />
                  <el-input v-model="contact.phone" placeholder="Ej. 3316879633" />
                  <!-- telefonos adicionales -->
                  <div v-for="(additionalPhone, index) in contact.additional_phones" :key="index"
                    class="flex items-center">
                    <el-input v-model="contact.additional_phones[index]" placeholder="Ej. 3316879633" class="mt-2" />
                    <button @click="removeAdditionalPhone(index)" type="button"
                      class="text-sm ml-1 hover:text-primary">x</button>
                  </div>
                  <button @click="createAdditionalPhone" type="button" class="text-xs text-primary ml-6">+ Agregar otro
                    teléfono</button>
                </div>
              </div>
              <div>
                <div>
                  <InputLabel value="Cumpleaños*" />
                  <div class="grid grid-cols-2 gap-2 w-full">
                    <el-select v-model="contact.birthdate_day" placeholder="Dia">
                      <el-option v-for="day in 31" :key="day" :label="day" :value="day" />
                    </el-select>
                    <el-select v-model="contact.birthdate_month" placeholder="Mes">
                      <el-option v-for="(month, index) in months" :key="index" :label="month" :value="index" />
                    </el-select>
                  </div>
                </div>
              </div>
              <SecondaryButton @click="addContact"
                :disabled="!this.contact.name || !this.contact.email || !this.contact.phone || !this.contact.charge">
                {{
                  editContactIndex !== null
                    ? "Actualizar contacto"
                    : "Agregar Contacto a lista"
                }}
              </SecondaryButton>
            </div>
            <ol v-if="contacts.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 divide-y-[1px]">
              <template v-for="(item, index) in contacts" :key="index">
                <li class="flex justify-between items-center border-[#999999] py-1">
                  <p class="text-xs">
                    <span class="text-primary">{{ index + 1 }}.</span>
                    {{ item.name }} | {{ item.email }}
                  </p>
                  <div class="flex space-x-2 items-center">
                    <el-tag v-if="editContactIndex == index" @close="editContactIndex = null; resetContactForm()" closable>En edición</el-tag>
                    <button @click="editContact(index)" type="button"
                      class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                      </svg>
                    </button>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                      title="¿Continuar?" @confirm="deleteContact(index)">
                      <template #reference>
                        <button type="button"
                          class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                          </svg>
                        </button>
                      </template>
                    </el-popconfirm>
                  </div>
                </li>
              </template>
            </ol>
            <div>
              <SecondaryButton @click="addBranch" :disabled="contacts.length == 0 ||
                !branch.name ||
                !branch.address ||
                !branch.post_code ||
                !branch.sat_method ||
                !branch.sat_type ||
                !branch.sat_way
                ">
                {{
                  editBranchIndex !== null
                    ? "Actualizar sucursal"
                    : "Agregar sucursal a lista"
                }}
              </SecondaryButton>
            </div>
          </div>
          <ol v-if="form.company_branches.length"
            class="rounded-lg bg-[#CCCCCC] text-black px-5 py-2 col-span-full space-y-1 mb-2 divide-y-[1px] mt-3">
            <template v-for="(item, index) in form.company_branches" :key="index">
              <li class="flex justify-between items-center border-[#999999] py-1">
                <p class="text-xs">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{ item.name }}
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editBranchIndex == index" @close="editBranchIndex = null; resetBranchForm()" closable>En edición</el-tag>
                  <button @click="editBranch(index)" type="button"
                    class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                    title="¿Continuar?" @confirm="deleteBranch(index)">
                    <template #reference>
                      <button type="button"
                        class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                      </button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>

          <!-- ---------------- Company Products starts ----------------- -->
          <el-divider content-position="left">Productos del cliente</el-divider>
          <div class="space-y-3 rounded-lg p-5">
            <div>
              <InputLabel value="Producto de catálogo*" />
              <el-select v-model="product.catalog_product_id" filterable placeholder="Buscar producto">
                <el-option v-for="item in catalog_products" :key="item.id" :label="item.name" :value="item.id" />
              </el-select>
            </div>
            <div class="md:grid gap-x-3 gap-y-2 mb-6 grid-cols-3">
              <div>
                <InputLabel value="Precio anterior" />
                <el-input v-model="product.old_price" type="text"
                  :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 14.50" />
              </div>
              <div>
                <InputLabel value="Moneda" />
                <el-select v-model="product.old_currency" placeholder="Seleccionar" :fit-input-width="true">
                  <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                    <span style="float: left">{{ item.label }}</span>
                    <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                  </el-option>
                </el-select>
              </div>
              <div>
                <InputLabel value="Precio anterior fijado el*" />
                <el-date-picker v-model="product.old_date" type="date" placeholder="Fecha" format="YYYY/MM/DD"
                  value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
              </div>
              <div>
                <InputLabel value="Precio actual*" />
                <el-input v-model="product.new_price" type="text"
                  :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 18.00" />
              </div>
              <div>
                <InputLabel value="Moneda*" />
                <el-select v-model="product.new_currency" placeholder="Seleccionar" :fit-input-width="true">
                  <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                    <span style="float: left">{{ item.label }}</span>
                    <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                  </el-option>
                </el-select>
              </div>
              <div>
                <InputLabel value="Precio actual fijado el*" />
                <el-date-picker v-model="product.new_date" type="date" placeholder="Fecha" format="YYYY/MM/DD"
                  value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
              </div>
            </div>
            <SecondaryButton @click="addProduct" :disabled="!product.catalog_product_id ||
              !product.new_date ||
              !product.new_currency ||
              !product.new_price
              ">
              {{
                editProductIndex !== null
                  ? "Actualizar producto"
                  : "Agregar producto a lista"
              }}
            </SecondaryButton>
          </div>
          <ol v-if="form.products.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 divide-y-[1px]">
            <template v-for="(item, index) in form.products" :key="index">
              <li class="flex justify-between items-center border-[#999999] py-1">
                <p class="text-xs">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{
                    catalog_products.find(
                      (prd) => prd.id === item.catalog_product_id
                    )?.name
                  }}
                  ({{ item.new_price }} {{ item.new_currency }} / unidad)
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editProductIndex == index" @close="editProductIndex = null; resetProductForm()" closable>En edición</el-tag>
                  <button @click="editProduct(index)" type="button"
                    class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                    title="¿Continuar?" @confirm="deleteProduct(index)">
                    <template #reference>
                      <button type="button"
                        class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                      </button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>

          <el-divider />
          <div class="md:text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Agregar Cliente
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
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      business_name: null,
      phone: null,
      rfc: null,
      post_code: null,
      fiscal_address: null,
      branches_number: null,
      seller_id: null,
      company_branches: [],
      products: [],
    });

    return {
      form,
      contacts: [],
      editContactIndex: null,
      editProductIndex: null,
      editBranchIndex: null,
      contact: {
        name: null,
        email: null,
        phone: null,
        charge: null,
        birthdate_day: null,
        birthdate_month: null,
        additional_emails: [],
        additional_phones: [],
      },
      branch: {
        name: null,
        address: null,
        state: null,
        post_code: null,
        meet_way: null,
        sat_method: null,
        sat_type: null,
        sat_way: null,
        important_notes: null,
        days_to_reactivate: 30,
      },
      states: [
        'Aguascalientes',
        'Baja California',
        'Baja California Sur',
        'Campeche',
        'Chiapas',
        'Chihuahua',
        'Ciudad de México',
        'Coahuila',
        'Colima',
        'Durango',
        'Estado de México',
        'Guanajuato',
        'Guerrero',
        'Hidalgo',
        'Jalisco',
        'Michoacán',
        'Morelos',
        'Nayarit',
        'Nuevo León',
        'Oaxaca',
        'Puebla',
        'Querétaro',
        'Quintana Roo',
        'San Luis Potosí',
        'Sinaloa',
        'Sonora',
        'Tabasco',
        'Tamaulipas',
        'Tlaxcala',
        'Veracruz',
        'Yucatán',
        'Zacatecas',
      ],
      meetWays: [
        'Recomendación',
        'Búsqueda en línea',
        'Publicidad ',
        'Evento o feria comercial',
        'Correo electrónico',
        'Llamada telefónica ',
        'Sitio web de la empresa',
        'Otro',
      ],
      product: {
        catalog_product_id: null,
        old_date: null,
        new_date: null,
        old_currency: null,
        new_currency: null,
        old_price: null,
        new_price: null,
      },
      charges: [
        'Compras',
        'Dirección',
        'Facturación',
        'Gerencia',
        'Marketing',
        'Pagos',
        'Otro',
      ],
      currencies: [
        { value: "$MXN", label: "MXN" },
        { value: "$USD", label: "USD" },
      ],
      months: [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
      ],
      sat_method: [
        { value: "PUE", label: "Pago en una sola exhibición" },
        { value: "PPD", label: "Pago en parcialidades o diferido" },
      ],
      sat_types: [
        { value: "G01", label: "Adquisición de mercancías" },
        // { value: "G02", label: "Devoluciones, descuentos o bonificaciones" },
        { value: "G03", label: "Gastos en general" },
        // { value: "I01", label: "Construcciones" },
        // {
        //   value: "I02",
        //   label: "Mobiliario y equipo de oficina por inversiones",
        // },
        // { value: "I03", label: "Equipo de transporte" },
        // { value: "I04", label: "Equipo de cómputo y accesorios" },
        // {
        //   value: "I05",
        //   label: "Dados, troqueles, moldes, matrices y herramental",
        // },
        // { value: "I06", label: "Comunicaciones telefónicas" },
        // { value: "I07", label: "Comunicaciones satelitales" },
        // { value: "I08", label: "Otra maquinaria y equipo" },
        // {
        //   value: "D01",
        //   label: "Honorarios médicos, dentales y gastos hospitalarios",
        // },
        // {
        //   value: "D02",
        //   label: "Gastos médicos por incapacidad o discapacidad",
        // },
        // { value: "D03", label: "Gastos funerales" },
        // { value: "D04", label: "Donativos" },
        // {
        //   value: "D05",
        //   label:
        //     "Initereses reales efectivamente pagados por créditos hipotecarios (casa habitación)",
        // },
        // { value: "D06", label: "Aportaciones voluntarias al SAR" },
        // { value: "D07", label: "Primas por seguro de gastos médicos" },
        // { value: "D08", label: "Gastos de transportación escolar obligatoria" },
        // {
        //   value: "D09",
        //   label:
        //     "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones",
        // },
        // { value: "D10", label: "Pagos por servicio educativo (colegiaturas)" },
        // { value: "P01", label: "Por definir" },
      ],
      sat_ways: [
        // { value: "01", label: "Efectivo" },
        { value: "02", label: "cheque nominativo" },
        { value: "03", label: "Transferencia electrónica de fondos" },
        // { value: "04", label: "Tarjeta de crédito" },
        // { value: "05", label: "Monedero electrónico" },
        // { value: "06", label: "Dinero electrónico" },
        // { value: "08", label: "Vales de despensa" },
        // { value: "12", label: "Dación en pago" },
        // { value: "13", label: "Pago por subrogación" },
        // { value: "14", label: "Pago por consignación" },
        // { value: "15", label: "Condonación" },
        // { value: "17", label: "Compensación" },
        // { value: "23", label: "Novación" },
        // { value: "24", label: "Confusión" },
        // { value: "25", label: "Remisión de deuda" },
        // { value: "26", label: "Prescripción o caducidad" },
        // { value: "27", label: "A satisfaccipon del acreedor" },
        // { value: "28", label: "Tarjeta de débito" },
        // { value: "29", label: "Tarjeta de servicios" },
        { value: "99", label: "Por definir" },
      ],
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Back,
    Link,
    InputLabel,
  },
  props: {
    catalog_products: Array,
    sellers: Array,
  },
  methods: {
    removeAdditionalEmail(index) {
      this.contact.additional_emails.splice(index, 1);
    },
    removeAdditionalPhone(index) {
      this.contact.additional_phones.splice(index, 1);
    },
    createAdditionalEmail() {
      this.contact.additional_emails.push(null);
    },
    createAdditionalPhone() {
      this.contact.additional_phones.push(null);
    },
    prefillBranchForm() {
      this.branch.name = this.form.business_name;
      this.branch.address = this.form.fiscal_address;
      this.branch.post_code = this.form.post_code;
    },
    store() {
      this.form.post(route("companies.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Cliente creado",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
    // contacts
    addContact() {
      const contact = { ...this.contact };

      if (this.editContactIndex !== null) {
        this.contacts[this.editContactIndex] = contact;
        this.editContactIndex = null;
      } else {
        this.contacts.push(contact);
      }

      this.resetContactForm();
    },
    resetContactForm() {
      this.contact.name = null;
      this.contact.email = null;
      this.contact.phone = null;
      this.contact.birthdate_day = null;
      this.contact.birthdate_month = null;
      this.contact.charge = null;
      this.contact.additional_emails = [];
      this.contact.additional_phones = [];
    },
    deleteContact(index) {
      this.contacts.splice(index, 1);
    },
    editContact(index) {
      const contact = { ...this.contacts[index] };
      this.contact = contact;
      this.editContactIndex = index;
    },
    // branches
    addBranch() {
      let branch = { ...this.branch };
      branch.contacts = this.contacts;

      if (this.editBranchIndex !== null) {
        this.form.company_branches[this.editBranchIndex] = branch;
        this.editBranchIndex = null;
      } else {
        this.form.company_branches.push(branch);
      }

      // reser branch form & list of contacts
      this.resetBranchForm();
    },
    resetBranchForm() {
      this.branch.name = null;
      this.branch.address = null;
      this.branch.post_code = null;
      this.branch.meet_way = null;
      this.branch.sat_method = null;
      this.branch.sat_type = null;
      this.branch.sat_way = null;
      this.branch.important_notes = null;
      this.branch.days_to_reactivate = null;
      this.contacts = [];
    },
    deleteBranch(index) {
      this.form.company_branches.splice(index, 1);
    },
    editBranch(index) {
      const branch = { ...this.form.company_branches[index] };

      this.form.company_branches[index].contacts.forEach((element) => {
        const contact = {
          name: element.name,
          email: element.email,
          phone: element.phone,
          charge: element.charge,
          birthdate_day: element.birthdate_day,
          birthdate_month: element.birthdate_month,
          additional_emails: element.additional_emails ?? [],
          additional_phones: element.additional_phones ?? [],
        };

        this.contacts.push(contact);
      });

      this.branch = branch;
      this.editBranchIndex = index;
    },
    // products
    addProduct() {
      let product = { ...this.product };

      if (this.editProductIndex !== null) {
        this.form.products[this.editProductIndex] = product;
        this.editProductIndex = null;
      } else {
        this.form.products.push(product);
      }

      // reser product form
      this.resetProductForm();
    },
    resetProductForm() {
      this.product.catalog_product_id = null;
      this.product.old_date = null;
      this.product.new_date = null;
      this.product.old_currency = null;
      this.product.new_currency = null;
      this.product.old_price = null;
      this.product.new_price = null;
    },
    deleteProduct(index) {
      this.form.products.splice(index, 1);
    },
    editProduct(index) {
      const product = { ...this.form.products[index] };

      this.product = product;
      this.editProductIndex = index;
    },
  },
};
</script>
