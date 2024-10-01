<template>
  <div>
    <AppLayout title="Crear cliente">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar cliente {{ company.name }}
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <!-- ---------------- Company starts ----------------- -->
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
          <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-2 pb-4">
            <div>
              <IconInput v-model="form.business_name" inputPlaceholder="Razon social *" inputType="text">
                <el-tooltip content="Razon social" placement="top">
                  A
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.business_name" />
            </div>
            <div>
              <IconInput v-model="form.phone" inputPlaceholder="Teléfono *" inputType="text">
                <el-tooltip content="Teléfono" placement="top">
                  <i class="fa-solid fa-phone"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.phone" />
            </div>
            <div>
              <IconInput v-model="form.rfc" inputPlaceholder="RFC *" inputType="text">
                <el-tooltip content="RFC" placement="top">
                  <i class="fa-solid fa-sheet-plastic"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.rfc" />
            </div>
            <div>
              <IconInput v-model="form.post_code" inputPlaceholder="C.P. *" inputType="text">
                <el-tooltip content="C.P." placement="top">
                  <i class="fa-solid fa-envelopes-bulk"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.post_code" />
            </div>
            <div class="col-span-full">
              <IconInput v-model="form.fiscal_address" inputPlaceholder="Domicilio fiscal *">
                <el-tooltip content="Domicilio fiscal" placement="top">
                  <i class="fa-solid fa-building"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.fiscal_address" />
            </div>
            <div>
              <IconInput v-model="form.branches_number" inputPlaceholder="Sucursales totales con las que cuenta el cliente">
                <el-tooltip content="Sucursales totales con las que cuenta el cliente" placement="top">
                  <i class="fa-solid fa-code-branch"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.branches_number" />
            </div>
            <div class="flex items-center">
              <el-tooltip content="Vendedor *" placement="top">
                <i
                  class="fa-solid fa-user-tie font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"></i>
              </el-tooltip>
              <el-select v-model="form.seller_id" placeholder="Vendedor *">
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
          <!-- ---------------- Company ends ----------------- -->

          <!-- ---------------- Company Branch starts ----------------- -->
          <el-divider content-position="left">Sucursales</el-divider>
          <button @click="prefillBranchForm" type="button"
            class="text-sm text-primary underline w-full text-right pr-7">Llenar sucursal con la información
            anterior</button>
          <ol v-if="form.company_branches.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 mb-2">
            <template v-for="(item, index) in form.company_branches" :key="index">
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{ item.name }}
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editBranchIndex == index">En edición</el-tag>
                  <el-button @click="editBranch(index)" type="primary" circle>
                    <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                  </el-button>
                  <!-- <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                    @confirm="deleteBranch(index)">
                    <template #reference>
                      <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
                    </template>
                  </el-popconfirm> -->
                </div>
              </li>
            </template>
          </ol>
          <InputError :message="form.errors.company_branches" />
          <div class="space-y-3 md:w-[92%] mx-auto border-2 border-[#b8b7b7] rounded-lg p-5">
            <div>
              <IconInput v-model="branch.name" inputPlaceholder="Nombre de sucursal *" inputType="text">
                <el-tooltip content="Nombre de sucursal" placement="top">
                  A
                </el-tooltip>
              </IconInput>
            </div>
            <div class="md:col-span-2">
              <IconInput v-model="branch.address" inputPlaceholder="Dirección *" inputType="text">
                <el-tooltip content="Dirección" placement="top">
                  <i class="fa-solid fa-map-location-dot"></i>
                </el-tooltip>
              </IconInput>
            </div>
            <div class="flex items-center">
              <el-tooltip content="Estado" placement="top">
                <i
                  class="fa-solid fa-map-location-dot font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"></i>
              </el-tooltip>
              <el-select v-model="form.state" filterable placeholder="Selecciona el estado de la república" class="w-full"
                no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
                <el-option v-for="(item, index) in states" :key="index" :label="item" :value="item" />
              </el-select>
              <InputError :message="form.errors.state" />
            </div>
            <div>
              <IconInput v-model="branch.post_code" inputPlaceholder="C.P. *" inputType="text">
                <el-tooltip content="C.P." placement="top">
                  <i class="fa-solid fa-envelopes-bulk"></i>
                </el-tooltip>
              </IconInput>
            </div>
            <div class="flex items-center">
              <el-tooltip content="Cómo nos conoció el cliente *" placement="top">
                <i
                  class="fa-solid fa-user-check font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"></i>
              </el-tooltip>
              <el-select v-model="branch.meet_way" placeholder="Cómo nos conoció el cliente *">
                <el-option v-for="item in meetWays" :key="item" :value="item" :label="item" />
              </el-select>
              <InputError :message="form.errors.meet_way" />
            </div>
            <div class="flex items-center">
              <el-tooltip content="Método de pago" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                  sat
                </span>
              </el-tooltip>
              <el-select v-model="branch.sat_method" placeholder="Método de pago">
                <el-option v-for="item in sat_method" :key="item.value" :label="item.label" :value="item.value">
                  <span style="float: left">{{ item.label }}</span>
                  <span style="
                                            float: right;
                                            color: #cccccc;
                                            font-size: 11px;
                                            ">{{ item.value }}</span>
                </el-option>
              </el-select>
            </div>
            <div class="flex items-center">
              <el-tooltip content="Medio de pago" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                  sat
                </span>
              </el-tooltip>
              <el-select v-model="branch.sat_way" placeholder="Medio de pago">
                <el-option v-for="item in sat_ways" :key="item.value" :label="item.label" :value="item.value">
                  <span style="float: left">{{ item.label }}</span>
                  <span style="
                                            float: right;
                                            color: #cccccc;
                                            font-size: 11px;
                                            ">{{ item.value }}</span>
                </el-option>
              </el-select>
            </div>
            <div class="flex items-center">
              <el-tooltip content="Uso de factura" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                  sat
                </span>
              </el-tooltip>
              <el-select v-model="branch.sat_type" placeholder="Uso de factura">
                <el-option v-for="item in sat_types" :key="item.value" :label="item.label" :value="item.value">
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
              <IconInput v-model="branch.days_to_reactivate"
                inputPlaceholder="Días para convertirse en sucursal inactiva *" inputType="number">
                <el-tooltip
                  content="Días sin movimientos (cotizaciones, OV o muestras) para notificar como sucursal inactiva y poder reactivar interacciones"
                  placement="top">
                  <i class="fa-solid fa-angles-down"></i>
                </el-tooltip>
              </IconInput>
            </div>
            <div class="mt-2">
              <div class="flex">
                <el-tooltip
                  content="Estas notas se mostraran cuando se seleccione esta sucursal para crear cotizacion, orden de venta u otro movimiento"
                  placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                    <i class="fa-solid fa-grip-lines"></i>
                  </span>
                </el-tooltip>
                <textarea v-model="branch.important_notes" rows="4" class="textarea mb-1" autocomplete="off"
                  placeholder="Notas. Ejemplo: Precio acordado de 'x' producto en siguiente cotizacion $45.30"></textarea>
              </div>
              <InputError :message="branch.errors?.important_notes" />
            </div>
            <!-- ---------------- Company Branch ends ----------------- -->

            <!-- ---------------- Company Contacts starts ----------------- -->
            <el-divider content-position="left">Contactos</el-divider>
            <ol v-if="contacts.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
              <template v-for="(item, index) in contacts" :key="index">
                <li class="flex justify-between items-center">
                  <p class="text-sm">
                    <span class="text-primary">{{ index + 1 }}.</span>
                    {{ item.name }} | {{ item.email }}
                  </p>
                  <div class="flex space-x-2 items-center">
                    <el-tag v-if="editContactIndex == index">En edición</el-tag>
                    <el-button @click="editContact(index)" type="primary" circle>
                      <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                    </el-button>
                    <!-- <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                      title="¿Continuar?" @confirm="deleteContact(index)">
                      <template #reference>
                        <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
                      </template>
                    </el-popconfirm> -->
                  </div>
                </li>
              </template>
            </ol>
            <div class="md:w-[92%] mx-auto pt-3 md:space-y-3 rounded-lg p-5 border-2 border-[#b8b7b7]">
              <div>
                <IconInput v-model="contact.name" inputPlaceholder="Nombre de contacto *" inputType="text">
                  <el-tooltip content="Nombre de contacto" placement="top">
                    A
                  </el-tooltip>
                </IconInput>
                <!-- <InputError :message="form.errors.name_contact" /> -->
              </div>
              <div class="md:grid gap-x-6 gap-y-2 md:mb-6 grid-cols-2">
                <div class="col-span-full flex items-center">
                  <el-tooltip content="Puesto *" placement="top">
                    <i
                      class="fa-solid fa-briefcase font-bold text-[16px] items-center inline-flex text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    </i>
                  </el-tooltip>
                  <el-select v-model="contact.charge" placeholder="Puesto *">
                    <el-option v-for="item in charges" :key="item" :value="item" :label="item" />
                  </el-select>
                </div>
                <div class="cols-pan-full">
                  <IconInput v-model="contact.email" inputPlaceholder="Correo electrónico principal *" inputType="email">
                    <el-tooltip content="Correo electrónico principal *" placement="top">
                      <i class="fa-solid fa-envelope"></i>
                    </el-tooltip>
                  </IconInput>
                  <!-- correos adicionales -->
                  <div v-for="(additionalEmail, index) in contact.additional_emails" :key="index"
                    class="flex items-center">
                    <IconInput v-model="contact.additional_emails[index]" inputPlaceholder="Correo electrónico adicional"
                      inputType="email">
                      <el-tooltip content="Correo electrónico adicional" placement="top">
                        <i class="fa-solid fa-envelope"></i>
                      </el-tooltip>
                    </IconInput>
                    <button @click="removeAdditionalEmail(index)" type="button"
                      class="text-sm ml-1 hover:text-primary">x</button>
                  </div>
                  <button @click="createAdditionalEmail" type="button" class="text-xs text-primary ml-6">+ Agregar otro
                    correo</button>
                </div>
                <div>
                  <IconInput v-model="contact.phone" inputPlaceholder="Teléfono principal *" inputType="text">
                    <el-tooltip content="Teléfono principal *" placement="top">
                      <i class="fa-solid fa-phone"></i>
                    </el-tooltip>
                  </IconInput>
                  <!-- telefonos adicionales -->
                  <div v-for="(additionalPhone, index) in contact.additional_phones" :key="index"
                    class="flex items-center">
                    <IconInput v-model="contact.additional_phones[index]" inputPlaceholder="Teléfono adicional"
                      inputType="text">
                      <el-tooltip content="Teléfono adicional" placement="top">
                        <i class="fa-solid fa-phone"></i>
                      </el-tooltip>
                    </IconInput>
                    <button @click="removeAdditionalPhone(index)" type="button"
                      class="text-sm ml-1 hover:text-primary">x</button>
                  </div>
                  <button @click="createAdditionalPhone" type="button" class="text-xs text-primary ml-6">+ Agregar otro
                    teléfono</button>
                </div>
              </div>
              <div>
                <div class="flex items-center">
                  <el-tooltip content="Cumpleaños" placement="top">
                    <span
                      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md ">
                      <i class="fa-solid fa-cake"></i>
                    </span>
                  </el-tooltip>
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
              <SecondaryButton @click="addContact" :disabled="!this.contact.name || !this.contact.email || !this.contact.phone || !this.contact.charge
                ">
                {{
                  editContactIndex !== null
                  ? "Actualizar contacto"
                  : "Agregar Contacto a lista"
                }}
              </SecondaryButton>
            </div>
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
          <!-- ---------------- Company Contacts ends ----------------- -->

          <!-- ---------------- Company Products starts ----------------- -->
          <el-divider content-position="left">Productos del cliente</el-divider>
          <ol v-if="form.products.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
            <template v-for="(item, index) in form.products" :key="index">
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{
                    catalog_products.find(
                      (prd) => prd.id === item.catalog_product_id
                    )?.name
                  }}
                  ({{ item.new_price }} {{ item.new_currency }} / unidad)
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editProductIndex == index">En edición</el-tag>
                  <el-button @click="editProduct(index)" type="primary" circle>
                    <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                  </el-button>
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                    @confirm="deleteProduct(index)">
                    <template #reference>
                      <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>

          <div class="space-y-3 rounded-lg p-5">
            <div class="flex items-center">
              <el-tooltip content="Producto de catálogo" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </span>
              </el-tooltip>
              <el-select v-model="product.catalog_product_id" filterable placeholder="Buscar producto">
                <el-option v-for="item in catalog_products" :key="item.id" :label="item.name" :value="item.id" />
              </el-select>
            </div>
            <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-3">
              <div>
                <IconInput v-model="product.old_price" inputPlaceholder="Precio anterior *" inputType="number"
                  inputStep="0.01">
                  <el-tooltip content="Precio anterior" placement="top">
                    <i class="fa-solid fa-money-bill"></i>
                  </el-tooltip>
                </IconInput>
              </div>
              <div class="flex items-center">
                <el-tooltip content="Moneda" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                    <i class="fa-solid fa-dollar-sign"></i>
                  </span>
                </el-tooltip>
                <el-select v-model="product.old_currency" placeholder="Moneda *" :fit-input-width="true">
                  <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                    <span style="float: left">{{ item.label }}</span>
                    <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                  </el-option>
                </el-select>
              </div>
              <div class="flex items-center">
                <el-date-picker v-model="product.old_date" type="date" placeholder="Fecha" format="YYYY/MM/DD"
                  value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
              </div>
              <div>
                <IconInput v-model="product.new_price" inputPlaceholder="Precio nuevo *" inputType="number"
                  inputStep="0.01">
                  <el-tooltip content="Precio nuevo" placement="top">
                    <i class="fa-solid fa-money-bill"></i>
                  </el-tooltip>
                </IconInput>
              </div>
              <div class="flex items-center">
                <el-tooltip content="Moneda" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                    <i class="fa-solid fa-dollar-sign"></i>
                  </span>
                </el-tooltip>
                <el-select v-model="product.new_currency" placeholder="Moneda *" :fit-input-width="true">
                  <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                    <span style="float: left">{{ item.label }}</span>
                    <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                  </el-option>
                </el-select>
              </div>
              <div class="flex items-center">
                <el-date-picker v-model="product.new_date" type="date" placeholder="Fecha" format="YYYY/MM/DD"
                  value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
                <!-- <InputError :message="form.errors.branches.old_date" /> -->
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
          <!-- ---------------- Company Products ends ----------------- -->
          <el-divider />
          <div class="md:text-right">
            <PrimaryButton :disabled="form.processing">
              Actualizar Cliente
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

export default {
  data() {
    const form = useForm({
      business_name: this.company.business_name,
      phone: this.company.phone,
      rfc: this.company.rfc,
      post_code: this.company.post_code,
      fiscal_address: this.company.fiscal_address,
      seller_id: this.company.seller_id,
      branches_number: this.company.branches_number,
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
      charges: [
        'Compras',
        'Dirección',
        'Facturación',
        'Gerencia',
        'Marketing',
        'Pagos',
        'Otro',
      ],
      branch: {
        id: null,
        name: null,
        address: null,
        post_code: null,
        meet_way: null,
        sat_method: null,
        sat_type: null,
        sat_way: null,
        important_notes: null,
        days_to_reactivate: null,
      },
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
    Link
  },
  props: {
    company: Array,
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
    update() {
      this.form.put(route("companies.update", this.company.id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Cliente actualizado",
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

      this.contact.id = null;
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
      this.branch.id = null;
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
          id: element.id,
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
  mounted() {
    this.company.company_branches.forEach((element) => {
      let branch = {
        id: element.id,
        name: element.name,
        address: element.address,
        state: element.state,
        post_code: element.post_code,
        meet_way: element.meet_way,
        sat_method: element.sat_method,
        sat_type: element.sat_type,
        sat_way: element.sat_way,
        important_notes: element.important_notes,
        days_to_reactivate: element.days_to_reactivate,
        contacts: [],
      };

      element.contacts.forEach((item) => {
        const contact = {
          id: item.id,
          name: item.name,
          email: item.email,
          phone: item.phone,
          charge: item.charge,
          birthdate_day: item.birthdate_day,
          birthdate_month: item.birthdate_month,
          additional_emails: item.additional_emails,
          additional_phones: item.additional_phones,
        };

        branch.contacts.push(contact);
      });

      this.form.company_branches.push(branch);
    });

    this.company.catalog_products.forEach((element) => {
      const product = {
        catalog_product_id: element.pivot.catalog_product_id,
        old_date: element.pivot.old_date,
        new_date: element.pivot.new_date,
        old_currency: element.pivot.old_currency,
        new_currency: element.pivot.new_currency,
        old_price: element.pivot.old_price,
        new_price: element.pivot.new_price,
      };

      this.form.products.push(product);
    });
  },
};
</script>
  